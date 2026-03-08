=== PS Security ===
Contributors: PSource
Tags: updates, security, multisite, anti-spam, malware-scan
Requires at least: 5.0
Tested up to: 6.4 
ClassicPress 1.6.0
Requires PHP: 7.4
Stable tag: 1.0.2
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

= Werden die Daten von Anti-Splog migriert? =

Ja, wenn du vorher das Anti-Splog Plugin von WPMUDEV verwendet hast, werden die Daten automatisch migriert.

== Screenshots ==

1. Dashboard-Übersicht
2. Anti-Spam Moderation Interface
3. Pattern Management
4. Statistiken und Reports

== Changelog ==

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