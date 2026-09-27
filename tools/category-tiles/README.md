# Hanfjack Kategoriebilder – Generator (Variante B)

Erzeugt einheitliche 800×800 Produktkategorie-Kacheln im Hanfjack-Look
(Emblem, Hauptfarbe #06402B, Gold-Akzent, kategoriespezifische Icons).

## Nutzung
```
python3 tiles.py all ./out      # rendert HTML je Kategorie nach ./out (+ index.json)
python3 tiles.py sheet ./out    # Icon-Übersichtsblatt
```
Anschließend HTML mit headless Chromium in 800×800 als PNG rendern, z. B.:
```
chromium --headless --window-size=800,800 --screenshot=out.png file://out/<datei>.html
```

- Kategorie→Icon-Mapping und Icon-Bibliothek (36 Symbole) stehen in `tiles.py`.
- Die gerenderten Kacheln liegen unter `../../category-images/` (`<termID>_<slug>.png`).
- Ausgerollt als WooCommerce-Kategoriebild (Term-Meta `thumbnail_id`) auf hanfjack.de und hanfjack.com.

## Rendern (verifiziert 09/2026)
Chromium skaliert das SVG, wenn das Fenster genau 800×800 groß ist – daher mit
900×900 rendern und anschließend auf 800×800 zuschneiden:
```
chromium --headless --no-sandbox --disable-gpu --hide-scrollbars \
  --force-device-scale-factor=1 --window-size=900,900 \
  --screenshot=raw.png file://out/<datei>.html
python3 -c "from PIL import Image; Image.open('raw.png').convert('RGB').crop((0,0,800,800)).save('out.png')"
```
So gerendert sind die bestehenden Kacheln pixelgenau reproduzierbar (0 abweichende Pixel).
