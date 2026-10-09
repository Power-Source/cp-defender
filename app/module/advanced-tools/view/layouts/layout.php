<div class="wrap">
    <div id="cp-defender" class="cp-defender">
        <div class="advanced-tools">
            <h2 class="title">
				<?php _e( "Erweiterte Werkzeuge", 'cpsec' ) ?>
            </h2>
            <div class="row">
                <div class="col-third">
                    <ul class="inner-nav is-hidden-mobile">
                        <li class="issues-nav">
                            <a class="<?php echo \Hammer\Helper\HTTP_Helper::retrieve_get( 'view', false ) == false ? 'active' : null ?>"
                               href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-advanced-tools' ) ?>">
						        <?php _e( "Zwei-Faktor-Authentifizierung", 'cpsec' ) ?>
                            </a>
                        </li>
                    </ul>
                    <div class="is-hidden-tablet mline">
                        <select class="mobile-nav">
                            <option <?php selected( '', \Hammer\Helper\HTTP_Helper::retrieve_get( 'view' ) ) ?>
                                    value="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-advanced-tools' ) ?>"><?php _e( "Zwei-Faktor-Authentifizierung", 'cpsec' ) ?></option>
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