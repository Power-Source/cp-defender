<div class="wrap">
    <div id="cp-defender" class="cp-defender">
        <div class="def-dashboard">
            <h2 class="title"><?php _e( "Dashboard", cp_defender()->domain ) ?></h2>
            <div class="dev-box summary-box">
                <div class="box-content">
                    <div class="columns">
                        <div class="column is-7 issues-count">
                            <div>
                                <h5 class=""><?php list( $hCount, $sCount ) = $controller->countTotalIssues( true );
									$countAll = $hCount + $sCount;
									echo $countAll;
									?></h5>
								<?php if ( $countAll == 0 ): ?>
                                <span class=""
                                      tooltip="<?php esc_attr_e( 'Es bestehen keine offenen Sicherheitslücken.', cp_defender()->domain ); ?>">
                                        <i class="def-icon icon-tick" aria-hidden="true"></i>
									<?php else: ?>
									<?php
									if ( $sCount > 0 && $hCount > 0 ) :
									?>
                                    <span class=""
                                          tooltip="<?php esc_attr_e( sprintf( __( 'Du hast %d Sicherheitsanpassung(en) und %d verdächtige Datei(en), die Aufmerksamkeit benötigen.', cp_defender()->domain ), $hCount, $sCount ) ); ?>">
                                        <?php elseif ( $hCount > 0 ): ?>
                                        <span class=""
                                              tooltip="<?php esc_attr_e( sprintf( __( 'Du hast %d Sicherheitsanpassung(en), die Aufmerksamkeit benötigen.', cp_defender()->domain ), $hCount ) ); ?>">
                                        <?php elseif ( $sCount > 0 ): ?>
                                            <span class=""
                                                  tooltip="<?php esc_attr_e( sprintf( __( 'Du hast %d verdächtige Datei(en), die Aufmerksamkeit benötigen.', cp_defender()->domain ), $sCount ) ); ?>">
                                        <?php else: ?>
                                                <span class=""
                                                      tooltip="<?php esc_attr_e( 'Es bestehen keine offenen Sicherheitslücken.', cp_defender()->domain ); ?>">
                                        <?php endif; ?>
                                                    <i class="def-icon icon-warning icon-yellow <?php echo $sCount > 0 ? 'fill-red' : null ?>" aria-hidden="true"></i>
													<?php endif; ?>
                                </span>
                                <div class="clear"></div>
                                <span class="sub"><?php
	                                _e( "Sicherheitsprobleme", cp_defender()->domain ) ?></span>
                            </div>
                        </div>
                        <div class="column is-5">
                            <ul class="dev-list bold">
                                <li>
                                    <div>
                                        <span class="list-label"><?php _e( "Sicherheitsanpassungen durchgeführt", cp_defender()->domain ) ?></span>
                                        <span class="list-detail"><span>
                                            <?php
                                            $settings = \CP_Defender\Module\Hardener\Model\Settings::instance();
                                            echo count( $settings->fixed ) + count( $settings->ignore ) ?>
                                                /
												<?php echo count( $settings->getDefinedRules() ) ?>
                                        </span></span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <span class="list-label"><?php _e( "Datei-Scan-Probleme", cp_defender()->domain ) ?></span>
                                        <span class="list-detail">
                                       <?php echo $controller->renderScanStatusText() ?>
                                    </span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <span class="list-label"><?php _e( "Letzte IP-Sperrung" ) ?></span>
                                        <span class="list-detail lastLockout">.</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row is_multiline">
                <div class="col-half">
                    <?php echo $controller->renderHardenerWidget() ?>
                    <?php $controller->renderAuditWidget() ?>
                    <?php $controller->renderATWidget() ?>
                </div>
                <div class="col-half">
					<?php $controller->renderScanWidget() ?>
					<?php $controller->renderLockoutWidget() ?>
					<?php $controller->renderReportWidget() ?>
                </div>
            </div>
        </div>
    </div>
</div>