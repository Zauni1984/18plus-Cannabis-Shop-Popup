# -*- coding: utf-8 -*-
"""Minimaler PDF-Textauszug ohne Fremdbibliotheken.

Die Umgebung hat kein pdftotext, und pypdf scheitert an einer kaputten
cryptography-Bindung. Fuer Datenblaetter reicht es, die FlateDecode-Streams
auszupacken und die Text-Operatoren einzusammeln.
"""
import re, zlib

SPRACHMARKE = re.compile(r'[a-z]{2}-[A-Z]{2}')
OKTAL = re.compile(rb'\\([0-7]{1,3})')


def _stream_text(roh):
    teile = []
    for t in re.finditer(rb'\((?:\\.|[^\\()])*\)', roh):
        s = t.group(0)[1:-1]
        s = re.sub(rb'\\([()\\])', rb'\1', s)
        s = OKTAL.sub(lambda x: bytes([int(x.group(1), 8) & 0xFF]), s)
        teile.append(s.decode('latin1'))
    return ''.join(teile)


def text(pfad_oder_bytes):
    d = (open(pfad_oder_bytes, 'rb').read()
         if isinstance(pfad_oder_bytes, str) else pfad_oder_bytes)
    raus = []
    for m in re.finditer(rb'stream\r?\n(.*?)\r?\nendstream', d, re.S):
        try:
            roh = zlib.decompress(m.group(1))
        except Exception:
            continue
        if b'Tj' not in roh and b'TJ' not in roh:
            continue
        # Bilddaten aussortieren: echte Inhaltsstroeme sind ueberwiegend druckbar
        probe = roh[:4000]
        druckbar = sum(1 for b in probe if 32 <= b < 127 or b in (9, 10, 13))
        if not probe or druckbar / len(probe) < 0.85:
            continue
        s = _stream_text(roh)
        if s.strip():
            raus.append(s)
    t = SPRACHMARKE.sub(' ', '\n'.join(raus))
    return re.sub(r'[ \t]{2,}', ' ', t)


if __name__ == '__main__':
    import sys
    print(text(sys.argv[1])[:2200])
