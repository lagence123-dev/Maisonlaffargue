# Bestellwiderrufs-Modul für PrestaShop

## Überblick

Das Bestellwiderrufs-Modul ermöglicht es Ihren Kunden, ihr gesetzliches Widerrufsrecht für Bestellungen gemäß den EU-Verbraucherschutzgesetzen einfach und sicher online auszuüben. Das Modul bietet eine benutzerfreundliche Lösung mit mehrsprachiger Unterstützung, automatischen E-Mail-Benachrichtigungen und flexiblen Anzeigeoptionen.

### Hauptvorteile

- ✅ **EU-Konformität** - Erfüllt die Anforderungen der EU-Richtlinie 2011/83/EU
- ✅ **Einfache Bedienung** - Klare, intuitive Formulare für Ihre Kunden
- ✅ **Automatisierte Prozesse** - Statusänderungen und E-Mail-Benachrichtigungen erfolgen automatisch
- ✅ **Flexible Darstellung** - Widerrufs-Button an mehreren Stellen gleichzeitig anzeigen
- ✅ **Vollständig mehrsprachig** - Unterstützt 8 Sprachen out-of-the-box
- ✅ **Rechtlich abgesichert** - Integrierte Datenschutz- und Widerrufshinweise

## Funktionen im Detail

### 1. Mehrere Anzeigeoptionen

Zeigen Sie den Widerrufs-Button an den für Sie passenden Stellen an:

- **Im Footer** - Prominent als farbiger Button oder dezent als Textlink
- **Im Kundenkonto-Menü** - Für ältere PrestaShop 1.7 Themes
- **Auf der Kundenkonto-Seite** - Moderne Kachel-Darstellung für PrestaShop 8/9

Sie können auch mehrere Positionen gleichzeitig aktivieren, z. B. Footer + Kundenkonto-Seite.

![Widerrufs-Button Positionen](views/img/de/screen-1-where-the-withdrawal-button-appears.png)

### 2. Individuelle Anpassung

Passen Sie das Modul an Ihr Corporate Design an:

- **Frei wählbare Button-Farbe** - Hex-Farbcode zur perfekten Markenanpassung
- **Button oder Link** - Wählen Sie zwischen prominenter Button-Darstellung oder dezentem Textlink
- **Individuelle Beschriftungen** - Definieren Sie eigene Button-Texte pro Sprache

![Modul-Einstellungen im Backend](views/img/de/screen-2-module-settings-in-the-back-office.png)

### 3. CMS-Integration & Rechtliche Hinweise

Verbinden Sie das Modul mit Ihren rechtlichen Seiten:

#### Datenschutzhinweis
- Verlinken Sie Ihre Datenschutzerklärung pro Sprache
- Fügen Sie einen individuellen Datenschutzhinweis hinzu (z. B. "Ihre Daten werden nur zur Bearbeitung des Widerrufs verwendet")
- Der Hinweis wird automatisch auf der verknüpften CMS-Seite angezeigt

#### Widerrufsbelehrung
- Verlinken Sie Ihre Widerrufsbelehrung pro Sprache
- Fügen Sie einen Hinweis zum elektronischen Widerrufs-Button hinzu
- Informieren Sie Kunden über die automatische Eingangsbestätigung per E-Mail

![Rechtliche Texte und Datenschutzhinweise verwalten](views/img/de/screen-3-manage-legal-texts-and-privacy-notes.png)

### 4. Automatische Bestellstatus-Verwaltung

- Erstellt automatisch einen Bestellstatus **"Widerruf beantragt"** mit orangefarbener Kennzeichnung
- Ändert den Bestellstatus sofort nach erfolgreicher Widerrufseinreichung
- Ermöglicht einfaches Filtern und Verwalten aller Widerrufsanträge

![Bestellstatus "Widerruf beantragt"](views/img/de/screen-5-withdrawal-request-status-in-orders.png)

### 5. Intelligente Fristberechnung

Das Modul berechnet automatisch die Widerrufsfrist:

- **Widerrufsfrist** - Standard: 14 Tage (gemäß EU-Richtlinie)
- **Lieferpuffer** - Zusätzliche Tage für die Lieferzeit
- **Automatische Validierung** - System prüft, ob die Frist noch läuft

**Beispiel:** Bei 14 Tagen Widerrufsfrist + 3 Tagen Lieferpuffer = 17 Tage ab Bestelldatum

### 6. Doppeltes E-Mail-System

#### Kundenbestätigung
Ihre Kunden erhalten sofort nach dem Absenden eine Bestätigungs-E-Mail mit:
- Konfigurierter Bestellkennung (Bestell-ID, Bestellnummer oder beides)
- E-Mail-Adresse
- Datum und Uhrzeit der Widerrufseinreichung
- Bestätigung des Eingangs auf dauerhaftem Datenträger (gemäß § 356 BGB)

#### Administrator-Benachrichtigung
Sie und Ihre Mitarbeiter erhalten eine Benachrichtigung mit:
- Konfigurierter Bestellkennung plus Bestell-ID-/Bestellnummer-Platzhaltern
- Vor- und Nachname sowie E-Mail-Adresse
- Bestellwert
- Zeitstempel der Widerrufseinreichung

Sie können mehrere E-Mail-Adressen (kommagetrennt) für Benachrichtigungen hinterlegen.

### 7. Mehrsprachige Unterstützung

Das Modul ist vollständig übersetzt für:
- 🇩🇪 Deutsch
- 🇬🇧 English
- 🇫🇷 Français
- 🇮🇹 Italiano
- 🇪🇸 Español
- 🇵🇱 Polski
- 🇳🇱 Nederlands
- 🇷🇴 Română

Alle Texte, E-Mail-Vorlagen und Button-Beschriftungen sind für jede Sprache individuell anpassbar.

## Systemanforderungen

- **PrestaShop:** 1.7.8 oder höher (getestet mit PrestaShop 8.x und 9.x)
- **PHP:** 7.4 oder höher (PHP 8.x unterstützt)
- **MySQL/MariaDB:** 5.6+ / 10.1+
- **SSL-Zertifikat:** Empfohlen für sichere Formularübermittlung

## Installation

### Über PrestaShop Admin-Panel (empfohlen)

1. Laden Sie die Modul-ZIP-Datei herunter
2. Navigieren Sie zu **Module** → **Modulverwaltung**
3. Klicken Sie auf **Modul hochladen**
4. Wählen Sie die ZIP-Datei und laden Sie sie hoch
5. Klicken Sie auf **Installieren**

Das Modul erstellt bei der Installation automatisch:
- Bestellstatus "Widerruf beantragt" (orangefarbene Kennzeichnung)
- Standard-Konfiguration mit 14 Tagen Widerrufsfrist
- Alle notwendigen E-Mail-Vorlagen für 8 Sprachen

### Nach der Installation

1. Klicken Sie auf **Konfigurieren** im Modul
2. Prüfen Sie die Widerrufsfrist (Standard: 14 Tage)
3. Tragen Sie ggf. zusätzliche Liefertage ein
4. Wählen Sie aus, wo der Widerrufs-Button erscheinen soll
5. Passen Sie Button-Farbe und -Text nach Wunsch an
6. Verknüpfen Sie Ihre CMS-Seiten (Datenschutz, Widerrufsbelehrung)
7. Fügen Sie die rechtlichen Hinweistexte hinzu
8. Speichern Sie die Einstellungen

## Konfiguration im Detail

### Grundeinstellungen

| Einstellung | Beschreibung | Standard |
|------------|--------------|----------|
| **Widerrufsfrist (Tage)** | Gesetzliche Widerrufsfrist | 14 Tage |
| **Geschätzte Lieferzeit (Tage)** | Puffer für Lieferzeit | 0 Tage |
| **Angaben zur Bestellzuordnung** | Auswahl zwischen Vor- und Nachname + Bestellnummer, Vor- und Nachname + Bestell-ID, Vor- und Nachname + Bestell-ID oder Bestellnummer sowie E-Mail + Bestell-ID oder Bestellnummer (ältere Methode, nicht empfohlen) | Vor- und Nachname + Bestellnummer (empfohlen) |
| **Prüfmodus** | Manuelle Prüfung oder automatische Fristprüfung | Manuelle Prüfung |
| **Zusätzliche Admin-E-Mails** | Weitere Empfänger für Benachrichtigungen (kommagetrennt) | Leer |

### Anzeigeoptionen

| Einstellung | Optionen | Beschreibung |
|------------|----------|--------------|
| **Anzeigeposition** | Footer / Kundenkonto-Menü / Kundenkonto-Seite | Mehrfachauswahl möglich |
| **Footer-Darstellung** | Button / Link | Button ist prominent, Link ist dezent |
| **Button-Farbe** | Hex-Farbcode | Standard: #F44708 (Orange) |

### Pro Sprache konfigurierbar

- **Button-Beschriftung** - Individueller Text für den Widerrufs-Button
- **Datenschutz-Seite** - Verlinkte CMS-Seite für Datenschutzerklärung
- **Datenschutzhinweis** - Hinweistext zur Datenverarbeitung beim Widerruf
- **Widerrufs-Seite** - Verlinkte CMS-Seite für Widerrufsbelehrung
- **Widerrufshinweis** - Hinweistext zum elektronischen Widerrufs-Button

## Verwendung

### Für Ihre Kunden

1. **Widerrufs-Button finden** - Je nach Konfiguration im Footer, im Kundenkonto-Menü oder auf der Kundenkonto-Seite
2. **Formular öffnen** - Klick auf Button/Link öffnet das Widerrufsformular
3. **Daten eingeben:**
   - Bestell-ID (z. B. 12345), Bestellnummer (z. B. ABC123XYZ) oder beides, je nach Moduleinstellung
   - Vor- und Nachname im Standardmodus; E-Mail-Adresse nur in der älteren E-Mail-Prüfmethode (nicht empfohlen)
4. **Widerruf absenden** - Klick auf "Widerruf bestätigen"
5. **Bestätigung erhalten:**
   - Erfolgsmeldung auf dem Bildschirm
   - Bestätigungs-E-Mail mit der konfigurierten Bestellkennung
   - Bestellstatus ändert sich zu "Widerruf beantragt"

![Widerrufsformular für Kunden](views/img/de/screen-4-order-withdrawal-form.png)

**Wichtige Hinweise für Kunden:**
- Standardmäßig werden Vor- und Nachname zusammen mit der Bestellkennung geprüft
- Optional kann der Shop die ältere E-Mail-Prüfmethode verwenden; diese ist nicht empfohlen
- Die Moduleinstellung steuert, ob das Formular die numerische Bestell-ID, die alphanumerische Bestellnummer oder beide Werte akzeptiert
- Fristen können automatisch geprüft werden; Standard für neue Installationen ist die manuelle Prüfung im Back Office
- Bei abgelaufener berechneter Frist wird die Anfrage trotzdem angenommen, die E-Mails werden gesendet und die Anfrage zur manuellen Prüfung markiert

### Für Sie als Shop-Betreiber

#### Widerrufsanträge bearbeiten

1. **Benachrichtigung erhalten** - Sie erhalten eine E-Mail mit allen Bestelldetails
2. **Bestellung suchen** - Navigieren Sie zu **Bestellungen** → **Bestellungen**
3. **Status prüfen** - Bestellung hat Status "Widerruf beantragt" (orange)
4. **Rückerstattung bearbeiten:**
   - Prüfen Sie die Widerrufsbedingungen
   - Erstatten Sie den Betrag über die Zahlungsmethode
   - Aktualisieren Sie den Bestellstatus (z. B. "Erstattet")
5. **Kunde informieren** - Teilen Sie dem Kunden mit:
   - Wann die Rückerstattung erfolgt
   - Ob Ware zurückgesendet werden muss
   - Voraussichtliches Datum der Gutschrift

#### Alle Widerrufsanträge anzeigen

1. Navigieren Sie zu **Bestellungen** → **Bestellungen**
2. Filtern Sie nach Status: **"Widerruf beantragt"**
3. Sortieren Sie nach Datum, um älteste Anträge zuerst zu bearbeiten

#### E-Mail-Empfänger verwalten

Tragen Sie in der Modul-Konfiguration unter **"Zusätzliche Admin-E-Mails"** weitere Empfänger ein:

```
verkauf@ihreshop.de,retouren@ihreshop.de,recht@ihreshop.de
```

Alle Adressen erhalten parallele Benachrichtigungen.

## E-Mail-Vorlagen anpassen

Die E-Mail-Vorlagen finden Sie hier:
```
modules/orderwithdrawl/mails/{sprache}/
├── withdrawl_confirm.html    (Kundenbestätigung - HTML)
├── withdrawl_confirm.txt      (Kundenbestätigung - Text)
├── withdrawl_admin.html       (Admin-Benachrichtigung - HTML)
└── withdrawl_admin.txt        (Admin-Benachrichtigung - Text)
```

### Verfügbare Variablen

**Kundenbestätigung:**
- `{order_id}` - Numerische Bestell-ID
- `{order_reference}` - Bestellnummer (intern weiterhin als Referenz gespeichert; für eigene Templates verfügbar)
- `{order_identifier}` - Sichtbare Bestellkennung anhand der Moduleinstellung
- `{customer_name}` - Übermittelter Vor- und Nachname
- `{email}` - Kunden-E-Mail
- `{deadline_review_notice}` / `{deadline_review_notice_html}` - Hinweis bei abgelaufener berechneter Frist und manueller Prüfung
- `{date}` - Zeitstempel

**Admin-Benachrichtigung:**
- `{order_id}` - Numerische Bestell-ID
- `{order_reference}` - Bestellnummer (intern weiterhin als Referenz gespeichert; für eigene Templates verfügbar)
- `{order_identifier}` - Sichtbare Bestellkennung anhand der Moduleinstellung
- `{customer_email}` - Kunden-E-Mail
- `{customer_name}` - Vor- und Nachname
- `{date}` - Zeitstempel
- `{order_total}` - Bestellwert (formatiert)
- `{deadline_review_notice}` / `{deadline_review_notice_html}` - Hinweis bei abgelaufener berechneter Frist und manueller Prüfung

## Fehlerbehebung

### Hinweis zur manuellen Fristenprüfung

**Ursache:** Die berechnete Frist (Bestelldatum + Liefertage + Widerrufstage) ist überschritten.

**Lösung:**
- Die Anfrage wird trotzdem gespeichert und die Benachrichtigungs-E-Mails werden gesendet
- Prüfen Sie die Anfrage manuell im Back Office
- Prüfen Sie die Einstellungen für Widerrufsfrist und Lieferzeit
- Erhöhen Sie ggf. die Lieferzeit, wenn Pakete länger unterwegs sind
- Prüfen Sie, ob das Bestelldatum korrekt ist

### "Name stimmt nicht mit der Bestellung überein"

**Ursache:** Der eingegebene Name passt nicht zum Kundennamen der Bestellung.

**Lösung:**
- Schreibweise, Vorname und Nachname prüfen
- Bei Bedarf kann im Modul eine andere Bestellzuordnung mit Vor- und Nachname gewählt werden
- Die E-Mail-Prüfung ist als ältere Methode weiterhin verfügbar, aber nicht empfohlen

### "Widerrufsstatus nicht konfiguriert"

**Ursache:** Der Bestellstatus wurde bei der Installation nicht angelegt.

**Lösung:**
1. Deinstallieren Sie das Modul
2. Installieren Sie es erneut
3. Prüfen Sie unter **Bestellungen** → **Status**, ob "Widerruf beantragt" existiert

### E-Mail-Benachrichtigungen kommen nicht an

**Diagnose:**
1. Prüfen Sie **Erweiterte Einstellungen** → **E-Mail**
2. Senden Sie eine Test-E-Mail über PrestaShop
3. Prüfen Sie die Logs unter `var/logs/`

**Lösungen:**
- Konfigurieren Sie SMTP statt PHP mail()
- Prüfen Sie SPF- und DKIM-Einträge Ihrer Domain
- Kontrollieren Sie Spam-Ordner
- Stellen Sie sicher, dass die E-Mail-Adressen korrekt formatiert sind

### Widerrufs-Button erscheint nicht im Footer

**Diagnose:**
1. Prüfen Sie in der Modul-Konfiguration, ob "Footer" als Anzeigeposition aktiviert ist
2. Prüfen Sie im Quellcode der Seite, ob die CSS-Datei geladen wird
3. Öffnen Sie die Browser-Konsole und prüfen Sie auf Fehler

**Lösungen:**
- Aktivieren Sie die Footer-Anzeige in der Konfiguration
- Leeren Sie den Cache: **Erweiterte Einstellungen** → **Leistung** → **Cache leeren**
- Prüfen Sie, ob Ihr Theme den `displayFooter` Hook unterstützt

## Rechtliche Hinweise

### Gesetzliche Grundlagen für Deutschland

**§ 355 BGB - Widerrufsrecht**
- Verbraucher haben bei Fernabsatzverträgen ein 14-tägiges Widerrufsrecht
- Die Frist beginnt mit Erhalt der Ware
- Bei fehlender oder fehlerhafter Widerrufsbelehrung verlängert sich die Frist auf 12 Monate und 14 Tage

**§ 356 BGB - Ausübung des Widerrufsrechts**
- Der Widerruf ist in Textform zu erklären
- Eine Bestätigung des Widerrufs auf dauerhaftem Datenträger ist zu übermitteln
- **Dieses Modul erfüllt diese Anforderung durch die automatische Bestätigungs-E-Mail**

**§ 312g BGB - Widerrufsrecht bei Fernabsatzverträgen**
- Händler müssen über das Widerrufsrecht belehren
- Ein Muster-Widerrufsformular muss bereitgestellt werden
- **Dieses Modul bietet das elektronische Widerrufsformular**

**Art. 246a EGBGB - Informationspflichten**
- Verbraucher müssen über die Modalitäten der Ausübung des Widerrufsrechts informiert werden
- Dies umfasst auch die Möglichkeit der elektronischen Widerrufserklärung

### EU-Richtlinie 2011/83/EU

Das Modul erfüllt die technischen Anforderungen von:

- **Artikel 6(1)(h)** - Informationen zum Widerrufsrecht
- **Artikel 11** - Bereitstellung eines Widerrufsformulars
- **Artikel 14** - Bestätigung auf dauerhaftem Datenträger (E-Mail)

### Was das Modul leistet

✅ Technisches System für Widerrufsanträge
✅ E-Mail-Bestätigung auf dauerhaftem Datenträger
✅ Bestellstatus-Tracking
✅ Konfigurierbare Widerrufsfristen

### Was Sie als Händler sicherstellen müssen

⚠️ Vollständige Widerrufsbelehrung auf Ihrer Website
⚠️ Rücksendehinweise für Kunden
⚠️ Rückerstattung innerhalb von 14 Tagen nach Widerruf
⚠️ Ordnungsgemäße Dokumentation
⚠️ Schulung Ihrer Mitarbeiter
⚠️ Einhaltung lokaler Vorschriften

**Wichtig:** Dieses Modul ersetzt keine Rechtsberatung. Konsultieren Sie einen Rechtsanwalt, um vollständige Rechtskonformität sicherzustellen.

## Support & Kontakt

**Technischer Support:**
- Bitte nutzen Sie den Support-Kanal des Marktplatzes zu Ihrer Modulbestellung.

**Für Support-Anfragen benötigen wir:**
1. PrestaShop-Version
2. PHP-Version
3. Modul-Version
4. Detaillierte Fehlerbeschreibung
5. Schritte zur Reproduktion
6. Fehlermeldungen/Logs
7. Screenshots (falls relevant)

## Änderungsprotokoll

### Version 1.4.0 (Juni 2026)

**Öffentliche Version:**
- Optionaler Back-Office-Modus für Produktauswahl aus der Bestellung.
- Nach erfolgreicher Bestellzuordnung und Kundenprüfung kann das Front Office auswählbare Artikel der Bestellung anzeigen.
- Teilwiderrufe können dann per Artikelauswahl und gültiger Menge eingereicht werden, statt Artikel manuell einzugeben.
- Die Umsetzung läuft serverseitig ohne AJAX; beim finalen Absenden werden die Bestellartikel erneut serverseitig geladen.
- Die Option ist standardmäßig deaktiviert, damit bestehende Installationen stabil im bisherigen Ablauf bleiben.
- Keine Datenbank-Schemaänderung; Kompatibilitätsziel bleibt PrestaShop 1.7.8, 8.x und 9.x mit PHP 7.4+.

### Version 1.3.1 (Mai 2026)

**Öffentliche Version:**
- Strukturierte Auswahl im Front Office: gesamte Bestellung widerrufen oder einzelne Artikel/Dienstleistungen widerrufen.
- Teilwiderruf ohne Freitextgrund: Artikelname, Artikelnummer und Menge werden in wiederholbaren Zeilen abgefragt.
- Für Teilwiderrufe muss mindestens Artikelname oder Artikelnummer angegeben werden; leere Zeilen werden ignoriert.
- Kunden- und Admin-Eingangsbestätigungen enthalten Umfang des Widerrufs und die übermittelten Artikel-/Dienstleistungsangaben.
- Angaben zum Teilwiderruf werden als Bestellnachricht gespeichert, damit sie im Back Office geprüft werden können.
- Keine Datenbank-Schemaänderung; bestehende Händler-Einstellungen bleiben erhalten.
- Kompatibilitätsziel bleibt PrestaShop 1.7.8, 8.x und 9.x mit PHP 7.4+.

### Version 1.3.0 (Mai 2026)

**Öffentliche Version:**
- Standardmäßig manuelle Prüfung im Back Office statt automatischer Entscheidung
- Angaben zur Bestellzuordnung bündeln Kundenprüfung und Bestellsuche in einer Auswahl
- Empfohlener Standard: Vor- und Nachname + Bestellnummer
- Optional wählbar: Vor- und Nachname + Bestell-ID, Vor- und Nachname + Bestell-ID oder Bestellnummer sowie E-Mail + Bestell-ID oder Bestellnummer (ältere Methode, nicht empfohlen)
- Öffentliches Formular zeigt keinen Widerrufsbelehrungs-`<details>`-Block und keinen pauschalen 14-Tage-Hinweis mehr
- Frontend-Seitentitel lautet „Vertrag widerrufen“
- Frühere interne 1.3.x-Upgrade-Arbeiten wurden in `upgrade-1.3.0.php` zusammengeführt

**Upgrade von 1.2.4 auf 1.3.0:**
- Direkter Update-Pfad von 1.2.4 auf 1.3.0; frühere interne 1.3.x-Änderungen sind in dieser öffentlichen Version gebündelt.
- Keine Datenbank-Schemaänderung; bestehende Händler-Einstellungen bleiben erhalten.
- Neue fehlende Standardwerte: manuelle Prüfung, Vor- und Nachname + Bestellnummer zur Bestellzuordnung und aktivierter PHP-Mail-Fallback.
- Eingangsbestätigungen formulieren den Eingang der Widerrufsanfrage und die übermittelten Angaben, statt eine endgültige Entscheidung zu suggerieren.
- Neue Back-Office-Optionen für Angaben zur Bestellzuordnung, Mail-Fallback, Mail-Vorschau, Testversand und Zurücksetzen auf Standardwerte.
- Dokumentationslinks im Back Office führen sprachabhängig direkt zu den zentral gepflegten GetApps4Shops-Downloads.
- Öffentliches Formular gehärtet: CSRF-Token, Honeypot, IP-Rate-Limit, Dublettenprüfung und HTML-sichere Kundennotizen.
- Zuverlässigerer Mailversand über zentralen MailDispatcher, Diagnose-Logs und PHP-Mail-Fallback.
- Mailtemplates und Übersetzungen für alle ausgelieferten Sprachen vervollständigt.

### Version 1.0.0 (Oktober 2025)

**Neue Features:**
- EU-Richtlinien-konforme Widerrufsverwaltung
- Automatische Bestellstatus-Erstellung
- Doppeltes E-Mail-Benachrichtigungssystem
- 8 Sprachen vorinstalliert
- Flexible Anzeigeoptionen (Footer, Kundenkonto-Menü, Kundenkonto-Seite)
- Button/Link Toggle für Footer
- Anpassbare Button-Farbe (Hex-Farbcode)
- CMS-Integration für Datenschutz- und Widerrufshinweise
- Pro-Sprache-Konfiguration für CMS-Seiten und Hinweistexte
- Konfigurierbare Widerrufsfrist (1-365 Tage)
- Lieferpuffer (0-365 Tage)
- Mehrere Admin-E-Mail-Empfänger
- Modernes shadcn-inspiriertes Admin-Interface
- PrestaShop 8/9 Kompatibilität
- Locale-konforme Preisformatierung
- SSL-gesicherte Formulare
- SEO-freundliche URLs

## Lizenz

**Academic Free License 3.0 (AFL-3.0)**

Copyright © 2025 sjmedia GmbH

Dieses Modul ist freie Software: Sie können es unter den Bedingungen der Academic Free License weitergeben und/oder modifizieren.

Dieses Modul wird in der Hoffnung verbreitet, dass es nützlich ist, aber OHNE JEDE GEWÄHRLEISTUNG. Siehe die Academic Free License für weitere Details.

Eine Kopie der Lizenz sollte mit diesem Modul geliefert worden sein. Falls nicht, besuchen Sie bitte:
http://opensource.org/licenses/afl-3.0.php

---

**Haftungsausschluss**

Dieses Modul bietet technische Funktionalität zur Erleichterung von EU-konformen Widerrufsanträgen. Obwohl es so konzipiert ist, dass es mit den Anforderungen der EU-Richtlinie 2011/83/EU übereinstimmt, **bleiben Händler allein verantwortlich für die Gewährleistung vollständiger rechtlicher Konformität** mit allen anwendbaren Gesetzen und Vorschriften in ihrer Rechtsordnung.

**Verwendung auf eigene Gefahr.** Die Autoren und Urheberrechtsinhaber haften nicht für rechtliche Streitigkeiten, Verstöße gegen Vorschriften oder Schäden, die aus der Verwendung dieses Moduls entstehen.

---

*Zuletzt aktualisiert: 2. Juni 2026*
*Modul-Version: 1.4.0*
*Dokumentation: 1.4.0*
