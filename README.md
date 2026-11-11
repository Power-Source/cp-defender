# PS Security Suite

**Deutsch** | [English](README.en.md)

[![Version](https://img.shields.io/badge/Version-1.1.1-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.5.12%2B%20empfohlen-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.1.4-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

PS Security Suite hilft dir, deine ClassicPress- oder WordPress-Website Schritt für Schritt besser zu schützen. Das Plugin zeigt dir offene Sicherheitsrisiken, erklärt ihre Wirkung verständlich und lässt dich selbst entscheiden, welche Maßnahmen zu deiner Website passen.

> **Wichtig:** Sicherheitseinstellungen können Auswirkungen auf Themes, Plugins oder externe Dienste haben. Aktiviere Maßnahmen einzeln, halte ein aktuelles Backup bereit und prüfe danach die wichtigsten Funktionen deiner Website.

## Das kannst du mit PS Security machen

### Anmeldungen besser schützen

PS Security erkennt wiederholte fehlgeschlagene Anmeldungen und kann auffällige IP-Adressen sperren. Zusätzlich unterstützt das Plugin die Zwei-Faktor-Anmeldung mit einer Authenticator-App oder E-Mail-Code.

### Dateien und Änderungen prüfen

Ein Dateiscan hilft dir, unerwartete Änderungen, fehlende Core-Dateien und verdächtige Inhalte zu erkennen. Prüfe einen Fund immer erst, bevor du Dateien löschst oder wiederherstellst.

### Sinnvolle Sicherheits-Tweaks aktivieren

Unter **PS Security → Sicherheits-Tweaks** findest du Maßnahmen für häufige Angriffsflächen. Das Plugin beschreibt zu jeder Regel, was sie schützt und ob sie besondere Aufmerksamkeit benötigt.

Beispiele:

- Schutz vor PHP-Ausführung in Verzeichnissen, in denen WordPress sie normalerweise nicht benötigt
- optionaler XML-RPC-Schutz gegen automatisierte Angriffe und Pingbacks
- Schutz wichtiger Einstellungen und Dateien

### Zugriffe und IP-Adressen verwalten

Du siehst, warum eine IP-Adresse gesperrt wurde, und kannst sie gezielt sperren, freigeben oder auf die Whitelist setzen. Das ist hilfreich, wenn du echte Besucher von automatisierten Angriffen unterscheiden musst.

### Nachvollziehen, was passiert ist

Protokolle und Sicherheitsberichte helfen dir, wichtige Ereignisse auf deiner Website im Blick zu behalten, ohne ständig jedes Detail kontrollieren zu müssen.

## In fünf Minuten starten

1. Installiere und aktiviere das Plugin.
2. Öffne **PS Security → Sicherheits-Tweaks**.
3. Lies die offenen Hinweise und beginne mit einer Maßnahme, die zu deiner Website passt.
4. Prüfe nach jeder Änderung Anmeldung, Kontaktformulare, Shop und andere wichtige Funktionen.
5. Öffne anschließend **Dateiscanning** und starte einen ersten Scan.

So behältst du die Kontrolle und vermeidest, mehrere Änderungen gleichzeitig vornehmen und mögliche Probleme nicht mehr zuordnen zu können.

## Einzelne Website oder Multisite-Netzwerk?

PS Security funktioniert sowohl für einzelne Websites als auch für Multisite-Netzwerke.

In einem Netzwerk können Netzwerkadministratoren zentrale Vorgaben festlegen. Bei passenden Funktionen lässt sich zusätzlich erlauben, dass Administratoren einzelner Websites eigene Einstellungen verwalten.

Das Anti-Spam-Modul ist nur für Multisite-Netzwerke vorgesehen. Es hilft bei der Abwehr unerwünschter Registrierungen und Spam-Websites, zum Beispiel durch Regeln für auffällige Domains, E-Mail-Adressen oder Benutzernamen sowie Begrenzungen für wiederholte Registrierungen.

## Hinweise zu besonderen Funktionen

### XML-RPC

Einige Apps, externe Dienste und ältere Plugins benötigen XML-RPC. Aktiviere deshalb nicht sofort die vollständige Sperre. PS Security bietet abgestufte Optionen: Du kannst nur besonders missbrauchsanfällige Funktionen sperren oder XML-RPC vollständig deaktivieren, wenn deine Website es nicht benötigt.

### NGINX

Bei Apache oder LiteSpeed kann das Plugin bestimmte Schutzregeln selbst verwalten. NGINX-Konfigurationen liegen dagegen außerhalb von WordPress und können nicht automatisch geändert werden.

PS Security zeigt dir die benötigte Konfiguration zum Kopieren an. Bei einem Hosting-Paket leitest du sie an den Support weiter. Bei einem selbstverwalteten Server fügst du sie in den passenden NGINX-`server`-Block ein, prüfst sie mit `sudo nginx -t` und lädst NGINX erst danach neu.

### PHP-Version

Das Plugin läuft ab PHP 8.0. Für einen sicheren Betrieb empfiehlt PS Security PHP **8.5.12 oder neuer**. Ältere PHP-Versionen werden in den Sicherheits-Tweaks als Risiko angezeigt.

## Installation

1. Installiere PS Security über **Plugins → Installieren** oder lade den Ordner `cp-defender` nach `wp-content/plugins/` hoch.
2. Aktiviere **PS Security Suite** in der Plugin-Verwaltung.
3. In einem Multisite-Netzwerk aktivierst du das Plugin über die **Netzwerkverwaltung**.
4. Öffne **PS Security** im Administrationsmenü und beginne mit den Sicherheits-Tweaks.

## Häufige Fragen

### Kann ich eine Maßnahme wieder zurücksetzen?

Ja. Öffne die betreffende Regel und nutze die Rücksetzfunktion. Die Regel wird danach wieder als offen angezeigt und kann später erneut aktiviert werden.

### Kann ich mich selbst aussperren?

Das ist bei sehr strengen Login-Regeln möglich. Richte die Zwei-Faktor-Anmeldung deshalb sorgfältig ein und setze eine IP-Adresse nur dann auf die Whitelist, wenn sie dauerhaft und eindeutig dir zugeordnet ist.

### Was mache ich, wenn nach einer Änderung etwas nicht mehr funktioniert?

Setze zuerst die zuletzt aktivierte Maßnahme zurück und prüfe die Website erneut. Halte vor größeren Änderungen immer ein Backup bereit. Bei NGINX-Regeln wende dich an deinen Hosting-Support, wenn du keinen eigenen Server verwaltest.

### Funktioniert Anti-Spam auf einer einzelnen Website?

Nein. Das Anti-Spam-Modul ist für Multisite-Netzwerke entwickelt und steht auf einzelnen Websites nicht zur Verfügung.

## Hilfe und Lizenz

- Dokumentation: <https://psource.eimen.net/wiki/ps-security-suite-dokumentation/>
- Lizenz: [GPL-2.0-or-later](license.txt)

PS Security Suite wird von [PSOURCE](https://psource.eimen.net) bereitgestellt.
