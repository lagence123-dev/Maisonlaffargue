<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_3_1($module)
{

    $name_module = "blockblog";


    ## regenerate tabs in the admin panel ##
    $module->uninstallTab15();

    $module->createAdminTabs15();
    ## regenerate tabs in the admin panel ##



    // meta title, description, keywords

    $rss = 'Blog RSS';


    $languages = Language::getLanguages(false);
    foreach ($languages as $language){
        $i = $language['id_lang'];



        Configuration::updateValue($name_module.'title_rss_'.$i, $rss);
        Configuration::updateValue($name_module.'desc_rss_'.$i, $rss);
        Configuration::updateValue($name_module.'key_rss_'.$i, $rss);
        Configuration::updateValue($name_module.'blog_rss_alias_'.$i, 'rss');





        Configuration::updateValue($name_module.'blog_alias_'.$i, 'blog');
        Configuration::updateValue($name_module.'blog_cat_alias_'.$i, 'categories');
        Configuration::updateValue($name_module.'blog_allcom_alias_'.$i, 'comments');

        Configuration::updateValue($name_module.'blog_alltags_alias_'.$i, 'tags');
        Configuration::updateValue($name_module.'blog_tag_alias_'.$i, 'tag');

        Configuration::updateValue($name_module.'blog_authors_alias_'.$i, 'authors');
        Configuration::updateValue($name_module.'blog_author_alias_'.$i, 'author');

        Configuration::updateValue($name_module.'blog_gallery_alias_'.$i, 'gallery');


        Configuration::updateValue($name_module.'blog_mava_alias_'.$i, 'useraccount');
        Configuration::updateValue($name_module.'blog_mbposts_alias_'.$i, 'myblogposts');
        Configuration::updateValue($name_module.'blog_mbcom_alias_'.$i, 'myblogcomments');


    }
    // meta title, description, keywords


    // email templates
    $changestatustocust = 'Admin has changed status of the customer';
    $deleteallpoststocust = 'Admin has deleted all customers blog posts';

    $languages = Language::getLanguages(false);
    foreach ($languages as $language) {
        $i = $language['id_lang'];

        Configuration::updateValue($name_module . 'changestatustocust_' . $i, $changestatustocust);
        Configuration::updateValue($name_module . 'deleteallpoststocust_' . $i, $deleteallpoststocust);
    }

    Configuration::updateValue($name_module.'is_changestatustocust', 1);
    Configuration::updateValue($name_module.'is_deleteallpoststocust', 1);

    // email templates

    // main settings
    $text_readmore = "more";

    $languages = Language::getLanguages(false);
    foreach ($languages as $language) {
        $i = $language['id_lang'];

        Configuration::updateValue($name_module . 'text_readmore_' . $i, $text_readmore);
    }

    Configuration::updateValue($name_module.'bgcolor_main', '#2fb5d2');
    Configuration::updateValue($name_module.'bgcolor_hover', '#00cefd');

    Configuration::updateValue($name_module.'blog_date', 'd/m/Y');
    Configuration::updateValue($name_module.'blog_layout_type', 1);
    // main settings


    // sitemap
    Configuration::updateValue($name_module.'sitemapon', 1);

    Configuration::updateValue($name_module.'pages_main_blog', 1);
    Configuration::updateValue($name_module.'pages_blog_allcat', 1);
    Configuration::updateValue($name_module.'pages_blog_cat', 1);

    Configuration::updateValue($name_module.'pages_blog_post', 1);

    Configuration::updateValue($name_module.'pages_authors', 1);
    Configuration::updateValue($name_module.'pages_author', 1);
    Configuration::updateValue($name_module.'pages_allcom', 1);
    Configuration::updateValue($name_module.'pages_alltags', 1);
    Configuration::updateValue($name_module.'pages_tag', 1);
    Configuration::updateValue($name_module.'pages_gallery', 1);

    // sitemap



    // captcha comments //
    Configuration::updateValue($name_module.'bcaptcha_type', 1);
    // captcha comments //

    // social share buttons //
    Configuration::updateValue($name_module.'is_blog_f_share', 1);
    Configuration::updateValue($name_module.'is_blog_g_share', 1);
    Configuration::updateValue($name_module.'is_blog_t_share', 1);
    Configuration::updateValue($name_module.'is_blog_p_share', 1);
    Configuration::updateValue($name_module.'is_blog_l_share', 1);
    Configuration::updateValue($name_module.'is_blog_tu_share', 1);
    Configuration::updateValue($name_module.'is_blog_w_share', 1);
    /// social share buttons //

    // enable/disable items on the blog post page
    Configuration::updateValue($name_module.'is_tags_bp', 1);
    Configuration::updateValue($name_module.'is_cat_bp', 1);
    Configuration::updateValue($name_module.'is_author_bp', 1);
    // enable/disable items on the blog post page


    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_avatar2customer`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('id_shop', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_avatar2customer` ADD `id_shop` int(11) NOT NULL')) {
                return false;
            }

        }
    }

    $query = 'UPDATE '._DB_PREFIX_.'blog_avatar2customer SET id_shop = '.(int)$module->getIdShop();
    Db::getInstance()->Execute($query);;


    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_avatar2customer`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('info', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_avatar2customer` ADD `info` text')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_avatar2customer`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('status', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_avatar2customer` ADD `status` int(11) NOT NULL default \'1\'')) {
                return false;
            }
        }
    }


    /// clear smarty cache ///

    Tools::clearSmartyCache();

    /// clear smarty cache ///

    return true;

}