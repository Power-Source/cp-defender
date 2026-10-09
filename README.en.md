# PS Security Suite

[Deutsch](README.md) | **English**

[![Version](https://img.shields.io/badge/Version-1.0.8-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-up%20to%207.1.0-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.3-03768e?style=flat-square)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

PS Security Suite is a security plugin for ClassicPress and compatible WordPress installations. It brings file-integrity checks, hardening measures, IP lockouts, audit logging, security reports, and Multisite anti-spam protection together in one administration area.

> Security is an ongoing process. Enable only measures whose effects on your installation you have reviewed, and keep regular backups.

## Features

- **File scanning**: Detects changed, additional, and potentially suspicious files, as well as known vulnerabilities in plugins and themes.
- **Security tweaks**: Provides guided hardening measures for common attack surfaces.
- **IP lockouts**: Protects login forms and 404 endpoints from repeated access and brute-force attempts.
- **Audit logging**: Records relevant changes and security events for review.
- **Reports**: Creates regular summaries of security status and events.
- **Advanced tools**: Provides additional administration and security functions.
- **Two-factor authentication**: Supports authenticator apps and email codes for user accounts.
- **Multisite anti-spam**: Checks registrations and comments using rules, IP reputation, rate limits, disposable-email detection, and human verification.

## Requirements

The release metadata in [readme.txt](readme.txt) specifies:

- WordPress 5.0 or later, or ClassicPress 2.7.0 or later
- PHP 7.4 or later
- For Anti-Spam: a Multisite installation with network activation

The PHP environment should have sufficient file and network permissions for scans, updates, and hardening actions.

## Installation

1. Copy the `cp-defender` directory to `wp-content/plugins/`.
2. Activate **PS Security Suite** in the Plugins screen.
3. On Multisite, network-activate the plugin.
4. Open **PS Security** in the administration menu.
5. Review each module's settings before using it in production.

## Quick Start

1. Open **PS Security > File Scanning** and run an initial scan.
2. Review every reported issue and use a current backup before changing affected files.
3. Enable the hardening measures appropriate for your site under **Security Tweaks**.
4. Configure thresholds and notifications under **IP Lockouts**.
5. On Multisite networks, enable **Anti-Spam** and add custom rules where needed.

## Network Anti-Spam

The Anti-Spam module is designed for Multisite networks. It includes:

- Rules for domains, usernames, email addresses, and site titles
- Live rule tests and match statistics
- IP reputation and configurable rate limits
- Disposable-email detection
- Network-wide comment protection with local exceptions for subsites
- Cloudflare Turnstile, reCAPTCHA (legacy), or security questions for human verification
- Moderation and statistics for suspicious registrations

Test regular expressions before activating them. Rules that are too broad can block legitimate registrations.

## Translations

The text domain is `cpsec`. Language files are stored in [languages](languages/):

- `cpsec.pot` is the template for new translations.
- `cpsec-en_US.po` and `cpsec-en_US.mo` provide the English interface.
- `cpsec-de_DE.po` and `cpsec-de_DE.mo` provide German translations for English source strings.

Edit PO files with a Gettext-compatible tool such as Poedit, then compile the corresponding MO file. After changing PHP strings, regenerate the POT file and merge it into the existing PO files.

## Project Structure

```text
app/           Plugin logic, modules, controllers, and views
assets/        Global stylesheets, scripts, and images
languages/     Gettext catalogs
shared-ui/     Shared administration interface
vendor/        Bundled runtime libraries
```

The plugin starts in [cp-defender.php](cp-defender.php). Individual security areas are located in `app/module/`.

## Development

- Use the `cpsec` text domain for visible PHP strings.
- Keep translation calls static, for example `__( 'Text', 'cpsec' )`, so Gettext tools can extract them.
- Check changed PHP files with `php -l <file>`.
- When changing a release version, update both the plugin header and [readme.txt](readme.txt).

## Support and License

- Documentation: <https://psource.eimen.net/wiki/ps-security-suite-dokumentation/>
- License: [GPL-2.0-or-later](license.txt)

PS Security Suite is provided by [PSOURCE](https://psource.eimen.net).
