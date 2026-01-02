<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Reporting", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" class="scan-frm scan-settings">
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Schedule scans", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Configure Defender to automatically and regularly scan your website and email you reports.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="notification" value="0"/>
                        <input type="checkbox" class="toggle-checkbox" name="notification" value="1"
                               id="chk1" <?php checked( 1, $setting->notification ) ?>/>
                        <label class="toggle-label" for="chk1"></label>
                    </span>
                    <label><?php _e( "Run regular scans & reports", cp_defender()->domain ) ?></label>
                    <div class="clear mline"></div>
                    <div class="well well-white schedule-box">
                        <strong><?php _e( "Schedule", cp_defender()->domain ) ?></strong>
                        <label><?php _e( "Frequency", cp_defender()->domain ) ?></label>
                        <select name="frequency">
                            <option <?php selected( 1, $setting->frequency ) ?>
                                    value="1"><?php _e( "Daily", cp_defender()->domain ) ?></option>
                            <option <?php selected( 7, $setting->frequency ) ?>
                                    value="7"><?php _e( "Weekly", cp_defender()->domain ) ?></option>
                            <option <?php selected( 30, $setting->frequency ) ?>
                                    value="30"><?php _e( "Monthly", cp_defender()->domain ) ?></option>
                        </select>
                        <div class="days-container">
                            <label><?php _e( "Day of the week", cp_defender()->domain ) ?></label>
                            <select name="day">
								<?php foreach ( \CP_Defender\Behavior\Utils::instance()->getDaysOfWeek() as $day ): ?>
                                    <option <?php selected( $day, $setting->day ) ?>
                                            value="<?php echo $day ?>"><?php echo ucfirst( $day ) ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>
                        <label><?php _e( "Time of day", cp_defender()->domain ) ?></label>
                        <select name="time">
							<?php foreach ( \CP_Defender\Behavior\Utils::instance()->getTimes() as $time ): ?>
                                <option <?php selected( $time, $setting->time ) ?>
                                        value="<?php echo $time ?>"><?php echo wp_date( 'h:i A', strtotime( $time ) ) ?></option>
							<?php endforeach;; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Email recipients", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Choose which of your website’s users will receive scan report results to their email inboxes.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
					<?php $email->renderInput() ?>
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