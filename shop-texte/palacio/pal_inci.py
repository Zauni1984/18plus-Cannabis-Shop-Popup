# -*- coding: utf-8 -*-
"""INCI-Listen von Palacio aufbereiten: bereinigen und ins Deutsche uebersetzen.

Die Rohfelder aus der Palacio-API enthalten teils ein zweites, mit
"Extension" eingeleitetes Listenfeld. Das gehoert zu einer anderen Variante
und wird abgeschnitten - gemischt waere die Liste falsch.

Botanische Gattungsnamen bleiben im INCI-Block stehen; die deutsche Liste
nennt den gaengigen deutschen Namen. Unbekannte Begriffe bleiben unveraendert
und werden beim Lauf gemeldet, damit nichts stillschweigend verschwindet.
"""
import re

DE = {
 'Abies Alba Needle Oil': 'Weißtannennadelöl',
 'Achillea Millefolium Extract': 'Schafgarbenextrakt',
 'Achillea Millefolium Flower Extract': 'Schafgarbenblütenextrakt',
 'Achillea Millefolium Stem Extract': 'Schafgarben-Stängelextrakt',
 'Aesculus Hippocastanum Extract': 'Rosskastanienextrakt',
 'Aesculus Hippocastanum Fruit Extract': 'Rosskastanien-Fruchtextrakt',
 'Aesculus Hippocastanum Seed Extract': 'Rosskastanien-Samenextrakt',
 'Agrimonia Eupatoria Flower Extract': 'Odermennig-Blütenextrakt',
 'Alcohol Denat.': 'Vergällter Alkohol',
 'Aloe Barbadensis Leaf Extract': 'Aloe-vera-Blattextrakt',
 'Alpha-Isomethyl Ionone': 'Alpha-Isomethylionon',
 'Alpha-Terpinene': 'Alpha-Terpinen',
 'Aqua': 'Wasser',
 'Arnica Montana Flower Extract': 'Arnikablütenextrakt',
 'Aroma': 'Aroma',
 'Bacillus/Soybean Ferment Extract': 'Bacillus-/Sojabohnen-Fermentextrakt',
 'Bellis Perennis Flower Extract': 'Gänseblümchen-Blütenextrakt',
 'Benzaldehyde': 'Benzaldehyd',
 'Benzoic Acid': 'Benzoesäure',
 'Benzyl Benzoate': 'Benzylbenzoat',
 'Beta-Carotene': 'Beta-Carotin',
 'Beta-Caryophyllene': 'Beta-Caryophyllen',
 'Betula Alba Leaf Extract': 'Birkenblattextrakt',
 'Butylene Glycol': 'Butylenglycol',
 'CO2': 'Kohlensäure',
 'Calcium Carbonate': 'Calciumcarbonat',
 'Calendula Officialis Flower Extract': 'Ringelblumenblütenextrakt',
 'Calendula Officinalis Flower Extract': 'Ringelblumenblütenextrakt',
 'Camellia Sinensis Leaf Extract': 'Grüntee-Blattextrakt',
 'Camphor': 'Campher',
 'Cannabidiol': 'Cannabidiol (CBD)',
 'Cannabis Sativa Seed Extract': 'Hanfsamenextrakt',
 'Cannabis Sativa Seed Oil': 'Hanfsamenöl',
 'Cannabis Sativa Seed Oil Glycereth-8 Esters': 'Hanfsamenöl-Glycereth-8-Ester',
 'Caprylic/Capric Triglyceride': 'Caprylo-/Caprinsäure-Triglycerid',
 'Capsicum Frutescens Resin': 'Cayennepfeffer-Harz',
 'Caramel': 'Zuckerkulör',
 'Carbomer': 'Carbomer',
 'Carvone': 'Carvon',
 'Ceteareth-20': 'Ceteareth-20',
 'Cetearyl Alcohol': 'Cetearylalkohol',
 'Cetrimonium Chloride': 'Cetrimoniumchlorid',
 'Chamomilla Recutita Flower Extract': 'Kamillenblütenextrakt',
 'Charcoal Powder': 'Aktivkohlepulver',
 'Cinnamal': 'Zimtaldehyd',
 'Cinnamomum Cassia Leaf Oil': 'Zimtkassie-Blattöl',
 'Citral': 'Citral',
 'Citric Acid': 'Zitronensäure',
 'Citronellol': 'Citronellol',
 'Citrus Aurantium Peel Oil': 'Bitterorangenschalenöl',
 'Citrus Limon Peel Oil': 'Zitronenschalenöl',
 'Citrus Sinensis Peel Oil Expressed': 'Orangenschalenöl (kaltgepresst)',
 'Cocamide DEA': 'Cocamid DEA',
 'Cocamidopropyl Betaine': 'Cocamidopropylbetain',
 'Coumarin': 'Cumarin',
 'Dehydroacetic Acid': 'Dehydracetsäure',
 'Denatonium Benzoate': 'Denatoniumbenzoat',
 'Echinacea Purpurea Flower/Leaf/Stem Extract': 'Sonnenhut-Extrakt (Blüte/Blatt/Stängel)',
 'Equisetum Arvense Extract': 'Ackerschachtelhalmextrakt',
 'Equisetum Arvense Stem Extract': 'Ackerschachtelhalm-Stängelextrakt',
 'Ethyl Ferulate': 'Ethylferulat',
 'Ethylhexylglycerin': 'Ethylhexylglycerin',
 'Eucalyptus Globulus Leaf Oil': 'Eukalyptusblattöl',
 'Eucalyptus Globulus Oil': 'Eukalyptusöl',
 'Eugenia Caryophyllus Leaf Oil': 'Gewürznelkenblattöl',
 'Eugenia Caryophyllus Oil': 'Gewürznelkenöl',
 'Eugenol': 'Eugenol',
 'Eugenyl Acetate': 'Eugenylacetat',
 'Euphrasia Officinalis Extract': 'Augentrostextrakt',
 'Euphrasia Officinalis Stem Extract': 'Augentrost-Stängelextrakt',
 'Gamma-Terpinene': 'Gamma-Terpinen',
 'Gentiana Lutea Root Extract': 'Gelber-Enzian-Wurzelextrakt',
 'Geraniol': 'Geraniol',
 'Gingko Biloba Leaf Extract': 'Ginkgoblattextrakt',
 'Glycerin': 'Glycerin',
 'Glycol': 'Glycol',
 'Glyceryl Oleate': 'Glyceryloleat',
 'Glyceryl Stearate': 'Glycerylstearat',
 'Helianthus Annuus Seed Oil': 'Sonnenblumenöl',
 'Hexyl Cinnamal': 'Hexylzimtaldehyd',
 'Humulus Lupulus Extract': 'Hopfenextrakt',
 'Hydrogenated Coconut Oil': 'Gehärtetes Kokosöl',
 'Hydroxyapatite': 'Hydroxylapatit',
 'Isopropyl Alcohol': 'Isopropylalkohol',
 'Juniperus Communis Fruit Extract': 'Wacholderbeerextrakt',
 'Lactic Acid': 'Milchsäure',
 'Lamium Album Flower/Leaf/Stem Extract': 'Weiße-Taubnessel-Extrakt (Blüte/Blatt/Stängel)',
 'Lamium Album Stem Extract': 'Weiße-Taubnessel-Stängelextrakt',
 'Lanolin': 'Wollwachs (Lanolin)',
 'Lauric Acid': 'Laurinsäure',
 'Lauryl Glucoside': 'Laurylglucosid',
 'Lavandula Angustifolia Flower Extract': 'Lavendelblütenextrakt',
 'Lavandula Angustifolia Herb Oil': 'Lavendelöl',
 'Limonene': 'Limonen',
 'Linalool': 'Linalool',
 'Linalyl Acetate': 'Linalylacetat',
 'Linaria Vulgaris Extract': 'Leinkraut-Extrakt',
 'Linaria Vulgaris Stem Extract': 'Leinkraut-Stängelextrakt',
 'MEK': 'Methylethylketon',
 'Malus Domestica Fruit Water': 'Apfelfruchtwasser',
 'Malva Mauritiana Leaf Extract': 'Mauretanische-Malve-Blattextrakt',
 'Malva Sylvestris Leaf Extract': 'Wilde-Malve-Blattextrakt',
 'Melaleuca Leucadendron Cajuputi Leaf Oil': 'Cajeputöl',
 'Mentha Arvensis Herb Oil': 'Ackerminzenöl',
 'Mentha Piperita Herb Oil': 'Pfefferminzkrautöl',
 'Mentha Piperita Oil': 'Pfefferminzöl',
 'Mentha Spicata Leaf Extract': 'Grüne-Minze-Blattextrakt',
 'Menthol': 'Menthol',
 'Methyl Salicylate': 'Methylsalicylat',
 'Methylchloroisothiazolinone': 'Methylchlorisothiazolinon',
 'Methylisothiazolinone': 'Methylisothiazolinon',
 'Olea Europaea Fruit Oil': 'Olivenöl',
 'PEG-40 Hydrogenated Castor Oil': 'PEG-40 hydriertes Rizinusöl',
 'PEG-7 Glyceryl Cocoate': 'PEG-7 Glycerylcocoat',
 'Panthenol': 'Panthenol',
 'Paraffin': 'Paraffin',
 'Paraffinum Liquidum': 'Dickflüssiges Paraffin',
 'Parfum': 'Parfüm',
 'Pentylene Glycol': 'Pentylenglycol',
 'Petrolatum': 'Vaseline',
 'Phenoxyethanol': 'Phenoxyethanol',
 'Pimpinella Anisum Fruit Oil': 'Anisöl',
 'Pinene': 'Pinen',
 'Pinus Sylvestris Leaf Oil': 'Kiefernnadelöl',
 'Pinus Sylvestris Oil': 'Kiefernöl',
 'Plantago Lanceolata Leaf Extract': 'Spitzwegerich-Blattextrakt',
 'Polyquaternium-7': 'Polyquaternium-7',
 'Potassium Sorbate': 'Kaliumsorbat',
 'Potentilla Anserina Extract': 'Gänsefingerkraut-Extrakt',
 'Potentilla Anserina Stem Extract': 'Gänsefingerkraut-Stängelextrakt',
 'Propylene Glycol': 'Propylenglycol',
 'Prunus Amygdalus Dulcis Oil': 'Süßmandelöl',
 'Rosmarinus Officinalis Leaf Extract': 'Rosmarinblattextrakt',
 'Rosmarinus Officinalis Leaf Oil': 'Rosmarinblattöl',
 'Saccharin': 'Saccharin',
 'Salicylaldehyde': 'Salicylaldehyd',
 'Salvia Officinalis Leaf Extract': 'Salbeiblattextrakt',
 'Salvia Officinalis Oil': 'Salbeiöl',
 'Sambucus Nigra Flower Extract': 'Holunderblütenextrakt',
 'Scutellaria Baicalensis Leaf Extract': 'Baikal-Helmkraut-Blattextrakt',
 'Sodium Benzoate': 'Natriumbenzoat',
 'Sodium Chloride': 'Natriumchlorid',
 'Sodium Citrate': 'Natriumcitrat',
 'Sodium Hyaluronate': 'Natriumhyaluronat',
 'Sodium Hydroxide': 'Natriumhydroxid',
 'Sodium Laurate': 'Natriumlaurat',
 'Sodium Laureth Sulfate': 'Natriumlaurethsulfat',
 'Sodium Lauryl Sulfate': 'Natriumlaurylsulfat',
 'Sodium Saccharin': 'Natriumsaccharin',
 'Sodium Stearate': 'Natriumstearat',
 'Sodium Sulfate': 'Natriumsulfat',
 'Soluble Collagen': 'Lösliches Kollagen',
 'Sorbitol': 'Sorbit',
 'Stearic Acid': 'Stearinsäure',
 'Styrene/Acrylates Copolymer': 'Styrol-/Acrylat-Copolymer',
 'Taraxacum Officinale Leaf Extract': 'Löwenzahnblattextrakt',
 'Terpineol': 'Terpineol',
 'Terpinolene': 'Terpinolen',
 'Tetrasodium Etidronate': 'Tetranatriumetidronat',
 'Thymus Vulgaris Extract': 'Thymianextrakt',
 'Tilia Cordata Flower Extract': 'Winterlindenblütenextrakt',
 'Tilia Platyphyllos Flower Extract': 'Sommerlindenblütenextrakt',
 'Tocopherol': 'Tocopherol (Vitamin E)',
 'Tocopheryl Acetate': 'Tocopherylacetat',
 'Triethanolamine': 'Triethanolamin',
 'Triethylene Glycol': 'Triethylenglycol',
 'Urtica Dioica Leaf Extract': 'Brennnesselblattextrakt',
 'Veronica Officinalis Flower/Leaf/Stem Extract': 'Ehrenpreis-Extrakt (Blüte/Blatt/Stängel)',
 'Veronica Officinalis Stem Extract': 'Ehrenpreis-Stängelextrakt',
 'Viola Tricolor Extract': 'Stiefmütterchenextrakt',
 'Viola Tricolor Stem Extract': 'Stiefmütterchen-Stängelextrakt',
 'Vitis Vinifera Seed Oil': 'Traubenkernöl',
 'Xanthan Gum': 'Xanthan',
 'Zea Mays Starch': 'Maisstärke',
 'Zinc Citrate': 'Zinkcitrat',
 'hemp extract': 'Hanfextrakt',
 'Muscat Morio 60%': 'Muscat Morio 60 %',
 'Welschriesling 40%': 'Welschriesling 40 %',
}

def _norm(s):
    s = re.sub(r'\s+', ' ', s).strip().strip('.').strip()
    if s.lower() == 'alcohol denat':
        return 'Alcohol Denat.'
    return s

def zerlegen(roh):
    """Rohfeld -> Liste der INCI-Begriffe, Extension-Bloecke abgeschnitten."""
    t = roh.replace('\r\n', ' ').replace('\n', ' ')
    t = re.split(r'\bExtension\b', t)[0]
    t = re.sub(r'^\s*Ingredients\s*:\s*', '', t, flags=re.I)
    teile, gesehen = [], set()
    for s in t.split(','):
        s = _norm(s)
        if not s:
            continue
        k = s.lower()
        if k in gesehen:          # Palacio doppelt gelegentlich Begriffe
            continue
        gesehen.add(k)
        teile.append(s)
    return teile

_DE_KLEIN = {k.lower(): v for k, v in DE.items()}

def deutsch(teile):
    """(deutsche Liste, unbekannte Begriffe)"""
    raus, offen = [], []
    for s in teile:
        if s in DE:
            raus.append(DE[s])
        elif s.lower() in _DE_KLEIN:      # Palacio schreibt mal gross, mal klein
            raus.append(_DE_KLEIN[s.lower()])
        elif re.fullmatch(r'\+?/?-?\s*CI\s*\d+', s):
            raus.append('Farbstoff ' + re.sub(r'^[+/-]*\s*', '', s))
        else:
            raus.append(s)
            offen.append(s)
    return raus, offen

def block(roh):
    """HTML-Abschnitt Inhaltsstoffe: deutsch kommagetrennt plus INCI."""
    teile = zerlegen(roh)
    de, offen = deutsch(teile)
    html = ('<h3 style="margin-top:1.8em">Inhaltsstoffe</h3>\n'
            '<p>' + ', '.join(de) + '.</p>\n'
            '<p><strong>INCI:</strong><br />' + ', '.join(teile) + '.</p>')
    return html, offen
