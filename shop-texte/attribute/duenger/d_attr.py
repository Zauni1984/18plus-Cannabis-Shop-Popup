# -*- coding: utf-8 -*-
"""Duengerattribute aus Produktname und Beschreibung.

Gesetzt wird nur, was der Text ausdruecklich sagt. Vor jedem Treffer laeuft
ein Verneinungsschutz: 'nicht fuer die Bluete' zaehlt nicht als Bluete.
"""
import re, html

VERNEINUNG = r'(?:nicht|kein[ae]?[rnms]?|ohne|statt|weder|anstelle|verzicht\w*)\W{0,30}$'


def text(p):
    t = ' '.join([p.get('name', ''), p.get('short_description') or '',
                  p.get('description') or ''])
    t = html.unescape(re.sub(r'<[^>]+>', ' ', t))
    return re.sub(r'\s+', ' ', t)


def sagt(t, muster):
    """True, wenn das Muster vorkommt und davor keine Verneinung steht."""
    for m in re.finditer(muster, t, re.I):
        if not re.search(VERNEINUNG, t[max(0, m.start() - 34):m.start()], re.I):
            return True
    return False


def form(t, name):
    if sagt(t, r'\bTablette|\bTabs\b'):
        return 'Tabletten'
    if sagt(t, r'\bGranulat'):
        return 'Granulat'
    if sagt(t, r'\bStäbchen\b'):
        return 'Stäbchen'
    if sagt(t, r'\bPulver\b|\bpulverförmig'):
        return 'Pulver'
    if re.search(r'\d\s*(ml|l|liter)\b', name, re.I) or sagt(t, r'\bflüssig|Nährlösung|Gebinde|Kanister'):
        return 'Flüssig'
    return None


# Produktlinien sind eindeutiger als Fliesstext: "Flores", "Bloom", "Grow"
BLUETE_NAME = r'\bFlores\b|\bBloom\b|\bBlüte\w*|\bBlüh\w*|\bBud\b|\bPK\s?13|\bFlower\w*|\bFlowering\b'
WACHS_NAME  = r'\bVega\b|\bGrow\b|\bGro\b|\bWachstum\w*|\bVeg\b|\bVegetative\b'


def phase(t, name=''):
    if sagt(t, r'\bSpülphase\b|\bzum Spülen\b|\bspülen vor der Ernte\b|\bFlush\b|'
               r'\bletzte[nr]? Woche[n]? vor der Ernte\b|\bAusspülen der\b|'
               r'\bReservoir leeren\b|\bSpülung vor der Ernte\b'):
        return 'Spülphase'
    if sagt(t, r'\bfür alle Phasen\b|\bin allen Phasen\b|\bgesamte[nr]? Kultur\b|'
               r'\bganze[nr]? Kultur\b|\bvon der Anzucht bis zur Ernte\b|'
               r'\bwährend der gesamten\b|\bdurchgehend\w* Anwendung\b'):
        return 'Ganze Kultur'
    # Der Produktname schlaegt den Fliesstext: ein "Flores" ist ein Bluetedünger
    bl_n = bool(re.search(BLUETE_NAME, name, re.I))
    wa_n = bool(re.search(WACHS_NAME, name, re.I))
    if bl_n and not wa_n:
        return 'Blüte'
    if wa_n and not bl_n:
        return 'Wachstum'
    if bl_n and wa_n:
        return 'Wachstum und Blüte'
    bl = sagt(t, r'\bBlütephase\b|\bin der Blüte\b|\bfür die Blüte\b|\bwährend der Blüte\b|'
                 r'\bzur Blüte\b|\bBlütezeit\b|\bBlütebeginn\b|\bEndphase der Blüte\b|'
                 r'\bBlüte[nd]?dünger\b|\bBlütestimulator\b|\bBlütebooster\b')
    wa = sagt(t, r'\bWachstumsphase\b|\bfür das Wachstum\b|\bvegetative[nrs]? Phase\b|'
                 r'\bvegetative[nrs]? Wachstum\b|\bWuchsphase\b|\bWachstumsdünger\b')
    st = sagt(t, r'\bStecklinge?\b|\bAnzucht\b|\bKeimung\b|\bJungpflanzen\b|\bSämlinge\b')
    if bl and wa:
        return 'Wachstum und Blüte'
    if bl:
        return 'Blüte'
    if wa:
        return 'Wachstum'
    if st:
        return 'Keimung und Stecklinge'
    return None


def anwendungsart(t):
    giess = sagt(t, r'\bNährlösung\b|\bgieß\w*|\bGieß\w*|\bzum Gießwasser\b|\büber die Wurzel\b|'
                    r'\bins Gießwasser\b|\bzur fertigen Lösung\b')
    blatt = sagt(t, r'\bBlattdüngung\b|\bBlattspray\b|\bblattsprüh\w*|\bauf die Blätter sprüh\w*|'
                    r'\bfoliar\b|\bBlattapplikation\b')
    misch = sagt(t, r'\bin das Substrat einarbeiten\b|\bunter die Erde mischen\b|'
                    r'\bins Substrat einmischen\b|\beinarbeiten\b')
    if giess and blatt:
        return 'Gießen und Blattdüngung'
    if blatt:
        return 'Blattdüngung'
    if giess:
        return 'Gießen (Wurzel)'
    if misch:
        return 'In das Substrat einarbeiten'
    return None


def art(t, name):
    if re.search(r'Pflanzenhilfsmittel', name, re.I):
        return 'Pflanzenhilfsmittel'
    org = sagt(t, r'\b100\s*%\s*organisch\b|\brein organisch\b|\bvollständig organisch\b|'
                  r'\borganische[rns]? (Blüte[nd]?|Wachstums)?dünger\b|\bbiologischer Dünger\b|'
                  r'\bBio-Dünger\b|\borganische Rezeptur\b|\brein pflanzlich\b|'
                  r'\bausschließlich organisch\b')
    mn = sagt(t, r'\bmineralischer Dünger\b|\brein mineralisch\b|\bmineralische[nrs]? Dünger\b')
    om = sagt(t, r'\borgano-mineralisch\b|\borganisch-mineralisch\b')
    if om:
        return 'Organo-mineralisch'
    if org and not mn:
        return 'Organisch'
    if mn and not org:
        return 'Mineralisch'
    return None


def npk(t):
    m = re.search(r'\bN\W?P\W?K\b[^0-9]{0,18}(\d{1,2}(?:[.,]\d)?)\s*[-–/]\s*'
                  r'(\d{1,2}(?:[.,]\d)?)\s*[-–/]\s*(\d{1,2}(?:[.,]\d)?)', t, re.I)
    if not m:
        return None
    return '-'.join(x.replace('.', ',') for x in m.groups())



SUBSTRAT_MUSTER = [
  ('Erde',          r'\bErde\b|\bTerra\b|\bSoil\b|\btorfbasiert\w*|\bBlumenerde\b|\bErdmischung\w*'),
  ('Coco',          r'\bCoco\b|\bKokos\w*|\bCocos\b|\bKokosmatte\w*|\bCoir\b'),
  ('Hydrokultur',   r'\bHydro\w*|\bNFT\b|\bDWC\b|\bBlähton\b|\bEbbe[ /-]?(und|&)?[ /-]?Flut\b|'
                    r'\bAeroponi\w*|\bWasserkultur\b|\bRecirculating\b'),
  ('Steinwolle',    r'\bSteinwolle\b|\bRockwool\b|\bGrodan\b'),
]

NAEHR_MUSTER = [
  ('Stickstoff (N)',   r'\bStickstoff\b'),
  ('Phosphor (P)',     r'\bPhosphor\b'),
  ('Kalium (K)',       r'\bKalium\b'),
  ('Magnesium (Mg)',   r'\bMagnesium\b'),
  ('Calcium (Ca)',     r'\bCalcium\b|\bKalzium\b'),
  ('Schwefel (S)',     r'\bSchwefel\b'),
  ('Eisen (Fe)',       r'\bEisen\b'),
  ('Mangan (Mn)',      r'\bMangan\b'),
  ('Zink (Zn)',        r'\bZink\b'),
  ('Kupfer (Cu)',      r'\bKupfer\b'),
  ('Bor (B)',          r'\bBor\b'),
  ('Molybdän (Mo)',    r'\bMolybdän\b'),
  ('Silizium (Si)',    r'\bSilizium\b|\bKiesel\w*|\bSilica\b'),
  ('Huminsäuren',      r'\bHumin\w*'),
  ('Fulvosäuren',      r'\bFulvo\w*'),
  ('Aminosäuren',      r'\bAminosäure\w*'),
  ('Seetangextrakt',   r'\bSeetang\w*|\bAlgenextrakt\w*|\bKelp\b'),
  ('Enzyme',           r'\bEnzym\w*|\bCellulase\b'),
  ('Mykorrhiza',       r'\bMykorrhiza\w*'),
  ('Bakterienkulturen',r'\bBakterienkultur\w*|\bTrichoderma\b|\bRhizobakterien\b|\bBacillus\b'),
  ('Vitamine',         r'\bVitamin\w*'),
]


def substrat(t):
    out = [name for name, muster in SUBSTRAT_MUSTER if sagt(t, muster)]
    if len(out) >= 4:
        return ['Alle Substrate']
    return out or None


# Ein Naehrstoff zaehlt nur, wenn der Satz ihn als Bestandteil ausweist.
# "um den Stickstoff zu senken" ist keine Inhaltsangabe.
ENTHAELT_VOR = (r'(?:enthält|enthalten|mit|versorgt\w* (?:Deine |die )?Pflanzen? mit|reich an|'
                r'Gehalt[:\s]|liefert|angereichert mit|Quelle (?:von|für)|Zusatz von|'
                r'Kombination aus|Mischung aus|Anteil an|\d\s*%|,|\bund\b|\boder\b|'
                r'\bsowie\b|\bplus\b)\W{0,60}$')
ENTHAELT_NACH = r'^\W{0,40}(?:enthalten|angereichert|\d{1,2}(?:[.,]\d)?\s*%|in Chelatform|als Chelat)'
DAGEGEN = (r'(?:senk\w*|reduzier\w*|Bedarf|Mangel|verzicht\w*|setzte|ohne|frei von|'
           r'ersetz\w*|weniger|zu viel|Überschuss)')


def naehrstoffe(t):
    out = []
    for name, muster in NAEHR_MUSTER:
        for m in re.finditer(muster, t, re.I):
            vor = t[max(0, m.start() - 70):m.start()]
            nach = t[m.end():m.end() + 45]
            if re.search(DAGEGEN, vor, re.I) or re.search(DAGEGEN, nach[:25], re.I):
                continue
            if re.search(ENTHAELT_VOR, vor, re.I) or re.search(ENTHAELT_NACH, nach, re.I):
                out.append(name)
                break
    return out or None


def duengertyp(npk_wert, t):
    """Aus dem NPK: nur ein Hauptnaehrstoff > 0 heisst Einnaehrstoffduenger."""
    if npk_wert:
        zahlen = [float(x.replace(',', '.')) for x in npk_wert.split('-')]
        gesetzt = sum(1 for z in zahlen if z > 0)
        if gesetzt == 1:
            return 'Einnährstoffdünger'
        if gesetzt >= 2:
            return 'Mehrnährstoffdünger (Volldünger)'
    if sagt(t, r'\bVolldünger\b|\bKomplettdünger\b|\bAll-in-One\b|\bBasisdünger\b'):
        return 'Mehrnährstoffdünger (Volldünger)'
    return None


def loeslichkeit(t):
    if sagt(t, r'\bvollständig wasserlöslich\b|\b100\s*%\s*wasserlöslich\b|\bvöllig wasserlöslich\b'):
        return 'Vollständig wasserlöslich'
    if sagt(t, r'\bwasserlöslich\b|\bin (der )?(Wasser|Nährlösung) (auf)?gelöst\b|'
               r'\blöst sich (vollständig )?(in|im) Wasser\b|\bin der Nährlösung aufgelöst\b'):
        return 'Vollständig wasserlöslich'
    if sagt(t, r'\bnicht wasserlöslich\b|\bunlöslich\b'):
        return 'Nicht wasserlöslich'
    return None


def build(p):
    t = text(p)
    neu = {}
    f = form(t, p.get('name', ''))
    if f:
        neu['pa_form'] = [f]
    ph = phase(t, p.get('name', ''))
    if ph:
        neu['pa_anwendungsphase'] = [ph]
    aa = anwendungsart(t)
    if aa:
        neu['pa_anwendungsart'] = [aa]
    ar = art(t, p.get('name', ''))
    if ar:
        neu['pa_duengerart'] = [ar]
    n = npk(t)
    if n:
        neu['pa_npk'] = [n]
    su = substrat(t)
    if su:
        neu['pa_substrat'] = su
    na = naehrstoffe(t)
    if na:
        neu['pa_naehrstoffe'] = na
    dt = duengertyp(n, t)
    if dt:
        neu['pa_duengertyp'] = [dt]
    lo = loeslichkeit(t)
    if lo:
        neu['pa_loeslichkeit'] = [lo]
    return neu


if __name__ == '__main__':
    import json, collections
    D = json.load(open('d_desc.json'))
    c = collections.Counter()
    werte = collections.defaultdict(collections.Counter)
    for p in D:
        b = build(p)
        for k, v in b.items():
            c[k] += 1
            werte[k][v[0]] += 1
    print(f'{len(D)} Dünger')
    for k, n in c.most_common():
        print(f'  {n:4}  {k}')
        for w, m in werte[k].most_common(7):
            print(f'          {m:4}  {w}')
