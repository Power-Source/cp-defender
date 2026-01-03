<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "EREIGNISPROTOKOLLE", cp_defender()->domain ) ?></h3>
        <button type="button" class="button button-secondary button-small audit-csv"><?php _e( "Export CSV", cp_defender()->domain ) ?></button>
    </div>
    <div class="box-content">
        <p class="mline"><?php _e( "Hier findest du deine neuesten Ereignisprotokolle, die zeigen, was im Hintergrund passiert ist.", cp_defender()->domain ) ?></p>
        <div class="well well-white audit-filter mline">
            <form method="post">
                <strong>
					<?php _e( "Filter", cp_defender()->domain ) ?>
                </strong>
                <div class="columns">
                    <div class="column is-5">
						<?php echo $email_search->renderInput() ?>

                    </div>
                    <div class="column is-5">
                        <input name="date_from" id="wd_range_from" type="text" class="wd-calendar filterable"
                               value="<?php echo $from . ' - ' . $to ?>">
                    </div>
                </div>
                <div class="events">
					<?php foreach ( \CP_Defender\Module\Audit\Component\Audit_API::get_event_type() as $event ): ?>
                        <div class="event">
                            <input id="chk_<?php echo $event ?>" type="checkbox" class="filterable" name="event_type[]"
								<?php echo in_array( $event, \Hammer\Helper\HTTP_Helper::retrieve_get( 'event_type', \CP_Defender\Module\Audit\Component\Audit_API::get_event_type() ) ) ? 'checked="checked"' : null ?>
                                   value="<?php echo $event ?>">
                            <label
                                    for="chk_<?php echo $event ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $event ) ) ) ?></label>
                        </div>
					<?php endforeach; ?>
                </div>
            </form>
            <div class="clear"></div>
        </div>
        <div id="audit-table-container">
			<?php _e( "Lade Ereignisse...", cp_defender()->domain ) ?>
        </div>
    </div>
</div>