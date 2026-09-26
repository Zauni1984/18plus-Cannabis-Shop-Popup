# -*- coding: utf-8 -*-
"""Holt NPK, Duengerart und Loeslichkeit von den Herstellerseiten."""
import re, html, json, time, urllib.request

UA = {'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 '
                    '(KHTML, like Gecko) Chrome/124.0 Safari/537.36',
      'Accept-Language': 'de,en;q=0.8'}


def hole(url):
    try:
        r = urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=45)
        roh = r.read().decode(r.headers.get_content_charset() or 'utf-8', 'replace')
    except Exception as e:
        return None, str(e)[:60]
    t = re.sub(r'<(script|style|noscript)[^>]*>.*?</\1>', ' ', roh, flags=re.S | re.I)
    t = re.sub(r'<[^>]+>', '\n', t)
    return html.unescape(t), roh


def npk(t):
    m = re.search(r'\bNPK\b[^0-9\n]{0,14}(\d{1,2}(?:[.,]\d)?)\s*[-–/]\s*'
                  r'(\d{1,2}(?:[.,]\d)?)\s*[-–/]\s*(\d{1,2}(?:[.,]\d)?)', t, re.I)
    if not m:
        m = re.search(r'NPK-Dünger\s*\((\d{1,2}(?:[.,]\d)?)\s*[-–]\s*'
                      r'(\d{1,2}(?:[.,]\d)?)\s*[-–]\s*(\d{1,2}(?:[.,]\d)?)\)', t, re.I)
    return '-'.join(x.replace('.', ',') for x in m.groups()) if m else None


def art(t):
    if re.search(r'organo-?\s*mineralisch', t, re.I):
        return 'Organo-mineralisch'
    if re.search(r'\b(biologische[rns]?|organische[rns]?)\s+(NPK-)?Dünger\b|'
                 r'\b100\s*%\s*(bio|organisch)', t, re.I):
        return 'Organisch'
    if re.search(r'\bmineralische[rns]?\s+(NPK-)?Dünger\b', t, re.I):
        return 'Mineralisch'
    return None


def loeslich(t):
    if re.search(r'\b(vollständig|völlig|100\s*%)\s*wasserlöslich\b|\bwater soluble\b', t, re.I):
        return 'Vollständig wasserlöslich'
    return None


def form(t):
    if re.search(r'\bflüssige[rns]?\b|\bFlüssigdünger\b', t, re.I):
        return 'Flüssig'
    if re.search(r'\bPulver\b|\bpulverförmig', t, re.I):
        return 'Pulver'
    return None


def seite(url):
    t, _ = hole(url)
    if not t:
        return None
    return {k: v for k, v in
            {'pa_npk': npk(t), 'pa_duengerart': art(t),
             'pa_loeslichkeit': loeslich(t), 'pa_form': form(t)}.items() if v}


if __name__ == '__main__':
    import sys
    for u in sys.argv[1:]:
        print(u, '->', seite(u))
