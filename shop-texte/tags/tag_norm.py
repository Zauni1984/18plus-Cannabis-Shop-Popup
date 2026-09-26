# -*- coding: utf-8 -*-
"""Vereinheitlicht die Produkt-Tags nach deutscher Rechtschreibung.

Regeln aus der Vorgabe:
- normale Buchstaben, Zahlen, Bindestriche - keine Sonderzeichen
- keine Kommas, Dezimaltrennzeichen ist der Punkt (4.2 statt 4,2)
- feste Benennungslogik: Substantive gross, Adjektive und Partikeln klein,
  Abkuerzungen gross, Markennamen in ihrer Eigenschreibweise
"""
import re, html, json, unicodedata

# klein, weil Adjektiv, Partizip oder Partikel
KLEIN = set("""
feminisiert feminisierte feminisierter feminisiertes autoflowering autoflower
photoperiodisch regulär reguläre regulärer reguläres hoher hohe hohes hohem hohen
geeignet fruchtig fruchtige fruchtiger fruchtiges milder milde mild sanfter sanfte sanft
violett violette violetter kompakt kompakte kompakter kompaktes schnell schnelle schneller
entspannend biologisch biologische biologischer frostig frostige nachfüllbar konisch
vegan schwarz weiß rot grün blau gelb lila silbern golden viel kreativ kreative kreatives
organisch organische organischer trichomreich tropisch tropische tropisches zylindrisch
süß süße euphorisch abbaubar natürlich natürliche natürlicher robust robuste langlebig
lichtdicht lichtdichte lichtdichtes reflektierend kompatibel integriert geruchsdicht
faltbar dimmbar regelbar leise stark starke mineralisch wasserlöslich pflegeleicht
groß große großer kleine klein schnellwachsend ertragreich harzig harzreich würzig erdig
zitronig blumig holzig herb mehr wenig fein grob dicht locker weich hart mittel
dominant dominante dominanter dominiert indicadominiert sativadominiert kaufen bestellen
online frisch neu alt beliebt bekannt selten häufig ideal perfekt optimal einfach
schwer leicht hoch niedrig lang kurz breit schmal rund eckig flach tief
und für in mit aus im am zum zur von der die das den dem ohne bis pro je als wie oder
""".split())

# immer gross geschrieben
ABK = {'THC','CBD','CBG','CBN','CBC','THCV','LED','UV','IR','XL','XXL','OG','GSC','NPK',
       'PK','EC','PH','RQS','USA','EU','US','DE','NL','ES','IP','PPF','PPE','PAR','HPS',
       'CMH','MH','AC','DC','BHO','CO2','RSO','MCT','DNA','F1','F2','F3','F4','F5','BX1',
       'S1','GG4','NL5','AK','TDS','SDS','ISBN','WEEE','GPSR','B2B','B2C','SEO','LST','SCROG'}

SPEZIAL = {'ph': 'pH', 'mm': 'mm', 'cm': 'cm', 'ml': 'ml', 'l': 'L',
           'g': 'g', 'kg': 'kg', 'w': 'W', 'm': 'm', 'nm': 'nm', 'lm': 'lm',
           'mg': 'mg', 'µm': 'µm', 'dB': 'dB', 'db': 'dB'}
# Einheiten nie umschreiben - µmol darf nicht zu Mmol werden
EINHEIT_RE = re.compile(r'^[\u00b5\u03bc]?(?:mol|m|g|l|w|v|a|hz|s|pa|bar)\b|'
                        r'^\d|[\u00b5\u03bc]mol|m[\u00b3\u00b2]|/[hsm]$', re.I)


def entities(s):
    """&amp; -> & ; &gt; -> >  (die Entities stehen so in der Datenbank)"""
    vorher = None
    while vorher != s:
        vorher, s = s, html.unescape(s)
    return s


def grundform(s):
    """fuer den Dublettenvergleich: ohne Sonderzeichen, klein, ohne Umlaute"""
    s = unicodedata.normalize('NFKD', entities(s).lower()).replace('ß', 'ss')
    s = ''.join(c for c in s if not unicodedata.combining(c))
    return re.sub(r'[^a-z0-9]+', '', s)


def wort(w, marken_ci):
    if EINHEIT_RE.search(w):
        return w
    if '-' in w and w.lower() not in marken_ci and len(w) > 3:
        # Bindestrich-Zusammensetzungen teilweise gross: grow-zelt -> Grow-Zelt
        teile = w.split('-')
        if all(t and not t.isdigit() for t in teile):
            return '-'.join(wort(t, marken_ci) for t in teile)
    if w.lower() in marken_ci:
        return marken_ci[w.lower()]
    if w.upper() in ABK:
        return w.upper()
    if w.lower() in SPEZIAL:
        return SPEZIAL[w.lower()]
    if w.lower() in KLEIN:
        return w.lower()
    if re.search(r'[A-ZÄÖÜ]', w[1:]):        # BioTabs, HEMPER, McDonald
        return w
    if re.fullmatch(r'[\d.\-/x×]+', w):
        return w
    return w[:1].upper() + w[1:]


def saeubern(name, marken_ci):
    s = entities(name)
    s = s.replace('’', "'").replace('´', "'").replace('`', "'")
    s = re.sub(r'(?<=\d),(?=\d)', '.', s)     # 4,2 -> 4.2
    s = s.replace(',', ' ')                   # sonst kein Komma im Tag
    s = re.sub(r'[×]', 'x', s)
    s = re.sub(r'#(?!\d)', ' ', s)            # Raute nur vor Ziffern behalten
    s = re.sub(r':(?!\d)', ' ', s)            # Doppelpunkt nur im Verhaeltnis 1:1
    s = re.sub(r'[^\wÄÖÜäöüß%°.\-+/&#:\' ]', ' ', s)
    s = re.sub(r'\s+', ' ', s).strip(' -.')
    if not s:
        return None
    teile = [wort(w, marken_ci) for w in s.split(' ') if w]
    return ' '.join(teile)
