<?php


namespace CP_Defender\Module\Scan\Behavior;

use Hammer\Base\Behavior;
use Hammer\Helper\File_Helper;
use Hammer\Helper\Log_Helper;
use CP_Defender\Module\Scan\Component\Scan_Api;
use CP_Defender\Module\Scan\Model\Result_Item;

class Core_Scan extends Behavior {
	protected $model;
	protected $checksums = null;
	protected $coreChecksumCache = array();

	public function processItemInternal( $args, $current ) {
		$this->model = $args['model'];

		// Update currently scanned core item for UI visibility
		$this->model->currentFile = str_replace( ABSPATH, '', $current );

		// OPTIMIZATION: Check ignored list first before expensive operations
		if ( ( $oid = Scan_Api::isIgnored( $current ) ) !== false ) {
			//if this is ignored, we just need to update the parent ID
			$item           = Result_Item::findByID( $oid );
			$item->parentId = $this->model->id;
			$item->save();

			return true;
		}

		$isSuspiciousRoot = $this->isSuspiciousRootItem( $current );

		// OPTIMIZATION: Cache checksums per request to avoid repeated calls
		if ( $this->checksums === null ) {
			$this->checksums = Scan_Api::getCoreChecksums();
		}
		$checksums = $this->checksums;

		// If checksums are unavailable, still flag suspicious root items but avoid
		// marking all core files as unknown.
		if ( ! is_array( $checksums ) ) {
			if ( ! $isSuspiciousRoot ) {
				return true;
			}
			$checksums = array();
		}

		$item           = new Result_Item();
		$item->parentId = $this->model->id;
		$item->type     = 'core';
		$item->status   = Result_Item::STATUS_ISSUE;
		$relPath 		= Scan_Api::convertToUnixPath( $current ); //Windows File path fix set outside to be used in both file and dir checks
		$current_path	= Scan_Api::convertToWindowsAbsPath( $current ); //Windows needs fixing for the paths

		// OPTIMIZATION: Only check files that are in the checksum array
		// Skip unnecessary hash calculations for files not in the list
		if ( is_file( $current ) ) {
			// OPTIMIZATION: Only calculate hash if file is in checksum list
			if ( isset( $checksums[ $relPath ] ) ) {
				// Check cache first to avoid recalculating hashes
				if ( ! isset( $this->coreChecksumCache[ $relPath ] ) ) {
					$this->coreChecksumCache[ $relPath ] = md5_file( $current );
				}
				$file_hash = $this->coreChecksumCache[ $relPath ];
				if ( strcmp( $file_hash, $checksums[ $relPath ] ) !== 0 ) {
					$item->raw = array(
						'type' => 'modified',
						'file' => $current_path
					);
					$item->save();
				}
			} else {
				// File not in checksum list - unknown file in core directory
				$item->raw = array(
					'type' => 'unknown',
					'file' => $current_path
				);
				$item->save();
			}
		} elseif ( is_dir( $current ) ) {
			// OPTIMIZATION: Only check directories if they contain files
			$files = File_Helper::findFiles( $current, true, false );
			if ( count( $files ) ) {
				$item->raw = array(
					'type' => 'dir',
					'file' => $current_path
				);
				$item->save();
			}
		}

		return true;
	}

	/**
	 * Identify suspicious items located directly in the WordPress root.
	 * Unknown files/dirs at ABSPATH level should always be flagged.
	 */
	private function isSuspiciousRootItem( $path ) {
		$relative = ltrim( str_replace( ABSPATH, '', Scan_Api::convertToUnixPath( $path ) ), '/\\' );
		if ( $relative === '' || strpos( $relative, '/' ) !== false ) {
			return false; // Not a root-level item
		}

		$allowed_files = array(
			'wp-config.php',
			'wp-config-sample.php',
			'wp-blog-header.php',
			'wp-activate.php',
			'wp-app.php',
			'wp-atom.php',
			'wp-commentsrss2.php',
			'wp-cron.php',
			'wp-feed.php',
			'wp-links-opml.php',
			'wp-load.php',
			'wp-login.php',
			'wp-mail.php',
			'wp-rdf.php',
			'wp-rss.php',
			'wp-rss2.php',
			'wp-settings.php',
			'wp-signup.php',
			'wp-trackback.php',
			'wp-xmlrpc.php',
			'index.php',
			'.htaccess',
			'robots.txt',
			'sitemap.xml',
			'.gitignore',
			'readme.html',
			'license.txt',
		);

		$allowed_dirs = array(
			'wp-content',
			'wp-admin',
			'wp-includes',
		);

		if ( is_dir( $path ) ) {
			return ! in_array( basename( $path ), $allowed_dirs, true );
		}

		return ! in_array( basename( $path ), $allowed_files, true );
	}
}