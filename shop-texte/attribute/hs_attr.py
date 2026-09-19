# -*- coding: utf-8 -*-
"""Parser fuer den Haendlerkatalog house-of-seeds.de (Shopify /products.json).

Quelle je Produkt: <dl>-Block im body_html mit den Labels
  Geschmack | Wirkung | 3 Hauptterpene | Breeder & Kreuzung
Zielattribute: pa_aroma, pa_effekte, pa_terpene, pa_genetik
"""
import json, re, html, os

_T = json.load(open(os.path.join(os.path.dirname(__file__) or '.', 'shop_terms.json')))
# vorhandene Shop-Terme case-insensitiv wiederverwenden -> keine Dublettenterme anlegen
KANON = {slug: {n.lower(): n for n in namen} for slug, namen in _T.items()}

TERPEN = {
    'myrcene': 'Myrcen', 'β-myrcen': 'Myrcen', 'beta-myrcen': 'Myrcen',
    'βeta-myrcen': 'Myrcen', 'b-myrcen': 'Myrcen',
    'caryophyllene': 'Caryophyllen', 'β-caryophyllen': 'Caryophyllen',
    'β-caryophyllene': 'Caryophyllen', 'beta-caryophyllen': 'Caryophyllen',
    'trans-caryophyllen': 'Caryophyllen', 'b-caryophyllen': 'Caryophyllen',
    'pinene': 'Pinen', 'a-pinen': 'Pinen', 'α-pinen': 'Pinen',
    'β-pinen': 'Pinen', 'alpha-pinen': 'Pinen', 'beta-pinen': 'Pinen',
    'terpinolene': 'Terpinolen', 'ocimene': 'Ocimen', 'humulene': 'Humulen',
    'linalol': 'Linalool',
}
TERPEN_RAUS = {'sonstige', 'unbekannt', 'n/a', '-'}

AROMA = {
    'tropenfrucht': 'Tropische Früchte', 'zitrusfruechte': 'Zitrusfrüchte',
    'kekse': 'Keks', 'süßigkeiten': 'Süßigkeit', 'bonbon': 'Bonbons',
    'süßlich': 'Süß', 'beeren': 'Beere', 'holzig': 'Holzig',
    'tropisch': 'Tropisch', 'pinie': 'Pinie',
}
AROMA_RAUS = {'unbekannt', 'sonstige', 'n/a', '-'}

EFFEKT = {
    'euphorisierend': 'Euphorisch', 'entspannend': 'Entspannt',
    'körperlich-beruhigend': 'Körperlich entspannend',
    'körperlich- beruhigend': 'Körperlich entspannend',
    'muskelentspannend': 'Körperlich entspannend',
    'energetisierend': 'Energetisch', 'belebend': 'Energetisch',
    'erhebend': 'Stimmungshebend', 'ausgleichend': 'Ausgewogen',
    'aktivierend': 'Anregend', 'narkotisch': 'Sedierend',
    'fröhlich': 'Glücklich', 'erheiternd': 'Aufmunternd',
    'kreativfördernd': 'Kreativ', 'zerebral': 'Zerebral',
    'aufmerksamkeitssteigernd': 'Konzentration',
    'stresslindernd': 'stressreduzierend',
}
EFFEKT_RAUS = {'unbekannt', 'sonstige', 'n/a', '-'}


def _dl(body):
    """<dl>-Block -> dict {Label: Wert}"""
    d = {}
    for k, v in re.findall(r'<dt>(.*?)</dt>\s*<dd>(.*?)</dd>', body or '', re.S):
        key = html.unescape(re.sub(r'<[^>]+>', '', k)).strip()
        val = html.unescape(re.sub(r'<[^>]+>', ' ', v))
        d[key] = re.sub(r'\s+', ' ', val).strip()
    return d


def _kanon(wert, slug, tabelle, raus):
    w = wert.strip().strip('.').strip()
    if not w or w.lower() in raus:
        return None
    w = tabelle.get(w.lower(), w)
    return KANON.get(slug, {}).get(w.lower(), w)


def _liste(roh, slug, tabelle, raus):
    out = []
    for teil in re.split(r'\s*[,/]\s*|\s+und\s+', roh or ''):
        k = _kanon(teil, slug, tabelle, raus)
        if k and k not in out:
            out.append(k)
    return out


def genetik(roh):
    """'420 Fast Buds - (GSC x GG#4) x Gorilla Cookies Auto'
       -> ['GSC','GG#4','Gorilla Cookies Auto']  (alle Einzelstrains)"""
    if not roh:
        return []
    s = roh.strip()
    hatte_breeder = ' - ' in s
    if hatte_breeder:                   # Breeder-Praefix abschneiden
        s = s.split(' - ', 1)[1].strip()
    s = s.replace('(', ' ').replace(')', ' ')   # Klammern aufloesen
    teile = [t.strip(' ,.') for t in re.split(r'\s+[\u00d7xX]\s+', s)]
    teile = [t for t in teile if t]
    if len(teile) < 2 and not hatte_breeder:
        return []                       # nur ein Breedername ohne Kreuzung
    out = []
    for t in teile:
        t = re.sub(r'\s+', ' ', t)
        if 2 <= len(t) <= 60 and t.lower() not in ('unbekannt', 'unknown', 'n/a'):
            if t not in out:
                out.append(t)
    return out


def build(p):
    """Shopify-Produkt -> {attr_slug: [werte]}"""
    d = _dl(p.get('body_html'))
    neu = {}
    a = _liste(d.get('Geschmack'), 'pa_aroma', AROMA, AROMA_RAUS)
    if a: neu['pa_aroma'] = a
    e = _liste(d.get('Wirkung'), 'pa_effekte', EFFEKT, EFFEKT_RAUS)
    if e: neu['pa_effekte'] = e
    t = _liste(d.get('3 Hauptterpene'), 'pa_terpene', TERPEN, TERPEN_RAUS)
    if t: neu['pa_terpene'] = t
    g = genetik(d.get('Breeder & Kreuzung'))
    if g: neu['pa_genetik'] = g
    return neu
