# -*- coding: utf-8 -*-
"""Einheitliches Attributschema fuer alle Samen auf hanfjack.de."""

THC_CBD_STUFEN = ['0–1 %','1–5 %','5–10 %','10–15 %','15–20 %','20–25 %','25 % und mehr','Unbekannt']

def stufe(pct):
    """Zahl oder Bereich -> Stufe. None -> Unbekannt."""
    if pct is None: return 'Unbekannt'
    p=float(pct)
    if p<1: return '0–1 %'
    if p<5: return '1–5 %'
    if p<10: return '5–10 %'
    if p<15: return '10–15 %'
    if p<20: return '15–20 %'
    if p<25: return '20–25 %'
    return '25 % und mehr'

def stufe_max(pct):
    """Fuer Angaben der Form 'bis zu X %': X ist die Obergrenze."""
    if pct is None: return 'Unbekannt'
    return stufe(float(pct)-0.01)

def anteil(pct):
    """Genanteil auf 5er-Schritte runden, '45 %'. None -> Unbekannt."""
    if pct is None: return 'Unbekannt'
    return f'{int(round(float(pct)/5.0)*5)} %'

def typ(sativa):
    """Einordnung aus dem Sativa-Anteil."""
    if sativa is None: return None
    s=float(sativa)
    if s>=70: return 'Sativa-dominant'
    if s<=30: return 'Indica-dominant'
    if 45<=s<=55: return 'Hybrid (ausgewogen)'
    return 'Sativa-dominant' if s>55 else 'Indica-dominant'

VARIANTE   = ['Feminisiert','Autoflowering','Regulär','CBD','F1 Hybrid','Set']
KLIMA      = ['gemäßigt','mediterran','warm','heiß und trocken','feucht und tropisch','kühl und kurze Sommer']
ANBAU      = ['Indoor','Outdoor','Gewächshaus']
SCHWIERIG  = ['Anfänger','Fortgeschrittene','Profis']
ERNTEMONAT = ['August','September','Oktober','November','Ganzjährig (Auto)']

# Werte, die beim Vereinheitlichen ersetzt werden
ALT_NEU = {
  'pa_variante': {'Autoflower':'Autoflowering','Automatic':'Autoflowering','Regular':'Regulär',
                  'Klassisch':'Feminisiert'},
}

ATTR_ID = {'pa_aroma':7,'pa_thc-gehalt':4,'pa_cbd-gehalt':6,'pa_sativa':18,'pa_indica':17,
           'pa_ruderalis':19,'pa_terpene':38,'pa_variante':12,'pa_bluetezeit-tage':21,
           'pa_effekte':20,'pa_genetik':16,'pa_typ':15,'pa_klima':39,'pa_wuchshoehe':40,
           'pa_anbauumgebung':41,'pa_erntemonat':42,'pa_ertrag':43,'pa_schwierigkeitsgrad':44,'pa_geschmack':11}
