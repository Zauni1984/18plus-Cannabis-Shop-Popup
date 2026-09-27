# -*- coding: utf-8 -*-
"""Inhaltliche Zusammenfuehrungen: verschiedene Woerter, gleiche Bedeutung."""

# Ziel <- Varianten. Alles klein geschrieben verglichen.
MERGE = {
 # Samentyp
 'feminisiert': ['feminisierte samen', 'feminisierte hanfsamen', 'feminisierter samen',
                 'feminisierte cannabis samen', 'fem samen', 'feminized', 'feminisierte seeds'],
 'autoflowering': ['autoflower', 'autoflower samen', 'autoflowering samen', 'auto samen',
                   'automatic', 'automatik samen', 'selbstblühend', 'autoflowering hanfsamen'],
 'regulär': ['reguläre samen', 'regular samen', 'reguläre hanfsamen', 'regular seeds'],
 # Produktart
 'Hanfsamen': ['cannabis samen', 'cannabis samen kaufen', 'hanfsamen kaufen', 'zuchtsamen',
               'cannabissamen', 'marihuana samen', 'weed samen', 'cannabis seeds',
               'hanfsamen bestellen', 'samen'],
 # Anbauform
 'Indoor Grow': ['indoor', 'indoor anbau', 'indooranbau', 'indoor growing'],
 'Outdoor Grow': ['outdoor', 'outdoor anbau', 'outdooranbau', 'outdoor growing', 'freiland'],
 'Gewächshaus': ['greenhouse', 'gewächshausanbau'],
 # Typ
 'Hybrid': ['indica/sativa-hybrid', 'indica sativa hybrid', 'hybride'],
 'Indica-dominant': ['indicadominiert', 'indica dominant', 'indica-lastig', 'indicalastig'],
 'Sativa-dominant': ['sativadominiert', 'sativa dominant', 'sativa-lastig', 'sativalastig'],
 # Herkunft
 'USA Genetik': ['us-genetik', 'us genetik', 'usa-genetik', 'amerikanische genetik'],
 'Cali Weed': ['cali', 'california weed', 'kalifornische genetik', 'cali genetik'],
 # Marken-Kuerzel
 'Royal Queen Seeds': ['rqs'],
 'Barney\'s Farm': ['barneys farm', 'barney s farm'],
 'Green House Seed Co': ['green house seeds', 'greenhouse seed co', 'green house'],
 'Fast Buds': ['420 fast buds', 'fastbuds'],
 'Terra Aquatica': ['ghe', 'general hydroponics', 'terra aquatica ghe'],
 # Eigenschaften
 'hoher Ertrag': ['xl-ertrag', 'xl ertrag', 'hohe erträge', 'ertragreich', 'großer ertrag'],
 'Grow Zubehör': ['growzubehör', 'grow-zubehör', 'zubehör grow'],
}

ZIEL = {v: k for k, vs in MERGE.items() for v in vs}
