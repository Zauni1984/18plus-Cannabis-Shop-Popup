# GPSR: Bevollmaechtigte bei EU-Herstellern

## Die Regel

GPSR Art. 16 verlangt einen in der EU niedergelassenen Wirtschaftsakteur.
Sitzt der **Hersteller selbst in der EU**, ist er dieser Akteur – ein
zusaetzlicher Bevollmaechtigter ist dann nicht nur ueberfluessig, sondern
irrefuehrend, weil er eine Verantwortungskette suggeriert, die es nicht gibt.

## Erledigt: Dutch Passion (6071)

Zaadhandel Dutch Passion BV sitzt in Amsterdam. Im Datensatz stand zusaetzlich
**Lauwe Zaunreither GbR** als EU-Verantwortlicher. Entfernt – betrifft 110
Produkte. Der Stand davor liegt in `dutch-passion-6071-vorher.json`.

## Offen: 19 weitere Hersteller

Dieselbe Konstellation, zusammen rund 440 Produkte. Vollstaendig in
`eu-hersteller-mit-bevollmaechtigtem.json`:

| Hersteller | Sitz | Produkte | eingetragener Bevollmaechtigter |
|---|---|---:|---|
| Snorkel Spain S.L. | Spanien | 96 | |
| AutoPot Benelux | Niederlande | 69 | Lumen Max GmbH |
| SANlight GmbH | Österreich | 54 | |
| Terra Aquatica | Frankreich | 48 | |
| Plagron | Niederlande | 44 | |
| PALACIO CZ s.r.o. | Tschechien | 29 | |
| BIOBIZZ WORLDWIDE, S.L. | Spanien | 25 | |
| Mills Nutrients | Niederlande | 24 | Grow In AG |
| Prima Klima | Tschechien | 15 | |
| Heatex Glass B.V. | Niederlande | 11 | |
| Miquel y Costas & Miquel | Spanien | 9 | |
| GrowTechnology GmbH | Deutschland | 6 | |
| MIRON Violetglass BV | Niederlande | 4 | |
| TUX Smoking GmbH | Österreich | 4 | |
| Vandenberg Special Products B.V. | Niederlande | 1 | |
| Grounded Genetics, HGA Garden, hortione, Solux | EU | 0 | |

**Nicht angeruehrt, weil hier etwas anderes falsch ist: AC Infinity (8582,
100 Produkte).** Dort steht im Herstellerfeld eine deutsche Adresse
(Am Kanal 13/15, Eggingen) – das ist aber GrowTechnology, der EU-Importeur.
AC Infinity selbst ist ein US-Unternehmen. Hier gehoert der echte
US-Hersteller ins Herstellerfeld und GrowTechnology in das EU-Feld; ein
blosses Loeschen des EU-Eintrags waere falsch.

Die Liste wurde ueber das Land in der Herstelleradresse gebildet. Vor einem
Sammellauf lohnt der Blick auf jeden Einzelfall – AC Infinity zeigt, dass
die Datensaetze nicht durchweg sauber befuellt sind.
