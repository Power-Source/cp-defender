<div class="wrap">
    <div id="cp-defender" class="cp-defender">
        <div class="wdf-scanning">
            <h2 class="title">
				<?php _e( "Dateiscanning", 'cpsec' ) ?>
                <span>
                    <form id="start-a-scan" method="post" class="scan-frm">
						<?php
						wp_nonce_field( 'startAScan' );
						?>
                        <input type="hidden" name="action" value="startAScan"/>
                        <button type="submit"
                                class="button button-small"><?php _e( "Neuer Scan", 'cpsec' ) ?></button>
                </form>
            </span>
            </h2>

            <div class="scan">
                <div class="dev-box summary-box">
                    <div class="box-content">
                        <div class="columns">
                            <div class="column is-7 issues-count">
                                <div>
                                    <h5 class="def-issues def-issues-top-left"><?php echo $countAll = $model->countAll( \CP_Defender\Module\Scan\Model\Result_Item::STATUS_ISSUE ) ?></h5>
                                    <?php if ( $countAll > 0 ) : ?>
                                    <span class="def-issues-top-left-icon" tooltip="<?php esc_attr_e( sprintf( __('Du hast %d verdächtige Datei(en), die Aufmerksamkeit erfordern.', 'cpsec' ), $countAll ) ); ?>">
                                    <?php else: ?>
                                    <span class="def-issues-top-left-icon" tooltip="<?php esc_attr_e( 'Dein Code ist sauber, alles in Ordnung.', 'cpsec' ); ?>">
                                    <?php endif; ?>
									<?php
									$icon = $countAll == 0 ? ' <i class="def-icon icon-tick" aria-hidden="true"></i>' : ' <i class="def-icon icon-warning fill-red" aria-hidden="true"></i>';
									echo $icon;
									?>
                                </span>
                                    <div class="clear"></div>
                                    <span class="sub"><?php _e( "Probleme beim Scannen von Dateien erfordern Aufmerksamkeit.", 'cpsec' ) ?></span>
                                    <div class="clear mline"></div>
                                    <strong><?php echo $lastScanDate ?></strong>
                                    <span class="sub"><?php _e( "Letzter Scan", 'cpsec' ) ?></span>
                                </div>
                            </div>
                            <div class="column is-5">
                                <ul class="dev-list bold">
                                    <li>
                                        <div>
                                            <span class="list-label"><?php _e( "WordPress Core", 'cpsec' ) ?></span>
                                            <span class="list-detail def-issues-top-right-wp">
                                                <?php echo $model->getCount( 'core' ) == 0 ? ' <i class="def-icon icon-tick"></i>' : '<span class="def-tag tag-error">' . '<span class="def-issues">' . $model->getCount( 'core' ) . '</span></span>' ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <span class="list-label"><?php _e( "Plugins & Themes", 'cpsec' ) ?></span>
                                            <span class="list-detail def-issues-top-right-pt">
                                                <?php 
                                                $vuln_count = $model->getCount( 'vuln' );
                                                $has_wpscan_token = ! empty( trim( \CP_Defender\Module\Scan\Model\Settings::instance()->wpscan_api_token ) );
                                                
                                                if ( $vuln_count > 0 ) {
                                                    echo '<span class="def-tag tag-error">' . $vuln_count . '</span>';
                                                } elseif ( $has_wpscan_token ) {
                                                    echo ' <i class="def-icon icon-tick"></i>';
                                                } else {
                                                    echo ' <i class="def-icon icon-info" style="color: #ffc107;" title="' . esc_attr( __( 'Limited Mode: Nur Basis-Versionscheck aktiv. Konfiguriere WPScan API-Token für erweiterte Vulnerabilitätsprüfung.', 'cpsec' ) ) . '"></i>';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <span class="list-label"><?php _e( "Verdächtiger Code", 'cpsec' ) ?></span>
                                            <span class="list-detail def-issues-top-right-sc">
                                                <?php echo $model->getCount( 'content' ) == 0 ? ' <i class="def-icon icon-tick"></i>' : '<span class="def-tag tag-error">' . $model->getCount( 'content' ) . '</span>' ?>
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-third">
						<nav role="navigation" aria-label="Filters">
							<ul class="inner-nav is-hidden-mobile">
								<li class="issues-nav">
									<a class="<?php echo \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', false ) == false ? 'active' : null ?>"
						href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan' ) ?>">
										<?php _e( "Issues", 'cpsec' ) ?>
										<?php
										$issues = $model->countAll( \CP_Defender\Module\Scan\Model\Result_Item::STATUS_ISSUE );
										$tooltip = '';
										if ( $issues > 0 ) :
											$tooltip = 'tooltip="' . esc_attr( sprintf( __("Du hast %d verdächtige Dateien, die Deine Aufmerksamkeit erfordern.", 'cpsec' ), $countAll ) ) . '"';
										endif;
										echo $issues > 0 ? '<span class="def-tag tag-error def-issues-below" ' . $tooltip . '>' . $issues . '</span>' : '' ?>
									</a>
								</li>
								<!--                            <li>-->
								<!--                                <a class="-->
								<?php //echo $controller->isView( 'cleaned' ) ? 'active' : null ?><!--"-->
								<!--                                   href="-->
								<?php //echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'cleaned' ) ) ?><!--">--><?php //_e( "Cleaned", 'cpsec' ) ?>
								<!--                                    <span>-->
								<!--                                        --><?php
								//                                        $issues = $model->countAll( \CP_Defender\Module\Scan\Model\Result_Item::STATUS_FIXED );
								//                                        echo $issues > 0 ? $issues : '' ?>
								<!--                                    </span>-->
								<!--                                </a>-->
								<!--                            </li>-->
								<li>
									<a class="<?php echo $controller->isView( 'ignored' ) ? 'active' : null ?>"
						href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'ignored' ) ) ?>">
										<?php _e( "Ignored", 'cpsec' ) ?>
										<span class="def-ignored">
											<?php
											$issues = $model->countAll( \CP_Defender\Module\Scan\Model\Result_Item::STATUS_IGNORED );
											echo $issues > 0 ? $issues : '' ?>
										</span>
									</a>
								</li>
								<li>
									<a class="<?php echo $controller->isView( 'settings' ) ? 'active' : null ?>"
						href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'settings' ) ) ?>">
										<?php _e( "Settings", 'cpsec' ) ?></a>
								</li>
								<li>
									<a class="<?php echo $controller->isView( 'reporting' ) ? 'active' : null ?>"
						href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'reporting' ) ) ?>">
										<?php _e( "Reporting", 'cpsec' ) ?></a>
								</li>
							</ul>
						</nav>
                        <div class="is-hidden-tablet mline">
							<nav role="navigation" aria-label="Filters">
								<select class="mobile-nav">
									<option <?php selected( '', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view' ) ) ?>
											value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan' ) ?>"><?php _e( "Issues", 'cpsec' ) ?></option>
									<option <?php selected( 'ignored', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view' ) ) ?>
											value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'ignored' ) ) ?>"><?php _e( "Ignoriert", 'cpsec' ) ?></option>
									<option <?php selected( 'settings', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view' ) ) ?>
											value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'settings' ) ) ?>"><?php _e( "Einstellungen", 'cpsec' ) ?></option>
									<option <?php selected( 'reporting', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view' ) ) ?>
											value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'reporting' ) ) ?>"><?php _e( "Berichte", 'cpsec' ) ?></option>
								</select>
							</nav>
                        </div>
                    </div>
                    <div class="col-two-third">
						<?php echo $contents ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>