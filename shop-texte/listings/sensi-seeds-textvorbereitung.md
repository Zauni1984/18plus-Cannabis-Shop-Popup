# Sensi Seeds – Textvorbereitung (100 Produkte)

Stand 2026-09-11. Die Produkte sind gelistet; Beschreibungen, Kurzbeschreibungen,
Tags, Produkteigenschaften und Yoast-SEO fehlen noch.

## Warum die Texte warten

Der Tiger-One-Export liefert für Sensi Seeds fast keine Messwerte:

| Feld | vorhanden |
|---|---|
| Genetik / Kreuzung | 89 / 100 |
| Marketingtext (`alt_description`) | 81 / 100 |
| Ertrag indoor + outdoor | 22 / 100 |
| Wuchshöhe | 20 / 100 |
| Blütezeit | 18 / 100 |
| Aroma | 6 / 100 |
| THC | 3 / 100 |
| CBD | 0 / 100 |

Die Marketingtexte enthalten **keine einzige THC-Prozentangabe** und nur dreimal
eine Blütezeit – aus ihnen lässt sich nichts ableiten.

Da nach unserer Konvention niemals ein Wert erfunden wird, stünde bei 97 von 100
Produkten „keine Angabe des Züchters" beim THC und bei allen 100 beim CBD. Für
die Marke mit den bekanntesten Klassikern (Skunk #1, Northern Lights, Jack Herer,
Hindu Kush, Silver Haze) ist das zu wenig.

**Quelle der Wahl: sensiseeds.com/de** – der Züchter selbst. Die Domain ist in
der Netzwerk-Policy der Arbeitsumgebung gesperrt (Egress-Proxy antwortet 403 auf
CONNECT). Sobald `*.sensiseeds.com` freigegeben und eine neue Session gestartet
ist, werden die Werte pro Sorte dort geholt und als „Angabe Sensi Seeds"
gekennzeichnet.

Vorbereitet liegt `sen_facts.json` im Scratchpad: 100 Faktenblätter mit allen
Exportdaten und leeren Feldern `web_thc`, `web_cbd`, `web_bluete`,
`web_ertrag_in`, `web_ertrag_out`, `web_hoehe`, `web_aroma`, `web_wirkung`,
`web_url` für die Züchterangaben.

## Sortenstruktur

15 Sorten liegen doppelt vor – jeweils als feminisierte und als reguläre Version.
Das ist so gewollt und kein Dublettenproblem:

Afghani #1 · Big Bud · Black Domina · Durban · Early Skunk · Hindu Kush ·
Jack Herer · Jamaican Pearl · Mexican Sativa · Northern Lights · Sensi Skunk ·
Shiva Skunk · Silver Haze · Skunk #1 · Super Skunk

## Zwei offene Datenfragen beim Lieferanten

1. **California Indica vs. Californian Indica** – zwei Produkte mit
   unterschiedlicher Genetikangabe:
   `sensi-cal-indica-fem` = Californian Orange x (Northern Lights #1 x Hash Plant),
   `SEN2312` = Californian Orange x Afghani.
   Entweder zwei verschiedene Sorten oder ein Fehler im Export. Vor der
   Veröffentlichung beim Züchter klären, nicht stillschweigend zusammenführen.

2. **NL#5 x Haze vs. Northern Lights #5 x Haze** – identische Genetikangabe,
   nur abgekürzter Name (`sensi-nl5xh-fem` feminisiert, `SEN2305` regulär).
   Hier sollten die Produktnamen vereinheitlicht werden.

## Sammelpackungen ohne Sortenangabe

`SEN2306` Indoor Mix (25 Stück), `SEN2301` Outdoor Mix (25 Stück),
`SENF0006` Mixed (20 Stück). Für diese drei nennt der Lieferant keine
enthaltenen Sorten – die Texte weisen das wie bei den Sweet-Seeds-Mischpaketen
ausdrücklich aus.
