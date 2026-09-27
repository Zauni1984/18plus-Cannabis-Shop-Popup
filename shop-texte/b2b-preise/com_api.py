import json, base64, urllib.request, urllib.parse, time
U=None
for z in open('/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/.com_creds'):
    if z.startswith('COMAPP='): P=z.rstrip('\n').split('=',1)[1]
    if z.startswith('COMUSER='): U=z.rstrip('\n').split('=',1)[1]
AUTH='Basic '+base64.b64encode(f'{U}:{P}'.encode()).decode()
BASIS='https://hanfjack.com/wp-json/wc/v3/'

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    for v in range(5):
        try:
            r=urllib.request.Request(BASIS+pfad, data=d, method=methode,
                headers={'Authorization':AUTH,'Content-Type':'application/json','User-Agent':'hj/1'})
            with urllib.request.urlopen(r, timeout=180) as f: return json.load(f)
        except Exception as e:
            if v==4: raise
            time.sleep(2**v*2)
