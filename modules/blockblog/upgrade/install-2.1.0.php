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

function upgrade_module_2_1_0($module)
{

    $name_module = "blockblog";

    $img_default = "cart"."_"."default";
    Configuration::updateValue($name_module.'img_size_rp', $img_default);
    Configuration::updateValue($name_module.'post_img_width', 700);

    Configuration::updateValue($name_module.'posts_block_img_width', 200);


    Configuration::updateValue($name_module.'blog_post_effect', 'flipInX');
    Configuration::updateValue($name_module.'blog_cat_effect', 'flipInX');
    Configuration::updateValue($name_module.'blog_com_effect', 'flipInX');
    Configuration::updateValue($name_module.'blog_comp_effect', 'swing');



    Configuration::updateValue($name_module.'cat_img_width', 300);
    Configuration::updateValue($name_module.'clists_img_width', 300);
    Configuration::updateValue($name_module.'blog_catl_tr', 140);



    Configuration::updateValue($name_module.'npr_slider', 3);
    Configuration::updateValue($name_module.'relpr_slider', 1);

    Configuration::updateValue($name_module.'relp_slider', 1);
    Configuration::updateValue($name_module.'np_slider', 3);

    Configuration::updateValue($name_module.'blog_h', 3);
    Configuration::updateValue($name_module.'posts_w_h', 240);

    Configuration::updateValue($name_module.'blog_bp_sl', 3);

    Configuration::updateValue($name_module.'bposts_slider', 1);
    Configuration::updateValue($name_module.'bposts_sl', 2);

    Configuration::updateValue($name_module.'posts_block_img_width', 200);

    Configuration::updateValue($name_module.'blog_tr_b', 100);




    Configuration::updateValue($name_module.'bcat_slider', 1);
    Configuration::updateValue($name_module.'bcat_sl', 2);


    Configuration::updateValue($name_module.'bcom_slider', 1);
    Configuration::updateValue($name_module.'bcom_sl', 3);


    // sort posts
    Configuration::updateValue($name_module.'blog_post_order', 'time_add');
    Configuration::updateValue($name_module.'blog_post_ad', 'desc');

    Configuration::updateValue($name_module.'blog_rp_order', 'time_add');
    Configuration::updateValue($name_module.'blog_rp_ad', 'desc');
    // sort posts


    // sort categories
    Configuration::updateValue($name_module.'blog_cat_order', 'time_add');
    Configuration::updateValue($name_module.'blog_cat_ad', 'desc');

    Configuration::updateValue($name_module.'blog_catp_order', 'time_add');
    Configuration::updateValue($name_module.'blog_catp_ad', 'desc');
    // sort categories


    // sort categories in block //
    Configuration::updateValue($name_module.'blog_catbl_order', 'time_add');
    Configuration::updateValue($name_module.'blog_catbl_ad', 'desc');
    // sort categories in block //

    // sort posts in block //
    Configuration::updateValue($name_module.'blog_postbl_order', 'time_add');
    Configuration::updateValue($name_module.'blog_postbl_ad', 'desc');
    // sort posts in block //


    // sort posts on the home page in block //
    Configuration::updateValue($name_module.'blog_postblh_order', 'time_add');
    Configuration::updateValue($name_module.'blog_postblh_ad', 'desc');
    // sort posts on the home page in block //



    ### tags ###
    Configuration::updateValue($name_module.'tag_left', 1);
    Configuration::updateValue($name_module.'tag_footer', 1);
    Configuration::updateValue($name_module.'blog_tags', 10);
    ### tags ###


    ### statistics ###
    $module->createViewsStatisticsTable();
    Configuration::updateValue($name_module.'post_views', 1);
    Configuration::updateValue($name_module.'postl_views', 1);
    Configuration::updateValue($name_module.'postrel_views', 1);
    Configuration::updateValue($name_module.'postbl_views', 1);
    Configuration::updateValue($name_module.'postblh_views', 1);
    ### statistics ###


    ### rating ###
    Configuration::updateValue($name_module.'rating_post', 1);
    Configuration::updateValue($name_module.'rating_acom', 1);
    Configuration::updateValue($name_module.'rating_postp', 1);
    Configuration::updateValue($name_module.'rating_postrp', 1);
    Configuration::updateValue($name_module.'rating_postl', 1);
    Configuration::updateValue($name_module.'rating_bl', 1);
    Configuration::updateValue($name_module.'rating_blh', 1);
    Configuration::updateValue($name_module.'rating_bllc', 1);
    ### rating ###

    Configuration::updateValue($name_module.'number_fc', 5);



    ### add field img in ps_blog_category table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_category`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('img', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_category` ADD `img` text')) {
                return false;
            }

        }
    }


    ### add field ids_groups in ps_blog_category table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_category`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('ids_groups', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_category` ADD `ids_groups` varchar(1024) NOT NULL default \'1,2,3\'')) {
                return false;
            }

        }
    }

    ### add field position in ps_blog_category table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_category`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('position', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_category` ADD `position` int(10) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }

    $ids = Db::getInstance()->executeS('select id from `'._DB_PREFIX_.'blog_category`');
    foreach ($ids as $ids_data){
        $id = $ids_data['id'];
        $position = $id - 1;
        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_category` SET `position` = "'.(int)$position.'" WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);
    }



    ### add field content in ps_blog_category_data table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_category_data`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('content', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_category_data` ADD `content` text')) {
                return false;
            }

        }
    }





    ### add field ids_groups in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('ids_groups', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `ids_groups` varchar(1024) NOT NULL default \'1,2,3\'')) {
                return false;
            }

        }
    }

    ### add field position in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('count_views', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `count_views` int(10) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }


    ### add field position in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('position', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `position` int(10) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }

    $ids = Db::getInstance()->executeS('select id from `'._DB_PREFIX_.'blog_post`');
    foreach ($ids as $ids_data){
        $id = $ids_data['id'];
        $position = $id - 1;
        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET `position` = "'.(int)$position.'" WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);
    }


    ### add field publish_date in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('publish_date', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `publish_date` varchar(256) default \'0000-00-00 00:00:00\'')) {
                return false;
            }

        }
    }

    ### add field after_time_add in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('after_time_add', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `after_time_add` int(10) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }


    ### add field is_rss in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('is_rss', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `is_rss` int(10) NOT NULL default \'1\'')) {
                return false;
            }

        }
    }


    ### add field is_fbcomments in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('is_fbcomments', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `is_fbcomments` int(10) NOT NULL default \'1\'')) {
                return false;
            }

        }
    }





    ### add field tags in ps_blog_post_data table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post_data`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('tags', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post_data` ADD `tags` varchar(512) default NULL')) {
                return false;
            }

        }
    }


    ### add field tags in ps_blog_comments table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_comments`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('rating', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_comments` ADD `rating` int(11) NOT NULL default \'5\'')) {
                return false;
            }

        }
    }


    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_comments`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('response', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_comments` ADD `response` text')) {
                return false;
            }

        }
    }



    ### add field raging in ps_blog_post table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blog_post`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('avg_rating', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blog_post` ADD `avg_rating` int(11) NULL')) {
                return false;
            }

        }
    }

    $ids = Db::getInstance()->executeS('select id from `'._DB_PREFIX_.'blog_post`');
    foreach ($ids as $ids_data){
        $id = $ids_data['id'];

        $result_avg = Db::getInstance()->getRow('select ceil(AVG(`rating`)) AS "avg_rating" from `'._DB_PREFIX_.'blog_comments` where id_post ='.(int)$id);
        $result_avg_rating = $result_avg['avg_rating'];


        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET `avg_rating` = "'.(int)$result_avg_rating.'" WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);
    }



    ## regenerate tabs ##
    //$module->uninstallTab15();


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