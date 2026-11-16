<?php
/**
 * Renders one of the on-disk mail templates for the admin preview tab.
 *
 * Consumed exclusively by the back-office AJAX endpoint
 * `ajaxProcessPreviewMail`. Never used to send real mails - that path still
 * goes through {@see \OrderWithdrawl\Mail\MailDispatcher}.
 *
 * The renderer reads the raw HTML/TXT template, substitutes the documented
 * placeholder contract (see {@see \OrderWithdrawl\Mail\MailDispatcher} class
 * docblock) with safe sample data, and returns the resulting string. Input
 * parameters (template name, ISO code) are whitelist-validated so the
 * admin cannot be tricked into reading an arbitrary file.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;
use OrderWithdrawl\Mail\MailDispatcher;
use OrderWithdrawl\Security\SecurityGuard;

final class MailPreviewRenderer
{
    /** @var list<string> Template base names we allow the admin to preview. */
    public const ALLOWED_TEMPLATES = [
        'withdrawl_confirm',
        'withdrawl_admin',
        'withdrawl_approved',
        'withdrawl_rejected',
    ];

    /** @var string Absolute path to the mails/ directory (with trailing slash). */
    private $mailDirectory;

    public function __construct(string $mailDirectory)
    {
        $this->mailDirectory = rtrim($mailDirectory, '/') . '/';
    }

    /**
     * Return the rendered HTML of the requested template, or an empty string
     * if the template cannot be located. Placeholder substitution is applied.
     *
     * @param string $template One of {@see self::ALLOWED_TEMPLATES}
     * @param string $iso ISO-639-1 language code; silently falls back to the module's supported list when invalid
     * @param array<string, mixed> $sampleVars Optional explicit sample data; merged over the default map
     */
    public function render(string $template, string $iso, array $sampleVars = []): string
    {
        $template = self::normalizeTemplateName($template);
        if ($template === '') {
            return '';
        }

        $resolvedIso = SecurityGuard::validateLanguageIso($iso);
        $path = $this->resolveTemplatePath($resolvedIso, $template);
        if ($path === null) {
            return '';
        }

        $raw = @file_get_contents($path);
        if ($raw === false) {
            return '';
        }

        $vars = array_merge($this->getDefaultSampleVars($template, $resolvedIso), $sampleVars);

        return $this->substitute($raw, $vars);
    }

    public static function normalizeTemplateName(string $template): string
    {
        switch ($template) {
            case 'withdrawl_confirm':
                return 'withdrawl_confirm';
            case 'withdrawl_admin':
                return 'withdrawl_admin';
            case 'withdrawl_approved':
                return 'withdrawl_approved';
            case 'withdrawl_rejected':
                return 'withdrawl_rejected';
        }

        return '';
    }

    /**
     * The placeholder map we present as a preview. Values use neutral demo
     * data (never real customer info) so the admin can see how each token
     * renders without leaking anything.
     *
     * Keys intentionally mirror {@see MailDispatcher::getPlaceholders} so a
     * missing placeholder in either side is easy to spot during a code
     * review.
     *
     * @return array<string, string>
     */
    public function getDefaultSampleVars(string $template, string $iso = 'en'): array
    {
        $contractPartSample = $this->getLocalizedContractPartSample($iso);
        $previewCopy = $this->getLocalizedPreviewCopy($iso);
        $base = [
            '{firstname}' => 'Max',
            '{lastname}' => 'Mustermann',
            '{consumer_name}' => 'Max Mustermann',
            '{order_id}' => '12345',
            '{order_reference}' => 'ABC123XYZ',
            '{order_identifier}' => '12345 / ABC123XYZ',
            '{email}' => 'customer@example.com',
            '{date}' => MailDispatcher::formatMailDate(),
            '{history_url}' => 'https://shop.example.com/index.php?controller=history',
            '{withdrawal_scope}' => $contractPartSample['scope'],
            '{contract_part_details}' => $contractPartSample['plain'],
            '{contract_part_details_html}' => $contractPartSample['html'],
            '{customer_note}' => $previewCopy['customer_note'],
            '{customer_note_html}' => $previewCopy['customer_note'],
            '{deadline_review_notice}' => '',
            '{deadline_review_notice_html}' => '',
            '{process_continuation_notice}' => 'Note: The withdrawal was successfully submitted. Further processing, review and handling will follow your internal business process.',
            '{process_continuation_notice_html}' => 'Note: The withdrawal was successfully submitted. Further processing, review and handling will follow your internal business process.',
            '{customer_email}' => 'customer@example.com',
            '{customer_name}' => 'Max Mustermann',
            '{order_total}' => '199,00 &euro;',
            '{status_message}' => $previewCopy['status_message'],
            '{shop_name}' => 'Demo Shop',
            '{shop_url}' => 'https://shop.example.com/',
            '{shop_logo}' => '',
        ];

        // The shop-name/url/logo tokens are injected by PrestaShop itself at
        // send time. For the preview we substitute neutral values so the
        // template is self-contained.
        return $base;
    }

    /**
     * @return array{customer_note:string, status_message:string}
     */
    private function getLocalizedPreviewCopy(string $iso): array
    {
        $samples = [
            'de' => [
                'customer_note' => '[Vorschau] Beispielhafte Kundenanmerkung zum Widerruf.',
                'status_message' => '[Vorschau] Hier erscheint Ihre Freitext-Nachricht an den Kunden.',
            ],
            'en' => [
                'customer_note' => '[Preview] Sample customer note for the withdrawal request.',
                'status_message' => '[Preview] Your free-text message to the customer appears here.',
            ],
            'es' => [
                'customer_note' => '[Vista previa] Nota de ejemplo del cliente sobre el desistimiento.',
                'status_message' => '[Vista previa] Aquí aparece su mensaje libre para el cliente.',
            ],
            'fr' => [
                'customer_note' => '[Aperçu] Exemple de remarque client concernant la rétractation.',
                'status_message' => '[Aperçu] Votre message libre destiné au client apparaît ici.',
            ],
            'it' => [
                'customer_note' => '[Anteprima] Nota cliente di esempio relativa al recesso.',
                'status_message' => '[Anteprima] Qui appare il messaggio libero per il cliente.',
            ],
            'nl' => [
                'customer_note' => '[Voorbeeld] Voorbeeldopmerking van de klant over de herroeping.',
                'status_message' => '[Voorbeeld] Hier verschijnt uw vrije tekstbericht aan de klant.',
            ],
            'pl' => [
                'customer_note' => '[Podgląd] Przykładowa uwaga klienta dotycząca odstąpienia.',
                'status_message' => '[Podgląd] Tutaj pojawi się Państwa wiadomość tekstowa do klienta.',
            ],
            'ro' => [
                'customer_note' => '[Previzualizare] Notă exemplu a clientului privind retragerea.',
                'status_message' => '[Previzualizare] Aici apare mesajul dumneavoastră liber către client.',
            ],
        ];

        return $samples[SecurityGuard::validateLanguageIso($iso)] ?? $samples['en'];
    }

    /**
     * @return array{scope:string, plain:string, html:string}
     */
    private function getLocalizedContractPartSample(string $iso): array
    {
        $samples = [
            'de' => [
                'scope' => 'Einzelne Artikel oder Dienstleistungen',
                'item_name' => 'Artikelname:',
                'item_number' => 'Artikelnummer:',
                'quantity' => 'Menge:',
                'product' => 'Demo-Produkt',
            ],
            'en' => [
                'scope' => 'Selected items or services',
                'item_name' => 'Item name:',
                'item_number' => 'Item number:',
                'quantity' => 'Quantity:',
                'product' => 'Demo product',
            ],
            'es' => [
                'scope' => 'Artículos o servicios seleccionados',
                'item_name' => 'Nombre del artículo:',
                'item_number' => 'Número de artículo:',
                'quantity' => 'Cantidad:',
                'product' => 'Producto de demostración',
            ],
            'fr' => [
                'scope' => 'Articles ou services sélectionnés',
                'item_name' => 'Nom de l’article :',
                'item_number' => 'Numéro d’article :',
                'quantity' => 'Quantité :',
                'product' => 'Produit de démonstration',
            ],
            'it' => [
                'scope' => 'Articoli o servizi selezionati',
                'item_name' => 'Nome articolo:',
                'item_number' => 'Numero articolo:',
                'quantity' => 'Quantità:',
                'product' => 'Prodotto demo',
            ],
            'nl' => [
                'scope' => 'Geselecteerde artikelen of diensten',
                'item_name' => 'Artikelnaam:',
                'item_number' => 'Artikelnummer:',
                'quantity' => 'Aantal:',
                'product' => 'Demoproduct',
            ],
            'pl' => [
                'scope' => 'Wybrane artykuły lub usługi',
                'item_name' => 'Nazwa artykułu:',
                'item_number' => 'Numer artykułu:',
                'quantity' => 'Ilość:',
                'product' => 'Produkt demonstracyjny',
            ],
            'ro' => [
                'scope' => 'Articole sau servicii selectate',
                'item_name' => 'Nume articol:',
                'item_number' => 'Număr articol:',
                'quantity' => 'Cantitate:',
                'product' => 'Produs demonstrativ',
            ],
        ];

        $sample = $samples[SecurityGuard::validateLanguageIso($iso)] ?? $samples['en'];
        $plain = sprintf(
            "1.\n%s %s\n%s SKU-123\n%s 1",
            $sample['item_name'],
            $sample['product'],
            $sample['item_number'],
            $sample['quantity']
        );

        return [
            'scope' => $sample['scope'],
            'plain' => $plain,
            'html' => nl2br(htmlspecialchars($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8'), false),
        ];
    }

    /**
     * Language-fallback template resolver mirroring the MailDispatcher
     * chain so the preview faithfully reflects what would actually be
     * delivered.
     */
    private function resolveTemplatePath(string $iso, string $template)
    {
        $candidate = $this->resolveLocalTemplatePath($iso, $template);
        if ($candidate !== null) {
            return $candidate;
        }

        foreach (['de', 'en'] as $fallback) {
            if ($fallback === $iso) {
                continue;
            }
            $fallbackPath = $this->resolveLocalTemplatePath($fallback, $template);
            if ($fallbackPath !== null) {
                return $fallbackPath;
            }
        }

        return null;
    }

    private function resolveLocalTemplatePath(string $iso, string $template)
    {
        $iso = SecurityGuard::validateLanguageIso($iso);
        $template = self::normalizeTemplateName($template);
        if ($template === '') {
            return null;
        }

        $base = $this->getRealMailDirectory();
        if ($base === '') {
            return null;
        }

        $path = realpath($base . $iso . DIRECTORY_SEPARATOR . $template . '.html');
        if ($path === false || strpos($path, $base) !== 0) {
            return null;
        }

        return $path;
    }

    private function getRealMailDirectory(): string
    {
        $base = realpath($this->mailDirectory);
        if ($base === false) {
            return '';
        }

        return rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Replace every `{placeholder}` key with its sample value. Anything not
     * in the map is left verbatim so missing placeholders stand out during
     * the preview.
     *
     * @param array<string, mixed> $vars
     */
    private function substitute(string $raw, array $vars): string
    {
        $stringVars = [];
        foreach ($vars as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $stringVars[$key] = is_scalar($value) ? (string) $value : '';
        }

        return strtr($raw, $stringVars);
    }

    /**
     * Expose the whitelisted ISO list for the admin UI dropdown. Keeps the
     * tpl free of magic strings and guarantees the UI never offers a
     * language the module cannot deliver.
     *
     * @return list<string>
     */
    public static function getAllowedIsoCodes(): array
    {
        return OrderWithdrawlConfig::ALLOWED_LANGUAGE_ISO;
    }
}
