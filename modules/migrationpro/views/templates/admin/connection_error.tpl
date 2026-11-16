{*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from MigrationPro
* Use, copy, modification or distribution of this source file without written
* license agreement from the MigrationPro is strictly forbidden.
* In order to obtain a license, please contact us: contact@migration-pro.com
*
* INFORMATION SUR LA LICENCE D'UTILISATION
*
* L'utilisation de ce fichier source est soumise a une licence commerciale
* concedee par la societe MigrationPro
* Toute utilisation, reproduction, modification ou distribution du present
* fichier source sans contrat de licence ecrit de la part de la MigrationPro est
* expressement interdite.
* Pour obtenir une licence, veuillez contacter la MigrationPro a l'adresse: contact@migration-pro.com
*
* @author    MigrationPro
* @copyright Copyright (c) 2012-2019 MigrationPro
* @license   Commercial license
* @package   MigrationPro: Prestashop Upgrade and Migrate tool
*}

<div style="font-size:1.2rem; font-weight:500; margin-top:-5px; margin-bottom:5px">
  {l s='Connection with source PrestaShop cart was not established.' mod='migrationpro'}</div>
    <p>&raquo; {l s='Check the connector module.' mod='migrationpro'}
      <a style="display: inline;color: #00aff0" href="{$sourceURL|escape:'htmlall':'UTF-8'}" target="_blank">
            {l s='Test now' mod='migrationpro'}</a> </p>
    <p>&raquo; {l s='If you enabled an SSL certificate on your (old) source PrestaShop cart, please try with a HTTPS protocol.' mod='migrationpro'}</p>
    <p>&raquo; {l s='Try URL with or without "www" prefix. Check your Apache or Nginx server redirection.' mod='migrationpro'}</p>
    <p>&raquo; {l s='Disable maintenance mode or add (new) target PrestaShop server IP address.' mod='migrationpro'}</p>
