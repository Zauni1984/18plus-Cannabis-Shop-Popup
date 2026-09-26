# -*- coding: utf-8 -*-
"""Attribute aus dem 'Auf einen Blick'-Block der Produktbeschreibung.
Quelle sind die im Shop dokumentierten Herstellerangaben."""
import re,html,sys
sys.path.insert(0,'.')
import attr_schema as A

def plain(s):
    t=html.unescape(re.sub(r'<[^>]+>','|',s or ''))
    return re.sub(r'(\|\s*)+','|',t)

def feld(t,lab):
    m=re.search(r'\|'+re.escape(lab)+r':\|([^|]{1,120})\|',t)
    return m.group(1).strip() if m else None

def zahlen(s):
    return [float(x.replace(',','.')) for x in re.findall(r'\d+(?:[.,]\d+)?',s or '')]

def bluete(s):
    if not s or not re.search(r'\d',s): return None
    z=[int(x) for x in re.findall(r'\d+',s)]
    if not z: return None
    if re.search(r'\btag',s,re.I):
        pass
    elif re.search(r'woche',s,re.I):
        z=[x*7 for x in z]
    else:
        return None
    z=[x for x in z if 28<=x<=140]          # Plausibilitaet: 4 bis 20 Wochen
    if not z: return None
    return f'{z[0]}-{z[1]}' if len(z)>1 else str(z[0])

def variante(s):
    if not s: return None
    s=s.lower()
    if 'autoflower' in s or 'automatic' in s: return 'Autoflowering'
    if 'regul' in s: return 'Regulär'
    if 'femin' in s: return 'Feminisiert'
    return None

def typ_und_anteile(s):
    """-> (typ, sativa, indica)"""
    if not s: return None,None,None
    m=re.search(r'(\d{1,3})\s*%\s*Indica\s*/\s*(\d{1,3})\s*%\s*Sativa',s,re.I)
    if m: return A.typ(float(m.group(2))),float(m.group(2)),float(m.group(1))
    m=re.search(r'(\d{1,3})\s*%\s*Sativa\s*/\s*(\d{1,3})\s*%\s*Indica',s,re.I)
    if m: return A.typ(float(m.group(1))),float(m.group(1)),float(m.group(2))
    l=s.lower()
    if 'indica-dominant' in l or 'indicalastig' in l or 'indicabetont' in l: return 'Indica-dominant',None,None
    if 'sativa-dominant' in l or 'sativalastig' in l or 'sativabetont' in l: return 'Sativa-dominant',None,None
    if re.fullmatch(r'\s*indica\s*',l): return 'Indica-dominant',None,None
    if re.fullmatch(r'\s*sativa\s*',l): return 'Sativa-dominant',None,None
    if 'hybrid' in l: return 'Hybrid (ausgewogen)',None,None
    return None,None,None

def prozent_stufe(s):
    """'16–24 %' -> Mittelwert-Stufe. Nur wenn Zahlen da sind."""
    if not s or not re.search(r'\d',s): return None
    if re.search(r'nennt kein|keine angabe|nicht verf|unbekannt',s,re.I): return None
    z=zahlen(s)
    if not z: return None
    if re.search(r'\bbis zu\b|\bmax\b|\bunter\b|<',s,re.I) and len(z)==1:
        return A.stufe_max(z[0])
    return A.stufe(sum(z[:2])/2 if len(z)>1 else z[0])

def ertrag(s,zusatz='Indoor'):
    if not s or not re.search(r'\d',s): return None
    if not re.search(r'g\s*/\s*m|gramm|g\b',s,re.I): return None
    z=re.findall(r'\d+',s)
    if not z: return None
    r=f'{z[0]}-{z[1]}' if len(z)>1 else z[0]
    if re.search(r'\bbis zu\b',s,re.I) and len(z)==1: r='bis '+r
    einheit='g/Pflanze' if re.search(r'pflanze|plant',s,re.I) else 'g/m²'
    return f'{r} {einheit} ({zusatz})'

def hoehe(s):
    if not s or not re.search(r'\d',s): return None
    if 'cm' not in s.lower(): return None
    out=[]
    m=re.search(r'indoor\D{0,12}(\d+)\D{1,8}(\d+)?\s*cm',s,re.I)
    if m:
        z=[x for x in m.groups() if x]
        out.append(f"{'-'.join(z)} cm (Indoor)")
    m=re.search(r'outdoor\D{0,12}(\d+)\D{1,8}(\d+)?\s*cm',s,re.I)
    if m:
        z=[x for x in m.groups() if x]
        out.append(f"{'-'.join(z)} cm (Outdoor)")
    if not out:
        z=re.findall(r'\d+',s)
        if z: out.append(f"{z[0]}-{z[1]} cm" if len(z)>1 else f"{z[0]} cm")
    return out or None

def kreuzung(s):
    if not s: return None
    if re.search(r'nennt kein|keine angabe|nicht verf|unbekannt',s,re.I): return None
    s=re.sub(r'\([^)]*\)','',s)
    teile=[y.strip().rstrip('.,;') for y in re.split(r'\s+[×xX]\s+',s) if y.strip()]
    return teile if teile and len(' '.join(teile))>2 else None

def build(desc):
    t=plain(desc)
    out={}
    b=bluete(feld(t,'Blütezeit') or feld(t,'Blüte'))
    if b: out['pa_bluetezeit-tage']=[b]
    v=variante(feld(t,'Samentyp'))
    if v: out['pa_variante']=[v]
    ty,sa,ind=typ_und_anteile(feld(t,'Genanteile'))
    if ty: out['pa_typ']=[ty]
    if sa is not None: out['pa_sativa']=[A.anteil(sa)]
    if ind is not None: out['pa_indica']=[A.anteil(ind)]
    th=prozent_stufe(feld(t,'THC'))
    if th: out['pa_thc-gehalt']=[th]
    cb=prozent_stufe(feld(t,'CBD'))
    if cb: out['pa_cbd-gehalt']=[cb]
    k=kreuzung(feld(t,'Kreuzung') or feld(t,'Genetik'))
    if k: out['pa_genetik']=k
    e=ertrag(feld(t,'Ertrag'))
    if e: out['pa_ertrag']=[e]
    h=hoehe(feld(t,'Wuchshöhe'))
    if h: out['pa_wuchshoehe']=h
    return out
