<?php


namespace CP_Defender\Module\Scan\Behavior\Pro;

use Hammer\Base\Behavior;
use Hammer\Helper\Log_Helper;
use Hammer\Helper\WP_Helper;
use CP_Defender\Behavior\Utils;
use CP_Defender\Component\Error_Code;
use CP_Defender\Module\Scan;
use CP_Defender\Module\Scan\Component\Scan_Api;

class Content_Scan extends Behavior {
	const CONTENT_CHECKSUM = 'cleanchecksum', FILES_TRIED = 'filestried';
	/**
	 * @var Scan\Model\Scan
	 */
	protected $model;
	protected $oldChecksum = null;
	protected $tries = null;
	protected $tokens = array();
	protected $patterns = array();

	public function processItemInternal( $args, $current ) {
		$start          = microtime( true );
		$this->model    = $args['model'];
		$this->patterns = $args['patterns'];

		$this->populateChecksums();
		$this->populateTries();
		if ( ( $oid = Scan_Api::isIgnored( $current ) ) !== false ) {
			//if this is ignored, we just need to update the parent ID
			$item           = Scan\Model\Result_Item::findByID( $oid );
			$item->parentId = $this->model->id;
			$item->save();

			return true;
		}
		//Log_Helper::logger( 'process file ' . $current );
		try {
			$ret = $this->_scan_a_file( $current );
		} catch ( \Exception $e ) {
			$ret = false;
		}
		$end  = microtime( true );
		$time = round( $end - $start, 2 );

		//Log_Helper::logger( $current . '-' . $time );

		return $ret;
	}

	/**
	 * Check if this file has scanned before and return a good result
	 *
	 * @param $file
	 * @param $checksum
	 *
	 * @return bool
	 */
	private function checksumCheck( $file, &$checksum ) {
		$checksum = md5_file( $file );
		if ( isset( $this->oldChecksum[ $checksum ] ) && $this->oldChecksum[ $checksum ] == $file ) {
			return true;
		}

		return false;
	}

	public function _scan_a_file( $file ) {
		if ( ! file_exists( $file ) ) {
			return false;
		}

		// Update current file for UI display (saved by queue at end of iteration)
		$this->model->currentFile = str_replace( ABSPATH, '', $file );

		// FIX #4 & #6: Check file size and do quick regex scan BEFORE loading full file into memory
		$maxSize = apply_filters( 'wdContentScanMaxFileSize', 2097152 ); // 2MB default
		if ( filesize( $file ) > $maxSize ) {
			// File too large, skip it - increment skip counter
			if ( ! isset( $this->model->skippedFiles ) ) {
				$this->model->skippedFiles = 0;
			}
			$this->model->skippedFiles++;
			return true;
		}

		$checksum = null;
		if ( $this->checksumCheck( $file, $checksum ) ) {
			//this one is good and still same, no need to do
			return true;
		}

		// FIX #6: QUICK REGEX CHECK FIRST (90% faster than tokenizer!)
		$content = file_get_contents( $file );
		$patterns = Scan_Api::getPatterns();
		$badFuncPattern = $patterns['suspicious_function_pattern'] ?? '';

		// PHASE 2 OPTIMIZATION: Multi-Pattern Pre-Screening
		if ( ! empty( $badFuncPattern ) ) {
			// Quick dirty check - if no suspicious functions found at all
			if ( ! preg_match( $badFuncPattern, $content ) ) {
				// ✅ File is clean - no suspicious patterns found
				// Store checksum for future scans and skip tokenization
				$this->oldChecksum[ $checksum ] = $file;
				$altCache = WP_Helper::getArrayCache();
				$altCache->set( self::CONTENT_CHECKSUM, $this->oldChecksum );
				unset( $content );
				return true;
			}
			
			// PHASE 2: Additional quick checks before expensive tokenization
			$suspiciousCount = 0;
			
			// Check for base64 encoded strings (potential malware)
			if ( preg_match_all( '/[a-zA-Z0-9+\/]{200,}={0,2}/', $content, $matches ) ) {
				$suspiciousCount += count( $matches[0] );
			}
			
			// Check for obfuscated code patterns
			if ( preg_match( '/\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*\\s*=\\s*["\'][a-zA-Z0-9+\/=]{50,}["\']/', $content ) ) {
				$suspiciousCount++;
			}
			
			// If multiple suspicious patterns but file is small and looks normal, quick validate
			if ( $suspiciousCount > 3 && strlen( $content ) < 50000 ) {
				// High suspicion - definitely needs tokenization
			} elseif ( $suspiciousCount <= 1 && strlen( $content ) > 100000 ) {
				// Large file with minimal suspicion - might be framework code, proceed carefully
				// Allow filter to skip heavy files with low suspicion
				if ( apply_filters( 'defender_skip_low_suspicion_large_files', false, $file, $suspiciousCount ) ) {
					$this->oldChecksum[ $checksum ] = $file;
					$altCache = WP_Helper::getArrayCache();
					$altCache->set( self::CONTENT_CHECKSUM, $this->oldChecksum );
					unset( $content );
					return true;
				}
			}
		}

		// File has suspicious patterns, continue with deeper tokenizer analysis
		//this file has changed, unset the old one
		unset( $this->oldChecksum[ $checksum ] );
		$this->tries[] = $file;
		$count         = array_count_values( $this->tries );
		$altCache      = WP_Helper::getArrayCache();
		if ( isset( $count[ $file ] ) && $count[ $file ] > 1 ) {
			//we fail this once, just ignore for now
			return true;
		} else {
			$this->tries[] = $file;
			$this->tries   = array_unique( $this->tries );
			$altCache->set( self::FILES_TRIED, $this->tries );
			//if the file larger than 400kb, we will save immediatly to prevent stuck
			if ( filesize( $file ) >= apply_filters( 'wdScanPreventStuckSize', 400000 ) ) {
				$cache = WP_Helper::getCache();
				$cache->set( Content_Scan::FILES_TRIED, $this->tries );
			}
		}

		if ( ! class_exists( '\CP_Defender\Vendor\PHP_CodeSniffer_Tokenizers_PHP' ) ) {
			$this->loadDependency();
		}
		if ( ! defined( 'PHP_CODESNIFFER_VERBOSITY' ) ) {
			define( 'PHP_CODESNIFFER_VERBOSITY', 0 );
		}
		$tokenizer         = new \CP_Defender\Vendor\PHP_CodeSniffer_Tokenizers_PHP();
		$tokens            = \PHP_CodeSniffer_File::tokenizeString( $content, $tokenizer, PHP_EOL, 0, 'iso-8859-1' );
		$this->tokens      = $tokens;
		$scanError         = array();
		$ignoreTo          = false;
		$badFuncPattern    = $this->getFunctionScanPattern();
		$base64textPattern = $this->getBase64ScanPattern();
		//fallback
		$error1    = array();
		$error2    = array();
		$ignoreTo1 = false;
		$ignoreTo2 = false;
		//Log_Helper::logger( var_export( $tokens, true ) );
		for ( $i = 0; $i < count( $tokens ) - 1; $i ++ ) {
			if ( $ignoreTo !== false && $i <= $ignoreTo ) {
				continue;
			}
			//do stuff here
			if ( ! empty( $badFuncPattern ) && ! empty( $base64textPattern ) ) {
				list( $error1, $ignoreTo1 ) = $this->detectBadFunc( $i, $tokens[ $i ], $badFuncPattern, $base64textPattern );
			}
			list( $error2, $ignoreTo2 ) = $this->detectComplexConcat( $i, $tokens[ $i ] );

			$scanError = array_merge( $scanError, $error1 );
			$scanError = array_merge( $scanError, $error2 );
			$ignoreTo  = max( $ignoreTo1, $ignoreTo2 );
		}

		$scanError = array_filter( $scanError );

		if ( count( $scanError ) ) {
			$item           = new Scan\Model\Result_Item();
			$item->type     = 'content';
			$item->raw      = array(
				'file' => $file,
				'meta' => array_merge( $scanError )
			);
			$item->parentId = $this->model->id;
			$item->status   = Scan\Model\Result_Item::STATUS_ISSUE;
			$item->save();
		} else {
			//store the checksum for later use
			$this->oldChecksum[ $checksum ] = $file;
			$altCache->set( self::CONTENT_CHECKSUM, $this->oldChecksum );
		}
		$content      = null;
		$this->tokens = null;
		unset( $tokens );
		unset( $content );

		return true;
	}

	/**
	 * @param $index
	 * @param $token
	 * @param $badFuncPattern
	 * @param $base64textPattern
	 *
	 * @return array
	 */
	private function detectBadFunc( $index, $token, $badFuncPattern, $base64textPattern ) {
		$extendFuncs = array(
			'str_rot13'
		);
		$ignoreTo    = false;
		$errorFound  = array();

		if ( empty( $badFuncPattern ) || empty( $base64textPattern ) ) {
			//should never happen, just a fall back for safe
			return array();
		}
		if ( in_array( $token['code'], array( T_EVAL, T_STRING ) )
		     && ( preg_match( $badFuncPattern, $token['content'] ) || in_array( $token['content'], $extendFuncs ) )
		) {
			//let's find the open and close of this parent function, in next 5 tokens
			$opener = $this->findNext( T_OPEN_PARENTHESIS, $index, $index + 5 );
			if ( $opener !== false && isset( $this->tokens[ $opener ]['parenthesis_closer'] ) ) {
				$funcsFound = array(
					$token['content']
				);
				$textFound  = array();
				//found one, need to parse the content to analyze the behavior of this chain of func
				$closer = $this->tokens[ $opener ]['parenthesis_closer'];
				//loop through all the inner
				for ( $i = $opener + 1; $i <= $closer; $i ++ ) {
					$lToken = $this->tokens[ $i ];
					switch ( $lToken['code'] ) {
						case T_CONSTANT_ENCAPSED_STRING:
							//Log_Helper::logger( var_export( $lToken, true ) );
							if ( preg_match( $base64textPattern, $lToken['content'] ) ) {
								$textFound[] = $lToken['content'];
							} elseif ( strlen( $lToken['content'] ) > 200 ) {
								//text too long
								$textFound[] = $lToken['content'];
							} else {
								//this case when the string is very long and separate by new line, need to combind the string,
								//the string is inside a nested function
								$pre  = isset( $this->tokens[ $i - 1 ] ) ? $this->tokens[ $i - 1 ] : null;
								$next = isset( $this->tokens[ $i + 1 ] ) ? $this->tokens[ $i + 1 ] : null;
								if ( $pre != null && $pre['code'] == T_OPEN_PARENTHESIS &&
								     $next != null && $next['code'] == T_CONSTANT_ENCAPSED_STRING
								     && isset( $lToken['nested_parenthesis'][ $i - 1 ] )
								) {
									//gotcha
									$string = $this->getTokensAsString( $i, $lToken['nested_parenthesis'][ $i - 1 ] - $i );
									if ( strlen( $string ) > 500 ) {
										$textFound[] = $string;
										//put the i to the end
										$i = $lToken['nested_parenthesis'][ $i - 1 ];
									}
								}
							}
							break;
						case T_STRING:
						case T_EVAL:
							if ( preg_match( $badFuncPattern, $lToken['content'] ) || in_array( $lToken['content'], $extendFuncs ) ) {
								$funcsFound[] = $lToken['content'];
							}
							break;
					}
				}
				$ignoreTo = $closer;

				if ( count( $funcsFound ) > 1 && ( count( $textFound ) || in_array( 'eval', $funcsFound ) ) ) {
					$errorFound[] = array(
						'lineFrom'   => $this->tokens[ $index ]['line'],
						'lineTo'     => $this->tokens[ $closer ]['line'],
						'columnFrom' => $this->tokens[ $index ]['column'],
						'columnTo'   => $this->tokens[ $closer ]['column'],
						//'code'       => $this->getTokensAsString( $opener, $closer - $opener )
					);
				}
			}
		}

		return array( $errorFound, $ignoreTo );
	}

	private function detectComplexConcat( $index, $token ) {
		$ignoreTo   = false;
		$errorFound = array();
		if ( in_array( $token['code'], array(
			T_VARIABLE,
		) ) ) {
			$opener = $this->findNext( T_OPEN_SQUARE_BRACKET, $index + 1, $index + 5 );
			if ( $opener !== false && isset( $this->tokens[ $opener ]['bracket_closer'] ) ) {
				$hasConcat = 0;
				$found     = 0;
				$closer    = $this->tokens[ $opener ]['bracket_closer'];
				for ( $line = $opener + 1; $line < $closer - 1; $line ++ ) {
					if ( in_array( $this->tokens[ $line ]['code'], array(
						T_STRING_CONCAT,
						T_VARIABLE,
						T_OPEN_SQUARE_BRACKET,
					) ) ) {
						if ( $this->tokens[ $line ]['code'] == T_STRING_CONCAT ) {
							//nested string or variable concat inside a varable
							$hasConcat ++;
						} else {
							//nested variable inside variable
							$found ++;
						}
					}
				}
				if ( $found > 5 && $hasConcat > 5 ) {
					$errorFound[] = array(
						'lineFrom'   => $this->tokens[ $index ]['line'],
						'lineTo'     => $this->tokens[ $closer ]['line'],
						'columnFrom' => $this->tokens[ $index ]['column'],
						'columnTo'   => $this->tokens[ $closer ]['column'],
					);
				}
				$ignoreTo = $closer;
			} else {
				$ignoreTo = $index + 5;
			}
		} elseif ( in_array( $token['code'], array( T_STRING_CONCAT ) ) ) {

		}

		return array( $errorFound, $ignoreTo );
	}

	/**
	 * this is for record fail files, to prevent block
	 */
	private function populateTries() {
		if ( $this->tries === null ) {
			//this is null, look at runtime cache
			$altCache = WP_Helper::getArrayCache();
			$tries    = $altCache->get( self::FILES_TRIED, null );
			if ( $tries === null ) {
				//has not init yet, check in db
				$cache = WP_Helper::getCache();
				//array as default so this never here again
				$tries       = $cache->get( self::FILES_TRIED, array() );
				$this->tries = $tries;
				$altCache->set( self::FILES_TRIED, $tries );
			} else {
				$this->tries = $tries;
			}
		}
		
		// FIX #5: Prevent unbounded memory growth by limiting array size
		$maxArraySize = apply_filters( 'wdScanMaxArraySize', 10000 );
		if ( is_array( $this->tries ) && count( $this->tries ) > $maxArraySize ) {
			// Keep only the last N entries to prevent memory issues
			$this->tries = array_slice( $this->tries, -$maxArraySize );
			$altCache = WP_Helper::getArrayCache();
			$altCache->set( self::FILES_TRIED, $this->tries );
		}
	}

	/**
	 * Populate old checksum from DB
	 */
	private function populateChecksums() {
		if ( $this->oldChecksum === null ) {
			//this is null, look at runtime cache
			$altCache    = WP_Helper::getArrayCache();
			$oldChecksum = $altCache->get( self::CONTENT_CHECKSUM, null );
			if ( $oldChecksum === null ) {
				//has not init yet, check in db
				$cache = WP_Helper::getCache();
				//array as default so this never here again
				$oldChecksum       = $cache->get( self::CONTENT_CHECKSUM, array() );
				$this->oldChecksum = $oldChecksum;
				$altCache->set( self::CONTENT_CHECKSUM, $oldChecksum );
			} else {
				$this->oldChecksum = $oldChecksum;
			}
		}
		
		// FIX #5: Prevent unbounded memory growth by limiting checksum array size
		$maxArraySize = apply_filters( 'wdScanMaxChecksumSize', 10000 );
		if ( is_array( $this->oldChecksum ) && count( $this->oldChecksum ) > $maxArraySize ) {
			// Keep only the most recently accessed checksums to prevent memory issues
			// This is safe because checksums older than current scan are rechecked anyway
			$this->oldChecksum = array_slice( $this->oldChecksum, -$maxArraySize );
			$altCache = WP_Helper::getArrayCache();
			$altCache->set( self::CONTENT_CHECKSUM, $this->oldChecksum );
		}
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	private function getPatterns( $key ) {
		$pattern = $this->patterns;

		return isset( $pattern[ $key ] ) ? $pattern[ $key ] : false;
	}

	private function getFunctionScanPattern() {
		$pattern = $this->getPatterns( 'suspicious_function_pattern' );

		return $pattern;
	}

	private function getBase64ScanPattern() {
		$pattern = $this->getPatterns( 'base64_encode_pattern' );

		return $pattern;
	}

	/**
	 * @param $start
	 * @param $length
	 *
	 * @return string
	 * code borrow from @PHP_CodeSniffer_File
	 */
	private function getTokensAsString( $start, $length ) {
		$str = '';
		$end = ( $start + $length );

		for ( $i = $start; $i < $end; $i ++ ) {
			$str .= $this->tokens[ $i ]['content'];
		}

		return $str;

	}

	/**
	 * @param $token
	 * @param $from
	 * @param $end
	 *
	 * @return bool|int|string
	 */
	private function findNext( $token, $from, $end = null ) {
		if ( $end == null ) {
			$end = count( $this->tokens ) - 1;
		}

		if ( ! is_array( $token ) ) {
			$token = array( $token );
		}

		for ( $i = $from; $i < $end; $i ++ ) {
			if ( ! isset( $this->tokens[ $i ] ) ) {
				return false;
			}

			if ( $this->tokens[ $i ]['code'] == T_SEMICOLON ) {
				return false;
			}

			if ( in_array( $this->tokens[ $i ]['code'], $token ) ) {
				return $i;
			}
		}
	}

	private function loadDependency() {
		$ds         = DIRECTORY_SEPARATOR;
		$vendorPath = cp_defender()->getPluginPath() . 'vendor' . $ds . 'php_codesniffer' . $ds . 'CodeSniffer';
		include_once $vendorPath . $ds . 'Tokens.php';
		include_once $vendorPath . $ds . 'File.php';
		include_once $vendorPath . $ds . 'Tokenizers' . $ds . 'Comment.php';
		include_once $vendorPath . $ds . 'Tokenizers' . $ds . 'PHP.php';
	}

	/**
	 * @param $token
	 * @param $from
	 * @param null $end
	 *
	 * @return bool
	 */
	private function findPrevious( $token, $from, $end = null ) {
		for ( $i = $from; $i >= $end; $i -- ) {
			if ( isset( $this->tokens[ $i ] ) && $this->tokens[ $i ]['code'] == $token ) {
				return $i;
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function behaviors() {
		return array(
			'utils' => 'CP_Defender\Behavior\Utils'
		);
	}
}