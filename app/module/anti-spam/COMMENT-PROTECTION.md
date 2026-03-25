# Comment Protection System

## Überblick

Das neue **Comment Protection System** der Anti-Spam Modul schützt netzwerkweit alle Sub-Sites vor Spam-Kommentaren durch zentral verwaltete Blacklisten.

## Features

### ✅ Automatische Blacklist-Generierung
- **Aus verdächtigen Blogs**: Admin-Email, Signup-IP, Blog-Domain werden automatisch erfasst
- **Aus verdächtigen Signups**: Emails mit hohem Certainty-Wert
- **Aus Disposable-Emails**: Automatische Erfassung von Wegwerf-Email-Adressen

### 🔍 Multi-Level-Filterung
1. **E-Mail-Filter**: Blockiert bekannte Spammer-Emails
2. **IP-Filter**: Nutzt IP-Reputation + Custom Blacklist
3. **Domain-Filter**: Blockiert URLs von Spammer-Domains
4. **Disposable Email**: Verhindert Registrierung mit Wegwerf-Adressen
5. **Content-Pattern**: Optional benutzerdefinierte SPAM-Muster

### 🌐 Netzwerkweit synchronisiert
- Zentrale Verwaltung im Network-Admin
- Automatische Anwendung auf alle Sub-Sites
- Keine Duplikate (Deduplizierung)
- Performance-optimiert mit Caching

## Datenbankstruktur

### Neue Tabelle: `defender_antispam_comment_blacklist`

```sql
CREATE TABLE wp_defender_antispam_comment_blacklist (
  id bigint(20) auto_increment,
  type varchar(20),          -- 'email', 'ip', 'domain'
  value varchar(255),        -- E-Mail, IP, Domain
  reason text,              -- Grund (z.B. "Auto-blocked from spam blog")
  certainty int(3),         -- Vertrauenswert (0-100)
  status varchar(20),       -- 'active', 'inactive'
  created_at datetime,
  updated_at datetime,
  PRIMARY KEY (id),
  UNIQUE KEY type_value (type, value),
  KEY status (status),
  KEY type (type),
  KEY certainty (certainty)
);
```

## Implementierte Hooks

### `pre_comment_approved`
Prüft jeden Kommentar VOR dem Speichern:
```php
add_filter( 'pre_comment_approved', array( 'Comment_Protection', 'check_comment' ), 10, 2 );
```

Blockiert Kommentare zurück als `'spam'` wenn Blacklist Match.

### `cp_defender_blog_marked_spam`
Wird aufgerufen, wenn ein Blog als SPAM markiert wird:
```php
do_action( 'cp_defender_blog_marked_spam', $blog_data );
```

Fügt automatisch Admin-Email, Signup-IP und Blog-Domain zur Blacklist hinzu.

### `cp_defender_new_signup_logged`
Wird aufgerufen, wenn verdächtige Signup geloggt wird:
```php
do_action( 'cp_defender_new_signup_logged', $signup_data );
```

Fügt optional verdächtige Emails zur Kommentar-Blacklist hinzu.

## Einstellungen (in Settings Model)

```php
// Comment Protection aktivieren
'comment_protection_enabled' => true,

// Filter aktivieren
'comment_block_email' => true,
'comment_block_ip' => true,
'comment_block_domain' => true,
'comment_block_disposable_email' => true,
'comment_check_content' => false,

// Custom SPAM-Patterns
'comment_block_patterns' => [],

// Auto-Blacklist von Spam-Blogs
'auto_blacklist_spam_blogs' => true,

// Auto-Blacklist von verdächtigen Signups
'auto_blacklist_suspicious_signups' => false,
'suspicious_certainty_threshold' => 70,
```

## Verwendung im Code

### Prüfung ob E-Mail blacklisted ist
```php
use CP_Defender\Module\Anti_Spam\Model\Comment_Blacklist;

if ( Comment_Blacklist::is_email_blacklisted( 'spammer@example.com' ) ) {
    wp_die( 'Diese E-Mail-Adresse ist blockiert.' );
}
```

### E-Mail zur Blacklist hinzufügen
```php
Comment_Blacklist::add( 'email', 'spammer@example.com', 'Manual block', 100 );
```

### Mehrere Einträge hinzufügen (Batch)
```php
Comment_Blacklist::add_batch( array(
    array( 'type' => 'email', 'value' => 'spam1@example.com', 'reason' => 'Spam blog', 'certainty' => 90 ),
    array( 'type' => 'ip', 'value' => '192.168.1.100', 'reason' => 'Spam blog', 'certainty' => 80 ),
    array( 'type' => 'domain', 'value' => 'spammer-domain.com', 'reason' => 'Spam blog', 'certainty' => 85 ),
) );
```

### IP blockiert prüfen
```php
if ( Comment_Blacklist::is_ip_blacklisted( $_SERVER['REMOTE_ADDR'] ) ) {
    wp_die( 'Deine IP ist blockiert.' );
}
```

### Alle Blacklist-Einträge abrufen
```php
$blacklist = Comment_Blacklist::get_all( 'email' ); // nur Emails
$all = Comment_Blacklist::get_all();                // alle
$count = Comment_Blacklist::count( 'ip' );          // Anzahl IPs
```

## Workflow

```
┌─────────────────────────────────────────────┐
│ Sub-Site: Kommentar eingereicht             │
└──────────────┬──────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────┐
│ pre_comment_approved Hook                   │
│ → Comment_Protection::check_comment()       │
└──────────────┬──────────────────────────────┘
               │
        ┌──────┴──────┐
        │ Prüfungen:  │
        ├─────────────┤
        │ • E-Mail    │
        │ • IP        │
        │ • Domain    │
        │ • Disposable│
        │ • Content   │
        └──────┬──────┘
               │
       ┌───────┴────────┐
       │                │
    Match?            Kein Match
    (Spam)              (OK)
       │                │
       ▼                ▼
    'spam'         approve/hold
```

## Netzwerkweite Synchronisation

Alle Daten sind in der **WordPress Multisite Base Prefix Tabelle** gespeichert:
- `wp_defender_antispam_comment_blacklist` (bei `wp_` prefix)

Dies bedeutet:
- **Zentrale Verwaltung**: Ein Ort für alle Daten
- **Netzwerkweit wirksam**: Auf allen Sub-Sites
- **Performance**: Caching pro Blog für schnelle Abfragen
- **Skalierbar**: Massive Listenerstellung möglich

## Performance-Tipps

1. **Enable Caching**: Nutze WP Object Cache für `Comment_Blacklist`
2. **Batch Imports**: Verwende `add_batch()` für große Mengen
3. **Cleanup**: Alte Einträge regelmäßig mit `clear()` bereinigen
4. **Indexing**: Die MySQL Indizes sind optimiert für die häufigsten Abfragen

## Zukünftige Erweiterungen

- [ ] Import/Export UI für Blacklisten
- [ ] Community Blacklist Sharing
- [ ] Machine Learning zur Auto-Classification
- [ ] Whitelist-Support
- [ ] Geo-Blocking Integration
