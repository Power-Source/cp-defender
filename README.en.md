# PS Security Suite

[Deutsch](README.md) | **English**

[![Version](https://img.shields.io/badge/Version-1.1.1-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.5.12%2B%20recommended-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-up%20to%207.1.4-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

PS Security Suite helps you improve the security of your ClassicPress or WordPress website step by step. It shows open security risks, explains what they do in plain language, and lets you decide which measures suit your website.

> **Important:** Security settings can affect themes, plugins, and external services. Enable measures one at a time, keep a current backup, and test the important parts of your website afterwards.

## What PS Security helps you do

### Protect logins and user accounts

PS Security detects repeated failed login attempts and can lock out suspicious IP addresses. It also supports two-factor authentication with an authenticator app or an email code.

### Check files and unexpected changes

File scanning helps you find unexpected changes, missing core files, and suspicious content. Always review a finding before deleting or restoring files.

### Enable useful security tweaks

Under **PS Security → Security Tweaks**, you will find measures for common attack surfaces. Each rule explains what it protects and whether it needs special attention.

Examples include:

- preventing PHP execution in directories where WordPress does not normally need it
- optional XML-RPC protection against automated attacks and pingbacks
- protecting important settings and files

### Manage access and IP addresses

You can see why an IP address was locked out and choose to block, release, or whitelist it. This helps you distinguish genuine visitors from automated attacks.

### Understand what happened

Logs and security reports help you keep track of important events on your website without having to inspect every technical detail.

## Get started in five minutes

1. Install and activate the plugin.
2. Open **PS Security → Security Tweaks**.
3. Read the open notices and start with one measure that suits your website.
4. After each change, test logins, contact forms, your shop, and other important functions.
5. Then open **File Scanning** and run your first scan.

This keeps you in control and prevents you from making several changes at once without knowing which one caused a problem.

## Single site or Multisite network?

PS Security works with both single websites and Multisite networks.

In a network, network administrators can define central defaults. Where appropriate, they can also allow administrators of individual websites to manage their own settings.

The Anti-Spam module is only intended for Multisite networks. It helps prevent unwanted registrations and spam sites, for example through rules for suspicious domains, email addresses, or usernames, as well as limits on repeated registrations.

## Notes about specific features

### XML-RPC

Some apps, external services, and older plugins need XML-RPC. Do not immediately enable a complete block. PS Security provides graduated options: you can block only functions that are especially prone to abuse, or disable XML-RPC completely when your website does not need it.

### NGINX

With Apache or LiteSpeed, the plugin can manage certain protection rules itself. NGINX configuration is outside WordPress, however, so it cannot be changed automatically.

PS Security shows the required configuration for you to copy. With a hosting package, send it to support. On a self-managed server, add it to the appropriate NGINX `server` block, test it with `sudo nginx -t`, and reload NGINX only afterwards.

### PHP version

The plugin runs on PHP 8.0 or later. For secure operation, PS Security recommends PHP **8.5.12 or newer**. Older PHP versions are shown as a risk in Security Tweaks.

## Installation

1. Install PS Security through **Plugins → Add New**, or upload the `cp-defender` directory to `wp-content/plugins/`.
2. Activate **PS Security Suite** on the Plugins screen.
3. In a Multisite network, activate the plugin through **Network Admin**.
4. Open **PS Security** in the administration menu and start with Security Tweaks.

## Frequently asked questions

### Can I undo a security measure?

Yes. Open the relevant rule and use its reset option. The rule will be shown as open again, and you can enable it later if needed.

### Can I lock myself out?

This is possible with strict login rules. Configure two-factor authentication carefully, and whitelist an IP address only if it is permanently and uniquely assigned to you.

### What should I do if something stops working after a change?

First reset the last measure you enabled and test the website again. Always keep a backup before making larger changes. For NGINX rules, contact your hosting provider if you do not manage the server yourself.

### Does Anti-Spam work on a single website?

No. The Anti-Spam module is designed for Multisite networks and is not available on single websites.

## Help and license

- Documentation: <https://psource.eimen.net/wiki/ps-security-suite-dokumentation/>
- Optional donation: <https://psource.eimen.net/spendenaktionen/psource-unterstuetzen/>
- License: [GPL-2.0-or-later](license.txt)

PS Security Suite is provided by [PSOURCE](https://psource.eimen.net).
