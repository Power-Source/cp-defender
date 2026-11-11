=== PS Security ===
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

Erhalte regelmäßige Sicherheitsüberprüfungen, Schwachstellenberichte, Sicherheitsempfehlungen und individuelle Sicherheitsmaßnahmen für Deine Webseite – mit nur wenigen Klicks. PS Security ist Dein Analyst und Sicherheitsexperte, der rund um die Uhr für Dich da ist.

== Description ==

PS Security ist ein umfassendes ClassicPress/WordPress-Sicherheits-Plugin, das Deine Webseite vor verschiedenen Bedrohungen schützt. Es bietet fortgeschrittene Sicherheitsfunktionen wie:

- **Malware-Scans** - Regelmäßige Überprüfung auf Malware und verdächtige Dateien
- **Firewall-Schutz** - Blockierung bösartiger IP-Adressen und Anfragen
- **Login-Sicherheit** - Schutz vor Brute-Force-Attacken
- **Datei-Integrität** - Überwachung auf unerwartete Dateiveränderungen
- **Sicherheits-Berichte** - Detaillierte Logs und Benachrichtigungen
- **Anti-Spam (NEU!)** - Leistungsstarker Multisite-Schutz vor Spam-Registrierungen

=== Anti-Spam Features (Multisite) ===

Das neue Anti-Spam-Modul schützt Multisite-Installationen effektiv vor Spam-Blogs und bösartigen Registrierungen:

**Pattern Matching:**
- Flexible Regex-basierte Erkennung von Spam in Domains, Usernames, E-Mails und Site-Titeln
- Live-Testing von Patterns gegen bestehende Daten
- Automatische Statistiken zu Pattern-Matches

**IP-Reputation-System:**
- Automatisches Tracking verdächtiger IP-Adressen
- Blockierung nach wiederholten Spam-Versuchen
- Übersicht der Top-Spammer-IPs

**Rate Limiting:**
- Begrenzung der Registrierungen pro IP-Adresse
- Konfigurierbare Zeitfenster

**Human Verification:**
- reCAPTCHA v2 Integration
- Eigene Sicherheitsfragen definieren

**Moderation-Interface:**
- Übersichtliche Liste verdächtiger und gespammter Blogs
- Bulk-Aktionen für effizientes Management
- Detaillierte Spam-Certainty-Bewertungen

**Statistiken & Reports:**
- Detaillierte Auswertungen zu Spam-Aktivitäten
- Pattern-Effektivität
- Zeitliche Trends

Starte noch heute und sichere Deine ClassicPress/WordPress-Installation ab.

== Installation ==

1. Lade das Plugin in das `/wp-content/plugins/` Verzeichnis hoch
2. Aktiviere das Plugin im 'Plugins' Menü
3. Für Multisite: Netzwerk-Aktivierung erforderlich
4. Gehe zu 'PS Security' im Admin-Menü
5. Für Anti-Spam: Konfiguriere die Einstellungen unter "Anti-Spam"

== Frequently Asked Questions ==

= Funktioniert Anti-Spam auch auf Single-Site? =

Nein, das Anti-Spam-Modul ist speziell für Multisite-Installationen entwickelt. Auf Single-Sites wird es automatisch deaktiviert.

= Kann ich eigene Spam-Patterns hinzufügen? =

Ja! Unter "Patterns" kannst du beliebig viele eigene Regex-Patterns erstellen und live testen.

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