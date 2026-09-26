# -*- coding: utf-8 -*-
"""Sammelt alle Produktseiten von Hesi und Plagron ein."""
import re, json, time, sys
sys.path.insert(0, '.')
import d_web

def hesi():
    t, roh = d_web.hole('https://hesi.nl/de/produkte/')
    u = sorted({x for x in re.findall(r'href="(https?://hesi\.nl/de/[^"?#]+)"', roh)
                if not re.search(r'/(PRODUKTE|Downloads|Footer|de)/?$|/Footer/', x)})
    return u

def plagron():
    t, roh = d_web.hole('https://plagron.com/de/hobby/produkte')
    if not roh:
        return []
    return sorted(set(re.findall(r'href="(https://plagron\.com/de/hobby/produkte/[a-z0-9\-]+)"', roh)))


if __name__ == '__main__':
    h, p = hesi(), plagron()
    print(f'Hesi {len(h)} Seiten, Plagron {len(p)} Seiten')
    json.dump({'hesi': h, 'plagron': p}, open('d_web_urls.json', 'w'), indent=1)
    for x in (h[:4] + p[:4]):
        print('  ', x)
