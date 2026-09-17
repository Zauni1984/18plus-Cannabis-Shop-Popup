# -*- coding: utf-8 -*-
"""Welche Palacio-Bilder fehlen im Shop?

Drei Verfahren, zwei davon untauglich:

1. **Byte-Hash** - nutzlos. Der Shop speichert WebP, Palacio liefert PNG und
   JPEG; dieselbe Aufnahme hat nie denselben Hash. Ergebnis waere "alle 71
   Bilder neu".
2. **dHash** - ebenfalls untauglich. Die Shop-Bilder sind enger beschnitten
   als die Herstellerbilder. Schon dieser Versatz treibt den Abstand bei
   identischen Aufnahmen auf 35 von 256 Bit, also ueber jede brauchbare
   Schwelle.
3. **Weissrand abschneiden, dann vergleichen** - traegt. Alle Aufnahmen sind
   Freisteller auf Weiss. Nach dem Zuschnitt auf die Bounding Box der
   nicht-weissen Pixel, Skalierung auf 48x48 Graustufen und Autokontrast
   trennen die Werte sauber: gleiche Aufnahme 2,6 bis 7,5 RMSE, anderes Motiv
   76 bis 101. Die Schwelle liegt bei 30 und damit weit weg von beiden.
"""
import io, json, sys, urllib.request, concurrent.futures as cf
from PIL import Image, ImageOps

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hj

GRENZE = 30

def hol(u):
    r = urllib.request.Request(u, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(r, timeout=180) as f:
        return f.read()

def normal(roh, k=48):
    im = Image.open(io.BytesIO(roh))
    if im.mode in ('RGBA', 'LA', 'P'):
        hg = Image.new('RGB', im.size, 'white')
        im = im.convert('RGBA')
        hg.paste(im, mask=im.split()[-1])
        im = hg
    im = im.convert('L')
    box = im.point(lambda v: 255 if v < 240 else 0).getbbox()
    if box:
        im = im.crop(box)
    return list(ImageOps.autocontrast(im.resize((k, k))).getdata())

def rmse(a, b):
    return (sum((x - y) ** 2 for x, y in zip(a, b)) / len(a)) ** 0.5

def pruefe(eintrag):
    pid, kat = eintrag
    p = hj.ruf(f'products/{pid}?_fields=id,name,slug,images')
    shop = []
    for im in p['images']:
        try: shop.append(normal(hol(im['src'])))
        except Exception: pass
    fehlend = []
    for n, f in enumerate([x for x in (kat.get('files') or []) if x['type'] == 'image'], 1):
        try: pa = normal(hol(f['link']))
        except Exception as e:
            fehlend.append({'nr': n, 'link': f['link'], 'rmse': 'FEHLER ' + str(e)[:30]})
            continue
        d = min([rmse(pa, s) for s in shop], default=999)
        if d >= GRENZE:
            fehlend.append({'nr': n, 'link': f['link'], 'rmse': round(d, 1)})
    return pid, p['name'], p['slug'], len(p['images']), fehlend

if __name__ == '__main__':
    match = json.load(open(S + 'pal_match.json'))
    auftrag = [(int(i), v['kat']) for i, v in sorted(match.items(), key=lambda kv: int(kv[0]))]
    ergebnis = {}
    with cf.ThreadPoolExecutor(3) as ex:
        for pid, name, slug, ns, fehlend in ex.map(pruefe, auftrag):
            if fehlend:
                ergebnis[pid] = {'name': name, 'slug': slug, 'shop': ns, 'fehlend': fehlend}
                print(f'{pid} | Shop {ns} | fehlend {len(fehlend)} | {name[:46]} '
                      f'| RMSE {[f["rmse"] for f in fehlend]}')
    json.dump(ergebnis, open(S + 'pal_fehlende_bilder.json', 'w'), ensure_ascii=False)
    print()
    print('Produkte:', len(ergebnis), '| fehlende Bilder:',
          sum(len(v['fehlend']) for v in ergebnis.values()))
