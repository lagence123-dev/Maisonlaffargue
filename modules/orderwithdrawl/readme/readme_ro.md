# Modul de Retragere Comandă pentru PrestaShop

## Prezentare Generală

Modulul de Retragere Comandă permite clienților dvs. să își exercite cu ușurință și în siguranță dreptul legal de retragere pentru comenzi, în conformitate cu legile europene de protecție a consumatorilor. Modulul oferă o soluție prietenoasă cu suport multilingv, notificări automate prin e-mail și opțiuni flexibile de afișare.

### Avantaje Principale

- ✅ **Conform UE** - Îndeplinește cerințele Directivei 2011/83/UE
- ✅ **Ușor de Utilizat** - Formulare clare și intuitive pentru clienții dvs.
- ✅ **Procese Automatizate** - Modificările de status și notificările prin e-mail se fac automat
- ✅ **Afișare Flexibilă** - Afișați butonul de retragere în mai multe locuri simultan
- ✅ **Complet Multilingv** - Suportă 8 limbi gata de utilizare
- ✅ **Securitate Juridică** - Notificări de confidențialitate și retragere integrate

## Funcționalități în Detaliu

### 1. Opțiuni Multiple de Afișare

Afișați butonul de retragere în locurile care vi se potrivesc cel mai bine:

- **În Subsol** - Proeminent ca buton colorat sau discret ca link text
- **În Meniul Contului Client** - Pentru temele mai vechi PrestaShop 1.7
- **Pe Pagina Contului Client** - Afișare modernă în card pentru PrestaShop 8/9

De asemenea, puteți activa mai multe poziții simultan, de exemplu Subsol + Pagina contului client.

![Poziții Buton de Retragere](views/img/ro/screen-1-where-the-withdrawal-button-appears.png)

### 2. Personalizare Individuală

Adaptați modulul la designul corporativ:

- **Culoare Buton la Alegere** - Cod culoare hexazecimal pentru o aliniere perfectă cu brandul
- **Buton sau Link** - Alegeți între afișare de buton proeminent sau link text discret
- **Etichete Individuale** - Definiți propriile texte de buton pe limbă

![Setări Modul în Backend](views/img/ro/screen-2-module-settings-in-the-back-office.png)

### 3. Integrare CMS și Notificări Juridice

Conectați modulul cu paginile dvs. juridice:

#### Notificare de Confidențialitate
- Conectați politica de confidențialitate pe limbă
- Adăugați o notificare de confidențialitate personalizată (de exemplu, "Datele dvs. vor fi folosite doar pentru procesarea retragerii")
- Notificarea este afișată automat pe pagina CMS conectată

#### Informații de Retragere
- Conectați politica de retragere pe limbă
- Adăugați o notificare despre butonul de retragere electronic
- Informați clienții despre confirmarea automată de primire prin e-mail

![Gestionare Texte Juridice și Notificări de Confidențialitate](views/img/ro/screen-3-manage-legal-texts-and-privacy-notes.png)

### 4. Gestionare Automată a Statusului Comenzii

- Creează automat un status de comandă **"Retragere Solicitată"** cu marcare portocalie
- Schimbă statusul comenzii imediat după trimiterea cu succes a retragerii
- Permite filtrarea și gestionarea ușoară a tuturor cererilor de retragere

![Status Comandă "Retragere Solicitată"](views/img/ro/screen-5-withdrawal-request-status-in-orders.png)

### 5. Calcul Inteligent al Perioadei

Modulul calculează automat perioada de retragere:

- **Perioadă de Retragere** - Implicit: 14 zile (conform directivei UE)
- **Buffer de Livrare** - Zile suplimentare pentru timpul de livrare
- **Validare Automată** - Sistemul verifică dacă perioada este încă activă

**Exemplu:** Cu 14 zile perioadă de retragere + 3 zile buffer de livrare = 17 zile de la data comenzii

### 6. Sistem Dublu de E-mail

#### Confirmare Client
Clienții dvs. primesc un e-mail de confirmare imediat după trimitere cu:
- Referință comandă
- Adresă e-mail
- Data și ora trimiterii retragerii
- Confirmarea primirii pe suport durabil (conform legii europene)

#### Notificare Administrator
Dvs. și personalul dvs. primiți o notificare cu:
- Număr comandă și referință
- Nume client și adresă e-mail
- Valoare comandă
- Timestamp trimitere retragere

Puteți înregistra mai multe adrese de e-mail (separate prin virgule) pentru notificări.

### 7. Suport Multilingv

Modulul este complet tradus pentru:
- 🇩🇪 Deutsch
- 🇬🇧 English
- 🇫🇷 Français
- 🇮🇹 Italiano
- 🇪🇸 Español
- 🇵🇱 Polski
- 🇳🇱 Nederlands
- 🇷🇴 Română

Toate textele, șabloanele de e-mail și etichetele de butoane pot fi personalizate individual pentru fiecare limbă.

## Cerințe de Sistem

- **PrestaShop:** 1.7.8 sau superior (testat cu PrestaShop 8.x și 9.x)
- **PHP:** 7.4 sau superior (PHP 8.x acceptat)
- **MySQL/MariaDB:** 5.6+ / 10.1+
- **Certificat SSL:** Recomandat pentru trimiterea securizată a formularelor

## Instalare

### Prin Panoul de Administrare PrestaShop (recomandat)

1. Descărcați fișierul ZIP al modulului
2. Navigați la **Module** → **Manager Module**
3. Faceți clic pe **Încarcă un modul**
4. Selectați fișierul ZIP și încărcați-l
5. Faceți clic pe **Instalare**

Modulul creează automat în timpul instalării:
- Status comandă "Retragere Solicitată" (marcare portocalie)
- Configurare implicită cu 14 zile perioadă de retragere
- Toate șabloanele de e-mail necesare pentru 8 limbi

### După Instalare

1. Faceți clic pe **Configurare** în modul
2. Verificați perioada de retragere (implicit: 14 zile)
3. Introduceți zile de livrare suplimentare dacă este necesar
4. Selectați unde ar trebui să apară butonul de retragere
5. Personalizați culoarea și textul butonului după preferință
6. Conectați paginile dvs. CMS (politică de confidențialitate, informații de retragere)
7. Adăugați textele notificărilor juridice
8. Salvați configurația

## Configurare în Detaliu

### Setări de Bază

| Setare | Descriere | Implicit |
|--------|-----------|----------|
| **Perioadă de Retragere (zile)** | Perioadă legală de retragere | 14 zile |
| **Timp Estimat de Livrare (zile)** | Buffer pentru timp de livrare | 0 zile |
| **Verificare Client** | Recomandat: nume + identificator comandă; opțional doar identificator; legacy: e-mail + identificator | Nume + identificator |
| **Mod de Evaluare** | Revizuire manuală sau verificare automată a perioadei | Revizuire manuală |
| **E-mailuri Administrator Suplimentare** | Destinatari suplimentari pentru notificări (separați prin virgule) | Gol |

### Opțiuni de Afișare

| Setare | Opțiuni | Descriere |
|--------|---------|-----------|
| **Poziție de Afișare** | Subsol / Meniu cont / Pagină cont | Selecție multiplă posibilă |
| **Afișare Subsol** | Buton / Link | Butonul este proeminent, linkul este discret |
| **Culoare Buton** | Cod culoare hexazecimal | Implicit: #F44708 (Portocaliu) |

### Configurabil pe Limbă

- **Etichetă Buton** - Text individual pentru butonul de retragere
- **Pagină Confidențialitate** - Pagină CMS conectată pentru politica de confidențialitate
- **Notificare Confidențialitate** - Text notificare despre procesarea datelor în timpul retragerii
- **Pagină Retragere** - Pagină CMS conectată pentru informații de retragere
- **Notificare Retragere** - Text notificare despre butonul de retragere electronic

## Utilizare

### Pentru Clienții Dvs.

1. **Găsiți Butonul de Retragere** - În funcție de configurare, în subsol, meniul contului sau pagina contului
2. **Deschideți Formularul** - Faceți clic pe buton/link pentru a deschide formularul de retragere
3. **Introduceți Datele:**
   - Identificatorul comenzii configurat (număr, referință sau ambele)
   - Numele clientului în modul implicit; e-mail doar în modul legacy
4. **Trimiteți retragerea** - Faceți clic pe "Confirmare retragere"
5. **Primiți Confirmarea:**
   - Mesaj de succes pe ecran
   - E-mail de confirmare cu identificator comandă și numele trimis
   - Statusul comenzii se schimbă în "Retragere Solicitată"

![Formular de Retragere pentru Clienți](views/img/ro/screen-4-order-withdrawal-form.png)

**Note Importante pentru Clienți:**
- Implicit se verifică numele clientului + identificatorul comenzii
- Opțional, magazinul poate folosi doar identificatorul sau modul legacy cu e-mail + identificator
- Instalările noi folosesc revizuire manuală; verificarea automată a perioadei rămâne configurabilă
- Dacă perioada a expirat, se afișează un mesaj corespunzător

### Pentru Dvs. ca Proprietar de Magazin

#### Procesarea Cererilor de Retragere

1. **Primiți Notificarea** - Primiți un e-mail cu toate detaliile comenzii
2. **Căutați Comanda** - Navigați la **Comenzi** → **Comenzi**
3. **Verificați Statusul** - Comanda are statusul "Retragere Solicitată" (portocaliu)
4. **Procesați Rambursarea:**
   - Verificați condițiile de retragere
   - Rambursați suma prin metoda de plată
   - Actualizați statusul comenzii (de exemplu, "Rambursat")
5. **Informați Clientul** - Informați clientul:
   - Când va avea loc rambursarea
   - Dacă bunurile trebuie returnate
   - Data prevăzută a creditului

#### Afișați Toate Cererile de Retragere

1. Navigați la **Comenzi** → **Comenzi**
2. Filtrați după status: **"Retragere Solicitată"**
3. Sortați după dată pentru a procesa mai întâi cererile mai vechi

#### Gestionați Destinatarii de E-mail

Introduceți destinatari suplimentari în configurarea modulului la **"E-mailuri Administrator Suplimentare"**:

```
vanzari@magazinuldvs.ro,returnari@magazinuldvs.ro,juridic@magazinuldvs.ro
```

Toate adresele primesc notificări paralele.

## Personalizarea Șabloanelor de E-mail

Șabloanele de e-mail se găsesc aici:
```
modules/orderwithdrawl/mails/{limba}/
├── withdrawl_confirm.html    (Confirmare client - HTML)
├── withdrawl_confirm.txt      (Confirmare client - Text)
├── withdrawl_admin.html       (Notificare administrator - HTML)
└── withdrawl_admin.txt        (Notificare administrator - Text)
```

### Variabile Disponibile

**Confirmare Client:**
- `{order_id}` - Număr comandă
- `{order_reference}` - Referință comandă
- `{order_identifier}` - Identificator vizibil conform setării modulului
- `{customer_name}` - Numele trimis de client
- `{email}` - E-mail client
- `{date}` - Timestamp

**Notificare Administrator:**
- `{order_reference}` - Referință comandă
- `{order_id}` - Număr comandă
- `{order_identifier}` - Identificator vizibil conform setării modulului
- `{customer_email}` - E-mail client
- `{customer_name}` - Nume client
- `{date}` - Timestamp
- `{order_total}` - Valoare comandă (formatată)

## Depanare

### "Perioada de retragere a expirat"

**Cauză:** Perioada calculată (data comenzii + zile livrare + zile retragere) a fost depășită.

**Soluție:**
- Verificați setările pentru perioada de retragere și timpul de livrare
- Măriți timpul de livrare dacă pachetele durează mai mult
- Verificați dacă data comenzii este corectă

### "Numele nu se potrivește cu comanda"

**Cauză:** Numele introdus nu corespunde numelui clientului din comandă.

**Soluție:**
- Verificați ortografia, prenumele și numele
- Dacă este necesar, comerciantul poate trece la verificarea doar prin identificatorul comenzii
- Vechea verificare prin e-mail se folosește numai în modul legacy

### "Status de retragere neconfigurat"

**Cauză:** Statusul comenzii nu a fost creat în timpul instalării.

**Soluție:**
1. Dezinstalați modulul
2. Reinstalați-l
3. Verificați la **Comenzi** → **Statusuri** dacă există "Retragere Solicitată"

### Notificări prin e-mail neprimite

**Diagnostic:**
1. Verificați **Setări Avansate** → **E-mail**
2. Trimiteți un e-mail de test prin PrestaShop
3. Verificați jurnalele în `var/logs/`

**Soluții:**
- Configurați SMTP în loc de PHP mail()
- Verificați înregistrările SPF și DKIM ale domeniului dvs.
- Verificați folderul spam
- Asigurați-vă că adresele de e-mail sunt formatate corect

### Butonul de retragere nu apare în subsol

**Diagnostic:**
1. Verificați în configurarea modulului dacă "Subsol" este activat ca poziție de afișare
2. Verificați în codul sursă al paginii dacă fișierul CSS este încărcat
3. Deschideți consola browserului și verificați erorile

**Soluții:**
- Activați afișarea subsolului în configurare
- Goliți cache-ul: **Setări Avansate** → **Performanță** → **Golire cache**
- Verificați dacă tema dvs. suportă hook-ul `displayFooter`

## Informații Juridice

### Bază Juridică pentru UE

**Directiva 2011/83/UE**
- Consumatorii au un drept de retragere de 14 zile pentru contractele la distanță
- Perioada începe la primirea bunurilor
- Dacă informațiile de retragere lipsesc sau sunt incorecte, perioada se prelungește la 12 luni și 14 zile

**Articolul 6(1)(h)** - Informații despre dreptul de retragere
- Afișare clară a perioadei de retragere
- Accesibilitate formular de retragere
- Confirmare automată pe suport durabil (e-mail)

**Articolul 11** - Formular de retragere
- Opțiune de retragere electronică furnizată
- Alternativă la metodele tradiționale (poștă, e-mail, fax)
- Confirmare imediată de primire

**Articolul 14** - Cerințe formale
- Confirmare pe suport durabil (e-mail)
- Identificare comandă (număr de referință)
- Identificare client (verificare e-mail)

### Ce Oferă Modulul

✅ Sistem tehnic pentru cereri de retragere
✅ Confirmare prin e-mail pe suport durabil
✅ Urmărire status comandă
✅ Perioade de retragere configurabile

### Ce Trebuie să Asigurați ca Comerciant

⚠️ Informații complete de retragere pe site-ul dvs. web
⚠️ Instrucțiuni de returnare pentru clienți
⚠️ Rambursare în termen de 14 zile după retragere
⚠️ Documentație adecvată
⚠️ Instruirea personalului
⚠️ Conformitate cu reglementările locale

**Important:** Acest modul nu înlocuiește consultanța juridică. Consultați un avocat pentru a asigura conformitatea juridică completă.

## Suport și Contact

**Suport Tehnic:**
- Utilizați canalul de suport al marketplace-ului asociat comenzii modulului.

**Pentru Cereri de Suport Avem Nevoie de:**
1. Versiune PrestaShop
2. Versiune PHP
3. Versiune modul
4. Descriere detaliată a erorii
5. Pași pentru reproducere
6. Mesaje de eroare/jurnale
7. Capturi de ecran (dacă este relevant)

## Jurnal de Modificări

### Versiunea 1.4.0 (Iunie 2026)

**Lansare publică:**
- Opțiune nouă, opțională, în Back Office pentru afișarea produselor din comandă după verificarea datelor clientului.
- Clientul poate selecta întreaga comandă sau articole individuale cu o cantitate validă.
- Lista de produse este încărcată pe server și nu folosește AJAX.
- Opțiunea este dezactivată implicit și păstrează setările existente.
- Nicio modificare a schemei bazei de date; compatibilitatea rămâne orientată spre PrestaShop 1.7.8, 8.x și 9.x cu PHP 7.4+.

### Versiunea 1.3.1 (Mai 2026)

**Lansare publică:**
- Alegere structurată în Front Office: retragerea întregii comenzi sau a articolelor/serviciilor selectate.
- Retragerea parțială folosește rânduri repetabile cu nume articol, număr articol și cantitate.
- Nu se solicită un motiv de retragere; câmpurile servesc doar la identificarea contractului sau a părții din contract.
- Confirmările pentru client și administrator includ domeniul retragerii și detaliile trimise despre articole/servicii.
- Detaliile retragerii parțiale sunt salvate ca mesaj al comenzii pentru verificare în Back Office.
- Nicio modificare a schemei bazei de date; setările existente sunt păstrate.
- Compatibilitatea rămâne orientată spre PrestaShop 1.7.8, 8.x și 9.x cu PHP 7.4+.

### Versiunea 1.3.0 (Mai 2026)

**Lansare publică:**
- Revizuire manuală implicită pentru instalările noi
- Verificare recomandată prin nume client + identificator comandă
- Mod opțional doar cu identificator și mod legacy e-mail + identificator
- Formularul public nu mai afișează blocul `<details>` pentru politica de retragere
- Lucrările interne anterioare de upgrade 1.3.x au fost incluse în `upgrade-1.3.0.php`

**Actualizare de la 1.2.4 la 1.3.0:**
- Cale directă de actualizare de la 1.2.4 la 1.3.0; modificările interne anterioare 1.3.x sunt incluse în această versiune publică.
- Nicio modificare a schemei bazei de date; setările existente ale comerciantului sunt păstrate.
- Noi valori implicite lipsă: verificare manuală, verificare nume + identificator comandă, căutare după număr/referință comandă și fallback PHP mail activ.
- E-mailurile de confirmare a primirii confirmă cererea de retragere și datele transmise, fără a sugera o decizie finală.
- Opțiuni noi în Back Office pentru verificarea clientului, căutarea comenzii, fallback e-mail, previzualizare e-mail, trimitere test și resetare la valori implicite.
- Formular public consolidat: token CSRF, honeypot, limită IP, prevenire duplicate și note client sigure pentru HTML.
- Trimitere e-mail mai fiabilă prin MailDispatcher central, diagnostice și fallback PHP mail.
- Șabloane e-mail și traduceri completate pentru toate limbile livrate.

### Versiunea 1.0.0 (Octombrie 2024)

**Funcționalități Noi:**
- Gestionare retragere conformă cu Directiva UE
- Creare automată status comandă
- Sistem dublu de notificare prin e-mail
- 8 limbi preinstalate
- Opțiuni flexibile de afișare (subsol, meniu cont, pagină cont)
- Comutare buton/link pentru subsol
- Culoare buton personalizabilă (cod culoare hexazecimal)
- Integrare CMS pentru notificări de confidențialitate și retragere
- Configurare pe limbă pentru pagini CMS și texte notificare
- Perioadă de retragere configurabilă (1-365 zile)
- Buffer de livrare (0-365 zile)
- Mai mulți destinatari e-mail administrator
- Interfață de administrare modernă inspirată de shadcn
- Compatibilitate PrestaShop 8/9
- Formatare preț conformă cu setările locale
- Formulare securizate SSL
- URL-uri prietenoase SEO

## Licență

**Academic Free License 3.0 (AFL-3.0)**

Copyright © 2025 sjmedia GmbH

Acest modul este software liber: îl puteți redistribui și/sau modifica conform termenilor Academic Free License.

Acest modul este distribuit în speranța că va fi util, dar FĂRĂ NICIO GARANȚIE. Consultați Academic Free License pentru mai multe detalii.

O copie a licenței ar fi trebuit livrată cu acest modul. Dacă nu, vizitați:
http://opensource.org/licenses/afl-3.0.php

---

**Declinare a Responsabilității**

Acest modul oferă funcționalitate tehnică pentru a facilita cererile de retragere conforme cu UE. Deși este conceput pentru a se alinia cu cerințele Directivei 2011/83/UE, **comercianții rămân singurii responsabili pentru asigurarea conformității juridice complete** cu toate legile și reglementările aplicabile în jurisdicția lor.

**Utilizați pe propriul risc.** Autorii și deținătorii de drepturi de autor nu sunt responsabili pentru dispute juridice, încălcări de reglementări sau daune care rezultă din utilizarea acestui modul.

---

*Ultima actualizare: 2 iunie 2026*
*Versiune modul: 1.4.0*
*Documentație: 1.4.0*
