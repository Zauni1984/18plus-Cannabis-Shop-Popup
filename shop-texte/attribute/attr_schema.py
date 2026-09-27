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
           'pa_anbauumgebung':41,'pa_erntemonat':42,'pa_ertrag':43,'pa_schwierigkeitsgrad':44,'pa_geschmack':11,
           'pa_inhalt':2,'pa_verpackung':5,'pa_einheit':8,'pa_farbe':3,'pa_groesse':25,
           'pa_npk':45,'pa_naehrstoffe':46,'pa_duengertyp':47,'pa_duengerart':48,
           'pa_wirkdauer':49,'pa_form':50,'pa_loeslichkeit':51,'pa_anwendungsphase':52,
           'pa_substrat':53,'pa_anwendungsart':54,
           'pa_luftdurchsatz':55,'pa_anschluss':56,'pa_abmessungen':57,'pa_material':58,
           'pa_gewicht':59,'pa_schutzart':60,'pa_flaeche':61,
           'pa_weee-nummer':23,'pa_leistungsaufnahme':26,'pa_spannung':27,
           'pa_lichtspektrum':28,'pa_ppf':29,'pa_ppe':30,'pa_frequenz':31,
           'pa_stromverbrauch':32,'pa_lumen':33,'pa_amp':34,'pa_geraeuschpegel':35,'pa_maschenweite':62,'pa_presskraft':63,'pa_herkunft':64,'pa_format':65,'pa_motiv':66,'pa_brennstoff':67,'pa_laenge':68,'pa_durchmesser':69,'pa_spektrum':70,'pa_traegeroel':71,'pa_verlag':72,'pa_einband':73,'pa_isbn':74,'pa_grammatur':75,'pa_durchsatz':76,'pa_passend-fuer':77}
