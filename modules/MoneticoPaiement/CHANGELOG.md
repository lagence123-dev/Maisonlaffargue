# Changelog

All notable changes to this plugin will be documented in this file.

## [1.5.2] - 2026-03-10

- Fix a bug when upgrading from 1.4.1 to a newer version of the module
- Remove size limits on some fields

## [1.5.1] - 2026-02-16

- Fix integrated payments ACS validation

## [1.5.0] - 2026-01-27

- Add compatibility with Prestashop 9
- Add log files in the Support data file
- Follow payment means configuration in the CMS to hide alternative payment means on the deported payment page
- Fix CMS data in the Support data file that was never present when there was any third party modules
- Fix integrated card payments when the card is not enrolled for 3DS
- Fix VAT management

## [1.4.10] - 2025-10-23

#### Fixed

- Fixed callbackDemo.php issue (function getByReference doesn't exist in MoneticoPaiementTransaction)

## [1.4.9] - 2025-09-15

### Added

- Added payment initiation : bank transfer via web component

## [1.4.8] - 2025-07-31

### Added

- Added VAT management feature

## [1.4.7] - 2025-07-04

#### Added

- Added a "Support" section in the module configuration page

## [1.4.6] - 2025-05-14

#### Fixed

- Fixed prestashop function compatibility
- Fixed few bugs

## [1.4.5] - 2025-03-24

### Added

- Added Callback log

#### Fixed

- Fixed few bugs

## [1.4.4] - 2025-03-24

### Added

- Added new credit card logo
- Added new Cofidis Protocols

## [1.4.3] - 2025-03-20

### Fixed

- Bug StateOrProvince fixed

## [1.4.2] - 2025-02-17

### Fixed

- Error while installation due to multilingual configuration fixed
- Order bug due to several payment pages opened fixed

## [1.4.1] - 2024-12-16

### Added

- Added partial payment (payment, recovery , cancellation , refund)

## [1.4.0] - 2024-11-04

### Added

- Added integrated purchasing route
- Added module status version check

## [1.3.1] - 2024-11-04

### Fixed

- Split Payment dropdown list bug fixed
- Translation error correction
- PHP warning fixed

## [1.3.0] - 2024-09-25

### Added

- Added demonstration mode

## [1.2.2] - 2024-06-17

### Added

- Various fixes

## [1.2.1] - 2024-03-06

- Added Custom network logo

## [1.2.0] - 2024-02-07

### Added

- Added filter by shop language
- Added Alternative Payments
- Added filter by cart amount

### Fixed

- Various bug fixes
- Security improvement


## [1.1.2] - 2023-09-18

### Fixed

- Various bug fixes
- Security improvement

## [1.1.1] - 2023-08-17

### Fixed

- Upgrade MySQL fix
- Security fix

## [1.1.0] - 2023-06-21

### Added

- Added Instalment payment
- Added Capture event option

#### Fixed

- Fixed Api stat OSVersion
- Fixed logos cards url

## [1.0.3] - 2023-03-27

#### Fixed

- Fixed Module Version for api stat
- Fixed few bugs

## [1.0.2] - 2023-03-14

### Added

- Added logos cards option
- Added Monetico Paiement Reference column

### Fixed

* Update translation

## [1.0.1] - 2023-03-10

### Fixed

* Fixed when order amount is thousand or more

## [1.0.0] - 2023/01/01

### Added

- API stat request (called when a Monetico Paiement transaction has started (only one per day))
- documentation

### Fixes

* Update translation
* Change hook version dependancy
* Remove shopping cart context
* When a payment has error status and the customer creates a used MP reference , this reference was unchanged rather
  than using a new reference
