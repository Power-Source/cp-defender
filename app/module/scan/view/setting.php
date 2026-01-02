<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Settings", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" class="scan-frm scan-settings">
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Scan Types", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Choose the scan types you would like to include in your default scan. It's recommended you enable all types.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle" aria-hidden="true" role="presentation">
                        <input type="hidden" name="scan_core" value="0"/>
                        <input role="presentation" type="checkbox" name="scan_core" class="toggle-checkbox" id="core-scan" value="1"
	                        <?php checked( true, $setting->scan_core ) ?>/>
                        <label aria-hidden="true"  class="toggle-label" for="core-scan"></label>
                    </span>
                    <label for="core-scan"><?php _e( "WordPress Core", cp_defender()->domain ) ?></label>
                    <span class="sub inpos">
                        <?php _e( "Defender checks for any modifications or additions to WordPress core files.", cp_defender()->domain ) ?>
                    </span>
                    <div class="clear mline"></div>
                    <span class="toggle" aria-hidden="true" role="presentation">
                        <input type="hidden" name="scan_vuln" value="0"/>
                        <input role="presentation" type="checkbox" class="toggle-checkbox" name="scan_vuln" value="1"
                               id="scan-vuln" <?php checked( $setting->scan_vuln ) ?>/>
                        <label aria-hidden="true" class="toggle-label" for="scan-vuln"></label>
                    </span>
                    <label for="scan-vuln"><?php _e( "Plugins & Themes", cp_defender()->domain ) ?></label>
                    <span class="sub inpos">
                        <?php _e( "Defender looks for publicly reported vulnerabilities in your installed plugins and themes.", cp_defender()->domain ) ?>
                        <?php if ( empty( $setting->wpscan_api_token ) ): ?>
                            <br/><span style="color: #FFA500;"><i class="def-icon icon-warning"></i> <?php _e( "Limited Mode: Basic checks active. Optional WPScan API token for expanded vulnerability database.", cp_defender()->domain ) ?></span>
                        <?php else: ?>
                            <br/><span style="color: #1ABC9C;"><i class="def-icon icon-tick"></i> <?php _e( "Full Mode: WPScan API token configured for comprehensive vulnerability checks.", cp_defender()->domain ) ?></span>
                        <?php endif; ?>
                    </span>
                    <div class="clear mline"></div>
                    <span class="toggle" aria-hidden="true" role="presentation">
                        <input type="hidden" name="scan_content" value="0"/>
                        <input role="presentation" type="checkbox" class="toggle-checkbox" name="scan_content" value="1"
                               id="scan-content" <?php checked( true, $setting->scan_content ) ?>/>
                        <label aria-hidden="true" class="toggle-label" for="scan-content"></label>
                    </span>
                    <label for="scan-content"><?php _e( "Suspicious Code", cp_defender()->domain ) ?></label>
                    <span class="sub inpos">
                        <?php _e( "Defender looks inside all of your files for suspicious and potentially harmful code.", cp_defender()->domain ) ?>
                    </span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "WPScan API Token", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Enter your WPScan API token to enable vulnerability scanning for plugins and themes. Get a free token (25 requests/day) at", cp_defender()->domain ) ?>
                        <a href="https://wpscan.com/api" target="_blank">wpscan.com/api</a>
                    </span>
                </div>
                <div class="column">
                    <input type="text" name="wpscan_api_token" value="<?php echo esc_attr( $setting->wpscan_api_token ) ?>" placeholder="<?php esc_attr_e( "Enter API token", cp_defender()->domain ) ?>" style="width: 100%; max-width: 400px;"/>
                    <?php if ( !empty( $setting->wpscan_api_token ) ): ?>
                        <span class="sub" style="color: #1ABC9C;"><i class="def-icon icon-tick"></i> <?php _e( "API token configured - Full vulnerability database active", cp_defender()->domain ) ?></span>
                    <?php else: ?>
                        <span class="sub" style="color: #FFA500;"><i class="def-icon icon-warning"></i> <?php _e( "Optional: Add WPScan API token for comprehensive vulnerability checks. Scanning works in Limited Mode without it.", cp_defender()->domain ) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Maximum included file size", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Defender will skip any files larger than this size. The smaller the number, the faster Defender will scan your website.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <input type="text" size="4" value="<?php echo esc_attr( $setting->max_filesize ) ?>"
                           name="max_filesize"> <?php _e( "MB", cp_defender()->domain ) ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Optional emails", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "By default, you'll only get email reports when your site runs into trouble. Turn this option on to get reports even when your site is running smoothly.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="always_send" value="0"/>
                        <input type="checkbox" name="always_send" class="toggle-checkbox" value="1"
                               id="always_send" <?php checked( true, $setting->always_send ) ?>/>
                        <label class="toggle-label" for="always_send"></label>
                    </span>
                    <label><?php _e( "Send all scan report emails", cp_defender()->domain ) ?></label>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Email subject", cp_defender()->domain ) ?></strong>
                </div>
                <div class="column">
                    <input type="text" name="email_subject" value="<?php echo esc_attr( $setting->email_subject ) ?>"/>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Email templates", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                         <?php _e( "When Defender scans your website, a report will be generated with any issues that have been found. You can choose to have reports emailed to you.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <ul class="dev-list">
                        <li>
                            <div>
                                <span class="list-label"><?php _e( "When an issue is found", cp_defender()->domain ) ?></span>
                                <span class="list-detail tr">
                                    <a href="#issue-found" rel="dialog" role="button"><?php _e( "Edit", cp_defender()->domain ) ?></a></span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span class="list-label"><?php _e( "When no issues are found", cp_defender()->domain ) ?></span>
                                <span class="list-detail tr">
                                    <a href="#all-ok"
                                       rel="dialog" role="button"><?php _e( "Edit", cp_defender()->domain ) ?></a></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear line"></div>
            <input type="hidden" name="action" value="saveScanSettings"/>
			<?php wp_nonce_field( 'saveScanSettings' ) ?>
            <button class="button float-r"><?php _e( "Update Settings", cp_defender()->domain ) ?></button>
            <div class="clear"></div>
        </form>
    </div>
</div>
<dialog id="issue-found" title="<?php esc_attr_e( "Issues found", cp_defender()->domain ) ?>">
    <div class="cp-defender">
        <form method="post" class="scan-frm scan-settings">
            <textarea rows="12" name="email_has_issue"><?php echo $setting->email_has_issue ?></textarea>
            <strong class="small">
				<?php _e( "Available variables", cp_defender()->domain ) ?>
            </strong>
            <input type="hidden" name="action" value="saveScanSettings"/>
            <div class="clearfix"></div>
            <span class="def-tag tag-generic">{USER_NAME}</span>
            <span class="def-tag tag-generic">{SITE_URL}</span>
            <span class="def-tag tag-generic">{ISSUES_COUNT}</span>
            <span class="def-tag tag-generic">{ISSUES_LIST}</span>
			<?php wp_nonce_field( 'saveScanSettings' ) ?>
            <div class="clearfix mline"></div>
            <hr class="mline"/>
            <button type="button"
                    class="button button-light close"><?php _e( "Cancel", cp_defender()->domain ) ?></button>
            <button class="button float-r"><?php _e( "Save Template", cp_defender()->domain ) ?></button>
        </form>
    </div>
</dialog>
<dialog id="all-ok" title="<?php esc_attr_e( 'All OK', cp_defender()->domain ) ?>">
    <div class="cp-defender">
        <form method="post" class="scan-frm scan-settings">
            <input type="hidden" name="action" value="saveScanSettings"/>
			<?php wp_nonce_field( 'saveScanSettings' ) ?>
            <textarea rows="12" name="email_all_ok"><?php echo $setting->email_all_ok ?></textarea>
            <strong class="small">
		        <?php _e( "Available variables", cp_defender()->domain ) ?>
            </strong>
            <div class="clearfix"></div>
            <span class="def-tag tag-generic">{USER_NAME}</span>
            <span class="def-tag tag-generic">{SITE_URL}</span>
            <div class="clearfix mline"></div>
            <hr class="mline"/>
            <button type="button"
                    class="button button-light close"><?php _e( "Cancel", cp_defender()->domain ) ?></button>
            <button class="button float-r"><?php _e( "Save Template", cp_defender()->domain ) ?></button>
        </form>
    </div>
</dialog>