# Modulo di Recesso Ordine per PrestaShop

## Panoramica

Il modulo di recesso dell’ordine consente ai tuoi clienti di esercitare online, in modo semplice e sicuro, il loro diritto legale di recesso dagli ordini, in conformità con le leggi europee a tutela dei consumatori.
Il modulo offre una soluzione intuitiva, con supporto multilingue, notifiche e-mail automatiche e opzioni di visualizzazione flessibili.

### Vantaggi principali

- ✅ **Conformità UE** – Soddisfa i requisiti della Direttiva 2011/83/UE
- ✅ **Facile da usare** – Moduli chiari e intuitivi per i clienti
- ✅ **Processi automatizzati** – Cambi di stato e notifiche e-mail automatiche
- ✅ **Visualizzazione flessibile** – Pulsante di recesso visibile in più posizioni
- ✅ **Completamente multilingue** – Supporto immediato per 8 lingue
- ✅ **Tutela legale** – Include testi informativi su privacy e diritto di recesso

## Funzionalità nel dettaglio

### 1. Opzioni di visualizzazione multiple

Mostra il pulsante di recesso nei punti più adatti al tuo negozio:

- **Nel footer** – In evidenza come pulsante colorato o come link testuale discreto
- **Nel menu dell’account cliente** – Per temi PrestaShop 1.7 meno recenti
- **Nella pagina dell’account cliente** – Visualizzazione moderna a schede per PrestaShop 8/9

È possibile attivare più posizioni contemporaneamente, ad esempio Footer + Pagina account cliente.

![Posizioni del pulsante di recesso](views/img/de/screen-1-where-the-withdrawal-button-appears.png)

### 2. Personalizzazione completa

Adatta il modulo al tuo design aziendale:

- **Colore del pulsante personalizzabile** – Codice esadecimale per allinearsi al tuo brand
- **Pulsante o link** – Scegli tra pulsante ben visibile o link discreto
- **Testi personalizzati** – Definisci etichette specifiche per ogni lingua

![Impostazioni del modulo nel back office](views/img/de/screen-2-module-settings-in-the-back-office.png)

### 3. Integrazione CMS e note legali

Collega il modulo alle tue pagine legali:

#### Informativa sulla privacy
- Collega l’informativa sulla privacy per ogni lingua
- Aggiungi un testo personalizzato (es. “I tuoi dati saranno utilizzati solo per gestire la richiesta di recesso”)
- Il testo viene mostrato automaticamente nella pagina CMS collegata

#### Informativa sul diritto di recesso
- Collega la tua pagina d’informazioni sul recesso per ogni lingua
- Aggiungi un testo esplicativo sul pulsante elettronico di recesso
- Informa i clienti che riceveranno una conferma automatica via e-mail

![Gestione testi legali e informative privacy](views/img/de/screen-3-manage-legal-texts-and-privacy-notes.png)

### 4. Gestione automatica dello stato dell’ordine

- Crea automaticamente uno stato ordine **"Recesso richiesto"** evidenziato in arancione
- Aggiorna lo stato dell’ordine subito dopo l’invio del modulo di recesso
- Consente di filtrare e gestire facilmente tutte le richieste di recesso

![Stato ordine "Recesso richiesto"](views/img/de/screen-5-withdrawal-request-status-in-orders.png)

### 5. Calcolo intelligente dei termini

Il modulo calcola automaticamente il termine di recesso:

- **Periodo di recesso** – Predefinito: 14 giorni (secondo la direttiva UE)
- **Margine di consegna** – Giorni aggiuntivi per la spedizione
- **Validazione automatica** – Il sistema verifica se il termine è ancora valido

**Esempio:** 14 giorni di recesso + 3 giorni di margine = 17 giorni dalla data d’ordine.

### 6. Sistema e-mail doppio

#### Conferma per il cliente
Il cliente riceve subito un’e-mail di conferma con:
- Riferimento ordine
- Indirizzo e-mail
- Data e ora della richiesta di recesso
- Conferma di ricezione su supporto durevole (conforme a § 356 BGB)

#### Notifica per l’amministratore
Tu e il tuo staff ricevete un’e-mail con:
- Numero e riferimento ordine
- Nome ed e-mail del cliente
- Valore dell’ordine
- Data e ora dell’invio

È possibile indicare più indirizzi e-mail separati da virgola per le notifiche.

### 7. Supporto multilingue

Il modulo è completamente tradotto in:
- 🇩🇪 Tedesco
- 🇬🇧 Inglese
- 🇫🇷 Francese
- 🇮🇹 Italiano
- 🇪🇸 Spagnolo
- 🇵🇱 Polacco
- 🇳🇱 Olandese
- 🇷🇴 Rumeno

Tutti i testi, i modelli e-mail e le etichette dei pulsanti possono essere personalizzati per ogni lingua.

---

## Requisiti di sistema

- **PrestaShop:** 1.7.8 o superiore (testato con PrestaShop 8.x e 9.x)
- **PHP:** 7.4 o superiore (PHP 8.x supportato)
- **MySQL/MariaDB:** 5.6+ / 10.1+
- **Certificato SSL:** consigliato per un invio sicuro dei moduli

---

## Installazione

### Tramite il pannello Admin di PrestaShop (consigliato)

1. Scarica il file ZIP del modulo
2. Vai su **Moduli → Gestione moduli**
3. Clicca su **Carica modulo**
4. Seleziona il file ZIP e caricalo
5. Clicca su **Installa**

Durante l’installazione vengono creati automaticamente:
- Stato ordine “Recesso richiesto” (colore arancione)
- Configurazione predefinita con 14 giorni di recesso
- Tutti i modelli e-mail necessari per 8 lingue

### Dopo l’installazione

1. Clicca su **Configura** nel modulo
2. Controlla il periodo di recesso (standard 14 giorni)
3. Aggiungi eventuali giorni di consegna extra
4. Scegli dove mostrare il pulsante di recesso
5. Personalizza colore e testo del pulsante
6. Collega le pagine CMS (privacy, diritto di recesso)
7. Inserisci i testi legali necessari
8. Salva le impostazioni

---

## Configurazione dettagliata

### Impostazioni di base

| Impostazione | Descrizione | Valore predefinito |
|--------------|--------------|--------------------|
| **Periodo di recesso (giorni)** | Termine legale per il recesso | 14 giorni |
| **Tempo di consegna stimato (giorni)** | Margine per la spedizione | 0 giorni |
| **Verifica cliente** | Consigliata: nome + identificativo ordine; opzionale solo identificativo; legacy: e-mail + identificativo | Nome + identificativo |
| **Modalità di valutazione** | Revisione manuale o controllo automatico del periodo | Revisione manuale |
| **E-mail amministrative aggiuntive** | Destinatari extra separati da virgola | Vuoto |

### Opzioni di visualizzazione

| Impostazione | Opzioni | Descrizione |
|--------------|----------|-------------|
| **Posizione di visualizzazione** | Footer / Menu account cliente / Pagina account cliente | Possibile selezione multipla |
| **Visualizzazione footer** | Pulsante / Link | Il pulsante è più visibile, il link più discreto |
| **Colore pulsante** | Codice HEX | Predefinito #F44708 (arancione) |

### Configurabile per lingua

- **Etichetta del pulsante** – Testo personalizzato del pulsante di recesso
- **Pagina privacy** – Pagina CMS collegata all’informativa sulla privacy
- **Nota privacy** – Testo sull’utilizzo dei dati per la richiesta di recesso
- **Pagina recesso** – Pagina CMS collegata all’informativa sul recesso
- **Nota recesso** – Testo esplicativo sul pulsante elettronico di recesso

## Utilizzo

### Per i tuoi clienti

1. **Trovare il pulsante di recesso** – a seconda della configurazione nel footer, nel menu dell’account o nella pagina dell’account cliente
2. **Aprire il modulo** – il clic sul pulsante o link apre il modulo di recesso
3. **Inserire i dati:**
   - Identificativo ordine configurato (numero, riferimento o entrambi)
   - Nome cliente nella modalità predefinita; e-mail solo nella modalità legacy
4. **Inviare il recesso** – clic su “Conferma annullamento”
5. **Ricevere conferma:**
   - Messaggio di successo sullo schermo
   - E-mail di conferma con identificativo ordine e nome inviato
   - Stato ordine aggiornato a “Recesso richiesto”

![Modulo di recesso per clienti](views/img/de/screen-4-order-withdrawal-form.png)

**Note importanti per i clienti:**
- La modalità consigliata verifica nome cliente + identificativo ordine
- È disponibile una modalità opzionale solo con identificativo e una modalità legacy con e-mail + identificativo
- Per le nuove installazioni la revisione manuale è predefinita; il controllo automatico del termine resta configurabile
- Se il termine è scaduto, viene mostrato un messaggio dedicato

---

### Per il commerciante

#### Gestione delle richieste di recesso

1. **Ricezione notifica** – ricevi un’e-mail con tutti i dettagli dell’ordine
2. **Cerca l’ordine** – vai su **Ordini → Ordini**
3. **Controlla lo stato** – l’ordine avrà lo stato “Recesso richiesto” (arancione)
4. **Gestisci il rimborso:**
   - Verifica le condizioni di recesso
   - Effettua il rimborso tramite il metodo di pagamento usato
   - Aggiorna lo stato ordine (es. “Rimborsato”)
5. **Informa il cliente:**
   - Quando verrà effettuato il rimborso
   - Se è richiesta la restituzione del prodotto
   - La data prevista per l’accredito

#### Visualizzare tutte le richieste di recesso

1. Vai su **Ordini → Ordini**
2. Filtra per stato **"Recesso richiesto"**
3. Ordina per data per elaborare prima le più vecchie

#### Gestione dei destinatari e-mail

Nella configurazione del modulo, alla voce **“E-mail amministrative aggiuntive”**, inserisci altri destinatari separati da virgola:

## Utilizzo

### Per i tuoi clienti

1. **Trovare il pulsante di recesso** – a seconda della configurazione nel footer, nel menu dell’account o nella pagina dell’account cliente
2. **Aprire il modulo** – il clic sul pulsante o link apre il modulo di recesso
3. **Inserire i dati:**
   - Identificativo ordine configurato (numero, riferimento o entrambi)
   - Nome cliente nella modalità predefinita; e-mail solo nella modalità legacy
4. **Inviare il recesso** – clic su “Conferma annullamento”
5. **Ricevere conferma:**
   - Messaggio di successo sullo schermo
   - E-mail di conferma con identificativo ordine e nome inviato
   - Stato ordine aggiornato a “Recesso richiesto”

![Modulo di recesso per clienti](views/img/de/screen-4-order-withdrawal-form.png)

**Note importanti per i clienti:**
- La modalità consigliata verifica nome cliente + identificativo ordine
- È disponibile una modalità opzionale solo con identificativo e una modalità legacy con e-mail + identificativo
- Per le nuove installazioni la revisione manuale è predefinita; il controllo automatico del termine resta configurabile
- Se il termine è scaduto, viene mostrato un messaggio dedicato

---

### Per il commerciante

#### Gestione delle richieste di recesso

1. **Ricezione notifica** – ricevi un’e-mail con tutti i dettagli dell’ordine
2. **Cerca l’ordine** – vai su **Ordini → Ordini**
3. **Controlla lo stato** – l’ordine avrà lo stato “Recesso richiesto” (arancione)
4. **Gestisci il rimborso:**
   - Verifica le condizioni di recesso
   - Effettua il rimborso tramite il metodo di pagamento usato
   - Aggiorna lo stato ordine (es. “Rimborsato”)
5. **Informa il cliente:**
   - Quando verrà effettuato il rimborso
   - Se è richiesta la restituzione del prodotto
   - La data prevista per l’accredito

#### Visualizzare tutte le richieste di recesso

1. Vai su **Ordini → Ordini**
2. Filtra per stato **"Recesso richiesto"**
3. Ordina per data per elaborare prima le più vecchie

#### Gestione dei destinatari e-mail

Nella configurazione del modulo, alla voce **“E-mail amministrative aggiuntive”**, inserisci altri destinatari separati da virgola:

vendite@iltuoshop.it
,resi@iltuoshop.it
,legale@iltuoshop.it


Tutti riceveranno le notifiche simultaneamente.

---

## Modelli e-mail

I modelli si trovano in:

modules/orderwithdrawl/mails/{lingua}/
├── withdrawl_confirm.html (Conferma cliente – HTML)
├── withdrawl_confirm.txt (Conferma cliente – Testo)
├── withdrawl_admin.html (Notifica admin – HTML)
└── withdrawl_admin.txt (Notifica admin – Testo)


### Variabili disponibili

**Conferma cliente:**
- `{order_id}` – numero ordine
- `{order_reference}` – riferimento ordine
- `{order_identifier}` – identificativo visibile secondo l’impostazione del modulo
- `{customer_name}` – nome inviato dal cliente
- `{email}` – e-mail cliente
- `{date}` – data e ora

**Notifica amministratore:**
- `{order_reference}` – riferimento ordine
- `{order_id}` – numero ordine
- `{order_identifier}` – identificativo visibile secondo l’impostazione del modulo
- `{customer_email}` – e-mail cliente
- `{customer_name}` – nome cliente
- `{date}` – data e ora
- `{order_total}` – valore ordine formattato

---

## Risoluzione dei problemi

### “Il termine di recesso è scaduto”
**Causa:** somma di data ordine + giorni consegna + giorni recesso superata.
**Soluzione:**
- Controlla i valori di recesso e consegna
- Aumenta i giorni di consegna se necessario
- Verifica la correttezza della data d’ordine

### “Il nome non corrisponde all’ordine”
**Causa:** il nome inserito non corrisponde al nome cliente dell’ordine.
**Soluzione:**
- Controlla ortografia, nome e cognome
- Se necessario, il negoziante può passare alla verifica solo tramite identificativo ordine
- La vecchia verifica tramite e-mail viene usata solo nella modalità legacy

### “Stato recesso non configurato”
**Causa:** lo stato ordine non è stato creato in installazione.
**Soluzione:**
1. Disinstalla il modulo
2. Reinstalla
3. Controlla in **Ordini → Stati** se esiste “Recesso richiesto”

### “Le e-mail non arrivano”
**Diagnosi:**
1. Vai su **Parametri avanzati → E-mail**
2. Invia un’e-mail di test
3. Controlla i log in `var/logs/`

**Soluzioni:**
- Usa SMTP invece di `mail()`
- Verifica record SPF e DKIM del dominio
- Controlla la cartella spam
- Assicurati che gli indirizzi siano validi

### “Il pulsante di recesso non appare nel footer”
**Diagnosi:**
1. Controlla che “Footer” sia abilitato nelle impostazioni
2. Verifica nel codice sorgente che il CSS sia caricato
3. Apri la console browser e cerca errori

**Soluzioni:**
- Attiva la visualizzazione nel footer
- Svuota la cache: **Parametri avanzati → Prestazioni → Svuota cache**
- Controlla che il tuo tema supporti il hook `displayFooter`

---

## Note legali

### Fondamento giuridico (Germania)

**§ 355 BGB – Diritto di recesso**
- I consumatori hanno 14 giorni di tempo per recedere da contratti a distanza
- Il termine decorre dal ricevimento della merce
- In assenza di informativa valida si estende a 12 mesi + 14 giorni

**§ 356 BGB – Esercizio del recesso**
- Il recesso va dichiarato in forma scritta
- È richiesta una conferma su supporto durevole
- **Questo modulo soddisfa tale requisito tramite e-mail automatica**

**§ 312g BGB – Diritto di recesso nei contratti a distanza**
- Il venditore deve informare sul diritto di recesso
- Va fornito un modulo tipo
- **Questo modulo offre il modulo elettronico di recesso**

**Art. 246a EGBGB – Obblighi informativi**
- Il consumatore deve essere informato sulle modalità del recesso
- Include anche la possibilità di esercitarlo elettronicamente

### Direttiva UE 2011/83/UE

Il modulo soddisfa i requisiti tecnici di:
- **Art. 6(1)(h)** – Informazioni sul diritto di recesso
- **Art. 11** – Disponibilità di un modulo di recesso
- **Art. 14** – Conferma su supporto durevole (e-mail)

### Cosa offre il modulo

✅ Sistema tecnico per gestire le richieste di recesso
✅ Conferma e-mail su supporto durevole
✅ Tracciamento dello stato ordine
✅ Termini di recesso configurabili

### Cosa deve garantire il commerciante

⚠️ Informativa completa sul recesso sul sito
⚠️ Istruzioni per la restituzione
⚠️ Rimborso entro 14 giorni dalla richiesta
⚠️ Documentazione corretta
⚠️ Formazione del personale
⚠️ Rispetto delle normative locali

**Importante:** questo modulo non sostituisce la consulenza legale. Consulta un avvocato per la piena conformità normativa.

---

## Supporto e contatti

**Supporto tecnico:**
- Utilizza il canale di supporto del marketplace collegato al tuo ordine del modulo.

**Per richieste di supporto indica:**
1. Versione PrestaShop
2. Versione PHP
3. Versione modulo
4. Descrizione dettagliata del problema
5. Passaggi per riprodurlo
6. Messaggi d’errore / log
7. Screenshot (se rilevanti)

---

## Registro modifiche

### Versione 1.4.0 (Giugno 2026)

**Release pubblica:**
- Nuova opzione opzionale nel Back Office per mostrare i prodotti dell'ordine dopo la verifica dei dati cliente.
- Il cliente può selezionare l'intero ordine o singoli articoli con una quantità valida.
- La lista dei prodotti viene caricata lato server e non usa AJAX.
- L'opzione è disattivata per impostazione predefinita e mantiene le impostazioni esistenti.
- Nessuna modifica allo schema del database; compatibilità mantenuta con PrestaShop 1.7.8, 8.x e 9.x con PHP 7.4+.

### Versione 1.3.1 (Maggio 2026)

**Release pubblica:**
- Supporto strutturato per recesso completo o parziale nel Front Office.
- Il recesso parziale usa righe ripetibili con nome articolo, numero articolo e quantità.
- Non viene richiesto alcun motivo di recesso; i campi servono solo a identificare il contratto o la parte del contratto.
- Le e-mail di ricezione per cliente e amministratore includono ambito del recesso e dettagli inviati.
- I dettagli del recesso parziale vengono salvati come messaggio dell'ordine per la revisione nel Back Office.
- Nessuna modifica allo schema del database; le impostazioni esistenti vengono mantenute.
- Compatibilità mantenuta con PrestaShop 1.7.8, 8.x e 9.x con PHP 7.4+.

### Versione 1.3.0 (Maggio 2026)

**Release pubblica:**
- Revisione manuale predefinita per le nuove installazioni
- Verifica consigliata tramite nome cliente + identificativo ordine
- Modalità opzionale solo identificativo e modalità legacy e-mail + identificativo
- Il modulo pubblico non mostra più il blocco `<details>` dell’informativa di recesso
- I precedenti lavori interni di upgrade 1.3.x sono stati uniti in `upgrade-1.3.0.php`

**Aggiornamento da 1.2.4 a 1.3.0:**
- Percorso diretto di aggiornamento da 1.2.4 a 1.3.0; le precedenti modifiche interne 1.3.x sono incluse in questa release pubblica.
- Nessuna modifica allo schema del database; le impostazioni esistenti del commerciante vengono mantenute.
- Nuovi valori predefiniti mancanti: revisione manuale, verifica nome + identificativo ordine, ricerca per numero/riferimento ordine e fallback PHP mail attivo.
- Le e-mail di ricezione confermano la richiesta di recesso e i dati inviati, senza suggerire una decisione finale.
- Nuove opzioni Back Office per verifica cliente, ricerca ordine, fallback e-mail, anteprima e-mail, invio test e ripristino predefiniti.
- Form pubblico rafforzato: token CSRF, honeypot, limite per IP, prevenzione duplicati e note cliente sicure per HTML.
- Invio e-mail più affidabile con MailDispatcher centrale, diagnostica e fallback PHP mail.
- Template e-mail e traduzioni completati per tutte le lingue distribuite.

### Versione 1.0.0 (Ottobre 2025)

**Novità:**
- Gestione recesso conforme alle direttive UE
- Creazione automatica dello stato ordine
- Doppio sistema di notifica e-mail
- 8 lingue preinstallate
- Opzioni di visualizzazione flessibili (footer, menu account, pagina account)
- Alternanza pulsante/link nel footer
- Colore pulsante personalizzabile (codice HEX)
- Integrazione CMS per privacy e diritto di recesso
- Configurazione per lingua di testi e pagine CMS
- Termine di recesso configurabile (1–365 giorni)
- Margine di consegna configurabile (0–365 giorni)
- Più destinatari e-mail admin
- Interfaccia admin moderna ispirata a shadcn
- Compatibilità PrestaShop 8/9
- Formattazione prezzo locale
- Moduli SSL sicuri
- URL ottimizzati SEO

---

## Licenza

**Academic Free License 3.0 (AFL-3.0)**

Copyright © 2025 sjmedia GmbH

Questo modulo è software libero: puoi ridistribuirlo e/o modificarlo secondo i termini della Academic Free License.
Distribuito nella speranza che sia utile, ma **senza alcuna garanzia**. Consulta la licenza per i dettagli.

Una copia della licenza dovrebbe essere inclusa con il modulo. In caso contrario, visita:
[http://opensource.org/licenses/afl-3.0.php](http://opensource.org/licenses/afl-3.0.php)

---

**Disclaimer**

Questo modulo fornisce una funzione tecnica per facilitare le richieste di recesso conformi alla normativa UE.
Sebbene progettato in linea con la Direttiva 2011/83/UE, **la piena responsabilità della conformità legale resta al commerciante.**

**Uso a proprio rischio.** Gli autori non sono responsabili di controversie legali o danni derivanti dall’uso del modulo.

---

*Ultimo aggiornamento: 2 giugno 2026*
*Versione modulo: 1.4.0*
*Documentazione: 1.4.0*
