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

class BlockblogRssModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $_name = "blockblog";

        include_once(_PS_MODULE_DIR_.$_name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();



        $_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;

        $cookie = Context::getContext()->cookie;

        $id_lang = (int)$cookie->id_lang;
        $data_language = $obj_blog->getfacebooklib($id_lang);
        $rss_title =  Configuration::get($_name.'rssname_'.$id_lang);;
        $rss_description =  Configuration::get($_name.'rssdesc_'.$id_lang);;

        if (Configuration::get('PS_SSL_ENABLED') == 1)
            $url = "https://";
        else
            $url = "http://";

        $site = $_SERVER['HTTP_HOST'];

        // Lets build the page

        $latestBuild = date("r");

        // Lets define the the type of doc we're creating.
        header('Content-Type:text/xml; charset=utf-8');
        $createXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";


        if(Configuration::get($_name.'rsson') == 1){

            $rss_title = str_replace('&','&amp;', $rss_title);
            $rss_description = str_replace('&','&amp;', $rss_description);

            $createXML .= "<rss xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\" version=\"2.0\">\n";
            $createXML .= "<channel>
                            <title>".$rss_title."</title>
                            <description>".$rss_description."</description>
                            <link>$url$site</link>
                            <lastBuildDate>$latestBuild</lastBuildDate>
                        ";



            $data_rss_items = $obj_blog->getItemsForRSS();

            //echo "<pre>"; var_dump($data_rss_items); exit;

            foreach($data_rss_items['items'] as $_item)
            {
                $page = str_replace('&','&amp;', $_item['page']);
                $description = $_item['seo_description'];
                $title = $_item['title'];
                $count_comments = $_item['count_comments'];
                $pubdate = $_item['pubdate'];
                if(Tools::strlen($_item['img'])>0){
//                    if (defined('_PS_HOST_MODE_'))
//                        $img = $_http_host."modules/".$_name."/upload/".$_item['img'];
//                    else
//                        $img = $_http_host."upload/".$_name."/".$_item['img'];

                    $img = $_http_host."modules/".$_name."/upload/".$_item['img'];
                }else{
                    $img = '';
                }

                $author = $_item['author'];

                $createXML .= $obj_blog->createRSSFile($title,$description,$page,$pubdate,$img,$author,$count_comments);
            }
            $createXML .= "</channel>\n </rss>";
        // Finish it up
        }

        echo $createXML;
        exit;
    }

}