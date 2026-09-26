import asyncio, sys
from playwright.async_api import async_playwright

CSS = open(sys.argv[1]).read() if len(sys.argv) > 1 else ''

async def main():
    async with async_playwright() as p:
        b = await p.chromium.launch(executable_path='/opt/pw-browsers/chromium-1194/chrome-linux/chrome', args=['--no-sandbox','--ignore-certificate-errors-spki-list=KnP1OnzHv/y42eRQmbGwoYTHcSJF448m6CU5mdngwKk='])
        ctx = await b.new_context(viewport={'width':390,'height':844}, device_scale_factor=2,
            user_agent='Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            is_mobile=True, has_touch=True, locale='de-DE')
        pg = await ctx.new_page()
        import json as _j
        await ctx.add_cookies(_j.load(open('kasse_cookies.json')))
        await pg.goto('https://hanfjack.de/kasse/', wait_until='networkidle', timeout=150000)
        await pg.wait_for_timeout(2000)
        tab = pg.locator('table.woocommerce-checkout-review-order-table')
        print('Tabelle sichtbar:', await tab.count())
        if await tab.count():
            box = await tab.bounding_box()
            print('Tabellenbreite:', box and round(box['width']), 'Hoehe:', box and round(box['height']), 'x:', box and round(box['x']))
            breite = await pg.evaluate("""() => {
                const t = document.querySelector('table.woocommerce-checkout-review-order-table');
                const r = document.documentElement;
                return {tabelle: t.scrollWidth, sichtbar: t.clientWidth,
                        seite: r.scrollWidth, fenster: r.clientWidth};
            }""")
            print('Breiten:', breite)
            await tab.screenshot(path='kasse_vorher.png')
        if CSS:
            await pg.add_style_tag(content=CSS)
            await pg.wait_for_timeout(800)
            box = await tab.bounding_box()
            print('nachher Breite:', box and round(box['width']), 'Hoehe:', box and round(box['height']))
            breite = await pg.evaluate("""() => {
                const t = document.querySelector('table.woocommerce-checkout-review-order-table');
                const r = document.documentElement;
                return {tabelle: t.scrollWidth, sichtbar: t.clientWidth, seite: r.scrollWidth, fenster: r.clientWidth};
            }""")
            print('Breiten nachher:', breite)
            await tab.screenshot(path='kasse_nachher.png')
        await b.close()

asyncio.run(main())
