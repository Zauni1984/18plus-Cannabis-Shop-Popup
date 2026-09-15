# -*- coding: utf-8 -*-
"""Wie desc_attr, aber mit den Label-Varianten der uebrigen Beschreibungen
('Elternlinien' statt 'Kreuzung', 'Blütephase' statt 'Blütezeit', ...)."""
import re, sys
sys.path.insert(0, '.')
import desc_attr as D
import attr_schema as A

SYNONYM = {
    'Blütezeit': ['Blütezeit', 'Blütephase', 'Blütedauer', 'Blüte'],
    'Samentyp': ['Samentyp', 'Samen', 'Saatgut', 'Geschlecht'],
    'Genanteile': ['Genanteile', 'Anteile', 'Sortentyp', 'Typ', 'Genetischer Aufbau'],
    'THC': ['THC', 'THC-Gehalt'],
    'CBD': ['CBD', 'CBD-Gehalt'],
    'Kreuzung': ['Kreuzung', 'Genetik', 'Elternlinien', 'Eltern', 'Abstammung'],
    'Ertrag': ['Ertrag', 'Erträge', 'Ernte', 'Ertrag (Indoor)'],
    'Wuchshöhe': ['Wuchshöhe', 'Höhe', 'Wuchs', 'Endhöhe'],
}


def feld(t, gruppe):
    for lab in SYNONYM[gruppe]:
        v = D.feld(t, lab)
        if v:
            return v
    return None


def anteile(s):
    """ergaenzt 'Sativa 35 % / Indica 65 %' (Name vor der Zahl)"""
    ty, sa, ind = D.typ_und_anteile(s)
    if sa is not None or ty:
        return ty, sa, ind
    if not s:
        return None, None, None
    m1 = re.search(r'Sativa\D{0,3}(\d{1,3})\s*%', s, re.I)
    m2 = re.search(r'Indica\D{0,3}(\d{1,3})\s*%', s, re.I)
    if m1 and m2:
        sa, ind = float(m1.group(1)), float(m2.group(1))
        if 95 <= sa + ind <= 105:
            return A.typ(sa), sa, ind
    return None, None, None


def build(desc):
    t = D.plain(desc)
    out = {}
    b = D.bluete(feld(t, 'Blütezeit'))
    if b:
        out['pa_bluetezeit-tage'] = [b]
    v = D.variante(feld(t, 'Samentyp'))
    if v:
        out['pa_variante'] = [v]
    ty, sa, ind = anteile(feld(t, 'Genanteile'))
    if ty:
        out['pa_typ'] = [ty]
    if sa is not None:
        out['pa_sativa'] = [A.anteil(sa)]
    if ind is not None:
        out['pa_indica'] = [A.anteil(ind)]
    th = D.prozent_stufe(feld(t, 'THC'))
    if th:
        out['pa_thc-gehalt'] = [th]
    cb = D.prozent_stufe(feld(t, 'CBD'))
    if cb:
        out['pa_cbd-gehalt'] = [cb]
    k = D.kreuzung(feld(t, 'Kreuzung'))
    # Sortennamen, kein Fliesstext: hoechstens fuenf Woerter, keine Satzzeichen
    if k and all(len(x) <= 40 and len(x.split()) <= 5 and not re.search(r'[.;]', x) for x in k):
        out['pa_genetik'] = k
    e = D.ertrag(feld(t, 'Ertrag'))
    if e:
        out['pa_ertrag'] = [e]
    h = D.hoehe(feld(t, 'Wuchshöhe'))
    if h:
        out['pa_wuchshoehe'] = h
    return out


if __name__ == '__main__':
    import json
    DESC = {str(p['id']): p for p in json.load(open('seed_desc.json'))}
    S = {str(p['id']): p for p in json.load(open('seeds.json'))}
    offen = [str(x) for x in json.load(open('offen_ids.json'))]
    done = {l.strip() for l in open('DESC_DONE.txt')}
    hs = json.load(open('hs_map_alle.json'))
    rest = [x for x in offen if x not in done and x not in hs]
    M, n = {}, 0
    for x in rest:
        b = build(DESC.get(x, {}).get('description'))
        if b:
            n += 1
            M[x] = {'name': S[x]['name'], 'attr': b}
            if n <= 10:
                print(S[x]['name'][:44], '->', b)
    json.dump(M, open('desc2_map.json', 'w'), ensure_ascii=False, indent=1)
    print(f'{n} von {len(rest)} Restprodukten liefern jetzt Daten')
