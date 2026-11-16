<?php
/**
 * Central mail dispatcher for the orderwithdrawl module.
 *
 * Previously the module sent mails from three different places (the submit
 * controller, the module class, the decision hook) and recomputed the
 * template path every time. Consolidating that logic here removes the
 * duplication, centralises the language-ISO whitelist and gives tests a
 * single seam to mock.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 *
 * PLACEHOLDER CONTRACT (consumed by Batch 3 mail templates)
 * ---------------------------------------------------------
 * The following placeholders MUST be honoured by the on-disk mail templates.
 * Do not rename any of them without coordinating with mails/**.html/.txt.
 *
 *   withdrawl_confirm (customer)
 *     {firstname}          - customer first name (empty for guest orders)
 *     {lastname}           - customer last name (empty for guest orders)
 *     {consumer_name}      - name submitted in the withdrawal declaration
 *     {order_id}           - numeric PrestaShop order id
 *     {order_reference}    - public order reference
 *     {order_identifier}   - configured visible identifier (id, reference, or both)
 *     {email}              - customer email (as submitted, lowercased)
 *     {date}               - submission timestamp d.m.Y H:i:s
 *     {history_url}        - link to customer order-history (if available)
 *     {withdrawal_scope}   - entire order or selected items/services
 *     {contract_part_details}      - plain-text selected item/service details
 *     {contract_part_details_html} - HTML-encoded selected item/service details
 *     {customer_note}      - plain-text note (use only in .txt templates)
 *     {customer_note_html} - HTML-encoded note (use in .html templates)
 *     {deadline_review_notice}      - plain-text late-deadline review notice (empty unless needed)
 *     {deadline_review_notice_html} - HTML-encoded late-deadline review notice (empty unless needed)
 *     {process_continuation_notice}      - plain-text internal process continuation notice
 *     {process_continuation_notice_html} - HTML-encoded internal process continuation notice
 *
 *   withdrawl_admin (shop staff)
 *     {order_id}           - numeric PrestaShop order id
 *     {order_reference}    - public order reference
 *     {order_identifier}   - configured visible identifier (id, reference, or both)
 *     {customer_email}     - customer email
 *     {customer_name}      - name submitted in the withdrawal declaration
 *     {consumer_name}      - name submitted in the withdrawal declaration
 *     {date}               - submission timestamp d.m.Y H:i:s
 *     {order_total}        - locale-formatted order total
 *     {withdrawal_scope}   - entire order or selected items/services
 *     {contract_part_details}      - plain-text selected item/service details
 *     {contract_part_details_html} - HTML-encoded selected item/service details
 *     {customer_note}      - plain-text note
 *     {customer_note_html} - HTML-encoded note
 *     {deadline_review_notice}      - plain-text late-deadline review notice (empty unless needed)
 *     {deadline_review_notice_html} - HTML-encoded late-deadline review notice (empty unless needed)
 *
 *   withdrawl_approved / withdrawl_rejected (customer)
 *     {firstname}          - customer first name
 *     {lastname}           - customer last name
 *     {order_id}           - numeric PrestaShop order id
 *     {order_reference}    - public order reference
 *     {order_identifier}   - configured visible identifier (id, reference, or both)
 *     {email}              - customer email
 *     {date}               - timestamp d.m.Y H:i:s
 *     {status_message}     - admin-configured decision message
 */

declare(strict_types=1);

namespace OrderWithdrawl\Mail;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;
use OrderWithdrawl\Security\SecurityGuard;

class MailDispatcher
{
    /** @var list<string> */
    public const ALLOWED_TEMPLATES = [
        'withdrawl_confirm',
        'withdrawl_admin',
        'withdrawl_approved',
        'withdrawl_rejected',
    ];

    public const MAIL_DATE_FORMAT = 'd.m.Y H:i:s';

    /** @var string Absolute path ending with `/mails/` for Mail::Send. */
    private $mailDirectory;

    /** @var callable|null Override for tests; defaults to Mail::Send. */
    private $sender;

    /** @var callable|null Override for tests; defaults to PrestaShopLogger::addLog. */
    private $logger;

    /** @var callable|null Override for tests; defaults to PHP mail(). */
    private $phpMailSender;

    /**
     * @param string $mailDirectory Absolute path to the module's mails dir (must end with `/`)
     * @param callable|null $sender Optional override for Mail::Send signature
     * @param callable|null $logger Optional override for PrestaShopLogger::addLog signature
     * @param callable|null $phpMailSender Optional override for PHP mail() signature
     */
    public function __construct(string $mailDirectory, $sender = null, $logger = null, $phpMailSender = null)
    {
        $this->mailDirectory = rtrim($mailDirectory, '/') . '/';
        $this->sender = $sender;
        $this->logger = $logger;
        $this->phpMailSender = $phpMailSender;
    }

    /**
     * Send a module mail. The language ISO is resolved in the following order:
     *   1. explicit $idLang parameter
     *   2. template availability on disk (whitelist-guarded)
     *   3. fallback to `de` and then `en`
     *
     * @param string $template Template base name (e.g. `withdrawl_confirm`)
     * @param int $idLang Target language id. May be adjusted during fallback
     * @param array<string, mixed> $vars Placeholder map (keys already `{...}`-wrapped)
     * @param string $to Recipient email (must be pre-validated)
     * @param string $subject Subject line
     * @param string|null $toName Optional recipient display name
     * @param int|null $idShop Optional shop id
     *
     * @throws OrderWithdrawlMailException When Mail::Send fails or throws
     */
    public function send(string $template, int $idLang, array $vars, string $to, string $subject, $toName = null, $idShop = null): bool
    {
        $requestedTemplate = $template;
        $template = self::normalizeTemplateName($template);
        if ($template === '') {
            throw new OrderWithdrawlMailException($requestedTemplate, 'Invalid order withdrawal mail template.');
        }

        $resolvedLangId = $this->resolveLanguageId($template, $idLang);
        $from = $this->getShopEmail();
        $fromName = $this->getShopName();
        $diagnostics = $this->buildDiagnostics(
            $template,
            $resolvedLangId,
            $subject,
            $to,
            $from,
            $this->mailDirectory,
            $idShop
        );
        $sendStartedAt = date('Y-m-d H:i:s');

        try {
            $sendResult = (bool) $this->invokeSender(
                $resolvedLangId,
                $template,
                $subject,
                $vars,
                $to,
                $toName,
                $from,
                $fromName,
                $this->mailDirectory,
                $idShop
            );
        } catch (\Throwable $e) {
            $diagnostics = $this->withRecentCoreMailerError($diagnostics, $sendStartedAt);
            $this->logFailure($template, $e, $diagnostics);

            throw new OrderWithdrawlMailException($template, $this->buildFailureMessage($template, $diagnostics), $e);
        }

        if (!$sendResult) {
            // Mail::Send returned false: no exception bubbled up, but the mail
            // did not leave the system. Log and surface through the typed
            // exception so the caller gets the same contract as the throw path.
            $diagnostics = $this->withRecentCoreMailerError($diagnostics, $sendStartedAt);
            $diagnostics['php_mail_fallback_enabled'] = $this->boolLabel($this->isPhpMailFallbackEnabled());
            if ($this->isPhpMailFallbackEnabled()) {
                try {
                    if ($this->sendWithPhpMailFallback(
                        $resolvedLangId,
                        $template,
                        $subject,
                        $vars,
                        $to,
                        $toName,
                        $from,
                        $fromName,
                        $idShop
                    )) {
                        $diagnostics['php_mail_fallback_result'] = 'sent';
                        $this->logInfo(
                            'Order withdrawal PHP mail fallback sent (template: ' . $template . ')',
                            $diagnostics
                        );

                        return true;
                    }

                    $diagnostics['php_mail_fallback_result'] = 'failed';
                } catch (\Throwable $fallbackException) {
                    $diagnostics['php_mail_fallback_result'] = 'exception';
                    $diagnostics['php_mail_fallback_error'] = $fallbackException->getMessage();
                }
            } else {
                $diagnostics['php_mail_fallback_result'] = 'disabled';
            }
            $this->logFailure($template, new \RuntimeException('Mail::Send returned false.'), $diagnostics);

            throw new OrderWithdrawlMailException($template, $this->buildFailureMessage($template, $diagnostics));
        }

        return true;
    }

    /**
     * Resolve the language id for a given template using the on-disk fallback
     * chain. Language ISO codes are whitelisted via {@see SecurityGuard::validateLanguageIso}.
     *
     * The fallback order mirrors the legacy submit controller:
     *   1. customer language (only if the ISO is in the whitelist and the
     *      template exists on disk)
     *   2. `de`, then `en` as hard-coded module fallbacks
     */
    public function resolveLanguageId(string $template, int $idLang): int
    {
        $template = self::normalizeTemplateName($template);
        if ($template === '') {
            return $idLang;
        }

        $rawIso = $this->getIsoById($idLang);
        $iso = SecurityGuard::validateLanguageIso($rawIso);
        $isoIsWhitelisted = $iso === strtolower(trim($rawIso));

        if ($isoIsWhitelisted && $this->templateExists($iso, $template)) {
            return $idLang;
        }

        foreach (['de', 'en'] as $fallbackIso) {
            if ($this->templateExists($fallbackIso, $template)) {
                $fallbackId = $this->getIdByIso($fallbackIso);
                if ($fallbackId > 0) {
                    return $fallbackId;
                }
            }
        }

        return $idLang;
    }

    /**
     * Absolute mail directory used by Mail::Send.
     */
    public function getMailDirectory(): string
    {
        return $this->mailDirectory;
    }

    /**
     * Format dates as they appear in customer/admin mail placeholders.
     *
     * @param int|\DateTimeInterface|null $value
     */
    public static function formatMailDate($value = null): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(self::MAIL_DATE_FORMAT);
        }

        if ($value !== null) {
            return date(self::MAIL_DATE_FORMAT, (int) $value);
        }

        return date(self::MAIL_DATE_FORMAT);
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
     * Documented placeholder contract per template. The content is the
     * static mapping declared in the class docblock, exposed for Batch 3's
     * template generator so templates stay in sync with the producer.
     *
     * @return array<string, list<string>>
     */
    public static function getPlaceholders(): array
    {
        return [
            'withdrawl_confirm' => [
                '{firstname}',
                '{lastname}',
                '{consumer_name}',
                '{order_id}',
                '{order_reference}',
                '{order_identifier}',
                '{email}',
                '{date}',
                '{history_url}',
                '{withdrawal_scope}',
                '{contract_part_details}',
                '{contract_part_details_html}',
                '{customer_note}',
                '{customer_note_html}',
                '{deadline_review_notice}',
                '{deadline_review_notice_html}',
                '{process_continuation_notice}',
                '{process_continuation_notice_html}',
            ],
            'withdrawl_admin' => [
                '{order_id}',
                '{order_reference}',
                '{order_identifier}',
                '{customer_email}',
                '{customer_name}',
                '{consumer_name}',
                '{date}',
                '{order_total}',
                '{withdrawal_scope}',
                '{contract_part_details}',
                '{contract_part_details_html}',
                '{customer_note}',
                '{customer_note_html}',
                '{deadline_review_notice}',
                '{deadline_review_notice_html}',
            ],
            'withdrawl_approved' => [
                '{firstname}',
                '{lastname}',
                '{order_id}',
                '{order_reference}',
                '{order_identifier}',
                '{email}',
                '{date}',
                '{status_message}',
            ],
            'withdrawl_rejected' => [
                '{firstname}',
                '{lastname}',
                '{order_id}',
                '{order_reference}',
                '{order_identifier}',
                '{email}',
                '{date}',
                '{status_message}',
            ],
        ];
    }

    protected function templateExists(string $iso, string $template): bool
    {
        return $this->resolveTemplateFilePath($iso, $template, 'html') !== null;
    }

    protected function getIsoById(int $idLang): string
    {
        if (class_exists('Language', false)) {
            return (string) \Language::getIsoById($idLang);
        }

        return '';
    }

    protected function getIdByIso(string $iso): int
    {
        if (class_exists('Language', false)) {
            return (int) \Language::getIdByIso($iso);
        }

        return 0;
    }

    protected function getShopEmail(): string
    {
        if (class_exists('Configuration', false)) {
            return (string) \Configuration::get(OrderWithdrawlConfig::PS_SHOP_EMAIL);
        }

        return '';
    }

    protected function getShopName(): string
    {
        if (class_exists('Configuration', false)) {
            return (string) \Configuration::get(OrderWithdrawlConfig::PS_SHOP_NAME);
        }

        return '';
    }

    /**
     * @return array<string, string>
     */
    private function buildDiagnostics(string $template, int $idLang, string $subject, string $to, string $from, string $templatePath, $idShop): array
    {
        $safeTemplate = self::normalizeTemplateName($template);
        $iso = SecurityGuard::validateLanguageIso($this->getIsoById($idLang));
        $mailMethod = (string) $this->readConfiguration('PS_MAIL_METHOD');

        return [
            'mail_method' => $this->describeMailMethod($mailMethod),
            'mail_type' => $this->describeEmpty((string) $this->readConfiguration('PS_MAIL_TYPE')),
            'smtp_server_configured' => $this->boolLabel((string) $this->readConfiguration('PS_MAIL_SERVER') !== ''),
            'smtp_user_configured' => $this->boolLabel((string) $this->readConfiguration('PS_MAIL_USER') !== ''),
            'smtp_encryption' => $this->describeEmpty((string) $this->readConfiguration('PS_MAIL_SMTP_ENCRYPTION')),
            'smtp_port' => $this->describeEmpty((string) $this->readConfiguration('PS_MAIL_SMTP_PORT')),
            'php_mail_function' => $this->boolLabel(function_exists('mail')),
            'sendmail_path' => $this->describeEmpty((string) ini_get('sendmail_path')),
            'php_mail_fallback_enabled' => $this->boolLabel($this->isPhpMailFallbackEnabled()),
            'template_valid' => $this->boolLabel($safeTemplate !== ''),
            'subject_valid' => $this->boolLabel($this->isValidMailSubject($subject)),
            'id_lang' => (string) $idLang,
            'iso' => $this->describeEmpty($iso),
            'id_shop' => $idShop !== null ? (string) $idShop : 'auto',
            'template_path' => rtrim($templatePath, '/') . '/',
            'template_html' => $this->boolLabel($safeTemplate !== '' && $this->resolveTemplateFilePath($iso, $safeTemplate, 'html') !== null),
            'template_txt' => $this->boolLabel($safeTemplate !== '' && $this->resolveTemplateFilePath($iso, $safeTemplate, 'txt') !== null),
            'to' => $this->maskEmail($to),
            'to_valid' => $this->boolLabel($this->isValidEmail($to)),
            'from' => $this->maskEmail($from),
            'from_valid' => $this->boolLabel($this->isValidEmail($from)),
        ];
    }

    /**
     * @param array<string, string> $diagnostics
     *
     * @return array<string, string>
     */
    private function withRecentCoreMailerError(array $diagnostics, string $sendStartedAt): array
    {
        $coreMailerError = $this->findRecentCoreMailerError($sendStartedAt);
        if ($coreMailerError !== '') {
            $diagnostics['core_mailer_error'] = $coreMailerError;
        }

        return $diagnostics;
    }

    private function findRecentCoreMailerError(string $sendStartedAt): string
    {
        if (!class_exists('Db', false) || !defined('_DB_PREFIX_')) {
            return '';
        }

        $timestamp = strtotime($sendStartedAt);
        if ($timestamp === false) {
            $timestamp = time();
        }
        $from = date('Y-m-d H:i:s', max(0, $timestamp - 5));

        try {
            $row = \Db::getInstance()->getRow(
                'SELECT `message` FROM `' . _DB_PREFIX_ . 'log` '
                . 'WHERE (`object_type` = \'MailerMessage\' '
                . 'OR `message` LIKE \'Mailer Error:%\' '
                . 'OR `message` LIKE \'Error:%\' '
                . 'OR `message` LIKE \'Error - %\') '
                . 'AND (`object_type` IS NULL OR `object_type` <> \'OrderWithdrawl\') '
                . 'AND `date_add` >= \'' . $this->escapeSql($from) . '\' '
                . 'ORDER BY `id_log` DESC'
            );
        } catch (\Throwable $e) {
            return '';
        }

        if (!is_array($row) || !isset($row['message'])) {
            return '';
        }

        return $this->sanitizeDiagnosticValue((string) $row['message']);
    }

    private function buildFailureMessage(string $template, array $diagnostics): string
    {
        return 'Order withdrawal mail failed to send (template: ' . $template . '). Diagnostics: '
            . $this->formatDiagnostics($diagnostics);
    }

    /**
     * @param array<string, string> $diagnostics
     */
    private function formatDiagnostics(array $diagnostics): string
    {
        $parts = [];
        foreach ($diagnostics as $key => $value) {
            $parts[] = $key . '=' . $this->sanitizeDiagnosticValue($value);
        }

        return implode(', ', $parts);
    }

    private function sanitizeDiagnosticValue(string $value): string
    {
        $value = str_replace(["\r", "\n"], ' ', trim($value));
        $value = (string) preg_replace(
            '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i',
            '***@***',
            $value
        );

        if (strlen($value) > 240) {
            $value = substr($value, 0, 237) . '...';
        }

        return $value;
    }

    private function escapeSql(string $value): string
    {
        if (function_exists('pSQL')) {
            return pSQL($value);
        }

        return addslashes($value);
    }

    private function readConfiguration(string $key): string
    {
        if (class_exists('Configuration', false)) {
            return (string) \Configuration::get($key);
        }

        return '';
    }

    private function isPhpMailFallbackEnabled(): bool
    {
        if (!class_exists('Configuration', false)) {
            return (bool) OrderWithdrawlConfig::DEFAULT_PHP_MAIL_FALLBACK;
        }

        return (bool) (int) \Configuration::get(
            OrderWithdrawlConfig::PHP_MAIL_FALLBACK,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_PHP_MAIL_FALLBACK
        );
    }

    private function describeMailMethod(string $method): string
    {
        switch ($method) {
            case '1':
                return '1:phpmail/sendmail';
            case '2':
                return '2:smtp';
            case '3':
                return '3:disabled';
        }

        return $this->describeEmpty($method);
    }

    private function describeEmpty(string $value): string
    {
        $value = trim($value);

        return $value !== '' ? $value : 'empty';
    }

    private function boolLabel(bool $value): string
    {
        return $value ? 'yes' : 'no';
    }

    private function maskEmail(string $email): string
    {
        $email = trim($email);
        $parts = explode('@', $email, 2);
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return 'invalid';
        }

        return substr($parts[0], 0, 1) . '***@' . $parts[1];
    }

    private function isValidEmail(string $email): bool
    {
        if (class_exists('Validate', false)) {
            return (bool) \Validate::isEmail($email);
        }

        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function isValidMailSubject(string $subject): bool
    {
        if (class_exists('Validate', false) && method_exists('Validate', 'isMailSubject')) {
            return (bool) \Validate::isMailSubject($subject);
        }

        return trim($subject) !== '' && !preg_match('/[\r\n]/', $subject);
    }

    /**
     * @param array<string, mixed> $vars
     */
    protected function invokeSender(int $idLang, string $template, string $subject, array $vars, string $to, $toName, string $from, string $fromName, string $templatePath, $idShop): bool
    {
        if ($this->sender !== null) {
            return (bool) ($this->sender)(
                $idLang,
                $template,
                $subject,
                $vars,
                $to,
                $toName,
                $from,
                $fromName,
                $templatePath,
                $idShop
            );
        }

        if (!class_exists('Mail')) {
            return false;
        }

        return (bool) \Mail::Send(
            $idLang,
            $template,
            $subject,
            $vars,
            $to,
            $toName,
            $from,
            $fromName,
            null,
            null,
            $templatePath,
            false,
            $idShop
        );
    }

    /**
     * @param array<string, mixed> $vars
     */
    private function sendWithPhpMailFallback(int $idLang, string $template, string $subject, array $vars, string $to, $toName, string $from, string $fromName, $idShop): bool
    {
        unset($toName);

        if (!function_exists('mail') && $this->phpMailSender === null) {
            return false;
        }

        if (!$this->isValidEmail($to) || !$this->isValidEmail($from) || !$this->isValidMailSubject($subject)) {
            return false;
        }

        $content = $this->buildFallbackMailContent($idLang, $template, $vars, $idShop);
        if ($content === null) {
            return false;
        }

        $headers = $this->buildPhpMailHeaders($from, $fromName, $content['content_type']);

        return $this->invokePhpMailSender(
            $to,
            $this->encodeHeaderValue($subject),
            $content['body'],
            $headers
        );
    }

    /**
     * @param array<string, mixed> $vars
     *
     * @return array{body:string, content_type:string}|null
     */
    private function buildFallbackMailContent(int $idLang, string $template, array $vars, $idShop)
    {
        $template = self::normalizeTemplateName($template);
        if ($template === '') {
            return null;
        }

        $iso = SecurityGuard::validateLanguageIso($this->getIsoById($idLang));
        if ($iso === '') {
            return null;
        }

        $htmlPath = $this->resolveTemplateFilePath($iso, $template, 'html');
        $txtPath = $this->resolveTemplateFilePath($iso, $template, 'txt');
        $hasHtml = $htmlPath !== null;
        $hasTxt = $txtPath !== null;
        if (!$hasHtml && !$hasTxt) {
            return null;
        }

        $templateVars = array_merge($this->getFallbackCommonTemplateVars($idLang, $idShop), $vars);
        $mailType = (int) $this->readConfiguration('PS_MAIL_TYPE');

        $html = $hasHtml ? strtr($this->readTemplateFile((string) $htmlPath), $templateVars) : '';
        $text = $hasTxt ? strtr($this->readTemplateFile((string) $txtPath), $templateVars) : '';
        if ($text === '' && $html !== '') {
            $text = trim(strip_tags(html_entity_decode($html, ENT_COMPAT, 'UTF-8')));
        }

        if ($mailType === 2 && $text !== '') {
            return [
                'body' => $this->normalizeMailBody($text),
                'content_type' => 'text/plain; charset=UTF-8',
            ];
        }

        if ($mailType === 1 && $html !== '') {
            return [
                'body' => $this->normalizeMailBody($html),
                'content_type' => 'text/html; charset=UTF-8',
            ];
        }

        if ($html !== '' && $text !== '') {
            $boundary = '=_orderwithdrawl_' . hash('sha256', uniqid('', true));

            return [
                'body' => $this->buildMultipartAlternativeBody($boundary, $text, $html),
                'content_type' => 'multipart/alternative; boundary="' . $boundary . '"',
            ];
        }

        if ($html !== '') {
            return [
                'body' => $this->normalizeMailBody($html),
                'content_type' => 'text/html; charset=UTF-8',
            ];
        }

        return [
            'body' => $this->normalizeMailBody($text),
            'content_type' => 'text/plain; charset=UTF-8',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getFallbackCommonTemplateVars(int $idLang, $idShop): array
    {
        return [
            '{shop_logo}' => '',
            '{shop_name}' => $this->getShopName(),
            '{shop_url}' => $this->getFallbackPageLink('index', $idLang, $idShop),
            '{my_account_url}' => $this->getFallbackPageLink('my-account', $idLang, $idShop),
            '{guest_tracking_url}' => $this->getFallbackPageLink('guest-tracking', $idLang, $idShop),
            '{history_url}' => $this->getFallbackPageLink('history', $idLang, $idShop),
            '{order_slip_url}' => $this->getFallbackPageLink('order-slip', $idLang, $idShop),
            '{color}' => $this->readConfiguration('PS_MAIL_COLOR'),
        ];
    }

    private function getFallbackPageLink(string $page, int $idLang, $idShop): string
    {
        unset($idShop);

        $baseUrl = $this->getFallbackBaseUrl();
        if ($baseUrl === '') {
            return '';
        }

        if ($page === 'index') {
            return $baseUrl;
        }

        $query = ['controller' => $page];
        if ($idLang > 0) {
            $query['id_lang'] = (string) $idLang;
        }

        return $baseUrl . 'index.php?' . http_build_query($query, '', '&');
    }

    private function getFallbackBaseUrl(): string
    {
        if (class_exists('Tools', false) && method_exists('Tools', 'getShopDomainSsl')) {
            try {
                $domain = (string) \Tools::getShopDomainSsl(true, true);
                if (trim($domain) !== '') {
                    return rtrim($domain, '/') . '/';
                }
            } catch (\Throwable $e) {
                // Fall through to configuration-based domain lookup.
            }
        }

        $domain = trim($this->readConfiguration('PS_SHOP_DOMAIN_SSL'));
        if ($domain === '') {
            $domain = trim($this->readConfiguration('PS_SHOP_DOMAIN'));
        }
        if ($domain === '') {
            return '';
        }
        if (!preg_match('#^https?://#i', $domain)) {
            $domain = 'https://' . $domain;
        }

        return rtrim($domain, '/') . '/';
    }

    private function readTemplateFile(string $path): string
    {
        $path = $this->normalizeLocalTemplatePath($path);
        if ($path === '') {
            return '';
        }

        $content = file_get_contents($path);

        return $content !== false ? (string) $content : '';
    }

    private function resolveTemplateFilePath(string $iso, string $template, string $extension)
    {
        $iso = SecurityGuard::validateLanguageIso($iso);
        $template = self::normalizeTemplateName($template);
        if ($template === '' || !in_array($extension, ['html', 'txt'], true)) {
            return null;
        }

        $base = $this->getRealMailDirectory();
        if ($base === '') {
            return null;
        }

        return $this->normalizeLocalTemplatePath($base . $iso . DIRECTORY_SEPARATOR . $template . '.' . $extension) ?: null;
    }

    private function normalizeLocalTemplatePath(string $path): string
    {
        $base = $this->getRealMailDirectory();
        $realPath = realpath($path);
        if ($base === '' || $realPath === false) {
            return '';
        }

        return strpos($realPath, $base) === 0 ? $realPath : '';
    }

    private function getRealMailDirectory(): string
    {
        $base = realpath($this->mailDirectory);
        if ($base === false) {
            return '';
        }

        return rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    private function buildMultipartAlternativeBody(string $boundary, string $text, string $html): string
    {
        return '--' . $boundary . "\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
            . $this->normalizeMailBody($text) . "\r\n\r\n"
            . '--' . $boundary . "\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
            . $this->normalizeMailBody($html) . "\r\n\r\n"
            . '--' . $boundary . "--\r\n";
    }

    private function buildPhpMailHeaders(string $from, string $fromName, string $contentType): string
    {
        return implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: ' . $contentType,
            'From: ' . $this->formatMailboxHeader($from, $fromName),
            'Reply-To: ' . $this->formatMailboxHeader($from, $fromName),
            'X-Mailer: orderwithdrawl PHP mail fallback',
        ]);
    }

    private function formatMailboxHeader(string $email, string $name = ''): string
    {
        $name = $this->sanitizeHeaderText($name);
        if ($name === '') {
            return $email;
        }

        return $this->encodeHeaderPhrase($name) . ' <' . $email . '>';
    }

    private function encodeHeaderValue(string $value): string
    {
        $value = $this->sanitizeHeaderText($value);
        if ($value === '') {
            return '';
        }

        if (preg_match('/[^\x20-\x7E]/', $value)) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }

        return $value;
    }

    private function encodeHeaderPhrase(string $value): string
    {
        if (preg_match('/[^\x20-\x7E]/', $value)) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }

        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }

    private function sanitizeHeaderText(string $value): string
    {
        return trim(str_replace(["\r", "\n"], ' ', $value));
    }

    private function normalizeMailBody(string $body): string
    {
        return str_replace(["\r\n", "\r"], "\n", $body);
    }

    private function invokePhpMailSender(string $to, string $subject, string $body, string $headers): bool
    {
        if ($this->phpMailSender !== null) {
            return (bool) ($this->phpMailSender)($to, $subject, $body, $headers);
        }

        return (bool) mail($to, $subject, $body, $headers);
    }

    /**
     * @param array<string, string> $diagnostics
     */
    private function logInfo(string $message, array $diagnostics = [])
    {
        $diagnosticMessage = $diagnostics !== [] ? 'Diagnostic: ' . $this->formatDiagnostics($diagnostics) : '';
        if ($this->logger !== null) {
            ($this->logger)($message);
            if ($diagnosticMessage !== '') {
                ($this->logger)($diagnosticMessage);
            }

            return;
        }

        if (class_exists('PrestaShopLogger', false)) {
            \PrestaShopLogger::addLog(
                $message,
                \PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
                null,
                'OrderWithdrawl',
                null
            );
            if ($diagnosticMessage !== '') {
                \PrestaShopLogger::addLog(
                    $diagnosticMessage,
                    \PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
                    null,
                    'OrderWithdrawl',
                    null
                );
            }
        }
    }

    protected function logFailure(string $template, \Throwable $e, array $diagnostics = [])
    {
        $message = 'Order withdrawal mail failed to send (template: ' . $template . ')';
        $diagnosticMessage = $diagnostics !== [] ? 'Diagnostic: ' . $this->formatDiagnostics($diagnostics) : '';
        if ($this->logger !== null) {
            ($this->logger)($message);
            if ($diagnosticMessage !== '') {
                ($this->logger)($diagnosticMessage);
            }

            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                ($this->logger)('Detail: ' . $e->getMessage());
            }

            return;
        }

        if (class_exists('PrestaShopLogger', false)) {
            \PrestaShopLogger::addLog(
                $message,
                \PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR,
                null,
                'OrderWithdrawl',
                null
            );
            if ($diagnosticMessage !== '') {
                \PrestaShopLogger::addLog(
                    $diagnosticMessage,
                    \PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
                    null,
                    'OrderWithdrawl',
                    null
                );
            }
            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                \PrestaShopLogger::addLog(
                    'Detail: ' . $e->getMessage(),
                    \PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
                    null,
                    'OrderWithdrawl',
                    null
                );
            }
        }
    }
}
