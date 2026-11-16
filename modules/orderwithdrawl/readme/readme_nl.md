# Herroepingsmodule voor bestellingen in PrestaShop

## Overzicht

De herroepingsmodule stelt uw klanten in staat om hun wettelijk herroepingsrecht voor bestellingen conform de EU-consumentenbeschermingswetten eenvoudig en veilig online uit te oefenen. De module biedt een gebruiksvriendelijke oplossing met meertalige ondersteuning, automatische e-mailmeldingen en flexibele weergaveopties.

### Belangrijkste voordelen

- ✅ **EU-conformiteit** - Voldoet aan de vereisten van EU-richtlijn 2011/83/EU
- ✅ **Eenvoudige bediening** - Duidelijke, intuïtieve formulieren voor uw klanten
- ✅ **Geautomatiseerde processen** - Statuswijzigingen en e-mailmeldingen verlopen automatisch
- ✅ **Flexibele weergave** - Herroepingsknop op meerdere plaatsen tegelijk weergeven
- ✅ **Volledig meertalig** - Ondersteunt 8 talen out-of-the-box
- ✅ **Juridisch veilig** - Geïntegreerde privacy- en herroepingsinformatie

## Functies in detail

### 1. Meerdere weergaveopties

Toon de herroepingsknop op de posities die voor u geschikt zijn:

- **In de footer** - Opvallend als gekleurde knop of subtiel als tekstlink
- **In het klantaccountmenu** - Voor oudere PrestaShop 1.7-thema’s
- **Op de klantaccountpagina** - Moderne tegelweergave voor PrestaShop 8/9

U kunt ook meerdere posities tegelijk activeren, bijvoorbeeld Footer + klantaccountpagina.

![Posities van de herroepingsknop](views/img/de/screen-1-where-the-withdrawal-button-appears.png)

### 2. Individuele aanpassing

Pas de module aan uw huisstijl aan:

- **Vrije keuze knopkleur** - Hex-kleurcode voor perfecte merkafstemming
- **Knop of link** - Kies tussen opvallende knopweergave of subtiele tekstlink
- **Individuele labels** - Definieer eigen knopteksten per taal

![Module-instellingen in de backoffice](views/img/de/screen-2-module-settings-in-the-back-office.png)

### 3. CMS-integratie & juridische informatie

Verbind de module met uw juridische pagina’s:

#### Privacyverklaring
- Link uw privacyverklaring per taal
- Voeg een individuele privacy-opmerking toe (bijv. "Uw gegevens worden uitsluitend gebruikt voor de verwerking van de herroeping")
- De opmerking wordt automatisch weergegeven op de gekoppelde CMS-pagina

#### Herroepingsinstructie
- Link uw herroepingsinstructie per taal
- Voeg een opmerking toe over de elektronische herroepingsknop
- Informeer klanten over de automatische ontvangstbevestiging per e-mail

![Beheer juridische teksten en privacy-opmerkingen](views/img/de/screen-3-manage-legal-texts-and-privacy-notes.png)

### 4. Automatisch bestelstatusbeheer

- Creëert automatisch een bestelstatus **"Herroeping aangevraagd"** met oranje markering
- Wijzigt de bestelstatus direct na succesvolle indiening van een herroeping
- Maakt het eenvoudig om alle herroepingsaanvragen te filteren en te beheren

![Bestelstatus "Herroeping aangevraagd"](views/img/de/screen-5-withdrawal-request-status-in-orders.png)

### 5. Intelligente termijnberekening

De module berekent automatisch de herroepingstermijn:

- **Herroepingstermijn** - Standaard: 14 dagen (volgens EU-richtlijn)
- **Levertijdmarge** - Extra dagen voor levertijd
- **Automatische validatie** - Het systeem controleert of de termijn nog loopt

**Voorbeeld:** Bij 14 dagen herroepingstermijn + 3 dagen levertijdmarge = 17 dagen vanaf besteldatum

### 6. Dubbel e-mailsysteem

#### Klantbevestiging
Uw klanten ontvangen direct na verzending een bevestigingsmail met:
- Bestelreferentie
- E-mailadres
- Datum en tijd van indiening van de herroeping
- Bevestiging van ontvangst op een duurzaam gegevensdrager (conform § 356 BGB)

#### Administrator-melding
U en uw medewerkers ontvangen een melding met:
- Bestelnummer en referentie
- Klantnaam en e-mailadres
- Bestelwaarde
- Tijdstempel van de herroepingsindiening

U kunt meerdere e-mailadressen (kommagescheiden) voor meldingen instellen.

### 7. Meertalige ondersteuning

De module is volledig vertaald voor:
- 🇩🇪 Deutsch
- 🇬🇧 English
- 🇫🇷 Français
- 🇮🇹 Italiano
- 🇪🇸 Español
- 🇵🇱 Polski
- 🇳🇱 Nederlands
- 🇷🇴 Română

Alle teksten, e-mailsjablonen en knoplabels zijn per taal afzonderlijk aanpasbaar.

## Systeemvereisten

- **PrestaShop:** 1.7.8 of hoger (getest met PrestaShop 8.x en 9.x)
- **PHP:** 7.4 of hoger (PHP 8.x ondersteund)
- **MySQL/MariaDB:** 5.6+ / 10.1+
- **SSL-certificaat:** Aanbevolen voor een veilige formulierverzending

## Installatie

### Via PrestaShop Admin-panel (aanbevolen)

1. Download het module-ZIP-bestand
2. Navigeer naar **Modules** → **Modulebeheer**
3. Klik op **Module uploaden**
4. Selecteer het ZIP-bestand en upload dit
5. Klik op **Installeren**

De module maakt bij installatie automatisch aan:
- Bestelstatus "Herroeping aangevraagd" (oranje markering)
- Standaardconfiguratie met 14 dagen herroepingstermijn
- Alle noodzakelijke e-mailsjablonen voor 8 talen

### Na de installatie

1. Klik op **Configureren** in de module
2. Controleer de herroepingstermijn (standaard: 14 dagen)
3. Vul eventueel extra levertijd in
4. Kies waar de herroepingsknop moet verschijnen
5. Pas knopkleur en -tekst naar wens aan
6. Koppel uw CMS-pagina’s (privacy, herroepingsinstructie)
7. Voeg de juridische opmerkingen toe
8. Sla de instellingen op

## Configuratie in detail

### Basisinstellingen

| Instelling | Beschrijving | Standaard |
|------------|--------------|-----------|
| **Herroepingstermijn (dagen)** | Wettelijke herroepingstermijn | 14 dagen |
| **Geschatte levertijd (dagen)** | Marge voor levertijd | 0 dagen |
| **Klantverificatie** | Aanbevolen: naam + bestelkenmerk; optioneel alleen bestelkenmerk; legacy: e-mail + bestelkenmerk | Naam + bestelkenmerk |
| **Beoordelingsmodus** | Handmatige beoordeling of automatische termijncontrole | Handmatige beoordeling |
| **Extra admin-e-mails** | Extra ontvangers voor meldingen (kommagescheiden) | Leeg |

### Weergaveopties

| Instelling | Opties | Beschrijving |
|------------|--------|--------------|
| **Weergavepositie** | Footer / Klantaccountmenu / Klantaccountpagina | Meervoudige selectie mogelijk |
| **Footer-weergave** | Knop / Link | Knop is opvallend, link is subtiel |
| **Knopkleur** | Hex-kleurcode | Standaard: #F44708 (oranje) |

### Per taal configureerbaar

- **Knoplabel** - Individuele tekst voor de herroepingsknop
- **Privacy-pagina** - Gekoppelde CMS-pagina voor privacyverklaring
- **Privacy-opmerking** - Tekst over gegevensverwerking bij herroeping
- **Herroepingspagina** - Gekoppelde CMS-pagina voor herroepingsinstructie
- **Herroepingsopmerking** - Tekst bij de elektronische herroepingsknop


## Gebruik

### Voor uw klanten

1. **Herroepingsknop vinden** - Afhankelijk van de configuratie in de footer, in het klantaccountmenu of op de klantaccountpagina
2. **Formulier openen** - Klik op knop/link opent het herroepingsformulier
3. **Gegevens invoeren:**
   - Geconfigureerd bestelkenmerk (nummer, referentie of beide)
   - Klantnaam in de standaardmodus; e-mailadres alleen in legacy-modus
4. **Herroeping verzenden** - Klik op "Annulering bevestigen"
5. **Bevestiging ontvangen:**
   - Succesmelding op het scherm
   - Bevestigingsmail met bestelkenmerk en opgegeven naam
   - Bestelstatus verandert naar "Herroeping aangevraagd"

![Herroepingsformulier voor klanten](views/img/de/screen-4-order-withdrawal-form.png)

**Belangrijke aanwijzingen voor klanten:**
- Standaard wordt klantnaam + bestelkenmerk gecontroleerd
- Optioneel kan de shop alleen het bestelkenmerk gebruiken of legacy-verificatie met e-mail + bestelkenmerk
- Nieuwe installaties gebruiken handmatige beoordeling; automatische termijncontrole blijft configureerbaar
- Bij verlopen termijn verschijnt een overeenkomstige melding

### Voor u als webshopbeheerder

#### Herroepingsaanvragen verwerken

1. **Melding ontvangen** - U ontvangt een e-mail met alle bestelgegevens
2. **Bestelling zoeken** - Navigeer naar **Bestellingen** → **Bestellingen**
3. **Status controleren** - Bestelling heeft status "Herroeping aangevraagd" (oranje)
4. **Terugbetaling verwerken:**
   - Controleer de herroepingsvoorwaarden
   - Restitueer het bedrag via de betaalmethode
   - Werk de bestelstatus bij (bijv. "Terugbetaald")
5. **Klant informeren** - Informeer de klant over:
   - Wanneer de terugbetaling plaatsvindt
   - Of goederen teruggestuurd moeten worden
   - Verwachte datum van de terugbetaling

#### Alle herroepingsaanvragen weergeven

1. Navigeer naar **Bestellingen** → **Bestellingen**
2. Filter op status: **"Herroeping aangevraagd"**
3. Sorteer op datum om de oudste aanvragen eerst te verwerken

#### E-mailontvangers beheren

Voer in de moduleconfiguratie onder **"Extra admin-e-mails"** extra ontvangers in:

verkoop@uwshop.nl
,retouren@uwshop.nl
,rechten@uwshop.nl


Alle adressen ontvangen gelijktijdig meldingen.


## E-mailsjablonen aanpassen

De e-mailsjablonen vindt u hier:

modules/orderwithdrawl/mails/{taal}/
├── withdrawl_confirm.html (Klantbevestiging - HTML)
├── withdrawl_confirm.txt (Klantbevestiging - Tekst)
├── withdrawl_admin.html (Admin-melding - HTML)
└── withdrawl_admin.txt (Admin-melding - Tekst)


### Beschikbare variabelen

**Klantbevestiging:**
- `{order_id}` - Numeriek bestelnummer
- `{order_reference}` - Bestelreferentie
- `{order_identifier}` - Zichtbaar bestelkenmerk volgens de module-instelling
- `{customer_name}` - Ingevoerde klantnaam
- `{email}` - Klant-e-mailadres
- `{date}` - Tijdstempel

**Admin-melding:**
- `{order_reference}` - Bestelreferentie
- `{order_id}` - Bestelnummer
- `{order_identifier}` - Zichtbaar bestelkenmerk volgens de module-instelling
- `{customer_email}` - Klant-e-mailadres
- `{customer_name}` - Klantnaam
- `{date}` - Tijdstempel
- `{order_total}` - Bestelwaarde (geformatteerd)


## Probleemoplossing

### "Herroepingstermijn is verlopen"

**Oorzaak:** De berekende termijn (besteldatum + levertijd + herroepingsdagen) is overschreden.

**Oplossing:**
- Controleer de instellingen voor herroepingstermijn en levertijd
- Verhoog eventueel de levertijd als pakketten langer onderweg zijn
- Controleer of de besteldatum correct is

### "Naam komt niet overeen met de bestelling"

**Oorzaak:** De ingevoerde naam komt niet overeen met de klantnaam van de bestelling.

**Oplossing:**
- Controleer spelling, voornaam en achternaam
- Indien nodig kan de winkelier overschakelen naar controle alleen met bestelkenmerk
- De oude e-mailcontrole wordt alleen nog gebruikt in de legacy-controlemodus

### "Herroepingsstatus niet geconfigureerd"

**Oorzaak:** De bestelstatus is bij de installatie niet aangemaakt.

**Oplossing:**
1. Deïnstalleer de module
2. Installeer de module opnieuw
3. Controleer onder **Bestellingen** → **Status** of "Herroeping aangevraagd" bestaat

### E-mailmeldingen komen niet aan

**Diagnose:**
1. Controleer **Geavanceerde instellingen** → **E-mail**
2. Verstuur een testmail via PrestaShop
3. Controleer de logs onder `var/logs/`

**Oplossingen:**
- Configureer SMTP in plaats van PHP mail()
- Controleer SPF- en DKIM-records van uw domein
- Controleer de spammap
- Zorg dat de e-mailadressen correct zijn geformatteerd

### Herroepingsknop verschijnt niet in de footer

**Diagnose:**
1. Controleer in de moduleconfiguratie of "Footer" als weergavepositie is geactiveerd
2. Controleer in de broncode van de pagina of het CSS-bestand geladen wordt
3. Open de browserconsole en controleer op fouten

**Oplossingen:**
- Activeer de footer-weergave in de configuratie
- Leeg de cache: **Geavanceerde instellingen** → **Prestaties** → **Cache legen**
- Controleer of uw thema de `displayFooter`-hook ondersteunt


## Juridische aanwijzingen

### Wettelijke basis voor Duitsland

**§ 355 BGB - Herroepingsrecht**
- Consumenten hebben bij overeenkomsten op afstand een herroepingsrecht van 14 dagen
- De termijn begint bij ontvangst van de goederen
- Bij ontbrekende of foutieve herroepingsinstructie wordt de termijn verlengd tot 12 maanden en 14 dagen

**§ 356 BGB - Uitoefening van het herroepingsrecht**
- De herroeping moet in tekstvorm worden verklaard
- Een bevestiging van de herroeping moet op een duurzaam gegevensdrager worden verstrekt
- **Deze module voldoet aan deze eis door de automatische bevestigingsmail**

**§ 312g BGB - Herroepingsrecht bij overeenkomsten op afstand**
- Handelaren moeten informeren over het herroepingsrecht
- Een standaard herroepingsformulier moet beschikbaar worden gesteld
- **Deze module biedt het elektronische herroepingsformulier**

**Art. 246a EGBGB - Informatieplichten**
- Consumenten moeten worden geïnformeerd over de modaliteiten van de uitoefening van het herroepingsrecht
- Dit omvat ook de mogelijkheid van een elektronische herroepingsverklaring

### EU-richtlijn 2011/83/EU

De module voldoet aan de technische vereisten van:

- **Artikel 6(1)(h)** - Informatie over het herroepingsrecht
- **Artikel 11** - Verstrekking van een herroepingsformulier
- **Artikel 14** - Bevestiging op een duurzaam gegevensdrager (e-mail)

### Wat de module biedt

✅ Technisch systeem voor herroepingsaanvragen
✅ E-mailbevestiging op een duurzaam gegevensdrager
✅ Bestelstatus-tracking
✅ Configureerbare herroepingstermijnen

### Wat u als handelaar moet waarborgen

⚠️ Volledige herroepingsinstructie op uw website
⚠️ Retourinstructies voor klanten
⚠️ Terugbetaling binnen 14 dagen na herroeping
⚠️ Correcte documentatie
⚠️ Training van uw medewerkers
⚠️ Naleving van lokale voorschriften

**Belangrijk:** Deze module vervangt geen juridisch advies. Raadpleeg een advocaat om volledige juridische conformiteit te waarborgen.


## Support & Contact

**Technische support:**
- Gebruik het supportkanaal van de marketplace dat aan uw modulebestelling is gekoppeld.

**Voor supportaanvragen hebben wij nodig:**
1. PrestaShop-versie
2. PHP-versie
3. Moduleversie
4. Gedetailleerde foutbeschrijving
5. Stappen om het probleem te reproduceren
6. Foutmeldingen/logs
7. Screenshots (indien relevant)


## Wijzigingslogboek

### Versie 1.4.0 (juni 2026)

**Publieke release:**
- Nieuwe optionele Back Office-instelling om producten uit de bestelling te tonen na controle van de klantgegevens.
- De klant kan de volledige bestelling of afzonderlijke artikelen met een geldige hoeveelheid selecteren.
- De productlijst wordt server-side geladen en gebruikt geen AJAX.
- De optie is standaard uitgeschakeld en bestaande instellingen blijven behouden.
- Geen wijziging aan het databaseschema; compatibiliteit blijft gericht op PrestaShop 1.7.8, 8.x en 9.x met PHP 7.4+.

### Versie 1.3.1 (mei 2026)

**Publieke release:**
- Gestructureerde keuze in de Front Office: volledige bestelling herroepen of geselecteerde artikelen/diensten herroepen.
- Gedeeltelijke herroeping gebruikt herhaalbare regels met artikelnaam, artikelnummer en hoeveelheid.
- Er wordt geen reden voor herroeping gevraagd; de velden dienen alleen om het contract of contractdeel te identificeren.
- Ontvangstbevestigingen voor klant en beheerder bevatten de omvang van de herroeping en de verzonden artikeldetails.
- Details van gedeeltelijke herroeping worden opgeslagen als bestelbericht voor controle in de Back Office.
- Geen wijziging aan het databaseschema; bestaande instellingen blijven behouden.
- Compatibiliteit blijft gericht op PrestaShop 1.7.8, 8.x en 9.x met PHP 7.4+.

### Versie 1.3.0 (mei 2026)

**Publieke release:**
- Handmatige beoordeling is standaard voor nieuwe installaties
- Aanbevolen verificatie met klantnaam + bestelkenmerk
- Optionele modus alleen bestelkenmerk en legacy-modus e-mail + bestelkenmerk
- Het openbare formulier toont geen `<details>`-blok met herroepingsinformatie meer
- Eerdere interne 1.3.x-upgradewerkzaamheden zijn samengevoegd in `upgrade-1.3.0.php`

**Upgrade van 1.2.4 naar 1.3.0:**
- Direct upgradepad van 1.2.4 naar 1.3.0; eerdere interne 1.3.x-wijzigingen zijn gebundeld in deze publieke release.
- Geen wijziging aan het databaseschema; bestaande instellingen van de handelaar blijven behouden.
- Nieuwe ontbrekende standaardwaarden: handmatige beoordeling, verificatie met naam + bestelkenmerk, zoeken op bestelnummer/referentie en actieve PHP-mailfallback.
- Ontvangstbevestigingen bevestigen het herroepingsverzoek en de verzonden gegevens, zonder een definitieve beslissing te suggereren.
- Nieuwe Back Office-opties voor klantverificatie, bestelzoekmodus, e-mailfallback, mailvoorbeeld, testverzending en reset naar standaardwaarden.
- Openbaar formulier versterkt: CSRF-token, honeypot, IP-limiet, dubbele indiening voorkomen en HTML-veilige klantnotities.
- Betrouwbaardere e-mailverzending via centrale MailDispatcher, diagnostiek en PHP-mailfallback.
- E-mailtemplates en vertalingen voltooid voor alle meegeleverde talen.

### Versie 1.0.0 (oktober 2025)

**Nieuwe functies:**
- EU-richtlijn-conform herroepingsbeheer
- Automatische aanmaak van bestelstatus
- Dubbel e-mailmeldingssysteem
- 8 talen vooraf geïnstalleerd
- Flexibele weergaveopties (Footer, klantaccountmenu, klantaccountpagina)
- Knop/Link-toggle voor footer
- Aanpasbare knopkleur (Hex-kleurcode)
- CMS-integratie voor privacy- en herroepingsinformatie
- Per taal configureerbare CMS-pagina’s en opmerkingen
- Configureerbare herroepingstermijn (1-365 dagen)
- Levertijdmarge (0-365 dagen)
- Meerdere admin-e-mailontvangers
- Modern shadcn-geïnspireerd admin-interface
- PrestaShop 8/9-compatibiliteit
- Locale-conforme prijsweergave
- SSL-beveiligde formulieren
- SEO-vriendelijke URL’s


## Licentie

**Academic Free License 3.0 (AFL-3.0)**

Copyright © 2025 sjmedia GmbH

Deze module is vrije software: u kunt deze verspreiden en/of aanpassen onder de voorwaarden van de Academic Free License.

Deze module wordt verspreid in de hoop dat hij nuttig is, maar ZONDER ENIGE GARANTIE. Zie de Academic Free License voor meer details.

Een kopie van de licentie zou bij deze module geleverd moeten zijn. Indien niet, bezoek dan:
http://opensource.org/licenses/afl-3.0.php

---

**Disclaimer**

Deze module biedt technische functionaliteit ter ondersteuning van EU-conforme herroepingsaanvragen. Hoewel hij ontworpen is om te voldoen aan de vereisten van EU-richtlijn 2011/83/EU, **blijft de handelaar als enige verantwoordelijk voor het waarborgen van volledige juridische conformiteit** met alle toepasselijke wetten en voorschriften in zijn rechtsgebied.

**Gebruik op eigen risico.** De auteurs en rechthebbenden zijn niet aansprakelijk voor juridische geschillen, overtredingen van voorschriften of schade die voortvloeit uit het gebruik van deze module.

---

*Laatst bijgewerkt: 2 juni 2026*
*Moduleversie: 1.4.0*
*Documentatie: 1.4.0*
