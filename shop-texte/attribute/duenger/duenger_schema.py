# -*- coding: utf-8 -*-
"""Werteskalen fuer die Duengerattribute auf hanfjack.de.

Eingetragen wird nur, was das Herstellerdatenblatt, das Etikett oder die
Produktbeschreibung ausdruecklich nennt. Wo eine Angabe fehlt, bleibt das
Feld leer - geschaetzt wird nichts.
"""

NEUE_ATTRIBUTE = [
  ('pa_npk',            'NPK-Verhältnis'),
  ('pa_naehrstoffe',    'Nährstoffe'),
  ('pa_duengertyp',     'Düngertyp'),
  ('pa_duengerart',     'Düngerart'),
  ('pa_wirkdauer',      'Wirkdauer'),
  ('pa_form',           'Form'),
  ('pa_loeslichkeit',   'Löslichkeit'),
  ('pa_anwendungsphase','Anwendungsphase'),
  ('pa_substrat',       'Substrat'),
  ('pa_anwendungsart',  'Anwendungsart'),
]

# feste Wertelisten - alles andere waere Wildwuchs
DUENGERTYP      = ['Einnährstoffdünger', 'Mehrnährstoffdünger (Volldünger)']
DUENGERART      = ['Mineralisch', 'Organisch', 'Organo-mineralisch', 'Pflanzenhilfsmittel']
WIRKDAUER       = ['Sofortwirkung', 'Langzeitwirkung', 'Sofort- und Langzeitwirkung']
FORM            = ['Flüssig', 'Pulver', 'Granulat', 'Tabletten', 'Stäbchen', 'Paste']
LOESLICHKEIT    = ['Vollständig wasserlöslich', 'Teilweise wasserlöslich', 'Nicht wasserlöslich']
ANWENDUNGSPHASE = ['Keimung und Stecklinge', 'Wachstum', 'Blüte', 'Wachstum und Blüte',
                   'Spülphase', 'Ganze Kultur']
SUBSTRAT        = ['Erde', 'Coco', 'Hydrokultur', 'Steinwolle', 'Alle Substrate']
ANWENDUNGSART   = ['Gießen (Wurzel)', 'Blattdüngung', 'Gießen und Blattdüngung',
                   'In das Substrat einarbeiten']
# Sekundaer- und Spurennaehrstoffe, wie sie auf den Etiketten stehen
NAEHRSTOFFE     = ['Stickstoff (N)', 'Phosphor (P)', 'Kalium (K)', 'Magnesium (Mg)',
                   'Calcium (Ca)', 'Schwefel (S)', 'Eisen (Fe)', 'Mangan (Mn)', 'Zink (Zn)',
                   'Kupfer (Cu)', 'Bor (B)', 'Molybdän (Mo)', 'Silizium (Si)',
                   'Huminsäuren', 'Fulvosäuren', 'Aminosäuren', 'Seetangextrakt',
                   'Enzyme', 'Mykorrhiza', 'Bakterienkulturen', 'Vitamine']


def npk(n, p, k):
    """3, 1, 4 -> '3-1-4'. Zahlen werden unveraendert uebernommen."""
    def f(x):
        s = str(x).replace('.', ',').rstrip('0').rstrip(',')
        return s or '0'
    return f'{f(n)}-{f(p)}-{f(k)}'


def inhalt(menge, einheit):
    """500, 'ml' -> '500 ml'  ·  1.0, 'l' -> '1 L'"""
    e = {'l': 'L', 'liter': 'L', 'ml': 'ml', 'kg': 'kg', 'g': 'g'}[einheit.lower()]
    s = f'{menge:g}'.replace('.', ',')
    return f'{s} {e}'
