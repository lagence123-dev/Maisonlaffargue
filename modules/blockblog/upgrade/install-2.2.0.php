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

function upgrade_module_2_2_0($module)
{

    $name_module = "blockblog";

    // slider
    Configuration::updateValue($name_module.'slider_type', 1);
    Configuration::updateValue($name_module.'slider_h_on', 1);
    Configuration::updateValue($name_module.'slider_b_on', 1);
    Configuration::updateValue($name_module.'slider_widthfancytr', 800);
    Configuration::updateValue($name_module.'slider_heightfancytr', 500);
    Configuration::updateValue($name_module.'slider_titlefancytr', 80);
    Configuration::updateValue($name_module.'slide_limit', 5);
    Configuration::updateValue($name_module.'slider_pn_on', 1);
    // slider

    Configuration::updateValue($name_module.'author_left', 1);
    Configuration::updateValue($name_module.'author_footer', 1);

    Configuration::updateValue($name_module.'ava_on', 1);
    Configuration::updateValue($name_module.'ava_list_displ', 1);
    Configuration::updateValue($name_module.'ava_post_displ', 1);
    Configuration::updateValue($name_module.'sr_slideru', 1);
    Configuration::updateValue($name_module.'sr_slu', 3);

    Configuration::updateValue($name_module.'blog_authors', 5);

    Configuration::updateValue($name_module.'perpage_authors', 16);
    Configuration::updateValue($name_module.'d_eff_shopu', 'flipInX');

    Configuration::updateValue($name_module.'authors_home', 1);


    Configuration::updateValue($name_module.'blog_authorsh', 5);
    Configuration::updateValue($name_module.'sr_sliderhu', 1);
    Configuration::updateValue($name_module.'sr_slhu', 3);


    // slider on the product page
    Configuration::updateValue($name_module.'rp_img_widthp', 150);

    Configuration::updateValue($name_module.'postrel_viewsp', 1);
    Configuration::updateValue($name_module.'rating_postrpp', 1);
    Configuration::updateValue($name_module.'relp_sliderp', 1);

    Configuration::updateValue($name_module.'blog_relposts', 4);

    Configuration::updateValue($name_module.'np_sliderp', 3);

    // slider on the product page


    Configuration::updateValue($name_module.'is_newblogc', 1);
    Configuration::updateValue($name_module.'is_responseblogc', 1);

    Configuration::updateValue($name_module.'is_deleteblogcomcust', 1);
    Configuration::updateValue($name_module.'is_editblogcomcust', 1);
    Configuration::updateValue($name_module.'is_editblogcomtocust', 1);

    Configuration::updateValue($name_module.'is_deleteblogpostcust', 1);
    Configuration::updateValue($name_module.'is_editblogpostcust', 1);
    Configuration::updateValue($name_module.'is_addblogpostcust', 1);
    Configuration::updateValue($name_module.'is_editblogposttocust', 1);
    Configuration::updateValue($name_module.'is_addblogposttocust', 1);

    $languages = Language::getLanguages(false);
    foreach ($languages as $language){
        $i = $language['id_lang'];
        Configuration::updateValue($name_module.'newblogc_'.$i, 'New Comment from Your Blog');
        Configuration::updateValue($name_module.'responseblogc_'.$i, 'Response on the Blog Comment');

        Configuration::updateValue($name_module.'deleteblogcomcust_'.$i, 'One of your customers has delete own blog comment');
        Configuration::updateValue($name_module.'editblogcomcust_'.$i, 'One of your customers edited own blog comment');
        Configuration::updateValue($name_module.'editblogcomtocust_'.$i, 'Admin reviewed your edited blog comment');

        Configuration::updateValue($name_module.'deleteblogpostcust_'.$i, 'One of your customers has delete own blog post');
        Configuration::updateValue($name_module.'editblogpostcust_'.$i, 'One of your customers edited own blog post');
        Configuration::updateValue($name_module.'addblogpostcust_'.$i, 'One of your customers added own blog post');
        Configuration::updateValue($name_module.'editblogposttocust_'.$i, 'Admin reviewed your edited blog post');
        Configuration::updateValue($name_module.'addblogposttocust_'.$i, 'Admin reviewed your added blog post');

    }


    // meta title, description, keywords
    $categories = 'Blog categories';
    $posts = 'Blog All Posts';
    $comments = 'Blog All Comments';
    $tags = 'Blog Tags';
    $authors = 'Blog Authors';
    $myava = 'Blog Author Avatar';
    $myposts = 'My Blog Posts';
    $mycom = 'My Blog Comments';
    $gallery = 'Blog gallery';


    $languages = Language::getLanguages(false);
    foreach ($languages as $language){
        $i = $language['id_lang'];
        Configuration::updateValue($name_module.'title_allcat_'.$i, $categories);
        Configuration::updateValue($name_module.'desc_allcat_'.$i, $categories);
        Configuration::updateValue($name_module.'key_allcat_'.$i, $categories);

        Configuration::updateValue($name_module.'title_allposts_'.$i, $posts);
        Configuration::updateValue($name_module.'desc_allposts_'.$i, $posts);
        Configuration::updateValue($name_module.'key_allposts_'.$i, $posts);

        Configuration::updateValue($name_module.'title_allcom_'.$i, $comments);
        Configuration::updateValue($name_module.'desc_allcom_'.$i, $comments);
        Configuration::updateValue($name_module.'key_allcom_'.$i, $comments);

        Configuration::updateValue($name_module.'title_alltags_'.$i, $tags);
        Configuration::updateValue($name_module.'desc_alltags_'.$i, $tags);
        Configuration::updateValue($name_module.'key_alltags_'.$i, $tags);

        Configuration::updateValue($name_module.'title_allauthors_'.$i, $authors);
        Configuration::updateValue($name_module.'desc_allauthors_'.$i, $authors);
        Configuration::updateValue($name_module.'key_allauthors_'.$i, $authors);

        Configuration::updateValue($name_module.'title_myava_'.$i, $myava);
        Configuration::updateValue($name_module.'desc_myava_'.$i, $myava);
        Configuration::updateValue($name_module.'key_myava_'.$i, $myava);

        Configuration::updateValue($name_module.'title_myposts_'.$i, $myposts);
        Configuration::updateValue($name_module.'desc_myposts_'.$i, $myposts);
        Configuration::updateValue($name_module.'key_myposts_'.$i, $myposts);

        Configuration::updateValue($name_module.'title_mycom_'.$i, $mycom);
        Configuration::updateValue($name_module.'desc_mycom_'.$i, $mycom);
        Configuration::updateValue($name_module.'key_mycom_'.$i, $mycom);

        Configuration::updateValue($name_module.'title_gallery_'.$i, $gallery);
        Configuration::updateValue($name_module.'desc_gallery_'.$i, $gallery);
        Configuration::updateValue($name_module.'key_gallery_'.$i, $gallery);

    }
    // meta title, description, keywords



    // my account settings
    Configuration::updateValue($name_module.'myblogposts_on', 1);
    Configuration::updateValue($name_module.'is_addmyblogposts', 1);
    Configuration::updateValue($name_module.'is_editmyblogposts', 1);
    Configuration::updateValue($name_module.'is_delmyblogposts', 1);
    Configuration::updateValue($name_module.'d_eff_shopmyposts', 'flipInX');
    Configuration::updateValue($name_module.'perpage_myposts', 5);

    Configuration::updateValue($name_module.'myblogcom_on', 1);
    Configuration::updateValue($name_module.'is_editmyblogcom', 1);
    Configuration::updateValue($name_module.'is_delmyblogcom', 1);
    Configuration::updateValue($name_module.'d_eff_shopmycom', 'flipInX');
    Configuration::updateValue($name_module.'perpage_mycom', 5);


    Configuration::updateValue($name_module.'whocanaddc', 1);
    Configuration::updateValue($name_module.'ava_list_displ_call', 1);
    Configuration::updateValue($name_module.'ava_list_displ_cpost', 1);


    $module->registerHook('customerAccount');
    $module->registerHook('myAccountBlock');

    $module->registerHook('blogSearchSPM');
    $module->registerHook('blogArchivesSPM');
    $module->registerHook('blogTagsSPM');
    $module->registerHook('blogTopAuthorsSPM');
    $module->registerHook('blogGallerySPM');

    // GDPR
    $module->registerHook('registerGDPRConsent');
    $module->registerHook('actionDeleteGDPRCustomer');
    $module->registerHook('actionExportGDPRData');
    // GDPR

    Configuration::updateValue($name_module.'search_custom_hook', 1);
    Configuration::updateValue($name_module.'arch_custom_hook', 1);
    Configuration::updateValue($name_module.'tags_custom_hook', 1);
    Configuration::updateValue($name_module.'auth_custom_hook', 1);


    $module->createFolderAndSetPermissionsAvatar();

    $module->installUserTable();


    // gallery settings
    $module->createGalleryTable();
    $module->createFolderAndSetPermissionsGallery();

    Configuration::updateValue($name_module.'gallery_on', 1);
    Configuration::updateValue($name_module.'gallery_per_page', 24);
    Configuration::updateValue($name_module.'gallery_width', 200);
    Configuration::updateValue($name_module.'gallery_height', 200);

    Configuration::updateValue($name_module.'slider_effect', 'light_rounded');
    Configuration::updateValue($name_module.'gallery_autoplay', 1);
    Configuration::updateValue($name_module.'gallery_speed', 3000);


    Configuration::updateValue($name_module.'gallery_left', 1);
    Configuration::updateValue($name_module.'gallery_footer', 1);
    Configuration::updateValue($name_module.'gallery_home', 1);


    Configuration::updateValue($name_module.'blog_gallery', 6);
    Configuration::updateValue($name_module.'gallery_w', 60);

    Configuration::updateValue($name_module.'blog_galleryh', 12);
    Configuration::updateValue($name_module.'gallery_w_h', 150);

    Configuration::updateValue($name_module.'gallery_custom_hook', 1);


    // gallery settings

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('author', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `author` varchar(1024) NOT NULL default \'admin\'')) {
                return false;
            }

        }
    }


    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('author_id', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `author_id` int(11) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('is_slider', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `is_slider` int(11) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_comments`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('id_customer', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_comments` ADD `id_customer` int(11) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }



    ## regenerate tabs ##


    $tab_id = Tab::getIdFromClassName("AdminBlockblog");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    if(version_compare(_PS_VERSION_, '1.7', '>')) {
        $tab_id = Tab::getIdFromClassName("AdminBlockblogtest");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminBlockblogtest");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogcategories");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogposts");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogcomments");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockbloggallery");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogajax");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }


    $module->createAdminTabs15();
    ## regenerate tabs ##




    /// clear smarty cache ///

    Tools::clearSmartyCache();

    /// clear smarty cache ///




    return true;
}
?>