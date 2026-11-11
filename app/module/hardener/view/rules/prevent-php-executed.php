<div class="rule closed" id="disable-file-editor">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "PHP-Ausführung verhindern", 'cpsec' ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", 'cpsec' ) ?></h3>
        <div class="line end">
			<?php _e( "Standardmäßig könnte eine Schwachstelle in einem Plugin/Theme dazu führen, dass eine PHP-Datei in die Verzeichnisse deiner Website hochgeladen wird und schädliche Skripte ausgeführt werden, die deiner Website erheblichen Schaden zufügen können. Verhindere dies vollständig, indem du die direkte PHP-Ausführung in Verzeichnissen deaktivierst, die sie nicht benötigen.", 'cpsec' ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", 'cpsec' ) ?>
        </h3>
        <div class="well">
			<?php
            $setting = \CP_Defender\Module\Hardener\Model\Settings::instance();

            if ( $controller->check() ): ?>
                <p class="line"><?php _e( "Die PHP-Ausführung ist gesperrt.", 'cpsec' ) ?>
                <?php
                if ( in_array( $setting->active_server, array( 'apache', 'litespeed' ) ) ) {
                    $file_paths = $setting->getExcludedFilePaths();
                    $htaccess_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '.htaccess';
                    $can_update_exceptions = file_exists( $htaccess_path )
                        ? is_writable( $htaccess_path )
                        : is_writable( dirname( $htaccess_path ) );
                    $exception_write_error = $setting->getDValues( \CP_Defender\Module\Hardener\Component\Prevent_Php::EXCEPTION_WRITE_ERROR_KEY );
                    if ( ! empty( $file_paths ) && is_array( $file_paths ) && count( $file_paths ) > 0 ) {
                        _e(" Die folgenden Dateipfade wurden im Verzeichnis /wp-content zugelassen :", 'cpsec' );
                    }
                    if ( ! $can_update_exceptions || ! empty( $exception_write_error ) ) {
						?>
                        <div class="well well-red">
                            <p><?php echo esc_html( $exception_write_error ? $exception_write_error : sprintf( __( 'Die Datei %s ist nicht beschreibbar. Der PHP-Schutz bleibt aktiv, aber Ausnahmen können erst nach Korrektur der Datei- oder Verzeichnisberechtigungen gespeichert werden.', 'cpsec' ), $htaccess_path ) ); ?></p>
                        </div>
						<?php
                    } else {
						?>
                        <div class="hardener-instructions hardener-instructions-apache-litespeed">
                            <label for="php-execution-exceptions"><?php esc_html_e( 'Ausnahmen', 'cpsec' ); ?></label>
                            <p class="description"><?php esc_html_e( 'Gib pro Zeile einen Dateinamen an, der innerhalb von wp-content weiterhin PHP ausführen darf. Lasse das Feld leer, wenn keine Ausnahme erforderlich ist.', 'cpsec' ); ?></p>
                            <textarea id="php-execution-exceptions" class="hardener-php-excuted-ignore"><?php echo esc_textarea( implode( "\n", (array) $file_paths ) ); ?></textarea>
                            <form method="post" class="hardener-frm hardener-update-frm rule-process">
                                <?php $controller->createNonceField(); ?>
                                <input type="hidden" name="action" value="updateHardener"/>
                                <input type="hidden" name="file_paths" value="<?php echo esc_attr( implode( "\n", (array) $file_paths ) ); ?>"/>
                                <input type="hidden" name="current_server" value="<?php echo esc_attr( $setting->active_server ); ?>"/>
                                <input type="hidden" name="slug" value="<?php echo esc_attr( $controller::$slug ); ?>"/>
                                <button class="button button-small float-r"
                                        type="submit"><?php _e( "Aktualisiere .htaccess-Datei", 'cpsec' ) ?></button>
                            </form>
                        </div>
						<?php
                    }
                }
                ?>
                </p>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processRevert"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button button-small button-grey"
                            type="submit"><?php _e( "Zurücksetzen", 'cpsec' ) ?></button>
                </form>
			<?php else:
                $servers = \CP_Defender\Behavior\Utils::instance()->serverTypes();

                if ( DIRECTORY_SEPARATOR == '\\' ) {
                    //Windows
                    $wp_includes    = str_replace( ABSPATH, '', WPINC );
				    $wp_content     = str_replace( ABSPATH, '', WP_CONTENT_DIR );
                } else {
                    $wp_includes    = str_replace( $_SERVER['DOCUMENT_ROOT'], '', ABSPATH . WPINC );
				    $wp_content     = str_replace( $_SERVER['DOCUMENT_ROOT'], '', WP_CONTENT_DIR );
                }
                $detected_server = \CP_Defender\Behavior\Utils::instance()->determineServer( true );
                if ( array_key_exists( $detected_server, $servers ) ) {
                    $setting->active_server = $detected_server;
                }

            ?>
                <div class="columns">
                    <div class="column is-one-third">
                        <?php _e( 'Server Typ:', 'cpsec' ); ?>
                    </div>
                    <div class="column is-one-third">
                        <select class="mline hardener-server-list" name="server">
                            <?php foreach ( $servers as $server => $server_name ): ?>
                                <option value="<?php echo esc_attr( $server ); ?>" <?php selected( $server, $setting->active_server ); ?>><?php echo esc_html( $server_name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <input type="hidden" class="hardener-wp-content-dir" value="<?php echo $wp_content; ?>" />
                <input type="hidden" class="hardener-wp-includes-dir" value="<?php echo $wp_includes; ?>" />
                <div class="<?php echo ( $setting->active_server != 'apache' ) ? 'wd-hide' : ''; ?> hardener-instructions hardener-instructions-apache">
                    <div class="line">
                        <p><?php _e( "Wir werden eine <strong>.htaccess</strong>-Datei im Stammverzeichnis platzieren, um die darin enthaltenen Dateien und Ordner zu sperren.", 'cpsec' ) ?></p>
                    </div>
                    <form method="post" class="hardener-frm hardener-apache-frm rule-process">
                        <?php $controller->createNonceField(); ?>
                        <input type="hidden" name="action" value="processHardener"/>
                        <input type="hidden" name="file_paths" value=""/>
                        <input type="hidden" name="current_server" value="apache"/>
                        <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                        <button class="button float-r"
                                type="submit"><?php _e( "Füge .htaccess-Datei hinzu", 'cpsec' ) ?></button>
                    </form>
                </div>
                <div class="<?php echo ( $setting->active_server != 'litespeed' ) ? 'wd-hide' : ''; ?> hardener-instructions hardener-instructions-litespeed">
                    <div class="line">
                        <p><?php _e( "Wir werden eine <strong>.htaccess</strong>-Datei im Stammverzeichnis platzieren, um die darin enthaltenen Dateien und Ordner zu sperren.", 'cpsec' ) ?></p>
                    </div>
                    <form method="post" class="hardener-frm hardener-litespeed-frm rule-process">
                        <?php $controller->createNonceField(); ?>
                        <input type="hidden" name="action" value="processHardener"/>
                        <input type="hidden" name="file_paths" value=""/>
                        <input type="hidden" name="current_server" value="litespeed"/>
                        <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                        <button class="button float-r"
                                type="submit" ><?php _e( "Füge .htaccess-Datei hinzu", 'cpsec' ) ?></button>
                    </form>
                </div>
                <div class="<?php echo ( $setting->active_server != 'nginx' ) ? 'wd-hide' : ''; ?> hardener-instructions hardener-instructions-nginx">
                    <?php

                        $rules = "# Stop php access except to needed files in wp-includes
location ~* ^$wp_includes/.*(?<!(js/tinymce/wp-tinymce))\.php$ {
  internal; #internal allows ms-files.php rewrite in multisite to work
}

# Specifically locks down upload directories in case full wp-content rule below is skipped
location ~* /(?:uploads|files)/.*\.php$ {
  deny all;
}

# Deny direct access to .php files in the /wp-content/ directory (including sub-folders).
#  Note this can break some poorly coded plugins/themes, replace the plugin or remove this block if it causes trouble
location ~* ^$wp_content/.*\.php$ {
  deny all;
}
";
                    ?>

                    <div class="well well-yellow">
                        <h3><?php esc_html_e( 'NGINX-Konfiguration durch Hosting einrichten', 'cpsec' ); ?></h3>
                        <p><?php esc_html_e( 'PS Security kann diese Regel nicht automatisch aktivieren, weil NGINX-Konfigurationen nur mit Serverzugriff geändert werden können.', 'cpsec' ); ?></p>
                        <ol>
                            <li><?php esc_html_e( 'Kopiere die Konfiguration mit dem Button unten.', 'cpsec' ); ?></li>
                            <li><?php esc_html_e( 'Sende sie an deinen Hosting-Support und bitte darum, sie für diese Website in die NGINX-Konfiguration aufzunehmen.', 'cpsec' ); ?></li>
                            <li><?php esc_html_e( 'Bitte den Support, NGINX danach neu zu laden.', 'cpsec' ); ?></li>
                        </ol>
                    </div>
                    <div class="well well-grey">
                        <h3><?php esc_html_e( 'Für selbstverwaltete NGINX-Server', 'cpsec' ); ?></h3>
                        <ol>
                            <li><?php esc_html_e( 'Öffne die Server-Konfiguration dieser Website. Unter Debian und Ubuntu liegt sie häufig in /etc/nginx/sites-available/.', 'cpsec' ); ?></li>
                            <li><?php esc_html_e( 'Füge die unten stehende Konfiguration innerhalb des passenden server-Blocks ein.', 'cpsec' ); ?></li>
                            <li><?php esc_html_e( 'Prüfe die Konfiguration, bevor du sie aktivierst:', 'cpsec' ); ?> <code>sudo nginx -t</code></li>
                            <li><?php esc_html_e( 'Lade NGINX nach erfolgreicher Prüfung neu:', 'cpsec' ); ?> <code>sudo systemctl reload nginx</code></li>
                        </ol>
                        <p><?php esc_html_e( 'Verwende bei einer abweichenden Linux-Distribution oder einem anderen Verwaltungswerkzeug die entsprechenden Befehle deines Systems.', 'cpsec' ); ?></p>
                    </div>
                    <textarea id="hardener-nginx-config" class="large-text code" rows="16" readonly>## PS Security - Prevent PHP Execution ##
<?php echo esc_textarea( $rules ); ?>## PS Security - End ##</textarea>
                    <button class="button button-secondary hardener-copy-nginx-config" type="button" data-copied-message="<?php esc_attr_e( 'NGINX-Konfiguration wurde in die Zwischenablage kopiert.', 'cpsec' ); ?>" data-copy-failed-message="<?php esc_attr_e( 'Die NGINX-Konfiguration konnte nicht in die Zwischenablage kopiert werden. Bitte markiere und kopiere den Text manuell.', 'cpsec' ); ?>"><?php esc_html_e( 'NGINX-Konfiguration kopieren', 'cpsec' ); ?></button>
                </div>
                <div class="<?php echo ( $setting->active_server != 'iis' ) ? 'wd-hide' : ''; ?> hardener-instructions hardener-instructions-iis">
                    <div class="line">
                        <p><?php printf( __( 'Für IIS-Server besuche <a href="%s">Microsoft TechNet</a></a>', 'cpsec' ), 'https://technet.microsoft.com/en-us/library/cc725855(v=ws.10).aspx' ); ?></p>
                    </div>
                </div>
                <div class="<?php echo ( $setting->active_server != 'iis-7' ) ? 'wd-hide' : ''; ?> hardener-instructions hardener-instructions-iis-7">
                    <div class="line">
                        <p><?php _e( "Wir werden eine <strong>web.config</strong>-Datei in den Uploads-Ordner legen, um die darin enthaltenen Dateien und Ordner zu sperren.", 'cpsec' ) ?></p>
                    </div>
                    <div class="line">
                        <p><?php printf( __( 'Für weitere Informationen besuche bitte <a href="%s">Microsoft TechNet</a>', 'cpsec' ), 'https://technet.microsoft.com/en-us/library/cc725855(v=ws.10).aspx' ); ?></p>
                    </div>
                    <form method="post" class="hardener-frm hardener-litespeed-frm rule-process">
                        <?php $controller->createNonceField(); ?>
                        <input type="hidden" name="action" value="processHardener"/>
                        <input type="hidden" name="current_server" value="iis-7"/>
                        <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                        <button class="button float-r"
                                type="submit" ><?php _e( "web.config-Datei hinzufügen", 'cpsec' ) ?></button>
                    </form>

                </div>
                <?php $controller->showIgnoreForm();
                $prevent_php_style = "style='display:none'";
                if ( in_array( $setting->active_server, array( 'apache', 'litespeed' ) ) ) {
                    $prevent_php_style = "style='display:block'";
                }
                ?>
                <div <?php echo $prevent_php_style; ?> class="hardener-instructions hardener-instructions-extra-exceptions">
                    <h3>
                        <?php _e( "Exceptions", 'cpsec' ) ?>
                    </h3>
                    <div class="line">
                        <p><?php _e( "Standardmäßig sperrt PS Security Verzeichnisse, für die WordPress keine PHP-Ausführung benötigt. Wenn du jedoch bestimmte Dateien hast, für die du die PHP-Ausführung zulassen musst, kannst du Ausnahmen hinzufügen. Füge einen Dateinamen pro Zeile hinzu", 'cpsec' ) ?></p>
                        <button class="button button-grey hardener-php-excuted-execption" type="button"><?php _e( "Add Exception", 'cpsec' ) ?></button>
                    </div>
                    <div class="line">
                        <textarea class="hardener-php-excuted-ignore wd-hide"></textarea>
                    </div>
                </div>
                <?php endif; ?>
        </div>
    </div>
</div>