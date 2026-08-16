#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys, os
G="#06402B"; CREAM="#F4F1E9"; GOLD="#C9A24B"

def leaflets(base=94, Ls=(82,68,50,32), color=GOLD, op=None):
    lf=[(Ls[0],0),(Ls[1],32),(Ls[1],-32),(Ls[2],60),(Ls[2],-60),(Ls[3],85),(Ls[3],-85)]
    p=[]
    for L,a in lf:
        W=L*0.26
        d=f"M0,0 C {-W:.1f},{-L*0.35:.1f} {-W*0.5:.1f},{-L*0.85:.1f} 0,{-L:.1f} C {W*0.5:.1f},{-L*0.85:.1f} {W:.1f},{-L*0.35:.1f} 0,0 Z"
        p.append(f'<path d="{d}" transform="rotate({a})"/>')
    opa=f' fill-opacity="{op}"' if op else ''
    return f'<g transform="translate(50,{base})" fill="{color}"{opa} stroke="none">{"".join(p)}<rect x="-2" y="0" width="4" height="6"/></g>'

# ---------- ICONS (100x100, gold) ----------
def i_leaf():   return leaflets()
def i_seedling():
    s=f'<rect x="20" y="86" width="60" height="6" rx="3"/><rect x="47.5" y="50" width="5" height="38"/>'
    def blade(a):
        L=30;W=L*0.30
        d=f"M0,0 C {-W},{-L*0.35} {-W*0.5},{-L*0.85} 0,{-L} C {W*0.5},{-L*0.85} {W},{-L*0.35} 0,0 Z"
        return f'<path d="{d}" transform="rotate({a})"/>'
    return f'<g fill="{GOLD}" stroke="none">{s}<g transform="translate(50,58)">{blade(-45)}{blade(45)}{blade(0)}</g></g>'
def i_lamp():
    return f'''<g fill="{GOLD}" stroke="{GOLD}"><circle cx="50" cy="11" r="5" stroke="none"/>
<line x1="50" y1="14" x2="25" y2="45" stroke-width="3.5" stroke-linecap="round"/><line x1="50" y1="14" x2="75" y2="45" stroke-width="3.5" stroke-linecap="round"/>
<rect x="17" y="45" width="66" height="17" rx="8.5" stroke="none"/>
<line x1="31" y1="66" x2="27" y2="83" stroke-width="4.5" stroke-linecap="round"/><line x1="50" y1="66" x2="50" y2="86" stroke-width="4.5" stroke-linecap="round"/><line x1="69" y1="66" x2="73" y2="83" stroke-width="4.5" stroke-linecap="round"/></g>'''
def i_bottle():
    body='M33,30 a10,10 0 0 1 10,-10 h14 a10,10 0 0 1 10,10 v52 a10,10 0 0 1 -10,10 h-14 a10,10 0 0 1 -10,-10 z'
    hole='M50,64 C46,58 43,56 43,52 a7,7 0 0 1 14,0 c0,4 -3,6 -7,12 z'
    return f'<g fill="{GOLD}" stroke="none"><rect x="42" y="6" width="16" height="8" rx="2"/><rect x="45" y="13" width="10" height="8"/><path fill-rule="evenodd" d="{body} {hole}"/></g>'
def i_filter():
    cyl='M38,24 a12,12 0 0 1 12,-12 a12,12 0 0 1 12,12 v52 a12,12 0 0 1 -12,12 a12,12 0 0 1 -12,-12 z'
    holes=''.join(f'M54,{cy} a4,4 0 1 0 -8,0 a4,4 0 1 0 8,0 z' for cy in (34,50,66))
    return f'<g fill="{GOLD}" stroke="none"><path fill-rule="evenodd" d="{cyl} {holes}"/></g>'
def i_drop():
    return f'<g fill="{GOLD}" stroke="none"><path d="M50,14 C50,14 76,46 76,63 a26,26 0 1 1 -52,0 C24,46 50,14 50,14 Z"/></g>'
def i_fan():
    def bl(a):
        d="M0,0 C 6,-13 18,-20 28,-16 C 22,-4 10,-1 0,0 Z"
        return f'<path d="{d}" transform="rotate({a})"/>'
    return f'<g fill="{GOLD}" stroke="none"><circle cx="50" cy="50" r="32" fill="none" stroke="{GOLD}" stroke-width="4"/><g transform="translate(50,50)">{bl(0)}{bl(120)}{bl(240)}</g><circle cx="50" cy="50" r="6"/></g>'
def i_fantent(): return i_fan()
def i_tent():
    box='M22,30 h56 a4,4 0 0 1 4,4 v52 a4,4 0 0 1 -4,4 h-56 a4,4 0 0 1 -4,-4 v-52 a4,4 0 0 1 4,-4 z'
    zip='M48.5,34 h3 v48 h-3 z'
    v1='M30,38 a3,3 0 1 0 -0.1,0 z'; v2='M70,38 a3,3 0 1 0 -0.1,0 z'
    return f'<g fill="{GOLD}" stroke="none"><path fill-rule="evenodd" d="{box} {zip} {v1} {v2}"/></g>'
def i_box():
    top='30,32 50,22 70,32 50,42'; left='30,32 50,42 50,80 30,70'; right='70,32 50,42 50,80 70,70'
    return f'<g fill="{GOLD}" stroke="{G}" stroke-width="1.5" stroke-linejoin="round"><polygon points="{left}"/><polygon points="{right}"/><polygon points="{top}"/></g>'
def i_sack():
    body='M30,42 H70 L74,82 Q74,88 68,88 H32 Q26,88 26,82 Z'
    band='M28,58 H72 v5 H28 z'
    return f'<g fill="{GOLD}" stroke="none"><rect x="33" y="30" width="34" height="12" rx="2"/><path fill-rule="evenodd" d="{body} {band}"/></g>'
def i_gear():
    outer='M78,50 a28,28 0 1 0 -56,0 a28,28 0 1 0 56,0 Z'
    hole='M60,50 a10,10 0 1 0 -20,0 a10,10 0 1 0 20,0 Z'
    teeth=''
    import math
    for k in range(8):
        a=math.radians(k*45); cx=50+math.cos(a)*30; cy=50+math.sin(a)*30
        teeth+=f'<rect x="{cx-6:.1f}" y="{cy-6:.1f}" width="12" height="12" transform="rotate({k*45} {cx:.1f} {cy:.1f})"/>'
    return f'<g fill="{GOLD}" stroke="none">{teeth}<path fill-rule="evenodd" d="{outer} {hole}"/></g>'
def i_flame():
    return f'<g fill="{GOLD}" stroke="none"><path d="M50,14 C58,32 72,40 66,60 a17,17 0 1 1 -32,0 C30,50 42,44 50,14 Z"/></g>'
def i_bug():
    legs=''.join(f'<line x1="{x1}" y1="{y1}" x2="{x2}" y2="{y2}" stroke="{GOLD}" stroke-width="3.5" stroke-linecap="round"/>' for (x1,y1,x2,y2) in [(32,46,18,38),(32,56,16,56),(32,66,18,76),(68,46,82,38),(68,56,84,56),(68,66,82,76)])
    body='M50,32 a20,26 0 1 0 0.1,0 z'; split='M48.5,34 h3 v50 h-3 z'
    return f'<g stroke="none">{legs}<circle cx="50" cy="26" r="8" fill="{GOLD}"/><path fill-rule="evenodd" d="{body} {split}" fill="{GOLD}"/></g>'
def i_pot():
    return f'<g fill="{GOLD}" stroke="none"><rect x="27" y="38" width="46" height="9" rx="2"/><polygon points="31,49 69,49 63,86 37,86"/></g>'
def i_scissors():
    return f'''<g fill="none" stroke="{GOLD}" stroke-width="6" stroke-linecap="round"><line x1="40" y1="74" x2="70" y2="26"/><line x1="60" y1="74" x2="30" y2="26"/></g>
<g fill="none" stroke="{GOLD}" stroke-width="5"><circle cx="38" cy="78" r="8"/><circle cx="62" cy="78" r="8"/></g><circle cx="50" cy="52" r="3.5" fill="{GOLD}"/>'''
def i_glove():
    return f'''<g fill="{GOLD}" stroke="none"><rect x="34" y="30" width="7" height="20" rx="3.5"/><rect x="43" y="24" width="7" height="26" rx="3.5"/><rect x="52" y="26" width="7" height="24" rx="3.5"/><rect x="61" y="30" width="7" height="20" rx="3.5"/>
<rect x="32" y="44" width="36" height="40" rx="9"/><rect x="22" y="50" width="12" height="22" rx="6" transform="rotate(-18 28 61)"/></g>'''
def i_gauge():
    return f'''<g fill="none" stroke="{GOLD}" stroke-width="6" stroke-linecap="round"><path d="M24,64 A26,26 0 0 1 76,64"/></g>
<line x1="50" y1="64" x2="66" y2="44" stroke="{GOLD}" stroke-width="4" stroke-linecap="round"/><circle cx="50" cy="64" r="5" fill="{GOLD}"/><rect x="34" y="72" width="32" height="6" rx="3" fill="{GOLD}"/>'''
def i_bong():
    base='M50,54 L68,84 Q68,88 62,88 H38 Q32,88 32,84 Z'
    return f'<g fill="{GOLD}" stroke="none"><rect x="40" y="14" width="20" height="6" rx="3"/><rect x="45" y="18" width="10" height="40"/><path d="{base}"/><rect x="53" y="40" width="22" height="6" rx="3" transform="rotate(35 53 40)"/></g>'
def i_pipe():
    return f'''<g fill="{GOLD}" stroke="none"><rect x="16" y="42" width="18" height="26" rx="4"/></g>
<path d="M20,66 Q60,80 84,64" fill="none" stroke="{GOLD}" stroke-width="8" stroke-linecap="round"/>'''
def i_vape():
    return f'<g fill="{GOLD}" stroke="none"><rect x="44" y="10" width="12" height="8" rx="3"/><rect x="41" y="18" width="18" height="70" rx="9"/></g><circle cx="50" cy="48" r="3.2" fill="{G}"/>'
def i_joint():
    return f'''<g transform="rotate(-32 50 50)"><rect x="16" y="46" width="68" height="10" rx="5" fill="{GOLD}"/><rect x="16" y="46" width="16" height="10" rx="5" fill="{GOLD}" fill-opacity="0.55"/></g>
<path d="M74,30 q6,-6 0,-12" fill="none" stroke="{GOLD}" stroke-width="3" stroke-linecap="round" opacity="0.7"/>'''
def i_papers():
    return f'<g fill="{GOLD}" stroke="none"><rect x="44" y="16" width="16" height="18" rx="2" transform="rotate(8 52 25)"/><rect x="28" y="32" width="44" height="44" rx="4"/><rect x="28" y="50" width="44" height="6" fill="{G}"/></g>'
def i_ashtray():
    ring='M50,30 a26,24 0 1 0 0.1,0 z'; hole='M50,40 a16,15 0 1 0 0.1,0 z'
    return f'<g fill="{GOLD}" stroke="none"><path fill-rule="evenodd" d="{ring} {hole}"/><rect x="60" y="24" width="16" height="6" rx="3" transform="rotate(35 60 24)"/></g>'
def i_jar():
    return f'<g fill="{GOLD}" stroke="none"><rect x="28" y="22" width="44" height="12" rx="4"/><rect x="31" y="34" width="38" height="54" rx="9"/></g><rect x="31" y="44" width="38" height="7" fill="{G}"/>'
def i_grinder():
    return f'<g fill="{GOLD}" stroke="none"><rect x="28" y="34" width="44" height="22" rx="7"/><rect x="28" y="58" width="44" height="22" rx="7"/></g><g stroke="{G}" stroke-width="2">'+''.join(f'<line x1="{x}" y1="36" x2="{x}" y2="54"/>' for x in range(34,72,6))+'</g>'
def i_tray():
    outer='M20,40 h60 a8,8 0 0 1 8,8 v20 a8,8 0 0 1 -8,8 h-60 a8,8 0 0 1 -8,-8 v-20 a8,8 0 0 1 8,-8 z'
    inner='M26,48 h48 a3,3 0 0 1 3,3 v14 a3,3 0 0 1 -3,3 h-48 a3,3 0 0 1 -3,-3 v-14 a3,3 0 0 1 3,-3 z'
    return f'<g fill="{GOLD}" stroke="none"><path fill-rule="evenodd" d="{outer} {inner}"/></g>'
def i_lighter():
    return f'<g fill="{GOLD}" stroke="none"><rect x="36" y="40" width="28" height="48" rx="6"/><rect x="40" y="32" width="20" height="10" rx="2"/><path d="M50,14 C54,22 60,26 57,33 a7,7 0 1 1 -14,0 C41,29 46,26 50,14 Z"/></g>'
def i_tube():
    tube='M43,14 h14 v58 a7,7 0 0 1 -14,0 z'
    return f'<g fill="{GOLD}" stroke="none"><path fill-rule="evenodd" d="{tube}"/></g><rect x="40" y="12" width="20" height="5" rx="2" fill="{GOLD}"/><rect x="43" y="46" width="14" height="26" fill="{G}"/><path d="M43,46 h14 v26 a7,7 0 0 1 -14,0 z" fill="none"/>'
def i_scale():
    return f'<g fill="{GOLD}" stroke="none"><rect x="22" y="52" width="56" height="28" rx="6"/><rect x="34" y="44" width="32" height="10" rx="3"/></g><rect x="30" y="60" width="24" height="8" rx="2" fill="{G}"/>'
def i_paw():
    toes=''.join(f'<circle cx="{cx}" cy="{cy}" r="{r}"/>' for (cx,cy,r) in [(34,44,7),(46,36,7.5),(58,36,7.5),(70,44,7)])
    pad='M50,54 C63,54 72,64 72,74 C72,84 60,86 50,86 C40,86 28,84 28,74 C28,64 37,54 50,54 Z'
    return f'<g fill="{GOLD}" stroke="none">{toes}<path d="{pad}"/></g>'
def i_mug():
    return f'''<g fill="{GOLD}" stroke="none"><rect x="28" y="40" width="36" height="44" rx="8"/></g>
<path d="M64,50 h8 a10,10 0 0 1 0,20 h-8" fill="none" stroke="{GOLD}" stroke-width="6"/>
<g fill="none" stroke="{GOLD}" stroke-width="3" stroke-linecap="round" opacity="0.8"><path d="M40,32 q4,-6 0,-12"/><path d="M52,32 q4,-6 0,-12"/></g>'''
def i_shaker():
    holes=''.join(f'<circle cx="{cx}" cy="34" r="2.6"/>' for cx in (44,50,56))
    body='M36,44 h28 v40 a6,6 0 0 1 -6,6 h-16 a6,6 0 0 1 -6,-6 z'
    cap='M40,44 q10,-18 20,0 z'
    return f'<g fill="{GOLD}" stroke="none"><path d="{body}"/><path fill-rule="evenodd" d="{cap}" /></g><g fill="{G}">{holes}</g>'
def i_seed():
    return f'<g transform="rotate(20 50 50)"><ellipse cx="50" cy="50" rx="15" ry="21" fill="{GOLD}"/><path d="M50,32 C44,40 44,60 50,68" fill="none" stroke="{G}" stroke-width="2.5"/></g>'
def i_tshirt():
    return f'<g fill="{GOLD}" stroke="none"><path d="M34,28 L44,22 C46,29 54,29 56,22 L66,28 L80,40 L70,52 L64,46 V84 H36 V46 L30,52 L20,40 Z"/></g>'
def i_percent():
    return f'<text x="50" y="50" text-anchor="middle" dominant-baseline="central" font-family="Arial,DejaVu Sans,sans-serif" font-size="82" font-weight="800" fill="{GOLD}">%</text>'
def i_mystery():
    return i_box()+f'<text x="50" y="58" text-anchor="middle" dominant-baseline="central" font-family="Arial,DejaVu Sans,sans-serif" font-size="30" font-weight="800" fill="{G}">?</text>'

ICONS={
 'leaf':i_leaf,'seedling':i_seedling,'lamp':i_lamp,'bottle':i_bottle,'filter':i_filter,'drop':i_drop,'fan':i_fan,
 'tent':i_tent,'box':i_box,'sack':i_sack,'gear':i_gear,'flame':i_flame,'bug':i_bug,'pot':i_pot,'scissors':i_scissors,
 'glove':i_glove,'gauge':i_gauge,'bong':i_bong,'pipe':i_pipe,'vape':i_vape,'joint':i_joint,'papers':i_papers,
 'ashtray':i_ashtray,'jar':i_jar,'grinder':i_grinder,'tray':i_tray,'lighter':i_lighter,'tube':i_tube,'scale':i_scale,
 'paw':i_paw,'mug':i_mug,'shaker':i_shaker,'seed':i_seed,'tshirt':i_tshirt,'percent':i_percent,'mystery':i_mystery,
}

# category id -> (Display Name, icon)
CATS={
15:("Hanfprodukte","leaf"),532:("Samen","leaf"),548:("Feminisiert","leaf"),547:("Automatisch","leaf"),
549:("Regular","leaf"),2309:("CBD Samen","leaf"),12729:("F1 Samen","leaf"),13458:("Vermehrungsmaterial","seedling"),
12713:("Anzucht","seedling"),538:("Growshop","tent"),531:("Growboxen","tent"),831:("Komplettsets","box"),
844:("LED Growlampen","lamp"),1132:("Dünger","bottle"),4157:("Dünger Sets","bottle"),4235:("Erde & Substrate","sack"),
1401:("Bewässerung","drop"),539:("Lüfter & Filter","fan"),4159:("Ventilatoren","fan"),4160:("Zu- und Abluft","fan"),
4161:("Aktivkohlefilter","filter"),6523:("Growzubehör","gear"),6239:("Beheizung","flame"),6241:("Luftbefeuchter","drop"),
6242:("Luftentfeuchter","drop"),6243:("Controller","gear"),5833:("Schädlingsbekämpfung","bug"),855:("Pflanzentöpfe","pot"),
4225:("Erntescheren","scissors"),6240:("Trimmer","scissors"),5831:("Handschuhe","glove"),13023:("ph-Wert","drop"),
13408:("Messgeräte","gauge"),13416:("Pumpen","drop"),55:("Headshop","bong"),3852:("Bongs","bong"),7048:("Pipes","pipe"),
6862:("Vaporizer","vape"),6864:("Dabbing","flame"),4224:("Blunts","joint"),7645:("Tabakersatz","leaf"),
607:("Papers","papers"),4706:("Pre Rolled Papers","papers"),608:("Filter","filter"),4550:("Aktivkohlefilter","filter"),
4551:("Super Slim 5mm","filter"),4552:("Extra Slim 6 mm","filter"),4553:("Slim 7mm","filter"),4554:("Regular 8-9mm","filter"),
4555:("Konisch/Kegelförmig","filter"),4556:("Big 14mm","filter"),596:("Aschenbecher","ashtray"),598:("Aufbewahrung","jar"),
599:("Kräutermühlen","grinder"),1974:("Rolling Trays","tray"),5423:("Feuerzeuge & Zippo","lighter"),5424:("Zippo","lighter"),
5426:("Feuerzeuge","lighter"),4220:("Feuchtigkeitsregler","drop"),4223:("Terpene","drop"),5181:("Mundstücke","filter"),
5191:("Glas-Tips","filter"),4127:("THC Test","tube"),7042:("Waagen","scale"),1193:("CBD","drop"),46:("CBD Blüten","leaf"),
47:("CBD Öl","drop"),1194:("CBD für Tiere","paw"),51:("Lebensmittel","mug"),4144:("Knabberhanf","leaf"),
4146:("Getränke","mug"),4147:("Hanftee","mug"),4148:("Rohkost","leaf"),4149:("Gewürze","shaker"),4150:("Hanföl","drop"),
4151:("Mehl","sack"),4152:("Hanfsamen","seed"),56:("Pflegeprodukte","jar"),11497:("Merch","tshirt"),
11795:("Angebote","percent"),5818:("Bundles","box"),5819:("Mystery Boxen","mystery"),
}

def icon_slot(inner,cx=400,cy=282,target=150):
    s=target/100.0
    return f'<g transform="translate({cx-target/2},{cy-target/2}) scale({s})">{inner}</g>'

def title_block(title):
    t=title.upper()
    if len(t)<=12: lines=[t]
    elif ' ' in t:
        w=t.split(' '); best=None
        for i in range(1,len(w)):
            l1=' '.join(w[:i]); l2=' '.join(w[i:]); sc=abs(len(l1)-len(l2))
            if best is None or sc<best[0]: best=(sc,l1,l2)
        lines=[best[1],best[2]]
    else: lines=[t]
    ml=max(len(x) for x in lines)
    fs=60 if ml<=8 else 52 if ml<=11 else 42 if ml<=14 else 34
    return lines,fs

def tile(title,icon):
    inner=ICONS[icon]()
    lines,fs=title_block(title)
    wm=leaflets(base=118, Ls=(150,124,92,60), color=CREAM, op="0.055")
    wm=f'<g transform="translate(280,290) scale(2.4)">{wm}</g>'
    if len(lines)==1:
        ty=468; tsvg=f'<text x="400" y="{ty}" text-anchor="middle" fill="{CREAM}" font-size="{fs}" font-weight="800" letter-spacing="3">{lines[0]}</text>'; suby=508
    else:
        ty=446; tsvg=(f'<text x="400" y="{ty}" text-anchor="middle" fill="{CREAM}" font-size="{fs}" font-weight="800" letter-spacing="3">{lines[0]}</text>'
                      f'<text x="400" y="{ty+fs+4}" text-anchor="middle" fill="{CREAM}" font-size="{fs}" font-weight="800" letter-spacing="3">{lines[1]}</text>'); suby=ty+fs+4+30
    return f'''<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
<defs><radialGradient id="g" cx="50%" cy="42%" r="65%"><stop offset="0%" stop-color="#0a5c3f"/><stop offset="100%" stop-color="{G}"/></radialGradient></defs>
<rect width="800" height="800" fill="url(#g)"/>{wm}
<circle cx="400" cy="400" r="300" fill="none" stroke="{GOLD}" stroke-width="2.5"/>
<circle cx="400" cy="400" r="286" fill="none" stroke="{GOLD}" stroke-opacity="0.4" stroke-width="1"/>
{icon_slot(inner)}{tsvg}
<text x="400" y="{suby}" text-anchor="middle" fill="{GOLD}" font-size="18" font-weight="600" letter-spacing="6">&#8226; KATEGORIE &#8226;</text>
<text x="400" y="636" text-anchor="middle" fill="{GOLD}" font-size="23" font-weight="700" letter-spacing="9">HANFJACK</text></svg>'''

def wrap(s): return f'<!doctype html><html><head><meta charset="utf-8"><style>html,body{{margin:0}}text{{font-family:Arial,"DejaVu Sans",sans-serif}}</style></head><body>{s}</body></html>'

def slug(t):
    import re
    t=t.lower().replace('ä','ae').replace('ö','oe').replace('ü','ue').replace('ß','ss').replace('&','und')
    return re.sub(r'[^a-z0-9]+','-',t).strip('-')

if __name__=="__main__":
    mode=sys.argv[1] if len(sys.argv)>1 else "sheet"
    out=sys.argv[2] if len(sys.argv)>2 else "."
    os.makedirs(out,exist_ok=True)
    if mode=="sheet":
        # contact sheet of all distinct icons
        names=list(ICONS.keys()); cols=6
        cell=150; W=cols*cell; import math; rows=math.ceil(len(names)/cols); H=rows*cell
        parts=[f'<rect width="{W}" height="{H}" fill="{G}"/>']
        for idx,nm in enumerate(names):
            r=idx//cols; c=idx%cols; x=c*cell; y=r*cell
            parts.append(f'<g transform="translate({x},{y})"><rect x="4" y="4" width="{cell-8}" height="{cell-8}" fill="none" stroke="{GOLD}" stroke-opacity="0.3"/>')
            parts.append(f'<g transform="translate({(cell-90)/2},18) scale(0.9)">{ICONS[nm]()}</g>')
            parts.append(f'<text x="{cell/2}" y="{cell-14}" text-anchor="middle" fill="{CREAM}" font-family="Arial,DejaVu Sans,sans-serif" font-size="13">{nm}</text></g>')
        svg=f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}">{"".join(parts)}</svg>'
        open(os.path.join(out,"sheet.html"),"w").write(wrap(svg))
        print("SHEET", W, H)
    else:
        # render all tiles
        idx=[]
        for cid,(name,icon) in CATS.items():
            fn=f"{cid}_{slug(name)}"
            open(os.path.join(out,fn+".html"),"w").write(wrap(tile(name,icon)))
            idx.append((cid,name,icon,fn))
        import json; open(os.path.join(out,"index.json"),"w").write(json.dumps(idx,ensure_ascii=False))
        print("TILES", len(idx))
