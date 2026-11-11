=== PS Security Suite ===
Contributors: PSource
Tags: updates, security, multisite, anti-spam, malware-scan
Requires at least: 5.0
Requires CP: 2.7
Tested up to: 7.1.4
ClassicPress 2.7.4
Requires PHP: 8.0
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

PS Security hilft dir, deine ClassicPress- oder WordPress-Website Schritt für Schritt besser zu schützen. Du siehst offene Sicherheitsrisiken, entscheidest selbst über die passenden Maßnahmen und behältst Logins, Dateien und verdächtige Zugriffe im Blick.

== Description ==

Du musst kein Sicherheitsexperte sein, um PS Security zu verwenden. Unter **PS Security → Sicherheits-Tweaks** zeigt das Plugin verständlich, welche Schutzmaßnahmen noch offen sind. Du kannst jede Maßnahme einzeln ansehen, aktivieren oder bei Bedarf wieder zurücksetzen.

=== Das schützt PS Security ===

* **Anmeldung und Benutzerkonten:** Erkennt wiederholte fehlgeschlagene Anmeldungen, kann IP-Adressen sperren und unterstützt Zwei-Faktor-Anmeldung.
* **Dateien und Updates:** Prüft wichtige Website-Dateien auf unerwartete Änderungen, fehlende Core-Dateien und verdächtige Inhalte.
* **Sicherheits-Tweaks:** Hilft bei sinnvollen Grundeinstellungen, etwa beim Schutz der Dateibearbeitung, XML-RPC oder der PHP-Ausführung in Verzeichnissen, in denen sie nicht benötigt wird.
* **IP-Schutz:** Zeigt gesperrte Zugriffe an. Du kannst IP-Adressen gezielt sperren, wieder freigeben oder auf eine Whitelist setzen.
* **Protokolle und Hinweise:** Hält sicherheitsrelevante Ereignisse nachvollziehbar fest, damit du weißt, was auf deiner Website passiert ist.

=== Für Netzwerke und mehrere Websites ===

PS Security funktioniert sowohl für einzelne Websites als auch für ClassicPress- oder WordPress-Multisite-Netzwerke. Netzwerkadministratoren können zentrale Vorgaben festlegen und bei passenden Funktionen entscheiden, ob Website-Administratoren eigene Einstellungen verwenden dürfen.

Das Anti-Spam-Modul ist für Multisite-Netzwerke gedacht. Es hilft, unerwünschte Registrierungen und Spam-Websites zu erkennen und zu begrenzen. Dafür stehen unter anderem Regeln für auffällige Namen, E-Mail-Adressen und Domains, Begrenzungen für wiederholte Registrierungen sowie eine Übersicht verdächtiger Anmeldungen zur Verfügung.

=== Wichtig vor dem Aktivieren ===

Aktiviere Schutzmaßnahmen bewusst und einzeln. Manche Einstellungen können Funktionen von Themes, Plugins oder externen Diensten beeinflussen. Lege vor größeren Änderungen ein Backup an und prüfe die Website danach kurz als Besucher und als Administrator.

Für den bestmöglichen Schutz sollte deine Website PHP 8.5.12 oder neuer verwenden. PS Security weist auf ältere PHP-Versionen als Sicherheitsrisiko hin.

== Installation ==

1. Installiere und aktiviere PS Security wie jedes andere Plugin über **Plugins → Installieren** oder lade den Plugin-Ordner nach `/wp-content/plugins/` hoch.
2. In einem Multisite-Netzwerk aktivierst du das Plugin über die **Netzwerkverwaltung** für das gesamte Netzwerk.
3. Öffne anschließend **PS Security → Sicherheits-Tweaks**.
4. Lies die offenen Hinweise und aktiviere nur die Maßnahmen, die zu deiner Website passen.
5. Prüfe nach jeder Änderung Anmeldung, Kontaktformulare, Shop oder andere wichtige Funktionen.

== Frequently Asked Questions ==

= Wo beginne ich am besten? =

Öffne **PS Security → Sicherheits-Tweaks** und arbeite die offenen Hinweise nacheinander durch. Beginne mit Maßnahmen, die keine bestehenden Funktionen verändern. Bei jeder Regel erklärt PS Security, was sie schützt und welche Auswirkungen möglich sind.

= Kann ich eine Schutzmaßnahme wieder zurücksetzen? =

Ja. Gehe zu der betreffenden Regel und wähle die Rücksetzfunktion. Danach erscheint die Maßnahme wieder als offen, damit du sie bei Bedarf später erneut aktivieren kannst.

= Kann ich mich durch den Login-Schutz selbst aussperren? =

Das ist möglich, wenn du sehr strenge Regeln aktivierst oder keine Wiederherstellungsmöglichkeit hinterlegst. Richte bei der Zwei-Faktor-Anmeldung daher immer eine sichere Wiederherstellung ein und setze deine eigene feste IP-Adresse nur dann auf die Whitelist, wenn sie sich nicht regelmäßig ändert.

= Was muss ich bei NGINX beachten? =

PS Security kann NGINX-Konfigurationen nicht selbst auf dem Server ändern. Die Regel zeigt dir die benötigte Konfiguration zum Kopieren an. Bei Hosting-Paketen gibst du sie an den Support weiter; bei einem selbstverwalteten Server fügst du sie in den passenden NGINX-`server`-Block ein und prüfst die Konfiguration vor dem Neuladen.

= Funktioniert Anti-Spam auch auf einzelnen Websites? =

Nein. Das Anti-Spam-Modul ist speziell für Multisite-Netzwerke entwickelt und auf einzelnen Websites nicht verfügbar.

= Kann ich eigene Anti-Spam-Regeln anlegen? =

Ja. Im Netzwerkbereich kannst du eigene Regeln für auffällige Domains, Benutzernamen, E-Mail-Adressen und Seitentitel anlegen und vor dem Speichern testen.

== PSOURCE unterstützen ==

PS Security wird von PSOURCE bereitgestellt und weiterentwickelt. Wenn dir das Plugin hilft, kannst du die Arbeit mit einer freiwilligen Spende unterstützen:

https://psource.eimen.net/spendenaktionen/psource-unterstuetzen/

== Changelog ==

= 1.1.1 =
* Fix: Aktionen zum Sperren, Entsperren sowie zur Whitelist-Verwaltung in den IP-Sperrprotokollen werden nach der Ausführung wieder als anklickbare Links angezeigt.
* Neu: Konfigurierbarer XML-RPC-Schutz mit Brute-Force-, Pingback- und vollständigem Sperrmodus; Netzwerkadministratoren können Subsite-Administratoren eigene Einstellungen unter Werkzeuge erlauben.
* Fix: PHP-Ausführungs-Härtung zeigt das Eingabefeld für Ausnahme-Dateien wieder an und erkennt Apache, LiteSpeed, NGINX sowie IIS zuverlässiger.
* Fix: Die Ausnahme-Verwaltung für den PHP-Ausführungsschutz sperrt sich bei nicht beschreibbarer .htaccess-Datei und zeigt die konkrete Berechtigungsursache an.
* Fix: Der PHP-Ausführungsschutz wird nur nach erfolgreicher Aktivierung durch einen Administrator als behoben markiert; ein Zurücksetzen bleibt dauerhaft als offene Sicherheitslücke sichtbar.
* Fix: Fehlen die von PS Security verwalteten PHP-Schutzregeln, wird eine zuvor aktivierte Apache- oder LiteSpeed-Härtung automatisch wieder als offen markiert.
* Verbesserung: NGINX-Härtungen erklären den notwendigen Hosting-Schritt, enthalten eine Anleitung für selbstverwaltete Server und bieten die vollständige Serverkonfiguration zum Kopieren an.
* Fix: Die PHP-Härtungsregel bewertet nur noch unterstützte PHP-Versionen als aktuell und empfiehlt PHP 8.5.12 statt der veralteten Version 7.1.

= 1.1.0 =
* Fix: PHP-8.4-Deprecated-Warnung bei der Anti-Spam-Pattern-Prüfung durch explizit nullable deklarierten Aktionsfilter behoben.
* Fix: Die Dateiscan-Baseline wird für Plugins und Themes je installierter Versionsnummer geführt und nach Updates neu aufgebaut, um Fehlalarme durch reguläre Aktualisierungen zu verhindern.
* Fix: Audit-Zusammenfassungen und Sperrberichte werden nicht mehr durch obsolete Free-/Pro-Sperren blockiert; fehlgeschlagene E-Mail-Zustellungen werden protokolliert und nicht als erfolgreich markiert.
* Bereinigung: Die nicht mehr verfügbare Dashboard-API-Key- und Cloud-Audit-Integration wurde entfernt; Audit-Ereignisse werden ausschließlich lokal gespeichert.

= 1.0.9 =
* Fix: Migrationsbereinigung entfernt alle bestätigten Migrationsreste ohne eine fehleranfällige erneute Remote-Prüfung.
* Neu: Bulk-Aktion zum Löschen ausgewählter Scanbefunde ergänzt.
* Fix: Das PS-Security-Dashboard löst keinen unbeabsichtigten CSV-Download der Audit-Protokolle mehr aus.
* Fix: Dateiscans werden nach der letzten Scan-Queue zuverlässig abgeschlossen und bleiben nicht bei 100 % stehen.
* Fix: Die Migrationsrest-Prüfung lädt die ClassicPress- und WordPress-Releasebäume zuverlässig und klassifiziert Core-Dateien wieder korrekt.

= 1.0.8 =
* Neu: Dateiscanner gleicht ClassicPress-Core-Dateien mit dem offiziellen Releasebaum ab und erkennt fehlende Dateien.
* Neu: Fehlende ClassicPress-Core-Dateien können im Befunddialog gezielt aus dem passenden offiziellen Release wiederhergestellt werden.
* Neu: ClassicPress-Migrationsbereinigung entfernt nach Bestätigung nur Dateien, die im aktuellen ClassicPress-Core fehlen und im aktuellen WordPress-Core vorhanden sind.
* Verbesserung: Migrationsreste werden gegen den aktuellen stabilen WordPress-Release statt gegen eine historische Kompatibilitätsversion geprüft.
* Verbesserung: Bereits gespeicherte Scanbefunde werden vor Anzeige und Bereinigung erneut gegen die offiziellen Releasequellen validiert.
* Verbesserung: Datei-Integritätsprüfung erfasst nun auch wp-config.php und hält eine SHA-256-Baseline für Content-Dateien vor.
* Verbesserung: Der Content-Scan überspringt verschachtelte Vendor-, Snapshot-, Backup- und Build-Verzeichnisse zuverlässig.
* Verbesserung: Scan-Abbruch reagiert schneller, stoppt ausstehende Durchläufe zuverlässig und bereinigt zugehörige Sperren und Cron-Aufgaben.
* Verbesserung: Scan-Dialog, Fortschrittsanzeige und Befundaktionen wurden überarbeitet.
* Verbesserung: Englische Übersetzungen für die Dateiscan-Oberfläche und neue Befundtypen ergänzt.

= 1.0.7 =
* Fix: Ersetze deprecated socket_set_timeout() mit stream_set_timeout()
* Fix: Verhindere PHP-Deprecated-Warnung bei null-Werten in str_replace()
* Neu: Deutsche und englische Gettext-Kataloge für die Plugin-Oberfläche
* Verbesserung: Textdomain wird aus dem standardisierten Sprachdateiordner geladen
* Verbesserung: Übersetzbare Texte verwenden eine statische Textdomain für vollständige Kataloge
* Neu: Deutsch- und englischsprachige README-Dokumentation

= 1.0.6 =
* Fix: Audit-Logging liefert wieder lokale Ereignisse statt leerer Ergebnisse
* Fix: IP-Lockout-Ereignisse (Login/404) werden im Audit-Widget und Audit-Feed korrekt berücksichtigt
* NEU: Audit-Widget zeigt zusätzliche Kennzahlen getrennt an (Lockouts 24h, Honeypot-Blockierungen, Disposable-Blockierungen)
* NEU: Persistente Anti-Spam-Statistiken für Honeypot- und Wegwerf-E-Mail-Blockierungen
* Verbesserung: Hinweis auf Subsites in den Diskussions-Einstellungen zu netzwerkweiten Filtern und lokalen Ausnahmen
* Verbesserung: Hinweistext in korrektem Deutsch mit Umlauten

= 1.0.5 =

* Fix: Entfernung der veralteten jQuery Effects Core Abhängigkeit (deprecated seit CP 2.2.0)
* Verbesserung: Accordion- und Textarea-Animationen nutzen jetzt CSS3-Transitions statt jQuery Effects
* Verbesserung: Performance-Optimierung durch native CSS-Animationen
* Kompatibilität: Vorbereitung für ClassicPress 3.0.0
* Fix: Anti-Spam Settings-Seite nutzt jetzt das interne Defender UI-Framework konsistent
* Fix: Anti-Spam Einstellungen im Network-Admin übersichtlicher strukturiert und besser lesbar
* NEU: Netzwerkweiter Kommentar-Schutz mit zentraler Blacklist für E-Mail, IP und Domains
* NEU: Integration der Wegwerf-E-Mail-Erkennung auch für Kommentar-Spam-Abwehr
* Verbesserung: Automatische Übernahme verdächtiger Daten aus Spam-Blogs für Kommentar-Filterung

= 1.0.4 =

* Fix: Human-Verification wird bei Admin-User/Site-Erstellung im Dashboard nicht mehr fälschlich erzwungen
* Verbesserung: Signup-Validierung überspringt Anti-Spam-Prüfungen in vertrauenswürdigen Admin-Erstellungs-Workflows

= 1.0.3 =

* Fix: Zwei-Faktor-QR-Code in der Profil-Einrichtung wieder zuverlässig sichtbar
* Verbesserung: Robuster QR-Fallback bei externen QR-Code-Diensten
* Verbesserung: Manueller Secret-Key als Alternative zur QR-Einrichtung
* NEU: Optionale Zwei-Faktor-Authentifizierung per E-Mail-Code
* Verbesserung: Auswahl der 2FA-Methode pro Benutzerprofil (Authenticator-App oder E-Mail)
* Verbesserung: Login-Verifikation für E-Mail-OTP mit erneuter Code-Anforderung
* Verbesserung: Sicherheits-Cooldown von 5 Minuten für "Code erneut senden"

= 1.0.2 =

* NEU: Anti-Spam Modul für Multisite
* NEU: Pattern Matching System mit Regex-Support
* NEU: IP-Reputation-Tracking
* NEU: Rate Limiting für Signups
* NEU: Human Verification (reCAPTCHA & Q&A)
* NEU: Moderation-Interface mit Bulk-Actions
* NEU: Detaillierte Anti-Spam Statistiken
* Verbesserung: Migration von Anti-Splog Daten
* Verbesserung: PHP 8+ Standards
* Verbesserung: Moderne Admin-UI

= 1.0.1 =

* Performance-Boost für Dateiscanner

= 1.0.0 =

* Release