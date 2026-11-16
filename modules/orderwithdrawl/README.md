# Order Withdrawal Module for PrestaShop

**Version:** 1.4.3
**Author:** sjmedia GmbH
**Compatible:** PrestaShop 1.7.8+ | PrestaShop 8.x | PrestaShop 9.x
**License:** Academic Free License 3.0 (AFL-3.0)

## 📋 Overview

A comprehensive EU-compliant withdrawal management solution for PrestaShop stores. This module enables customers to exercise their statutory 14-day right of withdrawal (EU Directive 2011/83/EU) through a secure online process that defaults to manual merchant review. The module features flexible display options, multilingual support, automatic email notifications, and seamless integration with PrestaShop's order management system.

---

## ✨ Key Features

### 🎯 Core Functionality

- **EU Directive 2011/83/EU Compliance** - Fully aligned with European consumer protection regulations
- **Automatic Order Status Management** - Creates and assigns "Withdrawal Requested" status with custom color (#FFA500) and icon
- **Intelligent Period Calculation** - Configurable withdrawal period (1-365 days) + delivery time buffer
- **Dual Email Notification System** - Automatic emails to customers (confirmation) and administrators (alert)
- **Secure Validation** - First and last name + order identifier verification by default, with the older email verification method still available but not recommended
- **Structured Partial Withdrawal** - Customers can withdraw the full order or identify individual items/services with item name, item number and quantity fields; no withdrawal reason is requested
- **Optional Product Selection Mode** - After verified order details, customers can select order items from the order for partial withdrawal; disabled by default for upgrade stability
- **SSL-Secured Forms** - All withdrawal requests transmitted over secure HTTPS connection
- **SEO-Friendly URLs** - Custom module routes (`/order-withdrawl`, `/order-withdrawl-submit`)

### 🌍 Multilingual & Localization

- **8 Language Support** - Pre-configured translations for:
  - 🇩🇪 German (Deutsch)
  - 🇬🇧 English
  - 🇫🇷 French (Français)
  - 🇮🇹 Italian (Italiano)
  - 🇪🇸 Spanish (Español)
  - 🇵🇱 Polish (Polski)
  - 🇳🇱 Dutch (Nederlands)
  - 🇷🇴 Romanian (Română)
- **Per-Language Customization** - Override button labels, notices, and CMS pages for each language
- **Automatic Fallback** - Email templates fall back to default language if translation unavailable
- **Locale-Aware Formatting** - Currency and price formatting respects PrestaShop's locale system

### 🎨 Display & Customization

#### Multiple Display Locations
Configure where customers can access the withdrawal function:

1. **Footer Display**
   - Button (customizable color) or simple text link
   - Prominent placement in site footer
   - Fully customizable brand color (hex color picker)

2. **Customer Account Block**
   - Legacy PrestaShop 1.7 account menu integration
   - Icon-based display with subtitle

3. **Customer Account Page**
   - Modern card-based interface for PrestaShop 8/9
   - Professional dashboard integration

**Display Combinations:**
- Footer only
- Account block only
- Account page only
- Footer + Account block
- Footer + Account page
- Account block + Account page
- All three locations simultaneously

#### Visual Customization
- **Custom Button Color** - Full hex color support for brand alignment (#RRGGBB)
- **Button/Link Toggle** - Switch between prominent button or subtle text link in footer
- **Customizable Labels** - Define call-to-action text per language (max 255 characters)
- **Default Labels Provided** - Built-in translations for all 8 supported languages

### 📄 CMS Integration & Legal Notices

#### Privacy Notice System
- **CMS Page Assignment** - Link to privacy policy page per language
- **Custom Notice Text** - Add withdrawal-specific privacy information (max 4000 characters)
- **Automatic Display** - Shows privacy notice on configured CMS pages via `displayCMSDisputeInformation` hook
- **Default Templates** - Pre-written GDPR-compliant privacy notices in all 8 languages

#### Withdrawal Information System
- **CMS Page Assignment** - Link to withdrawal policy/information page per language
- **Custom Notice Text** - Explain electronic withdrawal button functionality (max 4000 characters)
- **Legal Compliance** - Inform customers about the availability of the withdrawal button
- **Automatic Display** - Renders withdrawal notice on designated CMS pages

### 📧 Email Notification System

#### Customer Confirmation Email (`withdrawl_confirm`)
Sent immediately upon successful withdrawal submission:
- Configured order identifier (order ID, reference, or both)
- Submitted consumer name
- Customer email address
- Withdrawal scope (entire order or selected items/services)
- Structured item/service details for partial withdrawals
- Timestamp of withdrawal request
- Confirmation of receipt on durable medium (email)

**Variables:**
- `{order_id}` - Numeric PrestaShop order ID
- `{order_reference}` - Order number (stored internally as the order reference and kept for custom template compatibility)
- `{order_identifier}` - Visible identifier based on the module setting
- `{customer_name}` - Submitted consumer name
- `{email}` - Customer email
- `{date}` - Submission timestamp
- `{withdrawal_scope}` - Entire order or selected items/services
- `{contract_part_details}` - Plain text details for selected items/services
- `{contract_part_details_html}` - HTML-safe details for selected items/services

#### Administrator Alert Email (`withdrawl_admin`)
Sent to all configured admin emails:
- Configured order identifier plus raw order ID/order number tokens
- First and last name plus email
- Order total (locale-formatted with currency)
- Withdrawal scope and structured item/service details, where submitted
- Timestamp of withdrawal request

**Variables:**
- `{order_id}` - Numeric PrestaShop order ID
- `{order_reference}` - Order number (stored internally as the order reference and kept for custom template compatibility)
- `{order_identifier}` - Visible identifier based on the module setting
- `{customer_email}` - Customer email
- `{customer_name}` - Full submitted first and last name
- `{date}` - Submission timestamp
- `{order_total}` - Formatted order total with currency
- `{withdrawal_scope}` - Entire order or selected items/services
- `{contract_part_details}` - Plain text details for selected items/services
- `{contract_part_details_html}` - HTML-safe details for selected items/services

**Recipients:**
- Primary: Shop email address (PS_SHOP_EMAIL)
- Additional: Comma-separated list from module configuration

### 🎛️ Advanced Configuration Options

#### General Settings

| Setting | Type | Range | Default | Description |
|---------|------|-------|---------|-------------|
| **Withdrawal Period** | Integer | 1-365 days | 14 days | Legal withdrawal timeframe per EU directive |
| **Delivery Days** | Integer | 0-365 days | 0 days | Buffer time for delivery added to withdrawal period |
| **Order Assignment Details** | Select | first and last name + order number/order ID/both, older email method | first and last name + order number | Which details customers must enter so the withdrawal request can be assigned to an order |
| **Evaluation Mode** | Select | manual/period | manual | Manual review records valid withdrawals for Back Office assessment before merchant decision |
| **Product Selection from Order** | Checkbox | on/off | off | Optional two-step flow that shows selectable order items after successful order verification |
| **Admin Emails** | String | Email list | Empty | Comma-separated additional notification recipients |
| **Display Location** | Multi-select | 3 options | Footer | Where withdrawal option appears (footer/account_block/account_page) |
| **Footer Display Mode** | Select | button/link | button | Visual presentation style in footer |
| **Button Color** | Color | Hex code | #F44708 | Brand color for button display |

#### Per-Language Settings

For each installed shop language:

| Setting | Type | Max Length | Description |
|---------|------|------------|-------------|
| **Button Label** | String | 255 chars | Call-to-action text (e.g., "Withdraw from contract") |
| **Privacy Page** | CMS Select | - | Link to privacy policy page |
| **Privacy Notice** | Textarea | 4000 chars | GDPR notice about data processing |
| **Withdrawal Page** | CMS Select | - | Link to withdrawal information page |
| **Withdrawal Notice** | Textarea | 4000 chars | Information about electronic withdrawal functionality |

#### Order Status Configuration

The module creates a custom order status:
- **Name:** "Withdrawal Requested" (translated per language)
- **Color:** #FFA500 (orange)
- **Icon:** Module logo (logo.png)
- **Email Notification:** Configurable (default: enabled)
- **Properties:** Non-logable, non-delivery, non-invoice

---

## 🔧 Technical Specifications

### System Requirements

- **PrestaShop:** 1.7.8.0 or higher (tested with PrestaShop 8.x and 9.x)
- **PHP:** 7.4+ (PHP 8.x supported)
- **MySQL:** 5.6+ or MariaDB 10.1+
- **SSL Certificate:** Strongly recommended for secure form submissions
- **PHP Extensions:** Standard PrestaShop requirements

### Module Architecture

#### File Structure
```
orderwithdrawl/
├── orderwithdrawl.php              # Main module class
├── config.xml                      # Module metadata
├── logo.png                        # Order status icon
├── controllers/
│   └── front/
│       ├── form.php               # Withdrawal form controller
│       └── submit.php             # Withdrawal submission handler
├── mails/
│   ├── de/ en/ es/ fr/ it/ nl/ pl/ ro/
│   │   ├── withdrawl_confirm.html # Customer confirmation template
│   │   ├── withdrawl_confirm.txt  # Plain text version
│   │   ├── withdrawl_admin.html   # Admin notification template
│   │   └── withdrawl_admin.txt    # Plain text version
├── translations/
│   ├── de.php, en.php, es.php, fr.php
│   ├── it.php, nl.php, pl.php, ro.php
├── views/
│   ├── css/
│   │   ├── admin.css              # Admin configuration styling
│   │   └── front.css              # Frontend styling
│   ├── templates/
│   │   ├── admin/
│   │   │   └── configure.tpl      # Modern admin interface
│   │   ├── front/
│   │   │   ├── form.tpl           # Withdrawal request form
│   │   │   └── confirm.tpl        # Success confirmation
│   │   └── hook/
│   │       ├── account_link.tpl   # Account area integration
│   │       ├── footer_link.tpl    # Footer button/link
│   │       └── privacy_notice.tpl # CMS page notices
└── readme/
    └── images/                     # Screenshots and documentation assets
```

#### Registered Hooks

| Hook | Purpose | Return |
|------|---------|--------|
| `displayMyAccountBlock` | Legacy account menu (PS 1.7) | HTML link |
| `displayCustomerAccount` | Modern account cards (PS 8/9) | HTML card |
| `displayFooter` | Footer button/link | HTML button or link |
| `displayOrderWithdrawlCta` | Optional custom theme placement | HTML button or link |
| `displayHeader` | CSS registration | Registers `front.css` |
| `displayCMSDisputeInformation` | Legal notices on CMS pages | HTML notice blocks |
| `moduleRoutes` | Custom URL routing | Route definitions |

#### Database Configuration Keys

All settings stored in `ps_configuration` table:

```php
ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS    // Integer: 1-365
ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS  // Integer: 0-365
ORDERWITHDRAWL_ORDER_IDENTIFIER_MODE    // String: 'order_id', 'reference', or 'both'
ORDERWITHDRAWL_CUSTOMER_VERIFICATION_MODE // String: 'name', 'email', or legacy 'identifier'
ORDERWITHDRAWL_ADMIN_EMAILS             // String: email list
ORDERWITHDRAWL_STATE_ID                 // Integer: order state ID
ORDERWITHDRAWL_FOOTER_DISPLAY           // String: 'button' or 'link'
ORDERWITHDRAWL_DISPLAY_LOCATION         // String: comma-separated locations
ORDERWITHDRAWL_BUTTON_LABEL             // Array: per-language labels
ORDERWITHDRAWL_BUTTON_COLOR             // String: hex color code
ORDERWITHDRAWL_PRIVACY_PAGE             // Array: CMS page IDs per language
ORDERWITHDRAWL_PRIVACY_NOTE             // Array: notice text per language
ORDERWITHDRAWL_WITHDRAWAL_PAGE          // Array: CMS page IDs per language
ORDERWITHDRAWL_WITHDRAWAL_NOTE          // Array: notice text per language
```

#### Custom Routes

```php
/order-withdrawl        → module-orderwithdrawl-form
/order-withdrawl-submit → module-orderwithdrawl-submit
```

### Workflow Logic

#### Customer Withdrawal Process

1. **Access Point** - Customer clicks withdrawal link/button from configured location(s)
2. **Form Display** - Controller renders form with withdrawal period information
3. **Validation** - On submission, system validates:
   - Numeric order ID or alphanumeric order number exists in the database
   - Submitted first and last name, or the older email mode if configured, match the order
   - For partial withdrawals, at least one item name or item number is provided
   - In period mode, late requests are accepted and flagged for manual review instead of being blocked
4. **Order Update** - Changes order status to "Withdrawal Requested"
5. **Email Dispatch** - Sends confirmation to customer and alert to admin(s)
6. **Confirmation** - Redirects to form with success message

#### Period Calculation Algorithm

```php
$order_date = DateTime('order creation date');
$period_days = Configuration::get('ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS'); // e.g., 14
$delivery_days = Configuration::get('ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS'); // e.g., 3
$start_mode = Configuration::get('ORDERWITHDRAWL_PERIOD_START_MODE'); // order_date | shipping_status

if ($start_mode === 'shipping_status') {
    $shipping_date = getFirstShippedStatusDate($order_id);

    if ($shipping_date) {
        $deadline = $shipping_date->modify('+' . ($period_days + $delivery_days) . ' days');
    } else {
        $deadline = $order_date->modify('+' . ($period_days + $delivery_days) . ' days');
    }
} else {
    $deadline = $order_date->modify('+' . ($period_days + $delivery_days) . ' days');
}

if (new DateTime() > $deadline) {
    // Flag request for manual review; status change and emails still continue.
}
```

#### Email Template Language Selection

1. Attempts customer's language (`$customer->id_lang`)
2. Falls back to shop default language (`PS_LANG_DEFAULT`)
3. Falls back to German if available
4. Falls back to English as final option

---

## 📥 Installation

### Method 1: PrestaShop Admin Panel (Recommended)

1. Download module ZIP file
2. Navigate to **Modules** → **Module Manager**
3. Click **Upload a module**
4. Select ZIP file and upload
5. Click **Install** when module appears
6. Configure module settings

### Method 2: Manual Installation

1. Extract module files
2. Upload `orderwithdrawl/` folder to `/modules/` directory via FTP/SSH
3. Set correct permissions: `chmod 755 orderwithdrawl/`
4. Navigate to **Modules** → **Module Manager** in admin
5. Search for "Order Withdrawal" or "European Legal Withdrawal"
6. Click **Install**
7. Configure module settings

### Post-Installation Steps

1. **Verify Order Status** - Check that "Withdrawal Requested" status was created:
   - Navigate to **Orders** → **Statuses**
   - Look for status with orange color (#FFA500)

2. **Configure Settings** - Click **Configure** on module:
   - Set withdrawal period (minimum 14 days for EU)
   - Add delivery buffer days if needed
   - Configure display locations
   - Customize button labels per language

3. **Set Up CMS Pages** (Optional but recommended):
   - Create/select privacy policy pages per language
   - Create/select withdrawal information pages per language
   - Add custom privacy and withdrawal notices

4. **Test Email Notifications**:
   - Verify shop email configuration
   - Add admin notification emails
   - Submit test withdrawal request

5. **Clear Cache**:
   ```bash
   docker exec prestashop-local-prestashop-1 rm -rf var/cache/*
   ```

---

## ⚙️ Configuration Guide

### Basic Configuration

#### Step 1: Access Module Configuration
1. Navigate to **Modules** → **Module Manager**
2. Search for "Order Withdrawal"
3. Click **Configure**

#### Step 2: General Settings
Configure core withdrawal parameters:

```
Withdrawal Period (days): 14          ← EU minimum requirement
Estimated Delivery Time: 3            ← Buffer for shipping
Order Lookup Field: Order ID and reference
Additional Admin Emails:
  sales@example.com,returns@example.com
```

#### Step 3: Display Configuration
Choose where customers can access withdrawal:

**Display Locations:** (Select one or more)
- ☑ Footer (recommended)
- ☐ Customer Account Block (legacy menu)
- ☑ Customer Account Page (modern cards)

**Footer Display Mode:**
- ⦿ Button (prominent, colored)
- ○ Link (subtle, text-only)

**Button Color:** `#F44708` (your brand color)

**Button Size (button mode only):**
- Small / Medium (default) / Large

**Button Alignment (button mode only):**
- Left-aligned / Centered / Right-aligned

### Advanced Configuration

#### Custom Button Labels

Default labels provided for all languages, but you can override:

| Language | Default | Custom Example |
|----------|---------|----------------|
| English | "Withdraw from contract" | "Cancel My Order" |
| German | "Vertrag widerrufen" | "Bestellung stornieren" |
| French | "Demander une rétractation" | "Annuler ma commande" |

Leave empty to use default translations.

#### CMS Page Integration

##### Privacy Notice Setup
1. **Select Privacy Page** - Choose your privacy policy CMS page per language
2. **Custom Privacy Notice** - Add withdrawal-specific text:

```
English Example:
"The personal data you submit with this withdrawal will be used
solely to process your request. For more details, please consult
our privacy policy."

German Example:
"Ihre im Rahmen des Widerrufs angegebenen Daten werden
ausschließlich zur Bearbeitung Ihrer Anfrage verwendet.
Weitere Informationen finden Sie in unserer Datenschutzerklärung."
```

##### Withdrawal Information Setup
1. **Select Withdrawal Page** - Choose your withdrawal policy CMS page per language
2. **Custom Withdrawal Notice** - Explain electronic withdrawal button:

```
English Example:
"In addition to conventional methods (email, post, fax), this
website offers an electronic withdrawal function. After submitting
your withdrawal, you will automatically receive an acknowledgment
of receipt on a durable medium (email)."

German Example:
"Zusätzlich zu den herkömmlichen Möglichkeiten (E-Mail, Post,
Fax) steht Ihnen auf dieser Website auch eine elektronische
Widerrufsfunktion zur Verfügung. Nach Absenden Ihres Widerrufs
erhalten Sie automatisch eine Eingangsbestätigung auf einem
dauerhaften Datenträger (E-Mail)."
```

### Email Configuration

#### Customer Confirmation Email
Location: `modules/orderwithdrawl/mails/{language}/withdrawl_confirm.*`

Available variables:
- `{order_id}` - Numeric PrestaShop order ID
- `{order_reference}` - Order number (stored internally as the order reference and kept for custom template compatibility)
- `{order_identifier}` - Visible identifier based on the module setting
- `{email}` - Customer email address
- `{date}` - Submission timestamp
- `{withdrawal_scope}` - Entire order or selected items/services
- `{contract_part_details}` - Plain text details for selected items/services
- `{contract_part_details_html}` - HTML-safe details for selected items/services

#### Admin Notification Email
Location: `modules/orderwithdrawl/mails/{language}/withdrawl_admin.*`

Available variables:
- `{order_id}` - Order ID
- `{order_reference}` - Order number (stored internally as the order reference and kept for custom template compatibility)
- `{order_identifier}` - Visible identifier based on the module setting
- `{customer_email}` - Customer email
- `{customer_name}` - Submitted first and last name
- `{date}` - Submission timestamp
- `{order_total}` - Formatted order total
- `{withdrawal_scope}` - Entire order or selected items/services
- `{contract_part_details}` - Plain text details for selected items/services
- `{contract_part_details_html}` - HTML-safe details for selected items/services

#### Customizing Email Templates

1. Copy template files:
   ```bash
   cp modules/orderwithdrawl/mails/en/withdrawl_confirm.html \
      themes/yourtheme/modules/orderwithdrawl/mails/en/
   ```

2. Edit HTML template with your branding
3. Maintain variable placeholders: `{variable_name}`
4. Include plain text version (.txt) for accessibility

---

## 👥 User Guide

### For Customers

#### Submitting a Withdrawal Request

1. **Locate Withdrawal Option** - Find the withdrawal link/button:
   - In shop footer (button or link)
   - In "My Account" menu (legacy themes)
   - On "My Account" dashboard (modern themes)

2. **Open Withdrawal Form** - Click link/button to open form

3. **Fill Required Information**:
   ```
   Order ID/reference: 12345 or ABC123456
   First and last name: Jane Customer
   Withdrawal scope: Entire order or selected items/services
   Partial withdrawal: Item name, item number, quantity
   ```

4. **Submit Request** - Click "Confirm withdrawal" button

5. **Receive Confirmation**:
   - On-screen success message
   - Confirmation email with the configured order identifier
   - Order status changes to "Withdrawal Requested"

#### Important Notes for Customers

- **Withdrawal Period**: Check your withdrawal deadline (typically 14 days + delivery time)
- **Verification Required**: By default, enter your name and the configured order identifier. Some shops may use the older email + order identifier method, which is not recommended.
- **Order ID / Order Number**: The module setting controls whether customers enter the numeric PrestaShop order ID, the alphanumeric order number, or either value.
- **Partial Withdrawal**: Select individual items/services only when you do not want to withdraw the full order. The item fields are used only to identify the contract part and do not ask for a withdrawal reason.
- **Automatic Processing**: Order status updates immediately upon submission
- **Email Confirmation**: Check spam folder if confirmation email not received

### For Shop Administrators

#### Processing Withdrawal Requests

1. **Email Notification** - Receive admin alert with order details:
   ```
   Order Identifier: 12345 / ABCDEFGH
   Order Reference: ABCDEFGH
   Customer: John Doe (customer@example.com)
   Order Total: 99.99 EUR
   Date: 2025-10-24 14:30:00
   ```

2. **Locate Order** - Navigate to **Orders** → **Orders** and search by reference

3. **Verify Status** - Order status should show "Withdrawal Requested" (orange)

4. **Process Refund** - Follow your shop's refund procedure:
   - Verify return conditions
   - Issue refund via payment method
   - Update order status (e.g., "Refunded")

5. **Customer Communication** - Inform customer of:
   - Refund processing timeline
   - Return instructions (if applicable)
   - Expected refund date

#### Monitoring Withdrawal Requests

**View All Withdrawal Orders:**
1. Navigate to **Orders** → **Orders**
2. Filter by status: "Withdrawal Requested"
3. Sort by date to prioritize oldest requests

**Order Status Actions:**
- **Refunded** - When refund completed
- **Canceled** - If withdrawal invalid/rejected
- **Shipped** - If goods already returned

#### Email Management

**Configure Recipients:**
```
Module Configuration → Additional Admin Emails
sales@shop.com,returns@shop.com,legal@shop.com
```

All addresses receive withdrawal notifications in parallel.

**Email Delivery Issues:**
1. Check PrestaShop email settings: **Advanced Parameters** → **E-mail**
2. Verify SMTP configuration
3. Check server logs: `var/logs/`
4. Test email: **Advanced Parameters** → **E-mail** → **Send test email**

---

## 🎨 Customization

### Styling

#### Frontend Styles
Location: `modules/orderwithdrawl/views/css/front.css`

Customize:
- Button appearance (size, padding, hover effects)
- Form layout and spacing
- Privacy notice formatting
- Account link styling

```css
/* Example: Custom button styling */
.orderwithdrawl-footer-btn {
    background-color: var(--orderwithdrawl-button-color);
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 600;
    transition: transform 0.2s;
}

.orderwithdrawl-footer-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
```

#### Admin Interface
Location: `modules/orderwithdrawl/views/css/admin.css`

Modern shadcn-inspired design with:
- Card-based layout
- Color-coded badges
- Icon integration
- Responsive grid system

### Template Customization

#### Override Templates (PrestaShop Standard)

1. **Create Override Directory**:
   ```bash
   mkdir -p themes/yourtheme/modules/orderwithdrawl/views/templates/
   ```

2. **Copy Template**:
   ```bash
   cp modules/orderwithdrawl/views/templates/front/form.tpl \
      themes/yourtheme/modules/orderwithdrawl/views/templates/front/
   ```

3. **Edit Template** - Maintain Smarty variables and form structure

4. **Clear Cache**:
   ```bash
   docker exec prestashop-local-prestashop-1 rm -rf var/cache/*
   ```

#### Hook Template Customization

**Footer Link** (`views/templates/hook/footer_link.tpl`):
- Button vs link display
- Icon integration
- Positioning

**Account Link** (`views/templates/hook/account_link.tpl`):
- Legacy block layout
- Modern card layout
- Icon and subtitle

**Privacy Notice** (`views/templates/hook/privacy_notice.tpl`):
- Notice block styling
- Title formatting
- Content layout

### Translation Customization

#### Method 1: Admin Interface (Recommended)

1. Navigate to **International** → **Translations**
2. Select **Modify translations**
3. Choose **Installed modules translations**
4. Select your language
5. Find "Order Withdrawal" module
6. Edit translations
7. Save

#### Method 2: Direct Translation Files

Location: `modules/orderwithdrawl/translations/{language}.php`

```php
<?php
global $_MODULE;
$_MODULE = array();
$_MODULE['<{orderwithdrawl}prestashop>orderwithdrawl_xxxxx'] = 'Your Translation';
```

### Adding New Languages

1. **Install Language** in PrestaShop: **International** → **Localization** → **Languages**

2. **Create Email Templates**:
   ```bash
   mkdir modules/orderwithdrawl/mails/xx/  # xx = ISO code
   cp modules/orderwithdrawl/mails/en/* modules/orderwithdrawl/mails/xx/
   ```

3. **Translate Email Templates** - Edit .html and .txt files

4. **Create Translation File**:
   ```bash
   cp modules/orderwithdrawl/translations/en.php \
      modules/orderwithdrawl/translations/xx.php
   ```

5. **Add Default Labels** - Edit `orderwithdrawl.php`:
   ```php
   protected function getDefaultButtonLabelByIso($isoCode)
   {
       $defaults = [
           'xx' => 'Your Language Label',
           // ...
       ];
   }
   ```

6. **Clear Cache and Test**

---

## 🔍 Troubleshooting

### Common Issues

#### Issue: Late deadline review notice

**Cause:** The configured withdrawal deadline is already in the past. The module still records the request and sends the notification emails.

**Solution:**
1. Check module configuration:
   ```
   Start mode: order_date
   Withdrawal Period: 14 days
   Delivery Days: 3 days
   Total: 17 days from order date
   ```
2. If you use `shipping_status`, verify that the order has reached a status marked as shipped
3. Increase withdrawal period or delivery days if appropriate
4. Verify the relevant order dates are correct in database

#### Issue: "Email does not match the order"

**Cause:** Customer entered different email than used for order

**Solutions:**
- Customer should use exact email from order confirmation
- Check for typos (case-insensitive matching)
- Verify customer account email vs. order invoice email
- Admin can check order email: **Orders** → **Orders** → View order

#### Issue: "Withdrawal state not configured"

**Cause:** Order status not created during installation

**Solution:**
1. Reinstall module:
   ```bash
   docker exec prestashop-local-prestashop-1 \
     php bin/console prestashop:module:uninstall orderwithdrawl
   docker exec prestashop-local-prestashop-1 \
     php bin/console prestashop:module:install orderwithdrawl
   ```
2. Verify order status exists: **Orders** → **Statuses**
3. Check `ps_configuration` for `ORDERWITHDRAWL_STATE_ID`

#### Issue: Email notifications not received

**Symptoms:** Withdrawal submitted successfully but no emails

**Diagnosis:**
1. Check PrestaShop logs: `var/logs/`
2. Test shop email: **Advanced Parameters** → **E-mail** → **Send test email**
3. Verify SMTP settings: **Advanced Parameters** → **E-mail**

**Solutions:**
- Configure SMTP (recommended over PHP mail())
- Check SPF/DKIM records for domain
- Verify email addresses are valid
- Check spam folders
- Review firewall rules for SMTP port (587/465)

#### Issue: Button not appearing in footer

**Cause:** Display location not enabled or CSS not loading

**Diagnosis:**
1. Check module configuration → Display Location → Footer is checked
2. Inspect page source for CSS file: `modules/orderwithdrawl/views/css/front.css`
3. Check browser console for errors

**Solutions:**
- Enable footer display in module configuration
- Clear cache: `rm -rf var/cache/*`
- Verify theme compatibility with `displayFooter` hook
- Check file permissions on CSS file

#### Issue: CMS page notices not showing

**Cause:** CMS pages not configured or hook not supported by theme

**Diagnosis:**
1. Verify CMS pages selected in module configuration
2. Check if theme supports `displayCMSDisputeInformation` hook
3. Inspect page source for hook output

**Solutions:**
- Configure CMS pages for each language
- Add custom hook to theme if missing:
  ```smarty
  {hook h='displayCMSDisputeInformation' cms=$cms}
  ```
- Use alternative display locations (footer/account)

### Debug Mode

#### Enable PrestaShop Debug

1. Edit `config/defines.inc.php`:
   ```php
   define('_PS_MODE_DEV_', true);
   ```

2. View detailed errors on frontend

3. Check logs: `var/logs/`

#### Module-Specific Logging

Withdrawal errors logged to PrestaShop system log:
```php
PrestaShopLogger::addLog(
    'Order withdrawal email error: ...',
    3, // Error level
    null,
    'OrderWithdrawl',
    $order_id
);
```

View logs: **Advanced Parameters** → **Logs**

### Database Debugging

#### Check Configuration Values

```sql
SELECT * FROM ps_configuration
WHERE name LIKE 'ORDERWITHDRAWL%';
```

#### Find Withdrawal Orders

```sql
SELECT o.id_order, o.reference, os.name
FROM ps_orders o
JOIN ps_order_state_lang os ON os.id_order_state = o.current_state
WHERE os.name LIKE '%Withdrawal%';
```

#### Verify Order Status

```sql
SELECT * FROM ps_order_state
WHERE module_name = 'orderwithdrawl';
```

---

## 📚 Legal Compliance

### EU Directive 2011/83/EU

This module implements technical requirements for:

**Article 6(1)(h)** - Right of withdrawal information
- ✅ Clear withdrawal period display
- ✅ Withdrawal form accessibility
- ✅ Automatic confirmation on durable medium (email)

**Article 11** - Right of withdrawal form
- ✅ Electronic withdrawal option provided
- ✅ Alternative to traditional methods (post, email, fax)
- ✅ Immediate acknowledgment of receipt

**Article 14** - Formal requirements
- ✅ Durable medium confirmation (email)
- ✅ Order identification (order ID, order number, or both)
- ✅ Customer identification by first and last name + order identifier by default; the older email method remains available but is not recommended

### National Implementations

#### Germany (BGB § 312g, § 355, § 356)
- Minimum 14 days withdrawal period: ✅ Configurable
- Widerruf button requirement: ✅ Implemented
- Belehrung (instruction) requirement: ⚠️ Merchant responsibility
- Automatic confirmation: ✅ Email sent

#### France (Code de la consommation L221-18)
- 14 days délai de rétractation: ✅ Configurable
- Formulaire de rétractation: ✅ Provided
- Accusé de réception: ✅ Email confirmation

#### Other EU Member States
Module provides technical framework compliant with EU directive. Merchants must ensure:
- Correct withdrawal period per local law
- Proper legal texts on CMS pages
- Return procedures documented
- Refund processing within legal timeframes

### Merchant Responsibilities

**Module Provides:**
- ✅ Technical withdrawal request system
- ✅ Email confirmation on durable medium
- ✅ Order status tracking
- ✅ Configurable withdrawal periods

**Merchant Must Ensure:**
- ⚠️ Legal withdrawal texts on website
- ⚠️ Return instructions for customers
- ⚠️ Refund processing within 14 days
- ⚠️ Proper documentation retention
- ⚠️ Compliance with local regulations
- ⚠️ Staff training on withdrawal procedures

**Important:** This module does NOT:
- ❌ Automatically issue refunds
- ❌ Generate legal withdrawal forms
- ❌ Replace legal advice
- ❌ Guarantee full legal compliance

**Recommendation:** Consult with legal counsel to ensure complete compliance with applicable consumer protection laws.

---

## 🛠️ Developer Reference

### Hooks Available

#### Custom Module Hooks

```php
// Display withdrawal link in footer
public function hookDisplayFooter($params): string

// Display withdrawal CTA through the optional custom theme hook
public function hookDisplayOrderWithdrawlCta($params): string

// Display in legacy account menu (PS 1.7)
public function hookDisplayMyAccountBlock($params): string

// Display in modern account page (PS 8/9)
public function hookDisplayCustomerAccount($params): string

// Register CSS assets
public function hookDisplayHeader($params): void

// Display notices on CMS pages
public function hookDisplayCMSDisputeInformation($params): string

// Define custom routes
public function hookModuleRoutes($params): array
```

#### Extending the Module

**Add Custom Validation:**
```php
// Override submit controller
class CustomOrderWithdrawlSubmitController extends orderwithdrawlsubmitModuleFrontController
{
    public function postProcess()
    {
        // Your custom validation
        if (!$this->customValidation()) {
            $this->errors[] = 'Custom validation failed';
            $this->redirectWithNotifications(...);
            return;
        }

        // Call parent processing
        parent::postProcess();
    }
}
```

**Add Custom Email Variables:**
```php
// In submit.php controller
$templateVars = [
    '{order_id}' => (int) $orderId,
    '{order_reference}' => $orderReference,
    '{order_identifier}' => $orderIdentifier,
    '{custom_field}' => $your_custom_data,
];

Mail::Send(..., $templateVars, ...);
```

**Hook Into Withdrawal Events:**
```php
// In your custom module
public function hookActionOrderStatusPostUpdate($params)
{
    $order = new Order($params['id_order']);
    $withdrawal_state_id = Configuration::get('ORDERWITHDRAWL_STATE_ID');

    if ($order->current_state == $withdrawal_state_id) {
        // Withdrawal request detected
        // Your custom logic here
    }
}
```

### API Reference

#### Public Methods

```php
// Get button label for specific language
$label = $module->getButtonLabelByLanguageId(int $idLang): string

// Get default privacy notice text
$notice = $module->getDefaultPrivacyNote(): string

// Format module routes
$routes = $module->hookModuleRoutes(array $params): array
```

#### Configuration Getters

```php
// Get withdrawal period
$days = (int) Configuration::get('ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS');

// Get delivery buffer days
$delivery = (int) Configuration::get('ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS');

// Get order state ID
$state_id = (int) Configuration::get('ORDERWITHDRAWL_STATE_ID');

// Get button color
$color = Configuration::get('ORDERWITHDRAWL_BUTTON_COLOR');

// Get multilang value (button label)
$label = Configuration::get('ORDERWITHDRAWL_BUTTON_LABEL', $id_lang);
```

### Database Schema

```sql
-- Configuration stored in ps_configuration
-- Multilang values stored in ps_configuration_lang

-- Order state in ps_order_state
CREATE TABLE IF NOT EXISTS `ps_order_state` (
  `id_order_state` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) DEFAULT NULL,
  `color` varchar(32) DEFAULT NULL,
  `send_email` tinyint(1) unsigned NOT NULL DEFAULT 0,
  -- ... other fields
  PRIMARY KEY (`id_order_state`)
);
```

---

## 📊 Performance

### Resource Usage

- **Database Queries:** 3-5 per withdrawal request
- **Email Operations:** 1-10 (1 customer + up to 9 admin emails)
- **Cache Impact:** Minimal (configuration read on page load)
- **Asset Size:**
  - CSS: ~11KB (admin) + ~2.5KB (front)
  - No external JavaScript asset required

### Optimization Tips

1. **Email Configuration:**
   - Use SMTP instead of PHP mail() for better performance
   - Limit admin email list to necessary recipients
   - Consider email queue for high-volume stores

2. **Cache Management:**
   - Configuration values cached by PrestaShop
   - Clear cache only when settings changed
   - No need for cache clearing on regular use

3. **Database:**
   - Order lookups use indexed order ID/order number fields
   - Minimal database writes per withdrawal

---

## 🔄 Changelog

### Version 1.4.3 (June 2026)
- Public 1.4.3 release.
- The withdrawal form page title now uses the configured button label for the current language.
- Cleaned up German source fallback strings in PHP/Smarty translation calls and added matching translations.
- No database schema change. Existing merchant settings are preserved by `upgrade-1.4.3.php`.

### Version 1.4.2 (June 2026)
- Public 1.4.2 release.
- Improved the Back Office exclusion settings for product-selection mode with a product search picker, selected product chips and a searchable category tree.
- Existing excluded product and category IDs are preserved because the release keeps the same configuration keys.
- Added an advanced manual ID editor for fallback cases.
- No database schema change. Existing merchant settings are preserved by `upgrade-1.4.2.php`.
- Compatibility target remains PrestaShop 1.7.8, 8.x and 9.x with PHP 7.4+.

### Version 1.4.0 (June 2026)
- Public 1.4.0 release.
- Added optional product-selection mode in the Back Office.
- When enabled, customers first enter their order details and then see selectable order items only after successful verification.
- Partial withdrawals can be submitted by selecting order items and a valid quantity instead of manually entering item data.
- The flow is server-side and does not use AJAX; final submissions reload order products on the server and ignore forged browser data.
- The setting is disabled by default and existing merchant settings are preserved by `upgrade-1.4.0.php`.
- No database schema change. Compatibility target remains PrestaShop 1.7.8, 8.x and 9.x with PHP 7.4+.

### Version 1.3.1 (May 2026)
- Public 1.3.1 release.
- Added structured withdrawal scope selection to the public form: customers can withdraw the entire order or selected items/services.
- Partial withdrawals use repeatable item rows with item name, item number and quantity; no free-text withdrawal reason is requested.
- Customer and admin receipt emails now include the withdrawal scope and submitted item/service details.
- Partial-withdrawal details are added to the order messages so Back Office staff can review them with the request.
- Added optional custom theme hook `displayOrderWithdrawlCta`; enable it in the Back Office and place `{hook h='displayOrderWithdrawlCta'}` in the theme where the CTA should appear.
- Invalid partial rows are rejected safely before state changes or emails; blank rows are ignored and input is sanitized/truncated defensively.
- No database schema change. Existing merchant settings are preserved and the order must still be found before a withdrawal request is processed.
- Compatibility target remains PrestaShop 1.7.8, 8.x and 9.x with PHP 7.4+.

### Version 1.3.0 (May 2026)
- Public release 1.3.0.
- Public withdrawal form uses first and last name + order number as the recommended default verification mode.
- Back office can switch verification between first and last name + order ID/order number and email + order identifier (older method, not recommended).
- Fresh installs default to manual review so withdrawals are recorded first and assessed in the Back Office.
- Confirmation mails include the submitted first and last name.
- The public form no longer displays the withdrawal policy `<details>` block; withdrawal and privacy notices remain managed through CMS/legal notice configuration.
- Earlier internal 1.3.x upgrade work was folded into `upgrade-1.3.0.php` for the public 1.3.0 release.
- Back-office required markers are placed before the label text while help tooltips stay after it.
- Back-office `?` help tooltips are placed before the label text.
- Back-office `.ow-help` texts are shown as hover/focus `?` tooltips before required markers.
- Back-office setting for order assignment: first and last name + order number, first and last name + order ID, first and last name + both, or email + order ID/order number (older method, not recommended).
- Mail templates use `{order_identifier}` for the configured visible value while keeping `{order_id}` and `{order_reference}` available.

**Security hardening (public form is now defense-in-depth):**
- **Customer note HTML-encoded** through a central `SecurityGuard` before hitting any mail template; the HTML-safe placeholder `{customer_note_html}` replaces raw insertion in `.html` templates.
- **CSRF token** enforced on the withdrawal form submission.
- **IP-based rate limit** (5 submissions / 60 s) plus **honeypot field** against bots; no database table added, runtime bucket only.
- **Duplicate withdrawal submissions** rejected before any state change.
- **Admin recipient list** now filters `\r`, `\n`, `\0` before `Validate::isEmail`.
- **Mail template path** hardened with a locale whitelist (no path traversal via ISO code).
- **`.htaccess` deny-all** guards added for `mails/`, `pdf/`, `upgrade/`.
- Customer and admin notification mails use `{order_identifier}` for the configured visible value; `{order_id}` and `{order_reference}` remain available for custom templates.
- Logging switched to PrestaShop severity constants; exception details only in dev mode.

**Upgrade from 1.2.4 to 1.3.0:**
- Direct update path for customers running 1.2.4; earlier internal 1.3.x work was consolidated into the public 1.3.0 release.
- No database schema change. Existing merchant settings are preserved; new defaults are written only when missing.
- New default settings for fresh installs and missing upgrade keys: manual review, first and last name + order number assignment, and PHP mail fallback enabled.
- Legal wording aligned with the electronic withdrawal flow: customer receipt mails confirm receipt of a withdrawal request and list the submitted details, instead of implying a final decision.
- New Back Office options for customer verification mode, order lookup mode, and PHP mail fallback.
- Mail delivery is more robust through the shared `MailDispatcher`, SMTP/sendmail diagnostics, and optional PHP `mail()` fallback.
- Public form security is strengthened with CSRF protection, honeypot, IP rate limit, duplicate submission prevention, and HTML-safe customer notes.
- Compatibility focus for PrestaShop 1.7.8, 8.x and 9.x with PHP 7.4+.
- Mail templates and translations were completed and aligned for all shipped locales.

**PrestaShop correctness:**
- `OrderHistory` transition is now dispatched exactly once per submit (no more double dispatch).
- `hookActionGetExtraMailTemplateVars` filters on module templates before loading `Order`; all four templates are covered (`withdrawl_confirm`, `withdrawl_admin`, `withdrawl_approved`, `withdrawl_rejected`).
- All hook-side `Configuration` reads run through `SettingsService` with shop context so multishop setups can override.
- Admin decision mails go through the shared `MailDispatcher` (single source of truth for template path, language fallback, placeholders).
- `Tools::getContextLocale` signature is now detected via reflection instead of `version_compare`.
- Upgrade scripts unified to raw `Db::execute()` with `$success &&=` chaining; `upgrade-1.2.1.php` corrected to `send_email = 0` directly with `upgrade-1.2.4.php` kept as a safety net.

**Mail templates:**
- `withdrawl_admin.html`/`.txt` shipped for **all seven locales** (es, fr, it, nl, pl added; previously only de/en).
- Deutsche `withdrawl_confirm.html` rebuilt as responsive HTML layout (parity with en).
- Placeholders `{firstname}`, `{lastname}`, `{history_url}`, `{customer_note_html}` now consistent across all language variants.

**Code quality:**
- **Service layer extracted** under `classes/` (PSR-4 namespace `OrderWithdrawl\\`):
  - `Config/OrderWithdrawlConfig` (≈37 config keys + defaults + whitelists)
  - `Security/SecurityGuard`, `Mail/MailDispatcher`, `Mail/OrderWithdrawlMailException`
  - `Settings/SettingsService`, `RateLimit/RateLimiter`
  - `Repository/OrderRepository`, `Repository/CustomerRepository`
  - `Admin/PolicyRenderer`, `Admin/MailPreviewRenderer`
- Null-safe order/customer lookups via repositories.
- Customer note truncation happens **exactly once** (`OrderWithdrawlConfig::CUSTOMER_NOTE_MAX_LENGTH`).
- Single language-fallback strategy; `MailDispatcher` is the only resolver.
- Typed `OrderWithdrawlMailException` for mail-delivery failures.
- Log-level literal `3` replaced with `PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR`.
- PHP 8.5 compatibility: explicit nullable parameters.
- UI strings normalised from `Withdrawl` → `Withdrawal` (slugs, config keys and template filenames unchanged for API-contract stability).

**Accessibility & UX (frontend):**
- `aria-invalid`, `aria-describedby`, `aria-live="polite"` on the withdrawal form.
- Inline per-field errors instead of one global banner.
- `:focus-visible` outline with ≥3:1 contrast on button and inputs.
- Mobile breakpoint at 480 px, textarea min-height, touch-friendly button size.
- Order-reference field gets placeholder plus help text.
- Public form keeps the submission flow focused and does not render the withdrawal policy `<details>` block.
- Confirmation page consumes a POST-Redirect-GET payload (`$orderwithdrawl_confirmation`) and keeps the legacy inline fallback.
- Account entry moved to `displayCustomerAccount` card for higher visibility.
- CSS variables unified to `--orderwithdrawl-button-color`.

**Accessibility & UX (admin):**
- New **Mail Preview** tab with per-template / per-language HTML preview and a Test-Send button; driven by `MailPreviewRenderer`.
- **Reset-to-Defaults** button with confirmation dialog.
- Mobile-friendly tab navigation with accordion fallback under 768 px.
- Clearer help texts for Period vs Manual mode.
- Inline `aria-required` hints and per-field error spans.
- Status multiselect upgraded to show 10 rows at once.
- Language tabs keep all fields in the DOM on switch (prevents data loss on submit).
- Mode-label logic moved out of Smarty templates into the controller.

**Translations:**
- **`translations/en.php` added** as explicit fallback locale.
- Key parity across all seven language files (de, en, es, fr, it, nl, pl, ro).
- Hybrid labels split into single-language variants.

**Test suite:**
- PHPUnit 10.5 baseline with 66 tests / 244 assertions, 0 skipped.
- Coverage for: order ownership, deadline calculation, state transition + duplicate, mail template resolution, hook registration roundtrip, `hookActionGetExtraMailTemplateVars`, upgrade roundtrip, multi-language button labels, customer note truncation, pdf directory status, admin-template coverage per locale.

### Version 1.2.0 (December 2025)

**New Features & Improvements:**
- **Flexible footer placement**: support for multiple hooks (`displayFooter`, `displayFooterBefore`, `displayFooterAfter`, `displayBeforeBodyClosingTag`) with per-hook enable/disable in the module configuration.
- **Enhanced CTA styling**:
  - Configurable **Bootstrap button classes** for the footer button/link and account entry.
  - Separate **button background color** and **button font color** (with color pickers).
  - Optional **link text color** when using the simple footer link mode.
  - Selectable **button sizes** (small, medium, large) and **alignment** (left, centered, right) for the prominent footer button.
- **Customer comment field** (optional) on the withdrawal form:
  - Can be enabled/disabled in General Settings.
  - Free-text note (e.g. partial deliveries, special cases) is sanitized and included in the admin notification email.

**Withdrawal Workflow Enhancements:**
- **Two evaluation modes**:
  - **Period mode (default)**: legal period calculation with configurable start mode:
    - `order_date`: X days + delivery buffer from the order date
    - `shipping_status`: X days + delivery buffer from the first status marked as shipped, with fallback to order date + delivery buffer
    - Optional custom trigger statuses can be defined; if set, the first of these statuses in the order history is used before the generic shipped-status fallback
    - Requests after the calculated deadline are accepted, emailed and marked for manual review.
  - **Manual review mode**: all valid withdrawal submissions are accepted; merchants decide later in the Back Office whether to approve or reject.
- **Additional order states** created and managed by the module:
  - **"Withdrawal request approved"** (green).
  - **"Withdrawal request rejected"** (red).
- **Automatic decision emails** on status change:
  - When an order is moved to the approved/rejected withdrawal status, the module sends corresponding customer emails using the new `withdrawl_approved` / `withdrawl_rejected` templates.
  - New **"Withdrawal Decision Emails"** tab in the configuration to define per-language texts for approved/rejected decisions, injected into the templates as `{status_message}`.

**Quality & UX:**
- Improved footer button/link HTML & CSS (custom properties + explicit `background-color`/`color` for better theme compatibility).
- Minor admin UI refinements (new settings grouped into appropriate tabs/cards).

### Version 1.0.0 (October 2025)

**Initial Release**

**Core Features:**
- EU Directive 2011/83/EU compliant withdrawal system
- Automatic "Withdrawal Requested" order status creation
- Dual email notification system (customer + admin)
- SSL-secured withdrawal request forms
- Order ID/reference and email validation

**Multilingual Support:**
- 8 languages: DE, EN, FR, IT, ES, PL, NL, RO
- Per-language customizable button labels
- Email templates in all supported languages
- Automatic language fallback system

**Display Options:**
- Multiple display locations (footer, account block, account page)
- Button/link toggle for footer display
- Customizable button color (hex color picker)
- Modern shadcn-inspired admin interface

**Advanced Features:**
- CMS page integration for privacy and withdrawal notices
- Per-language CMS page assignment
- Custom notice texts (4000 chars max)
- Configurable withdrawal period (1-365 days)
- Delivery day buffer (0-365 days)
- Multiple admin email recipients
- Locale-aware price formatting
- PrestaShop 8/9 compatibility

**Technical:**
- Custom module routes (SEO-friendly URLs)
- PSR compliance
- Modern hook integration
- Comprehensive error handling
- Detailed logging system

---

## 🤝 Support

### Contact Information

**Technical Support:**
- Please use the marketplace support channel linked to your module order.

**For Support Requests, Please Include:**
1. PrestaShop version
2. PHP version
3. Module version
4. Detailed issue description
5. Steps to reproduce
6. Error messages/logs
7. Screenshots if applicable

### Professional Services

**Available Services:**
- Custom module development
- Theme integration
- Legal compliance consulting
- Email template customization
- Multi-store setup
- Performance optimization
- Staff training

### Useful Resources

**PrestaShop Documentation:**
- https://devdocs.prestashop.com/
- https://doc.prestashop.com/

**EU Consumer Rights:**
- EU Directive 2011/83/EU: https://eur-lex.europa.eu/
- European Consumer Centre Network: https://ec.europa.eu/consumers/ecc/

**German Law (BGB):**
- § 355 Widerrufsrecht: https://www.gesetze-im-internet.de/bgb/__355.html
- § 312g Widerrufsrecht: https://www.gesetze-im-internet.de/bgb/__312g.html

---

## 📄 License

**Academic Free License 3.0 (AFL-3.0)**

Copyright © 2025 sjmedia GmbH

This module is free software: you can redistribute it and/or modify it under the terms of the Academic Free License as published by the PrestaShop SA.

This module is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the Academic Free License for more details.

You should have received a copy of the Academic Free License along with this module. If not, please visit:
http://opensource.org/licenses/afl-3.0.php

---

## 🙏 Acknowledgments

- **PrestaShop Community** - For the excellent e-commerce platform
- **EU Commission** - For clear consumer protection guidelines
- **shadcn/ui** - Design inspiration for admin interface
- **Open Source Contributors** - For continuous improvement suggestions

---

## ⚠️ Disclaimer

This module provides technical functionality to facilitate EU-compliant withdrawal requests. While designed to align with EU Directive 2011/83/EU requirements, **merchants remain solely responsible for ensuring full legal compliance** with all applicable laws and regulations in their jurisdiction.

**This module does NOT:**
- Constitute legal advice
- Automatically ensure legal compliance
- Replace consultation with legal professionals
- Automatically process refunds or returns

**Merchants are responsible for:**
- Consulting with legal counsel
- Ensuring proper legal texts on website
- Processing refunds within legal timeframes
- Maintaining proper documentation
- Training staff on withdrawal procedures
- Complying with local consumer protection laws

**Use at your own risk.** The authors and copyright holders are not liable for any legal disputes, regulatory violations, or damages arising from use of this module.

---

**Last Updated:** June 15, 2026
**Module Version:** 1.4.3
**Documentation Version:** 1.4.3
