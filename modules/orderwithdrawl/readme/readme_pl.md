# Moduł Odstąpienia od Zamówienia dla PrestaShop

## Przegląd

Moduł odstąpienia od zamówienia umożliwia Twoim klientom łatwe i bezpieczne skorzystanie z ustawowego prawa do odstąpienia od umowy zgodnie z przepisami UE o ochronie konsumentów.
Moduł oferuje przyjazne rozwiązanie z obsługą wielu języków, automatycznymi powiadomieniami e-mail i elastycznymi opcjami wyświetlania.

### Główne zalety

- ✅ **Zgodność z prawem UE** – spełnia wymogi dyrektywy 2011/83/UE
- ✅ **Łatwa obsługa** – proste, intuicyjne formularze dla klientów
- ✅ **Automatyzacja procesów** – zmiana statusów i e-maile wysyłane automatycznie
- ✅ **Elastyczna prezentacja** – przycisk odstąpienia widoczny w kilku miejscach jednocześnie
- ✅ **Pełna wielojęzyczność** – obsługa 8 języków „out-of-the-box”
- ✅ **Bezpieczeństwo prawne** – wbudowane informacje o ochronie danych i odstąpieniu

## Funkcje w szczegółach

### 1. Wiele opcji wyświetlania

Pokaż przycisk odstąpienia w miejscach najlepiej pasujących do Twojego sklepu:

- **W stopce** – jako widoczny kolorowy przycisk lub dyskretny link tekstowy
- **W menu konta klienta** – dla starszych motywów PrestaShop 1.7
- **Na stronie konta klienta** – nowoczesny wygląd kafelkowy dla PrestaShop 8/9

Możesz też włączyć kilka pozycji jednocześnie, np. stopka + strona konta klienta.

![Pozycje przycisku odstąpienia](views/img/de/screen-1-where-the-withdrawal-button-appears.png)

### 2. Indywidualne dostosowanie

Dopasuj moduł do identyfikacji wizualnej swojej marki:

- **Dowolny kolor przycisku** – kod HEX dopasowany do kolorystyki marki
- **Przycisk lub link** – wybierz między wyraźnym przyciskiem a dyskretnym linkiem
- **Własne etykiety** – zdefiniuj teksty przycisków osobno dla każdego języka

![Ustawienia modułu w panelu administracyjnym](views/img/de/screen-2-module-settings-in-the-back-office.png)

### 3. Integracja CMS i informacje prawne

Połącz moduł ze stronami prawnymi sklepu:

#### Informacja o ochronie danych
- Podłącz stronę polityki prywatności dla każdego języka
- Dodaj własną notę informacyjną (np. „Twoje dane są wykorzystywane wyłącznie do obsługi wniosku o odstąpienie”)
- Tekst wyświetla się automatycznie na powiązanej stronie CMS

#### Informacja o prawie odstąpienia
- Podłącz stronę z pouczeniem o odstąpieniu dla każdego języka
- Dodaj informację o elektronicznym przycisku odstąpienia
- Poinformuj klientów o automatycznym potwierdzeniu otrzymania e-maila

![Zarządzanie tekstami prawnymi i informacjami o prywatności](views/img/de/screen-3-manage-legal-texts-and-privacy-notes.png)

### 4. Automatyczne zarządzanie statusami zamówień

- Automatycznie tworzy status zamówienia **„Odstąpienie zgłoszone”** oznaczony kolorem pomarańczowym
- Zmienia status natychmiast po wysłaniu formularza odstąpienia
- Umożliwia łatwe filtrowanie i zarządzanie wszystkimi wnioskami o odstąpienie

![Status zamówienia „Odstąpienie zgłoszone”](views/img/de/screen-5-withdrawal-request-status-in-orders.png)

### 5. Inteligentne obliczanie terminów

Moduł automatycznie oblicza termin odstąpienia:

- **Okres odstąpienia** – domyślnie 14 dni (zgodnie z dyrektywą UE)
- **Bufor dostawy** – dodatkowe dni na wysyłkę
- **Automatyczna walidacja** – system sprawdza, czy termin nadal obowiązuje

**Przykład:** 14 dni odstąpienia + 3 dni bufora = 17 dni od daty zamówienia

### 6. Podwójny system e-mail

#### Potwierdzenie dla klienta
Klient otrzymuje natychmiast po wysłaniu potwierdzenie e-mail zawierające:
- Numer referencyjny zamówienia
- Adres e-mail
- Datę i godzinę wysłania żądania
- Potwierdzenie odbioru na trwałym nośniku (zgodnie z § 356 BGB)

#### Powiadomienie administratora
Ty i Twój zespół otrzymujecie wiadomość z:
- Numerem i referencją zamówienia
- Imieniem, nazwiskiem i e-mailem klienta
- Wartością zamówienia
- Znacznikiem czasu wysłania

Możesz dodać wiele adresów e-mail (oddzielonych przecinkami) do powiadomień.

### 7. Obsługa wielu języków

Moduł jest całkowicie przetłumaczony na:
- 🇩🇪 Niemiecki
- 🇬🇧 Angielski
- 🇫🇷 Francuski
- 🇮🇹 Włoski
- 🇪🇸 Hiszpański
- 🇵🇱 Polski
- 🇳🇱 Niderlandzki
- 🇷🇴 Rumuński

Wszystkie teksty, szablony e-mail i etykiety przycisków można indywidualnie dostosować w każdym języku.

---

## Wymagania systemowe

- **PrestaShop:** 1.7.8 lub nowsza (testowane z PrestaShop 8.x i 9.x)
- **PHP:** 7.4 lub nowszy (obsługiwany PHP 8.x)
- **MySQL/MariaDB:** 5.6+ / 10.1+
- **Certyfikat SSL:** zalecany dla bezpiecznego przesyłania formularzy

---

## Instalacja

### Przez panel administracyjny PrestaShop (zalecane)

1. Pobierz plik ZIP modułu
2. Przejdź do **Moduły → Zarządzanie modułami**
3. Kliknij **Prześlij moduł**
4. Wybierz plik ZIP i prześlij go
5. Kliknij **Zainstaluj**

Podczas instalacji moduł automatycznie tworzy:
- Status zamówienia „Odstąpienie zgłoszone” (kolor pomarańczowy)
- Domyślną konfigurację z 14 dniami na odstąpienie
- Wszystkie potrzebne szablony e-mail w 8 językach

### Po instalacji

1. Kliknij **Konfiguruj** w module
2. Sprawdź okres odstąpienia (domyślnie 14 dni)
3. Wprowadź dodatkowe dni dostawy, jeśli potrzebne
4. Wybierz, gdzie ma się pojawiać przycisk odstąpienia
5. Dostosuj kolor i tekst przycisku
6. Połącz strony CMS (polityka prywatności, pouczenie o odstąpieniu)
7. Dodaj wymagane teksty prawne
8. Zapisz ustawienia

---

## Szczegóły konfiguracji

### Ustawienia podstawowe

| Ustawienie | Opis | Domyślne |
|-------------|------|-----------|
| **Okres odstąpienia (dni)** | Ustawowy okres odstąpienia | 14 dni |
| **Szacowany czas dostawy (dni)** | Bufor dla czasu dostawy | 0 dni |
| **Weryfikacja klienta** | Zalecane: imię i nazwisko + identyfikator zamówienia; opcjonalnie sam identyfikator; legacy: e-mail + identyfikator | Imię i nazwisko + identyfikator |
| **Tryb oceny** | Ręczna weryfikacja albo automatyczna kontrola terminu | Ręczna weryfikacja |
| **Dodatkowe e-maile administratora** | Dodatkowi odbiorcy powiadomień (oddzieleni przecinkami) | Puste |

### Opcje wyświetlania

| Ustawienie | Opcje | Opis |
|-------------|--------|------|
| **Pozycja wyświetlania** | Stopka / Menu konta klienta / Strona konta klienta | Można wybrać kilka jednocześnie |
| **Wygląd w stopce** | Przycisk / Link | Przycisk jest bardziej widoczny, link subtelny |
| **Kolor przycisku** | Kod HEX | Domyślnie #F44708 (pomarańczowy) |

### Konfiguracja dla każdego języka

- **Etykieta przycisku** – indywidualny tekst przycisku odstąpienia
- **Strona prywatności** – powiązana strona CMS z polityką prywatności
- **Informacja o prywatności** – tekst dotyczący przetwarzania danych przy odstąpieniu
- **Strona odstąpienia** – powiązana strona CMS z pouczeniem o odstąpieniu
- **Notatka o odstąpieniu** – tekst informacyjny o elektronicznym przycisku odstąpienia


## Użycie

### Dla klientów

1. **Znajdź przycisk odstąpienia** – w zależności od konfiguracji w stopce, menu konta lub na stronie konta klienta
2. **Otwórz formularz** – kliknięcie przycisku/linku otwiera formularz odstąpienia
3. **Wprowadź dane:**
   - Skonfigurowany identyfikator zamówienia (numer, referencja albo oba)
   - Imię i nazwisko klienta w trybie domyślnym; e-mail tylko w trybie legacy
4. **Wyślij odstąpienie** – kliknij „Potwierdź odstąpienie”
5. **Odbierz potwierdzenie:**
   - Komunikat o powodzeniu na ekranie
   - E-mail z potwierdzeniem, identyfikatorem zamówienia i podanym imieniem/nazwiskiem
   - Status zamówienia zmienia się na „Odstąpienie zgłoszone”

![Formularz odstąpienia dla klientów](views/img/de/screen-4-order-withdrawal-form.png)

**Ważne informacje dla klientów:**
- Domyślnie sprawdzane są imię i nazwisko klienta oraz identyfikator zamówienia
- Sklep może opcjonalnie używać samego identyfikatora albo trybu legacy z e-mailem
- Nowe instalacje domyślnie używają ręcznej weryfikacji; automatyczna kontrola terminu pozostaje konfigurowalna
- Po upływie terminu wyświetlany jest odpowiedni komunikat

---

### Dla sprzedawców

#### Obsługa zgłoszeń odstąpienia

1. **Otrzymanie powiadomienia** – otrzymujesz e-mail ze wszystkimi szczegółami zamówienia
2. **Znajdź zamówienie** – przejdź do **Zamówienia → Zamówienia**
3. **Sprawdź status** – zamówienie ma status „Odstąpienie zgłoszone” (pomarańczowy)
4. **Przetwórz zwrot:**
   - Sprawdź warunki odstąpienia
   - Zwróć środki zgodnie z metodą płatności
   - Zmień status zamówienia (np. „Zwrócone”)
5. **Poinformuj klienta:**
   - Kiedy nastąpi zwrot pieniędzy
   - Czy produkt ma zostać odesłany
   - Przewidywana data zwrotu środków

#### Przegląd wszystkich zgłoszeń

1. Przejdź do **Zamówienia → Zamówienia**
2. Filtrowanie po statusie **„Odstąpienie zgłoszone”**
3. Sortuj według daty, aby przetwarzać najstarsze zgłoszenia w pierwszej kolejności

#### Zarządzanie odbiorcami e-mail

W konfiguracji modułu w polu **„Dodatkowe e-maile administratora”** dodaj kolejne adresy oddzielone przecinkami:

sprzedaz@twojsklep.pl
,zwroty@twojsklep.pl
,prawo@twojsklep.pl


Wszyscy adresaci otrzymają powiadomienia równocześnie.

---

## Szablony e-mail

Pliki e-mail znajdują się w:

modules/orderwithdrawl/mails/{jezyk}/
├── withdrawl_confirm.html (Potwierdzenie klienta – HTML)
├── withdrawl_confirm.txt (Potwierdzenie klienta – tekst)
├── withdrawl_admin.html (Powiadomienie administratora – HTML)
└── withdrawl_admin.txt (Powiadomienie administratora – tekst)


### Dostępne zmienne

**Potwierdzenie klienta:**
- `{order_id}` – numer zamówienia
- `{order_reference}` – numer referencyjny
- `{order_identifier}` – widoczny identyfikator zgodny z ustawieniem modułu
- `{customer_name}` – imię i nazwisko podane w formularzu
- `{email}` – adres e-mail klienta
- `{date}` – znacznik czasu

**Powiadomienie administratora:**
- `{order_reference}` – numer referencyjny
- `{order_id}` – numer zamówienia
- `{order_identifier}` – widoczny identyfikator zgodny z ustawieniem modułu
- `{customer_email}` – e-mail klienta
- `{customer_name}` – imię i nazwisko klienta
- `{date}` – znacznik czasu
- `{order_total}` – wartość zamówienia (sformatowana)

---

## Rozwiązywanie problemów

### „Termin odstąpienia minął”
**Przyczyna:** wyliczony termin (data zamówienia + dostawa + okres odstąpienia) został przekroczony.
**Rozwiązanie:**
- Sprawdź ustawienia dotyczące odstąpienia i dostawy
- Zwiększ bufor dostawy, jeśli przesyłki trwają dłużej
- Upewnij się, że data zamówienia jest poprawna

### „Adres e-mail nie pasuje do zamówienia”
**Przyczyna:** klient podał inny adres e-mail.
**Rozwiązanie:**
- Klient musi użyć dokładnie tego adresu, który podał przy zamówieniu
- Sprawdź e-mail w szczegółach zamówienia
- Wielkość liter nie ma znaczenia

### „Status odstąpienia nie skonfigurowany”
**Przyczyna:** status nie został utworzony przy instalacji.
**Rozwiązanie:**
1. Odinstaluj moduł
2. Zainstaluj ponownie
3. Sprawdź w **Zamówienia → Statusy**, czy istnieje „Odstąpienie zgłoszone”

### „Powiadomienia e-mail nie dochodzą”
**Diagnoza:**
1. Sprawdź **Ustawienia zaawansowane → E-mail**
2. Wyślij wiadomość testową
3. Sprawdź logi w `var/logs/`

**Rozwiązania:**
- Skonfiguruj SMTP zamiast PHP mail()
- Sprawdź rekordy SPF i DKIM swojej domeny
- Sprawdź folder spam
- Upewnij się, że adresy są prawidłowe

### „Przycisk odstąpienia nie wyświetla się w stopce”
**Diagnoza:**
1. Upewnij się, że opcja „Stopka” jest aktywna
2. Sprawdź w kodzie źródłowym, czy plik CSS jest załadowany
3. Otwórz konsolę przeglądarki i sprawdź błędy

**Rozwiązania:**
- Włącz wyświetlanie w stopce
- Wyczyść pamięć podręczną: **Ustawienia zaawansowane → Wydajność → Wyczyść cache**
- Upewnij się, że motyw obsługuje hook `displayFooter`

---

## Informacje prawne

### Podstawa prawna (Niemcy)

**§ 355 BGB – Prawo odstąpienia**
- Konsumenci mają 14 dni na odstąpienie od umowy zawartej na odległość
- Termin liczony jest od dnia otrzymania towaru
- Brak pouczenia wydłuża termin do 12 miesięcy + 14 dni

**§ 356 BGB – Wykonanie prawa odstąpienia**
- Odstąpienie musi być złożone w formie pisemnej
- Wymagane jest potwierdzenie na trwałym nośniku
- **Moduł spełnia ten wymóg przez automatyczny e-mail potwierdzający**

**§ 312g BGB – Prawo odstąpienia przy sprzedaży na odległość**
- Sprzedawca musi poinformować o prawie odstąpienia
- Należy udostępnić wzór formularza
- **Moduł zawiera elektroniczny formularz odstąpienia**

**Art. 246a EGBGB – Obowiązki informacyjne**
- Konsument musi być poinformowany o sposobie odstąpienia
- Obejmuje to możliwość odstąpienia elektronicznego

### Dyrektywa UE 2011/83/UE

Moduł spełnia wymogi techniczne:
- **Art. 6(1)(h)** – Informacje o prawie odstąpienia
- **Art. 11** – Udostępnienie formularza odstąpienia
- **Art. 14** – Potwierdzenie na trwałym nośniku (e-mail)

### Co oferuje moduł

✅ Techniczny system obsługi odstąpień
✅ E-mailowe potwierdzenie na trwałym nośniku
✅ Śledzenie statusu zamówień
✅ Konfigurowalne terminy odstąpienia

### Co musi zapewnić sprzedawca

⚠️ Pełne pouczenie o odstąpieniu na stronie
⚠️ Instrukcje dotyczące zwrotu towaru
⚠️ Zwrot pieniędzy w ciągu 14 dni od odstąpienia
⚠️ Prawidłową dokumentację
⚠️ Przeszkolenie pracowników
⚠️ Zgodność z przepisami lokalnymi

**Ważne:** moduł nie zastępuje porady prawnej. W celu zapewnienia pełnej zgodności skonsultuj się z prawnikiem.

---

## Wsparcie i kontakt

**Pomoc techniczna:**
- Skorzystaj z kanału wsparcia marketplace przypisanego do zamówienia modułu.

**W zgłoszeniu podaj:**
1. Wersję PrestaShop
2. Wersję PHP
3. Wersję modułu
4. Opis problemu
5. Kroki do jego odtworzenia
6. Komunikaty błędów / logi
7. Screenshoty (jeśli potrzebne)

---

## Dziennik zmian

### Wersja 1.4.0 (czerwiec 2026)

**Wydanie publiczne:**
- Nowa opcjonalna opcja w Back Office pokazująca produkty z zamówienia po weryfikacji danych klienta.
- Klient może wybrać całe zamówienie albo konkretne artykuły z prawidłową ilością.
- Lista produktów jest ładowana po stronie serwera i nie używa AJAX.
- Opcja jest domyślnie wyłączona, a istniejące ustawienia są zachowane.
- Brak zmian w schemacie bazy danych; kompatybilność pozostaje ukierunkowana na PrestaShop 1.7.8, 8.x i 9.x z PHP 7.4+.

### Wersja 1.3.1 (maj 2026)

**Wydanie publiczne:**
- Strukturalny wybór w Front Office: odstąpienie od całego zamówienia albo od wybranych artykułów/usług.
- Częściowe odstąpienie używa powtarzalnych wierszy: nazwa artykułu, numer artykułu i ilość.
- Nie jest wymagany powód odstąpienia; pola służą wyłącznie do identyfikacji umowy lub części umowy.
- Potwierdzenia dla klienta i administratora zawierają zakres odstąpienia oraz przekazane szczegóły artykułów/usług.
- Szczegóły częściowego odstąpienia są zapisywane jako wiadomość zamówienia do weryfikacji w Back Office.
- Brak zmian w schemacie bazy danych; istniejące ustawienia są zachowane.
- Kompatybilność pozostaje ukierunkowana na PrestaShop 1.7.8, 8.x i 9.x z PHP 7.4+.

### Wersja 1.3.0 (maj 2026)

**Wydanie publiczne:**
- Domyślna ręczna weryfikacja dla nowych instalacji
- Zalecana weryfikacja: imię i nazwisko klienta + identyfikator zamówienia
- Opcjonalny tryb samego identyfikatora oraz tryb legacy e-mail + identyfikator
- Formularz publiczny nie pokazuje już bloku `<details>` z informacją o odstąpieniu
- Wcześniejsze wewnętrzne prace aktualizacyjne 1.3.x połączono w `upgrade-1.3.0.php`

**Aktualizacja z 1.2.4 do 1.3.0:**
- Bezpośrednia ścieżka aktualizacji z 1.2.4 do 1.3.0; wcześniejsze wewnętrzne zmiany 1.3.x są połączone w tym publicznym wydaniu.
- Brak zmian w schemacie bazy danych; istniejące ustawienia sprzedawcy są zachowane.
- Nowe brakujące wartości domyślne: ręczna weryfikacja, weryfikacja imię i nazwisko + identyfikator zamówienia, wyszukiwanie po numerze/referencji zamówienia oraz aktywny fallback PHP mail.
- Wiadomości potwierdzające odbiór potwierdzają wniosek o odstąpienie i przekazane dane, bez sugerowania ostatecznej decyzji.
- Nowe opcje Back Office dla weryfikacji klienta, wyszukiwania zamówień, awaryjnej wysyłki e-maili, podglądu wiadomości, wysyłki testowej i resetu ustawień.
- Formularz publiczny wzmocniony: token CSRF, honeypot, limit IP, blokada duplikatów oraz notatki klienta bezpieczne dla HTML.
- Bardziej niezawodna wysyłka e-mail dzięki centralnemu MailDispatcher, diagnostyce i fallbackowi PHP mail.
- Szablony e-mail i tłumaczenia uzupełnione dla wszystkich dostarczanych języków.

### Wersja 1.0.0 (październik 2025)

**Nowe funkcje:**
- Obsługa odstąpień zgodna z dyrektywami UE
- Automatyczne tworzenie statusu zamówienia
- Podwójny system powiadomień e-mail
- 8 języków w zestawie
- Elastyczne opcje wyświetlania (stopka, menu konta, strona konta)
- Przycisk/link przełączany w stopce
- Kolor przycisku konfigurowalny (HEX)
- Integracja CMS dla stron prawnych i prywatności
- Konfiguracja stron i tekstów osobno dla każdego języka
- Konfigurowalny termin odstąpienia (1–365 dni)
- Bufor dostawy (0–365 dni)
- Wielu odbiorców e-mail admina
- Nowoczesny interfejs admina (inspirowany shadcn)
- Kompatybilność z PrestaShop 8/9
- Lokalna obsługa formatowania cen
- Formularze zabezpieczone SSL
- Przyjazne dla SEO adresy URL

---

## Licencja

**Academic Free License 3.0 (AFL-3.0)**

Copyright © 2025 sjmedia GmbH

Ten moduł jest wolnym oprogramowaniem: możesz go rozpowszechniać i/lub modyfikować zgodnie z warunkami licencji Academic Free License.
Udostępniany w nadziei, że będzie użyteczny, ale **bez żadnej gwarancji**. Szczegóły w treści licencji.

Kopia licencji powinna być dołączona do modułu. Jeśli nie, odwiedź:
[http://opensource.org/licenses/afl-3.0.php](http://opensource.org/licenses/afl-3.0.php)

---

**Zastrzeżenie**

Moduł zapewnia techniczną funkcjonalność ułatwiającą składanie odstąpień zgodnie z prawem UE.
Mimo zgodności z dyrektywą 2011/83/UE, **pełna odpowiedzialność za zgodność prawną spoczywa na sprzedawcy.**

**Używasz na własne ryzyko.** Autorzy nie ponoszą odpowiedzialności za spory prawne ani szkody wynikające z korzystania z modułu.

---

*Ostatnia aktualizacja: 2 czerwca 2026*
*Wersja modułu: 1.4.0*
*Dokumentacja: 1.4.0*
