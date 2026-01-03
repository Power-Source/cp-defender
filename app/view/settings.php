<div class="wrap">
	<div class="wpmud">
		<div class="cp-defender">
			<div class="wd-settings">
				<section id="header">
					<h1 class="tl"><?php esc_html_e( "Einstellungen", cp_defender()->domain ) ?></h1>
				</section>
				<?php if ( $controller->has_flash( 'updated' ) ): ?>
					<div class="wd-success wd-left">
						<a href="#" class="wd-dismiss">
							&times;
						</a>
						<i class="dev-icon dev-icon-tick"></i>
						<?php echo esc_html( $controller->get_flash( 'updated' ) ) ?>
					</div>
				<?php endif; ?>
				<section class="dev-box">
					<div class="box-title">
						<h3><?php esc_html_e( "Allgemeine Einstellungen", cp_defender()->domain ) ?></h3>
					</div>
					<div class="box-content">
						<form method="post">
							<div class="row setting-field">
								<div class="col-left">
									<label><?php esc_html_e( "Scan-Typen", cp_defender()->domain ) ?></label>

									<div class="setting-description">
										<?php esc_html_e( "Standardmäßig empfehlen wir, alle Scans auszuführen, aber du kannst diese ausschalten, wenn du möchtest", cp_defender()->domain ) ?>
										<div class="wd-clearfix"></div>
										<br/>
									</div>
								</div>
								<div class="col-right">
									<div class="group">
										<?php
										$key     = 'use_' . WD_Scan_Api::SCAN_CORE_INTEGRITY . '_scan';
										$tooltip = WD_Utils::get_setting( $key ) == 1 ? esc_html__( "Deaktiviere diesen Scan", cp_defender()->domain ) : esc_html__( "Aktiviere diesen Scan", cp_defender()->domain );
										?>
										<div class="col span_4_of_12">
											<label><?php esc_html_e( "WordPress Core Integrität", cp_defender()->domain ) ?></label>
										</div>
										<div class="col span_8_of_12">
											<div class="group">
												<div class="col span_1_of_12">
													<span class="toggle"
													      tooltip="<?php echo esc_attr( $tooltip ) ?>">
											<input type="checkbox" class="toggle-checkbox"
											       id="<?php echo esc_html( $key ) ?>"
												<?php checked( 1, WD_Utils::get_setting( $key ) ) ?>/>
											<label class="toggle-label" for="<?php echo esc_attr( $key ) ?>"></label>
										</span>
												</div>
												<div class="col span_11_of_12">
													<small class="">
														<?php esc_html_e( "PS Security prüft, ob Änderungen oder Ergänzungen an den WordPress-Kerndateien vorgenommen wurden.", cp_defender()->domain ) ?>
													</small>
												</div>
											</div>

										</div>
										<div class="wd-clear"></div>
									</div>
									<div class="group wd-relative-position">
										<div class="col span_4_of_12">
											<label><?php esc_html_e( "Plugin- & Theme-Schwachstellen", cp_defender()->domain ) ?></label>
										</div>
										<div class="col span_8_of_12">
											<div class="group">
												<div class="col span_1_of_12">
													<?php
													$key     = 'use_' . WD_Scan_Api::SCAN_VULN_DB . '_scan';
													$tooltip = WD_Utils::get_setting( 'use_' . WD_Scan_Api::SCAN_VULN_DB . '_scan' ) == 1 ? esc_html__( "Deaktiviere diesen Scan", cp_defender()->domain ) : esc_html__( "Aktiviere diesen Scan", cp_defender()->domain );
													?>
													<span class="toggle"
													      tooltip="<?php echo esc_attr( $tooltip ) ?>">
											<input type="checkbox" class="toggle-checkbox"
											       id="<?php echo esc_attr( $key ) ?>"
												<?php checked( 1, WD_Utils::get_setting( $key ) ) ?>/>
											<label class="toggle-label" for="<?php echo esc_attr( $key ) ?>"></label>
										</span>
												</div>
												<div class="col span_11_of_12">
													<small>
														<?php esc_html_e( "PS Security sucht nach veröffentlichten Schwachstellen in deinen installierten Plugins und Themes.", cp_defender()->domain ) ?>
													</small>
												</div>
											</div>
										</div>
										<div class="wd-clear"></div>
									</div>
									<div class="group wd-relative-position">
										<div class="col span_4_of_12">
											<label><?php esc_html_e( "Verdächtiger Code", cp_defender()->domain ) ?></label>
										</div>
										<div class="col span_8_of_12">
											<div class="group">
												<div class="col span_1_of_12">
													<?php
													$key     = 'use_' . WD_Scan_Api::SCAN_SUSPICIOUS_FILE . '_scan';
													$tooltip = WD_Utils::get_setting( $key ) == 1 ? esc_html__( "Deaktiviere diesen Scan", cp_defender()->domain ) : esc_html__( "Aktiviere diesen Scan", cp_defender()->domain );
													?>
													<span class="toggle"
													      tooltip="<?php echo esc_attr( $tooltip ) ?>">
											<input type="checkbox" class="toggle-checkbox"
											       id="<?php echo esc_attr( $key ) ?>"
												<?php checked( 1, WD_Utils::get_setting( 'use_' . WD_Scan_Api::SCAN_SUSPICIOUS_FILE . '_scan' ) ) ?>/>
											<label class="toggle-label" for="<?php echo esc_attr( $key ) ?>"></label>
										</span>
												</div>
												<div class="col span_11_of_12">
													<small>
														<?php esc_html_e( "PS Security durchsucht alle deine Dateien nach verdächtigem und potenziell schädlichem Code.", cp_defender()->domain ) ?>
													</small>
												</div>
											</div>
										</div>
										<div class="wd-clearfix"></div>
									</div>
								</div>
								<div class="wd-clearfix"></div>
							</div>
							<div class="row setting-field">
								<div class="col-left">
									<label><?php esc_html_e( "Maximale Dateigröße (MB)", cp_defender()->domain ) ?></label>

									<div class="setting-description">
										<?php esc_html_e( "PS Security überspringt alle Dateien, die größer als diese Größe sind. Je kleiner diese Zahl ist, desto schneller kann PS Security dein System scannen.", cp_defender()->domain ) ?>
										<div class="wd-clearfix"></div>
										<br/>
									</div>
								</div>
								<div class="col-right">
									<div class="group">
										<div class="col span_4_of_12">
											<input type="text" name="max_file_size"
											    value="<?php echo esc_attr( WD_Utils::get_setting( 'max_file_size' ) ) ?>">
										</div>
									</div>
								</div>
								<div class="wd-clearfix"></div>
							</div>
							<div class="row setting-field">
								<div class="col-left">
									<label><?php esc_html_e( "Alle E-Mail-Berichte aktivieren", cp_defender()->domain ) ?></label>

									<div class="setting-description">
										<?php esc_html_e( "PS Security benachrichtigt Dich standardmäßig per E-Mail, sobald Probleme auf Deiner Webseite auftreten. Durch Aktivieren dieser Option bleibst Du stets informiert, auch wenn Deine Webseite reibungslos funktioniert.", cp_defender()->domain ) ?>
										<div class="wd-clearfix"></div>
										<br/>
									</div>
								</div>
								<div class="col-right">
									<div class="group">
										<div class="col span_4_of_12">
											<?php
											$key = 'always_notify';
											//$tooltip = WD_Utils::get_setting( $key, 0 ) == 1 ? esc_html__( "Send only problem", cp_defender()->domain ) : esc_html__( "Always send", cp_defender()->domain );
											?>
											<span class="toggle">
											<input type="checkbox" class="toggle-checkbox"
											       id="<?php echo esc_attr( $key ) ?>"
												<?php checked( 1, WD_Utils::get_setting( $key, 0 ) ) ?>/>
											<label class="toggle-label" for="<?php echo esc_attr( $key ) ?>"></label>
										</span>
										</div>
									</div>
								</div>
								<div class="wd-clearfix"></div>
							</div>
							<?php wp_nonce_field( 'wd_settings', 'wd_settings_nonce' ) ?>
							<input type="hidden" name="action" value="wd_settings_save"/>
							<br/>

							<div class="wd-clearfix"></div>
							<div class="wd-right">
								<button type="submit" class="button wd-button">
									<?php esc_html_e( "Einstellungen speichern", cp_defender()->domain ) ?>
								</button>
							</div>
						</form>
						<br/>
					</div>
				</section>
				<section class="dev-box">
					<div class="box-title">
						<h3><?php esc_html_e( "E-Mail-Empfänger", cp_defender()->domain ) ?></h3>
					</div>
					<div class="box-content">
						<form id="email-recipients-frm">
							<p>
								<?php esc_html_e( "Wähle aus, welche Benutzer deiner Webseite Scan-Berichtsergebnisse in ihrem E-Mail-Posteingang erhalten sollen.", cp_defender()->domain ) ?>
							</p>
							<div class="wd-error wd-hide"></div>
							<div class="wd-clear"></div>
							<br/>
							<?php echo $controller->display_recipients() ?>
							<input name="username" id="email-recipient" class="user-search"
							       data-empty-msg="<?php esc_attr_e( "Wir haben keinen Administrator mit diesem Namen gefunden...", cp_defender()->domain ) ?>"
							       placeholder="<?php esc_attr_e( "Gib den Namen eines Benutzers ein", cp_defender()->domain ) ?>"
							       type="search"/>
							<button type="submit" disabled="disabled"
							        class="button wd-button"><?php esc_html_e( "Hinzufügen", cp_defender()->domain ) ?></button>
							<div class="clearfix"></div>
							<input type="hidden" name="action" value="wd_add_recipient">
							<?php wp_nonce_field( 'wd_add_recipient', 'wd_settings_nonce' ) ?>
						</form>
					</div>
				</section>
				<section class="dev-box">
					<div class="box-title">
						<h3><?php esc_html_e( "E-Mail-Vorlagen", cp_defender()->domain ) ?></h3>
					</div>
					<div class="box-content">
						<p>
							<?php esc_html_e( "Wenn Defender diese Webseite scannt, wird ein Bericht über etwaige Probleme erstellt. Du kannst wählen, diese Benachrichtigungen an eine bestimmte E-Mail-Adresse zu senden und den untenstehenden Text anpassen.", cp_defender()->domain ) ?>
						</p>

						<p>
							<?php esc_html_e( "Verfügbare Variablen", cp_defender()->domain ) ?>
						</p>

						<div class="wd-well">
							<div class="group">
								<div class="col span_4_of_12">
									<p>{USER_NAME}</p>
								</div>
								<div class="col span_8_of_12">
									<?php esc_html_e( "Wir verwenden den Vornamen des Benutzers oder den Anzeigenamen, falls der Vorname nicht verfügbar ist", cp_defender()->domain ) ?>
								</div>
							</div>
							<div class="wd-clearfix"></div>
							<div class="group">
								<div class="col span_4_of_12">
									<p>{ISSUES_COUNT}</p>
								</div>
								<div class="col span_8_of_12">
									<?php esc_html_e( "Die Anzahl der von Defender gefundenen Probleme", cp_defender()->domain ) ?>
								</div>
							</div>
							<div class="wd-clearfix"></div>
							<div class="group">
								<div class="col span_4_of_12">
									<p>{ISSUES_LIST}</p>
								</div>
								<div class="col span_8_of_12">
									<?php esc_html_e( "Die Liste der Probleme", cp_defender()->domain ) ?><br/>
								</div>
							</div>
							<div class="wd-clearfix"></div>
							<div class="group">
								<div class="col span_4_of_12">
									<p>{SCAN_PAGE_LINK}</p>
								</div>
								<div class="col span_8_of_12">
									<?php esc_html_e( "Ein Link zurück zum Tab 'Scans' dieser Webseite", cp_defender()->domain ) ?>
								</div>
							</div>
						</div>
						<br/>

						<form method="post">
							<div class="setting-field">
								<div class="col-left">
									<label
										for="completed_scan_email_subject"><?php esc_html_e( "Betreff", cp_defender()->domain ) ?></label>
								</div>
								<div class="col-right">
									<input type="text" id="completed_scan_email_subject"
									       name="completed_scan_email_subject"
									       value="<?php esc_attr_e( WD_Utils::get_setting( 'completed_scan_email_subject' ) ) ?>"/>
								</div>
								<div class="wd-clearfix"></div>
							</div>
							<div class="setting-field">
								<div class="col-left">
									<label
										for="completed_scan_email_content_error"><?php esc_html_e( "Gefundene Probleme", cp_defender()->domain ) ?></label>

									<div class="setting-description">
										<?php esc_html_e( "Wenn während eines automatisierten Scans ein Problem gefunden wurde, sendet Defender diese E-Mail an deine Empfänger.", cp_defender()->domain ) ?>
										<div class="wd-clearfix"></div>
										<br/>
									</div>
								</div>
								<div class="col-right">
								<textarea rows="10" id="completed_scan_email_content_error"
								          name="completed_scan_email_content_error"><?php echo esc_textarea( WD_Utils::get_setting( 'completed_scan_email_content_error' ) ) ?></textarea>
								</div>
								<div class="wd-clearfix"></div>
							</div>
							<div class="setting-field">
								<div class="col-left">
									<label for="completed_scan_email_content_success">
										<?php esc_html_e( "Alles OK", cp_defender()->domain ) ?></label>

									<div class="setting-description">
										<?php esc_html_e( "Wenn keine Probleme vom Scan erkannt werden, erhalten deine Empfänger diese E-Mail.", cp_defender()->domain ) ?>
										<div class="wd-clearfix"></div>
										<br/>
									</div>
								</div>
								<div class="col-right">
								<textarea rows="10" id="completed_scan_email_content_success"
								          name="completed_scan_email_content_success"><?php echo esc_textarea( WD_Utils::get_setting( 'completed_scan_email_content_success' ) ) ?></textarea>
								</div>
								<div class="wd-clearfix"></div>
							</div>
							<?php wp_nonce_field( 'wd_settings', 'wd_settings_nonce' ) ?>
							<br/>

							<div class="wd-clearfix"></div>
							<input type="hidden" name="action" value="wd_settings_save"/>

							<div class="wd-right">
								<button type="submit" class="button wd-button">
									<?php esc_html_e( "Einstellungen speichern", cp_defender()->domain ) ?>
								</button>
							</div>
						</form>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>