<div class="wpmud">
    <div class="wrap">
        <div id="cp-defender" class="cp-defender">
            <div class="iplockout">
                <h2 class="title">
					<?php _e( "IP-SPERRUNGEN", cp_defender()->domain ) ?>
                </h2>
                <div class="dev-box summary-box" id="lockoutSummary">
                    <div class="wd-overlay">
                        <i class="wdv-icon wdv-icon-fw wdv-icon-refresh spin"></i>
                    </div>
                    <input type="hidden" id="summaryNonce" value="<?php echo wp_create_nonce( 'lockoutSummaryData' ) ?>"/>
                    <div class="box-content">
                        <div class="columns">
                            <div class="column is-7 issues-count">
                                <div>
                                    <h5 class="lockoutToday">.</h5>
                                    <div class="clear"></div>
                                    <span class="sub"><?php _e( "Aussperrungen in den letzten 24 Stunden", cp_defender()->domain ) ?></span>
                                    <h6 class="lockoutThisMonth">.</h6>
                                    <span class="sub"><?php _e( "Gesamtzahl der Aussperrungen in den letzten 30 Tagen", cp_defender()->domain ) ?></span>
                                </div>
                            </div>
                            <div class="column is-5">
                                <ul class="dev-list bold">
                                    <li>
                                        <div>
                                            <span class="list-label"><?php _e( "Letzte Aussperrung", cp_defender()->domain ) ?></span>
                                            <span class="list-detail lastLockout">.</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <span class="list-label"><?php _e( "Login-Aussperrungen in den letzten 7 Tagen", cp_defender()->domain ) ?></span>
                                            <span class="list-detail loginLockoutThisWeek">.</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <span class="list-label"><?php _e( "404-Aussperrungen in den letzten 7 Tagen", cp_defender()->domain ) ?></span>
                                            <span class="list-detail lockout404ThisWeek">.</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-third">
                        <ul class="inner-nav is-hidden-mobile">
                            <li>
                                <a class="<?php echo \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', false ) == false ? 'active' : null ?>"
                                   href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout' ) ?>"><?php _e( "Login Protection", cp_defender()->domain ) ?></a>
                            </li>
                            <li>
                                <a class="<?php echo $controller->isView( '404' ) ? 'active' : null ?>"
                                   href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => '404' ) ) ?>"><?php _e( "404 Detection", cp_defender()->domain ) ?></a>
                            </li>
                            <li>
                                <a class="<?php echo $controller->isView( 'blacklist' ) ? 'active' : null ?>"
                                   href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'blacklist' ) ) ?>"><?php _e( "IP Banning", cp_defender()->domain ) ?></a>
                            </li>
                            <li>
                                <a class="<?php echo $controller->isView( 'logs' ) ? 'active' : null ?>"
                                   href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'logs' ) ) ?>"><?php _e( "Logs", cp_defender()->domain ) ?></a>
                            </li>
                            <li>
                                <a class="<?php echo $controller->isView( 'notification' ) ? 'active' : null ?>"
                                   href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'notification' ) ) ?>"><?php _e( "Notifications", cp_defender()->domain ) ?></a>
                            </li>
                            <li>
                                <a class="<?php echo $controller->isView( 'settings' ) ? 'active' : null ?>"
                                   href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'settings' ) ) ?>"><?php _e( "Settings", cp_defender()->domain ) ?></a>
                            </li>
                            <li>
                                <a class="<?php echo $controller->isView( 'reporting' ) ? 'active' : null ?>"
                                   href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'reporting' ) ) ?>"><?php _e( "Reporting", cp_defender()->domain ) ?></a>
                            </li>
                        </ul>
                        <div class="is-hidden-tablet mline">
                            <select class="mobile-nav">
                                <option <?php selected( null, \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', null ) ) ?>
                                        value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout' ) ?>"><?php _e( "Login Protection", cp_defender()->domain ) ?></option>
                                <option <?php selected( '404', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', null ) ) ?>
                                        value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => '404' ) ) ?>"><?php _e( "404 Detection", cp_defender()->domain ) ?></option>
                                <option <?php selected( 'blacklist', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', null ) ) ?>
                                        value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'blacklist' ) ) ?>"><?php _e( "IP Blacklist", cp_defender()->domain ) ?></option>
                                <option <?php selected( 'logs', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', null ) ) ?>
                                        value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'logs' ) ) ?>"><?php _e( "Logs", cp_defender()->domain ) ?></option>
                                <option <?php selected( 'notification', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', null ) ) ?>
                                        value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'notification' ) ) ?>"><?php _e( "Notifications", cp_defender()->domain ) ?></option>
                                <option <?php selected( 'settings', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', null ) ) ?>
                                        value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'settings' ) ) ?>"><?php _e( "Settings", cp_defender()->domain ) ?></option>
                                <option <?php selected( 'reporting', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', null ) ) ?>
                                        value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'reporting' ) ) ?>"><?php _e( "Reporting", cp_defender()->domain ) ?></option>
                            </select>
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