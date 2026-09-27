# -*- coding: utf-8 -*-
"""Die 33 fehlenden Palacio-Bilder hochladen und hinten anhaengen.

Bestehende Bilder bleiben unveraendert und behalten ihre Reihenfolge - das
Hauptbild wechselt also nicht. Die neuen kommen dahinter.
"""
import base64, json, re, sys, urllib.request

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hj

t = open(S + '.wp_creds').read()
u = re.search(r'WPUSER=(\S+)', t).group(1)
p = re.search(r"WPAPP=['\"]?([^'\"\n]+)", t).group(1)
AUTH = 'Basic ' + base64.b64encode(f'{u}:{p}'.encode()).decode()

def hoch(roh, ct, name):
    # Der erste Lauf ist an einem Connection Reset gestorben - Uploads von
    # mehreren MB brechen gelegentlich ab. Deshalb mit Wiederholung.
    import time
    for v in range(5):
        try:
            r = urllib.request.Request('https://hanfjack.de/wp-json/wp/v2/media', data=roh,
                method='POST', headers={'Authorization': AUTH, 'Content-Type': ct,
                'Content-Disposition': f'attachment; filename="{name}"', 'User-Agent': 'hj/1'})
            with urllib.request.urlopen(r, timeout=300) as f:
                return json.load(f)
        except Exception as e:
            if v == 4: raise
            print(f'   Wiederholung {v+1} fuer {name}: {str(e)[:60]}')
            time.sleep(2 ** v * 3)

def hol(url):
    r = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(r, timeout=240) as f:
        return f.read(), f.headers.get('Content-Type', 'image/jpeg').split(';')[0]

if __name__ == '__main__':
    fehl = json.load(open(S + 'pal_fehlende_bilder.json'))
    # Nur die Produkte, die der abgebrochene erste Lauf nicht geschafft hat.
    REST = {'1877', '1878', '1879', '13783', '13786', '20192'}
    # 45413 wurde vor dem Abbruch schon hochgeladen, aber nie zugeordnet.
    SCHON_DA = {'1877': {1: 45413}}
    protokoll = {}
    for pid, v in sorted(fehl.items(), key=lambda kv: int(kv[0])):
        if pid not in REST:
            continue
        prod = hj.ruf(f'products/{pid}?_fields=id,name,slug,images')
        vorhanden = [i['id'] for i in prod['images']]
        neu = []
        for f in v['fehlend']:
            if not isinstance(f['rmse'], (int, float)):
                print('   uebersprungen (Downloadfehler):', pid, f['nr']); continue
            vorab = SCHON_DA.get(pid, {}).get(f['nr'])
            if vorab:
                neu.append(vorab)
                continue
            roh, ct = hol(f['link'])
            ext = {'image/jpeg': 'jpg', 'image/png': 'png',
                   'image/webp': 'webp'}.get(ct, 'jpg')
            name = f"{v['slug']}-{len(vorhanden) + len(neu) + 1}.{ext}"
            m = hoch(roh, ct, name)
            neu.append(m['id'])
        q = hj.ruf(f'products/{pid}',
                   {'images': [{'id': i} for i in vorhanden + neu]}, 'PUT')
        protokoll[pid] = {'vorher': vorhanden, 'neu': neu,
                          'nachher': [i['id'] for i in q['images']]}
        print(f"{pid} | {len(vorhanden)} -> {len(q['images'])} Bilder | "
              f"neu {neu} | {q['name'][:42]}")
    json.dump(protokoll, open(S + 'pal_bilder_protokoll2.json', 'w'))
    print()
    print('Produkte:', len(protokoll), '| neue Bilder:',
          sum(len(v['neu']) for v in protokoll.values()))
