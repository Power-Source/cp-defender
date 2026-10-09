# PS Security Suite

**Deutsch** | [English](README.en.md)

[![Version](https://img.shields.io/badge/Version-1.0.7-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.1.0-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.3-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)


PS Security Suite ist ein Sicherheits-Plugin für ClassicPress und kompatible WordPress-Installationen. Es bündelt Dateiintegritätsprüfungen, Härtungsmaßnahmen, IP-Sperren, Audit-Protokollierung, Sicherheitsberichte und einen Multisite-Anti-Spam-Schutz in einer zentralen Administration.

> Sicherheit ist ein fortlaufender Prozess. Aktiviere nur Maßnahmen, deren Auswirkungen auf deine Installation du geprüft hast, und halte regelmäßige Backups vor.

## Funktionen

- **Dateiscanning**: Erkennt geänderte, zusätzliche und potenziell verdächtige Dateien sowie bekannte Schwachstellen in Plugins und Themes.
- **Sicherheits-Tweaks**: Führt geführte Härtungsmaßnahmen für typische Angriffsflächen aus.
- **IP-Sperren**: Schützt Anmeldeformulare und 404-Endpunkte vor wiederholten Zugriffen und Brute-Force-Versuchen.
- **Audit-Protokollierung**: Hält relevante Änderungen und Sicherheitsereignisse nachvollziehbar fest.
- **Berichte**: Erstellt regelmäßige Zusammenfassungen zu Sicherheitsstatus und Ereignissen.
- **Erweiterte Werkzeuge**: Stellt zusätzliche Verwaltungs- und Sicherheitsfunktionen bereit.
- **Zwei-Faktor-Authentifizierung**: Unterstützt Authenticator-Apps und E-Mail-Codes für Benutzerkonten.
- **Anti-Spam für Multisite**: Prüft Registrierungen und Kommentare mit Regeln, IP-Reputation, Rate Limits, Wegwerf-E-Mail-Erkennung und menschlicher Verifikation.

## Voraussetzungen

Die Release-Metadaten in [readme.txt](readme.txt) nennen:

- WordPress ab 5.0 oder ClassicPress ab 2.7.0
- PHP ab 7.4
- Für Anti-Spam: eine Multisite-Installation mit Netzwerkaktivierung

Für Scans, Updates und Härtungsmaßnahmen sollte die PHP-Installation über ausreichende Datei- und Netzwerkberechtigungen verfügen.

## Installation

1. Kopiere das Verzeichnis `cp-defender` nach `wp-content/plugins/`.
2. Aktiviere **PS Security Suite** in der Plugin-Verwaltung.
3. In einer Multisite aktivierst du das Plugin netzwerkweit.
4. Öffne im Administrationsmenü **PS Security**.
5. Prüfe die Einstellungen jedes Moduls, bevor du es produktiv einsetzt.

## Schnellstart

1. Öffne **PS Security > Dateiscanning** und führe einen ersten Scan aus.
2. Bearbeite gefundene Probleme erst nach Prüfung der betroffenen Datei und mit einem aktuellen Backup.
3. Aktiviere unter **Sicherheits-Tweaks** die für deine Website passenden Härtungsmaßnahmen.
4. Konfiguriere unter **IP-Sperren** Schwellenwerte und Benachrichtigungen.
5. Aktiviere auf Multisite-Netzwerken **Anti-Spam** und lege bei Bedarf eigene Regeln fest.

## Anti-Spam im Netzwerk

Das Anti-Spam-Modul ist für Multisite-Netzwerke ausgelegt. Es bietet unter anderem:

- Regeln für Domains, Benutzernamen, E-Mail-Adressen und Websitetitel
- Live-Tests für Regeln und Auswertungen zu Treffern
- IP-Reputation und konfigurierbare Rate Limits
- Prüfung von Wegwerf-E-Mail-Adressen
- Netzwerkweiten Kommentar-Schutz mit lokalen Ausnahmen für Subsites
- Cloudflare Turnstile, reCAPTCHA (Legacy) oder Sicherheitsfragen als menschliche Verifikation
- Moderation und Statistiken für verdächtige Registrierungen

Nutze bei regulären Ausdrücken zunächst die Testfunktion. Zu breit formulierte Regeln können legitime Registrierungen blockieren.

## Übersetzungen

Die Textdomain lautet `cpsec`. Sprachdateien befinden sich im Verzeichnis [languages](languages/):

- `cpsec.pot` ist die Vorlage für neue Übersetzungen.
- `cpsec-en_US.po` und `cpsec-en_US.mo` liefern die englische Oberfläche.
- `cpsec-de_DE.po` und `cpsec-de_DE.mo` enthalten deutsche Übersetzungen für englische Quelltexte.

Bearbeite PO-Dateien mit einem Gettext-kompatiblen Werkzeug wie Poedit und kompiliere danach die zugehörige MO-Datei neu. Nach Änderungen an PHP-Strings muss die POT-Datei aktualisiert und mit den vorhandenen PO-Dateien zusammengeführt werden.

## Struktur

```text
app/           Plugin-Logik, Module, Controller und Ansichten
assets/        Globale Stylesheets, Skripte und Bilder
languages/     Gettext-Kataloge
shared-ui/     Gemeinsame Administrationsoberfläche
vendor/        Eingebundene Laufzeitbibliotheken
```

Das Plugin startet in [cp-defender.php](cp-defender.php). Die einzelnen Sicherheitsbereiche liegen unter `app/module/`.

## Entwicklung

- Verwende für sichtbare PHP-Texte stets die Textdomain `cpsec`.
- Halte Übersetzungsaufrufe statisch, zum Beispiel `__( 'Text', 'cpsec' )`, damit Gettext-Werkzeuge sie extrahieren können.
- Prüfe geänderte PHP-Dateien mit `php -l <datei>`.
- Aktualisiere bei Änderungen am Release-Stand sowohl Plugin-Header als auch [readme.txt](readme.txt).

## Support und Lizenz

- Dokumentation: <https://psource.eimen.net/wiki/ps-security-suite-dokumentation/>
- Lizenz: [GPL-2.0-or-later](license.txt)

PS Security Suite wird von [PSOURCE](https://psource.eimen.net) bereitgestellt.
