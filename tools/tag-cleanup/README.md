# Produkt-Tags löschen

`delete-product-tags.php` löscht Produkt-Tags (Taxonomie `product_tag`) eines
WooCommerce-Shops. Gedacht für **hanfjack.com** und **moinkiffers.de**, um dort einmal
aufzuräumen und neu aufzubauen.

## Vor dem ersten Lauf

Oben im Skript stehen drei Sicherungen. Sieh sie dir an, bevor du startest:

```php
const SCHUTZ_SLUGS   = ['beilngries', 'hanfjack'];
const GESPERRTE_HOSTS = ['hanfjack.de', 'www.hanfjack.de'];
const TOKEN = '';
```

- **`SCHUTZ_SLUGS`** wird nie gelöscht, auch nicht mit `--confirm`. `beilngries` steht dort,
  weil der Tag auf hanfjack.de den eigenen Lagerbestand markiert.
- **`GESPERRTE_HOSTS`** bricht den Lauf sofort ab. hanfjack.de steht drin, damit dort nicht
  versehentlich die gepflegte Tag-Struktur gelöscht wird.
- **`TOKEN`** ist leer, also ist der Browser-Aufruf gesperrt. Nur setzen, wenn du kein
  WP-CLI hast — und die Datei danach vom Server nehmen.

## Aufruf

**Mit WP-CLI** (am saubersten, keine Datei muss auf den Webserver):

```bash
wp eval-file delete-product-tags.php                              # Trockenlauf
wp eval-file delete-product-tags.php -- --confirm                 # löscht wirklich
wp eval-file delete-product-tags.php -- --only-empty --confirm    # nur leere Tags
wp eval-file delete-product-tags.php -- --min-count=1 --confirm   # nur Tags mit höchstens 1 Produkt
wp eval-file delete-product-tags.php -- --exclude=neu,wichtig --confirm
```

**Ohne WP-CLI**, Datei ins WordPress-Verzeichnis legen und per SSH aufrufen:

```bash
php delete-product-tags.php --confirm
```

**Über den Browser** — nur wenn es nicht anders geht: `TOKEN` im Skript setzen, dann
`https://deinshop.de/delete-product-tags.php?token=DEINTOKEN&confirm=1` aufrufen und die
Datei anschließend löschen.

## Optionen

| Option | Wirkung |
| --- | --- |
| *(keine)* | **Trockenlauf** — zeigt nur an, was gelöscht würde |
| `--confirm` | löscht wirklich |
| `--only-empty` | nur Tags ohne Produkte |
| `--min-count=N` | nur Tags mit höchstens N Produkten |
| `--exclude=a,b` | diese Slugs zusätzlich schützen |
| `--batch=N` | Fortschrittsausgabe alle N Tags, Voreinstellung 200 |
| `--no-backup` | Sicherung überspringen (nicht empfohlen) |

## Sicherung

Vor jedem Lauf — auch vor dem Trockenlauf — schreibt das Skript die betroffenen Tags als
JSON und CSV ins Upload-Verzeichnis:

```
wp-content/uploads/product-tags-backup-20260916-154529.json
wp-content/uploads/product-tags-backup-20260916-154529.csv
```

Darin stehen ID, Name, Slug, Beschreibung und Produktzahl. Damit lassen sich die Tags wieder
anlegen — **die Zuordnung zu den Produkten ist nach dem Löschen allerdings weg.** Wenn du die
brauchst, exportiere sie vorher gesondert.

Die CSV hat ein BOM und Semikolon als Trenner, damit Excel die Umlaute richtig anzeigt.

## Nach dem Lauf

Das Skript setzt die Taxonomie-Zähler neu und leert die WooCommerce-Transients. Falls im
Backend trotzdem alte Zahlen stehen, einmal den Objekt-Cache leeren.
