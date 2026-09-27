# -*- coding: utf-8 -*-
"""Minimaler xlsx-Leser - openpyxl ist in dieser Umgebung nicht installiert.

Eine xlsx ist ein Zip mit XML: sharedStrings.xml haelt alle Texte, die
Blatt-XML verweist per Index darauf. Zahlen stehen direkt in der Zelle.
"""
import zipfile, re
from xml.etree import ElementTree as ET

NS = '{http://schemas.openxmlformats.org/spreadsheetml/2006/main}'
NSR = '{http://schemas.openxmlformats.org/officeDocument/2006/relationships}'

def _spalte(ref):
    """A1 -> 0, B1 -> 1, AA1 -> 26"""
    b = re.match(r'([A-Z]+)', ref or '')
    if not b: return 0
    n = 0
    for z in b.group(1): n = n*26 + (ord(z) - 64)
    return n - 1

def blaetter(pfad):
    with zipfile.ZipFile(pfad) as z:
        wb = ET.fromstring(z.read('xl/workbook.xml'))
        rel = {r.get('Id'): r.get('Target') for r in
               ET.fromstring(z.read('xl/_rels/workbook.xml.rels'))}
        raus = []
        for s in wb.iter(NS + 'sheet'):
            ziel = rel.get(s.get(NSR + 'id'), '')
            raus.append((s.get('name'), 'xl/' + ziel.lstrip('/')))
        return raus

def lies(pfad, blatt=None):
    """Gibt Zeilen als Listen von Strings zurueck."""
    with zipfile.ZipFile(pfad) as z:
        texte = []
        if 'xl/sharedStrings.xml' in z.namelist():
            for si in ET.fromstring(z.read('xl/sharedStrings.xml')).iter(NS + 'si'):
                texte.append(''.join(t.text or '' for t in si.iter(NS + 't')))
        ziel = blatt or blaetter(pfad)[0][1]
        wurzel = ET.fromstring(z.read(ziel))
        zeilen = []
        for zl in wurzel.iter(NS + 'row'):
            werte = {}
            for c in zl.iter(NS + 'c'):
                v = c.find(NS + 'v')
                if c.get('t') == 'inlineStr':
                    w = ''.join(t.text or '' for t in c.iter(NS + 't'))
                elif v is None or v.text is None:
                    w = ''
                elif c.get('t') == 's':
                    i = int(v.text)
                    w = texte[i] if 0 <= i < len(texte) else ''
                else:
                    w = v.text
                werte[_spalte(c.get('r'))] = w
            if werte:
                breit = max(werte) + 1
                zeilen.append([werte.get(i, '') for i in range(breit)])
        return zeilen
