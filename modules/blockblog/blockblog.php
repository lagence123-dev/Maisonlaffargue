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
#[\AllowDynamicProperties]
class blockblog extends Module
{

	private $_is15;
	private $_is_friendly_url;
	private $_iso_lng;
	private $_is16;
	private $_id_shop;
	private $_is_cloud;
	private $path_img_cloud;
    private $_token_cron;
    public $is_demo = 0;
    private $_disable_blocks_if_on_the_front_office_no_blog_messages = 1;

    private $_template_name;
    private $_template_path = 'views/templates/hooks/';

    private $_prefix_loyality = 'l';
    private $_prefix_loyality_db = 'spm';


    private $_translate;
    private $_id_instagram_twitter_pinterest_module = 41665;
    private $_id_vk_module = 0;

    private $_data_translate_custom = array();

    private $_img_slider_width_mobile;
    private $_img_slider_height_mobile;

    private $_is_mobile = 0;




    public function __construct()
	{
		$this->name = 'blockblog';
		$this->tab = 'content_management';
		$this->version = '2.6.0';
        $this->author = 'SPM';
		$this->module_key = 'df7c0e4b1fa647cf305f531127ca97f3';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->confirmUninstall = $this->l('Are you sure you want to remove it ? Be careful, all your configuration and your data will be lost');

        $this->_token_cron = md5($this->name._PS_BASE_URL_SSL_);

        if(version_compare(_PS_VERSION_, '1.7.4', '>') && version_compare(_PS_VERSION_, '1.7.4.2', '<')) {
            $this->_template_name = '';
        } else {
            $this->_template_name = $this->_template_path;
        }

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->bootstrap = true;
            $this->need_instance = 0;
        }


		if (defined('_PS_HOST_MODE_'))
			$this->_is_cloud = 1;
		else
			$this->_is_cloud = 0;
		
		
		// for test 
		//$this->_is_cloud = 1;
		// for test
		
		
//		if($this->_is_cloud){
//			$this->path_img_cloud = "modules/".$this->name."/upload/";
//		} else {
//			$this->path_img_cloud = "upload/".$this->name."/";
//		}
        $this->path_img_cloud = "modules/".$this->name."/upload/";


        if(version_compare(_PS_VERSION_, '1.5', '>') && version_compare(_PS_VERSION_, '1.7', '<')){

            require_once(_PS_TOOL_DIR_.'mobile_Detect/Mobile_Detect.php');
            $mobile_detect = new Mobile_Detect();

            //echo "<pre>"; var_dump($this->mobile_detect);exit;
            if ($mobile_detect->isMobile()){
                $this->_is_mobile = 1;
            }

        }

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $mobile_detect = new \Mobile_Detect();
            if ($mobile_detect->isMobile()) {
                $this->_is_mobile = 1;
            }
        }


        //$this->_is_mobile = 1;
		
		$this->_id_shop = Context::getContext()->shop->id_shop_group;
        $this->_is15 = 1;
		

		$this->_is16 = 1;


        $this->_translate = array(
            'name'=>$this->name,
            'title'=>$this->l('Automatically posts Integration'),
            'title_pstwitterpost'=>$this->l('Automatically Social Wall Posts'),
            'title_psvkpost'=>$this->l('Vkontakte Wall Post'),

            'buy_module_pstwitterpost'=>$this->l('This feature requires you to have purchased, installed and correctly configured our Automatically Social Wall Posts module.')
                .' '
                .$this->l('You may purchase it on:')
                .' '
                .$this->_linkAHtml(array('title'=>$this->l('PrestaShop Addons'),'href'=>'http://addons.prestashop.com/product.php?id_product='.$this->_id_instagram_twitter_pinterest_module))
                .' '
                .$this->l('Once this is all set, you will see the configuration options instead of this red text.'),

            'buy_module_psvkpost'=>$this->l('This feature requires you to have purchased, installed and correctly configured our Vkontakte Wall Post module.')
                .' '
                .$this->l('You may purchase it on:')
                .$this->_linkAHtml(array('title'=>$this->l('PrestaShop Addons'),'href'=>'http://addons.prestashop.com/product.php?id_product='.$this->_id_vk_module))
                .$this->l('Once this is all set, you will see the configuration options instead of this red text.'),

            'hint1'=>$this->l('This section lets you integrate with our')

                .' '.$this->_linkAHtml(array('title'=>$this->l('Automatically Social Wall Posts module'),'href'=>'http://addons.prestashop.com/product.php?id_product='.$this->_id_instagram_twitter_pinterest_module,'is_b'=>1))

                .$this->l(', and allows you posted blog posts on your PrestaShop website to be automatically posted to your')
                .' '
                .$this->l(' Social fan pages').'.'.' ',

            'hint2'=>'',

            'update_button'=>$this->l('Update settings'),
            'form_action'=>Tools::safeOutput($_SERVER['REQUEST_URI']),

            'enable_psvkpost'=> $this->l('Enable Vkontakte Wall Post'),

            'enable_pstwitterpost_main'=> $this->l('Enable Automatically Social Wall Posts'),

            'enable_pstwitterpost'=> $this->l('Enable Twitter Wall Posts'),
            'enable_pstwitterpost_i'=> $this->l('Enable Instagram Wall Posts'),
            'enable_pstwitterpost_p'=> $this->l('Enable Pinterest Wall Posts'),

            'template_text'=>$this->l('Post template text'),

        );


		
 	 	include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');

		$obj = new blogspm();
		$is_friendly_url = $obj->isURLRewriting();
		$this->_is_friendly_url = $is_friendly_url;
		$this->_iso_lng = $obj->getLangISO();

        parent::__construct(); // The parent construct is required for translations


        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Blog PRO + Loyalty Program');
        $this->description = $this->l('Add Blog PRO + Loyalty Program');
		
		$this->initContext();


        ## prestashop 1.7 ##
        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_.$this->name.'/classes/ps17helpblockblog.class.php');
            $ps17help = new ps17helpblockblog();
            $ps17help->setMissedVariables();
        } else {
            $smarty = $this->context->smarty;
            $smarty->assign($this->name.'is17' , 0);
        }
        ## prestashop 1.7 ##

        //$this->uninstallTab15();

        //$this->createAdminTabs15();

        // slider for mobile devices
        $this->_img_slider_height_mobile = Configuration::get($this->name.'slider_heightfancytr_mobile');
        $this->_img_slider_width_mobile = Configuration::get($this->name.'slider_widthfancytr_mobile');
        // slider for mobile devices


	}


    public function getAllAvailablePositionsBlog(){

        return  array(
            'right'=>$this->l('Right Column'),
            'left'=>$this->l('Left Column'),
            'footer'=>$this->l('Footer'),
            'home'=>$this->l('Home page'),
            //'chook'=>$this->l('Custom Hook'),
        );
    }

    public function getLoyalityPrefix(){
        return $this->_prefix_loyality;
    }

    public function getLoyalityDbPrefix(){
        return $this->_prefix_loyality_db;
    }

    public function getIsMobile(){
        return $this->_is_mobile;
    }

    public function getIdShop(){
        return $this->_id_shop;
    }

    public function getIdModule(){
        return $this->id;
    }

    public function getoken(){
        $_token = sha1(_COOKIE_KEY_ . $this->name);
        return $_token;
    }

	private function initContext()
	{

	  $this->context = Context::getContext();
      $this->context->currentindex = isset(AdminController::$currentIndex)?AdminController::$currentIndex:'index.php?controller=AdminModules';
        $this->context->done = isset($this->done)?$this->done:null;

	}
	
	public function getCloudImgPath(){
		return $this->path_img_cloud;
	}

	public function install()
	{

		if (!parent::install())
			return false;


        $_prefix_loyality = "l";


        Configuration::updateValue($this->name.'loyality_on'.$_prefix_loyality, 1);

        $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
        foreach ($cur AS $_cur){
            if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
                Configuration::updateValue('lamount_'.(int)$_cur['id_currency'], 0.1);
            }
        }

        Configuration::updateValue($this->name.'lorderstatuses', implode(",",array(2,5)));

        //Configuration::updateValue($this->name.$_prefix_loyality.'orderstatusescan', implode(",",array(2,5)));


        Configuration::updateValue($this->name.'validity_period'.$_prefix_loyality, 365);


        // voucher settings - loyality program

        Configuration::updateValue($this->name.'vis_on'.$_prefix_loyality, 1);

        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];
            $iso = Tools::strtoupper(Language::getIsoById($i));

            $coupondesc = 'Blog PRO + Loyalty Program';
            Configuration::updateValue($this->name.'coupondesc'.$_prefix_loyality.'_'.$i, $coupondesc.' '.$iso);
        }

        Configuration::updateValue($this->name.'vouchercode'.$_prefix_loyality, "LOYALITY");

        Configuration::updateValue($this->name.'tax'.$_prefix_loyality, 1);


        Configuration::updateValue($this->name.'sdvvalid'.$_prefix_loyality, 365);

        // cumulable
        Configuration::updateValue($this->name.'cumulativeother'.$_prefix_loyality, 0);
        Configuration::updateValue($this->name.'cumulativereduc'.$_prefix_loyality, 0);
        // cumulable

        Configuration::updateValue($this->name.'highlight'.$_prefix_loyality, 0);
        // categories
        Configuration::updateValue($this->name.'catbox'.$_prefix_loyality, $this->getIdsCategories());
        // categories

        // voucher settings - loyality program


        // points and actions //
        $points_and_actions = $this->getAvailablePoints();

        foreach($points_and_actions as $k=> $item){

            $points = $item['points'];

            Configuration::updateValue($this->name.$k.'_points', (int)$points);
            Configuration::updateValue($this->name.$k.'_status', $k);

            $is_times = isset($item['count_times'])?$item['count_times']:0;
            Configuration::updateValue($this->name . $k . '_times', (int)$is_times);

            $languages = Language::getLanguages(false);
            foreach ($languages as $language){
                $i = $language['id_lang'];
                Configuration::updateValue($this->name.$k.'description_'.$i, $item['desc_points']);
            }
        }

        // points and actions //

        // my account //
        Configuration::updateValue($this->name.'d_eff_loyalty_my'.$_prefix_loyality, 'flipInX');
        Configuration::updateValue($this->name.'perpagemy'.$_prefix_loyality, 10);
        // me account //


        /* enable/disable emails */
        Configuration::updateValue($this->name.'noti'.$_prefix_loyality, 1);

        Configuration::updateValue($this->name.'is_points'.$_prefix_loyality, 1);
        Configuration::updateValue($this->name.'is_loyaltyvouch'.$_prefix_loyality, 1);

        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];
            Configuration::updateValue($this->name.'points'.$_prefix_loyality.'_'.$i, 'You received loyalty points');
            Configuration::updateValue($this->name.'loyaltyvouch'.$_prefix_loyality.'_'.$i, 'You transform points into a voucher and get voucher with discount');
        }
        /* enable/disable emails */


        foreach($this->getAvailablePages() as $available_pages_array) {
            $alias_position = $available_pages_array['alias'];
            $is_layout = $available_pages_array['is_layout'];
            Configuration::updateValue($this->name . 'sidebar_pos' . $alias_position, 3);

            if($is_layout) {
                Configuration::updateValue($this->name . 'blog_layout_type' . $alias_position, 1);
            }
        }

        // sitemap

        Configuration::updateValue($this->name.'sitemapon', 1);

        Configuration::updateValue($this->name.'pages_main_blog', 1);
        Configuration::updateValue($this->name.'pages_blog_allcat', 1);
        Configuration::updateValue($this->name.'pages_blog_cat', 1);

        Configuration::updateValue($this->name.'pages_blog_post', 1);

        Configuration::updateValue($this->name.'pages_authors', 1);
        Configuration::updateValue($this->name.'pages_author', 1);
        Configuration::updateValue($this->name.'pages_allcom', 1);
        Configuration::updateValue($this->name.'pages_alltags', 1);
        Configuration::updateValue($this->name.'pages_tag', 1);
        Configuration::updateValue($this->name.'pages_gallery', 1);

        //Configuration::updateValue($this->name.'sitemap_limit', 5000);

        // sitemap


        // main settings
        $text_readmore = $this->l("more");

        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $i = $language['id_lang'];

            Configuration::updateValue($this->name . 'text_readmore_' . $i, $text_readmore);
        }

        Configuration::updateValue($this->name.'bgcolor_main', '#2fb5d2');
        Configuration::updateValue($this->name.'bgcolor_hover', '#00cefd');

        Configuration::updateValue($this->name.'blog_date', 'd/m/Y');
        //Configuration::updateValue($this->name.'blog_layout_type', 1);
        // main settings


        // meta title, description, keywords

        $rss = 'Blog RSS';
        $myloyalty = 'Loyalty Program';


        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];

            Configuration::updateValue($this->name.'title_loyalty_'.$i, $myloyalty);
            Configuration::updateValue($this->name.'desc_loyalty_'.$i, $myloyalty);
            Configuration::updateValue($this->name.'key_loyalty_'.$i, $myloyalty);
            Configuration::updateValue($this->name.'blog_loyalty_alias_'.$i, 'loyalty');

            Configuration::updateValue($this->name.'title_rss_'.$i, $rss);
            Configuration::updateValue($this->name.'desc_rss_'.$i, $rss);
            Configuration::updateValue($this->name.'key_rss_'.$i, $rss);
            Configuration::updateValue($this->name.'blog_rss_alias_'.$i, 'rss');




            Configuration::updateValue($this->name.'blog_alias_'.$i, 'blog');
            Configuration::updateValue($this->name.'blog_cat_alias_'.$i, 'categories');
            Configuration::updateValue($this->name.'blog_allcom_alias_'.$i, 'comments');

            Configuration::updateValue($this->name.'blog_alltags_alias_'.$i, 'tags');
            Configuration::updateValue($this->name.'blog_tag_alias_'.$i, 'tag');

            Configuration::updateValue($this->name.'blog_authors_alias_'.$i, 'authors');
            Configuration::updateValue($this->name.'blog_author_alias_'.$i, 'author');

            Configuration::updateValue($this->name.'blog_gallery_alias_'.$i, 'gallery');


            Configuration::updateValue($this->name.'blog_mava_alias_'.$i, 'useraccount');
            Configuration::updateValue($this->name.'blog_mbposts_alias_'.$i, 'myblogposts');
            Configuration::updateValue($this->name.'blog_mbcom_alias_'.$i, 'myblogcomments');


        }
        // meta title, description, keywords


        #### posts api ###
        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];
            $iso = Tools::strtoupper(Language::getIsoById($i));
            Configuration::updateValue($this->name.'twdesc_'.$i, $this->l('New blog post has been added in our shop').' '.$iso);
            Configuration::updateValue($this->name.'idesc_'.$i, $this->l('New blog post has been added in our shop').' '.$iso);
            Configuration::updateValue($this->name.'pdesc_'.$i, $this->l('New blog post has been added in our shop').' '.$iso);
            Configuration::updateValue($this->name.'vkdesc_'.$i, $this->l('New blog post has been added in our shop').' '.$iso);
        }

        #### posts api ###


        // gallery
        Configuration::updateValue($this->name.'gallery_on', 1);
        Configuration::updateValue($this->name.'gallery_per_page', 24);
        Configuration::updateValue($this->name.'gallery_width', 200);
        Configuration::updateValue($this->name.'gallery_height', 200);

        Configuration::updateValue($this->name.'slider_effect', 'light_rounded');
        Configuration::updateValue($this->name.'gallery_autoplay', 1);
        Configuration::updateValue($this->name.'gallery_speed', 3000);

        Configuration::updateValue($this->name.'gallery_left', 1);
        Configuration::updateValue($this->name.'gallery_footer', 1);
        Configuration::updateValue($this->name.'gallery_footer', 1);

        Configuration::updateValue($this->name.'blog_gallery', 6);
        Configuration::updateValue($this->name.'gallery_w', 60);

        Configuration::updateValue($this->name.'blog_galleryh', 12);
        Configuration::updateValue($this->name.'gallery_w_h', 150);

        Configuration::updateValue($this->name.'gallery_home', 1);


        // gallery

        // slider
        Configuration::updateValue($this->name.'slider_type', 1);
        Configuration::updateValue($this->name.'slider_h_on', 1);
        Configuration::updateValue($this->name.'slider_b_on', 1);
        Configuration::updateValue($this->name.'slider_widthfancytr', 800);
        Configuration::updateValue($this->name.'slider_heightfancytr', 500);

        Configuration::updateValue($this->name.'slider_widthfancytr_mobile', 400);
        Configuration::updateValue($this->name.'slider_heightfancytr_mobile', 300);

        Configuration::updateValue($this->name.'slider_titlefancytr', 80);
        Configuration::updateValue($this->name.'slide_limit', 5);
        Configuration::updateValue($this->name.'slider_pn_on', 1);

        // slider


        Configuration::updateValue($this->name.'whocanaddc', 1);

        Configuration::updateValue($this->name.'ava_list_displ_call', 1);
        Configuration::updateValue($this->name.'ava_list_displ_cpost', 1);

        // my account
        Configuration::updateValue($this->name.'myblogposts_on', 1);
        Configuration::updateValue($this->name.'is_addmyblogposts', 1);
        Configuration::updateValue($this->name.'is_editmyblogposts', 1);
        Configuration::updateValue($this->name.'is_delmyblogposts', 1);
        Configuration::updateValue($this->name.'d_eff_shopmyposts', 'flipInX');
        Configuration::updateValue($this->name.'perpage_myposts', 5);

        Configuration::updateValue($this->name.'blog_ids_groups', '1,2,3');

        Configuration::updateValue($this->name.'myblogcom_on', 1);
        Configuration::updateValue($this->name.'is_editmyblogcom', 1);
        Configuration::updateValue($this->name.'is_delmyblogcom', 1);
        Configuration::updateValue($this->name.'d_eff_shopmycom', 'flipInX');
        Configuration::updateValue($this->name.'perpage_mycom', 5);

        // my account

        // meta title, description, keywords
        $categories = $this->l('Blog categories');
        $posts = $this->l('Blog All Posts');
        $comments = $this->l('Blog All Comments');
        $tags = $this->l('Blog Tags');
        $authors = $this->l('Blog Authors');
        $myava = $this->l('Blog Author Avatar');
        $myposts = $this->l('My Blog Posts');
        $mycom = $this->l('My Blog Comments');
        $gallery = $this->l('Blog gallery');

        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];
            Configuration::updateValue($this->name.'title_allcat_'.$i, $categories);
            Configuration::updateValue($this->name.'desc_allcat_'.$i, $categories);
            Configuration::updateValue($this->name.'key_allcat_'.$i, $categories);

            Configuration::updateValue($this->name.'title_allposts_'.$i, $posts);
            Configuration::updateValue($this->name.'desc_allposts_'.$i, $posts);
            Configuration::updateValue($this->name.'key_allposts_'.$i, $posts);

            Configuration::updateValue($this->name.'title_allcom_'.$i, $comments);
            Configuration::updateValue($this->name.'desc_allcom_'.$i, $comments);
            Configuration::updateValue($this->name.'key_allcom_'.$i, $comments);

            Configuration::updateValue($this->name.'title_alltags_'.$i, $tags);
            Configuration::updateValue($this->name.'desc_alltags_'.$i, $tags);
            Configuration::updateValue($this->name.'key_alltags_'.$i, $tags);

            Configuration::updateValue($this->name.'title_allauthors_'.$i, $authors);
            Configuration::updateValue($this->name.'desc_allauthors_'.$i, $authors);
            Configuration::updateValue($this->name.'key_allauthors_'.$i, $authors);

            Configuration::updateValue($this->name.'title_myava_'.$i, $myava);
            Configuration::updateValue($this->name.'desc_myava_'.$i, $myava);
            Configuration::updateValue($this->name.'key_myava_'.$i, $myava);

            Configuration::updateValue($this->name.'title_myposts_'.$i, $myposts);
            Configuration::updateValue($this->name.'desc_myposts_'.$i, $myposts);
            Configuration::updateValue($this->name.'key_myposts_'.$i, $myposts);

            Configuration::updateValue($this->name.'title_mycom_'.$i, $mycom);
            Configuration::updateValue($this->name.'desc_mycom_'.$i, $mycom);
            Configuration::updateValue($this->name.'key_mycom_'.$i, $mycom);

            Configuration::updateValue($this->name.'title_gallery_'.$i, $gallery);
            Configuration::updateValue($this->name.'desc_gallery_'.$i, $gallery);
            Configuration::updateValue($this->name.'key_gallery_'.$i, $gallery);

        }
        // meta title, description, keywords


        // avatar
        Configuration::updateValue($this->name.'ava_on', 1);
        Configuration::updateValue($this->name.'ava_list_displ', 1);
        Configuration::updateValue($this->name.'ava_post_displ', 1);
        Configuration::updateValue($this->name.'sr_slideru', 0);
        Configuration::updateValue($this->name.'sr_slu', 3);
        Configuration::updateValue($this->name.'perpage_authors', 16);
        Configuration::updateValue($this->name.'d_eff_shopu', 'flipInX');

        Configuration::updateValue($this->name.'blog_authorsh', 5);
        Configuration::updateValue($this->name.'sr_sliderhu', 0);
        Configuration::updateValue($this->name.'sr_slhu', 3);
        // avatar

        // slider on the product page
        Configuration::updateValue($this->name.'rp_img_widthp', 150);

        Configuration::updateValue($this->name.'postrel_viewsp', 1);
        Configuration::updateValue($this->name.'rating_postrpp', 1);
        Configuration::updateValue($this->name.'relp_sliderp', 0);

        Configuration::updateValue($this->name.'np_sliderp', 3);

        // slider on the product page


        if(Configuration::get('PS_REWRITING_SETTINGS')){
            Configuration::updateValue($this->name.'urlrewrite_on', 1);
        }

        Configuration::updateValue($this->name.'btabs_type', 1);


        Configuration::updateValue($this->name.'is_captcha_com', 1);
        Configuration::updateValue($this->name.'bcaptcha_type', 1);


        Configuration::updateValue($this->name.'cat_custom_hook', 1);
        Configuration::updateValue($this->name.'posts_custom_hook', 1);
        Configuration::updateValue($this->name.'comm_custom_hook', 1);
        Configuration::updateValue($this->name.'search_custom_hook', 1);
        Configuration::updateValue($this->name.'arch_custom_hook', 1);
        Configuration::updateValue($this->name.'tags_custom_hook', 1);
        Configuration::updateValue($this->name.'auth_custom_hook', 1);
        Configuration::updateValue($this->name.'gallery_custom_hook', 1);

        foreach(array("m","s") as $val) {
            Configuration::updateValue($this->name . $val.'cat_custom_hook', 1);
            Configuration::updateValue($this->name . $val.'posts_custom_hook', 1);
            Configuration::updateValue($this->name . $val.'comm_custom_hook', 1);
            Configuration::updateValue($this->name . $val.'search_custom_hook', 1);
            Configuration::updateValue($this->name . $val.'arch_custom_hook', 1);
            Configuration::updateValue($this->name . $val.'tags_custom_hook', 1);
            Configuration::updateValue($this->name . $val.'auth_custom_hook', 1);
            Configuration::updateValue($this->name . $val.'gallery_custom_hook', 1);
        }


        Configuration::updateValue($this->name.'blog_tags', 10);
        Configuration::updateValue($this->name.'blog_authors', 5);

		// comments //
        Configuration::updateValue($this->name.'blog_bp_sl', 3);
		Configuration::updateValue($this->name.'blog_com', 5);
        Configuration::updateValue($this->name.'blog_com_tr', 75);
		Configuration::updateValue($this->name.'perpage_com', 10);
        Configuration::updateValue($this->name.'pperpage_com', 3);

        Configuration::updateValue($this->name.'rating_post', 1);
        Configuration::updateValue($this->name.'rating_acom', 1);

        Configuration::updateValue($this->name.'blog_com_effect', 'flipInX');
        Configuration::updateValue($this->name.'blog_comp_effect', 'swing');
		// comments //

        //fb comm
        Configuration::updateValue($this->name.'number_fc', 5);

		// left
		Configuration::updateValue($this->name.'cat_left', 1);
		Configuration::updateValue($this->name.'posts_left', 1);
		Configuration::updateValue($this->name.'com_left', 1);
        Configuration::updateValue($this->name.'tag_left', 1);
        Configuration::updateValue($this->name.'author_left', 1);
		// left

		Configuration::updateValue($this->name.'blog_h', 3);
		Configuration::updateValue($this->name.'blog_bp_h', 4);
		Configuration::updateValue($this->name.'posts_w_h', 240);
        Configuration::updateValue($this->name.'blog_p_tr', 250);


		/*Configuration::updateValue($this->name.'search_left', 1);
		Configuration::updateValue($this->name.'arch_left', 1);

		Configuration::updateValue($this->name.'cat_footer', 1);
		Configuration::updateValue($this->name.'posts_footer', 1);
		Configuration::updateValue($this->name.'search_footer', 1);
		Configuration::updateValue($this->name.'arch_footer', 1);
		Configuration::updateValue($this->name.'com_footer', 1);
        Configuration::updateValue($this->name.'tag_footer', 1);
        Configuration::updateValue($this->name.'author_footer', 1);*/

        ### positions ##
        $data_blocks_for_home_page = array('posts','auth','gallery');
        foreach($this->getAllAvailablePositionsBlog() as $alias_position => $item_position) {

            // connect positions for current position
            $i = 1;
            $positions = array();
            foreach ($this->getBlocksArrayPrefix() as $_key_type => $_values_type) {

                if($alias_position == "home") {
                    if (!in_array($_key_type, $data_blocks_for_home_page))
                        continue;
                }

                $key = $alias_position."_".$_key_type;
                $position_item = $i;
                $positions[$key] = $position_item;


                $i++;
            }


            $positions = json_encode($positions);
            Configuration::updateValue($this->name . '_orderby' . $alias_position, $positions);

            // connect positions for current position


            Configuration::updateValue($this->name.$alias_position."mobile", 1);
            Configuration::updateValue($this->name.$alias_position, 1);



            foreach ($this->getBlocksArrayPrefix() as $_key_type => $_values_type) {

                Configuration::updateValue($this->name . '_' . $alias_position . $_key_type . "mobile", $alias_position.$_key_type."mobile");
                Configuration::updateValue($this->name . '_' . $alias_position . $_key_type , $alias_position.$_key_type);
            }

        }

        ### positions ##



		Configuration::updateValue($this->name.'cat_list_display_date', 1);
		Configuration::updateValue($this->name.'perpage_catblog', 10);
        Configuration::updateValue($this->name.'blog_cat_effect', 'flipInX');

        Configuration::updateValue($this->name.'cat_img_width', 300);
        Configuration::updateValue($this->name.'clists_img_width', 300);

        Configuration::updateValue($this->name.'blog_catl_tr', 300);



		Configuration::updateValue($this->name.'perpage_posts', 10);
		Configuration::updateValue($this->name.'p_list_displ_date', 1);
        Configuration::updateValue($this->name.'postl_views', 1);
        Configuration::updateValue($this->name.'rating_postl', 1);


        Configuration::updateValue($this->name.'postbl_views', 1);
        Configuration::updateValue($this->name.'rating_bl', 1);
        Configuration::updateValue($this->name.'rating_blh', 1);

		Configuration::updateValue($this->name.'block_display_date', 1);
		Configuration::updateValue($this->name.'block_display_img', 1);
		Configuration::updateValue($this->name.'posts_block_img_width', 200);





		Configuration::updateValue($this->name.'tab_blog_pr', 1);

        Configuration::updateValue($this->name.'blog_cat_order', 'time_add');
        Configuration::updateValue($this->name.'blog_cat_ad', 'desc');
        Configuration::updateValue($this->name.'blog_catp_order', 'time_add');
        Configuration::updateValue($this->name.'blog_catp_ad', 'desc');

        Configuration::updateValue($this->name.'authors_home', 1);

		Configuration::updateValue($this->name.'block_last_home', 1);
        Configuration::updateValue($this->name.'postblh_views', 1);
		Configuration::updateValue($this->name.'lists_img_width', 300);
        //Configuration::updateValue($this->name.'lists_img_height', 200);
        Configuration::updateValue($this->name.'blog_pl_tr', 140);


        Configuration::updateValue($this->name.'blog_post_effect', 'flipInX');

        Configuration::updateValue($this->name.'blog_post_order', 'time_add');
        Configuration::updateValue($this->name.'blog_post_ad', 'desc');

        Configuration::updateValue($this->name.'blog_rp_order', 'time_add');
        Configuration::updateValue($this->name.'blog_rp_ad', 'desc');


		Configuration::updateValue($this->name.'post_display_date', 1);
		Configuration::updateValue($this->name.'post_img_width', 700);
		//Configuration::updateValue($this->name.'is_soc_buttons', 1);

        Configuration::updateValue($this->name.'is_blog_f_share', 1);
        Configuration::updateValue($this->name.'is_blog_g_share', 1);
        Configuration::updateValue($this->name.'is_blog_t_share', 1);
        Configuration::updateValue($this->name.'is_blog_p_share', 1);
        Configuration::updateValue($this->name.'is_blog_l_share', 1);
        Configuration::updateValue($this->name.'is_blog_tu_share', 1);
        Configuration::updateValue($this->name.'is_blog_w_share', 1);


        Configuration::updateValue($this->name.'post_views', 1);
        Configuration::updateValue($this->name.'rating_postp', 1);
        Configuration::updateValue($this->name.'rating_postrp', 1);


        Configuration::updateValue($this->name.'is_tags_bp', 1);
        Configuration::updateValue($this->name.'is_cat_bp', 1);
        Configuration::updateValue($this->name.'is_author_bp', 1);


        $img_default = "cart"."_"."default";
        Configuration::updateValue($this->name . 'img_size_rp', $img_default);
        Configuration::updateValue($this->name.'blog_rp_tr', 75);

        Configuration::updateValue($this->name.'rp_img_width', 150);

        Configuration::updateValue($this->name.'postrel_views', 1);

        Configuration::updateValue($this->name.'npr_slider', 3);
        Configuration::updateValue($this->name.'np_slider', 3);


        Configuration::updateValue($this->name.'blog_relposts', 4);

        Configuration::updateValue($this->name.'relpr_slider', 0);
        Configuration::updateValue($this->name.'relp_slider', 0);



		Configuration::updateValue($this->name.'noti', 1);
		Configuration::updateValue($this->name.'mail', @Configuration::get('PS_SHOP_EMAIL'));


        Configuration::updateValue($this->name.'is_newblogc', 1);
        Configuration::updateValue($this->name.'is_responseblogc', 1);

        Configuration::updateValue($this->name.'is_deleteblogcomcust', 1);
        Configuration::updateValue($this->name.'is_editblogcomcust', 1);
        Configuration::updateValue($this->name.'is_editblogcomtocust', 1);

        Configuration::updateValue($this->name.'is_deleteblogpostcust', 1);
        Configuration::updateValue($this->name.'is_editblogpostcust', 1);
        Configuration::updateValue($this->name.'is_addblogpostcust', 1);
        Configuration::updateValue($this->name.'is_editblogposttocust', 1);
        Configuration::updateValue($this->name.'is_addblogposttocust', 1);

        Configuration::updateValue($this->name.'is_changestatustocust', 1);
        Configuration::updateValue($this->name.'is_deleteallpoststocust', 1);


        $changestatustocust = $this->l('Admin has changed status of the customer');
        $deleteallpoststocust = $this->l('Admin has deleted all customers blog posts');

        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];

            Configuration::updateValue($this->name . 'changestatustocust_' . $i, $changestatustocust);
            Configuration::updateValue($this->name . 'deleteallpoststocust_' . $i, $deleteallpoststocust);

            Configuration::updateValue($this->name.'newblogc_'.$i, $this->l('New Comment from Your Blog'));
            Configuration::updateValue($this->name.'responseblogc_'.$i, $this->l('Response on the Blog Comment'));

            Configuration::updateValue($this->name.'deleteblogcomcust_'.$i, $this->l('One of your customers has delete own blog comment'));
            Configuration::updateValue($this->name.'editblogcomcust_'.$i, $this->l('One of your customers edited own blog comment'));
            Configuration::updateValue($this->name.'editblogcomtocust_'.$i, $this->l('Admin reviewed your edited blog comment'));

            Configuration::updateValue($this->name.'deleteblogpostcust_'.$i, $this->l('One of your customers has delete own blog post'));
            Configuration::updateValue($this->name.'editblogpostcust_'.$i, $this->l('One of your customers edited own blog post'));
            Configuration::updateValue($this->name.'addblogpostcust_'.$i, $this->l('One of your customers added own blog post'));
            Configuration::updateValue($this->name.'editblogposttocust_'.$i, $this->l('Admin reviewed your edited blog post'));
            Configuration::updateValue($this->name.'addblogposttocust_'.$i, $this->l('Admin reviewed your added blog post'));


        }



		Configuration::updateValue($this->name.'blog_bcat', 5);
		Configuration::updateValue($this->name.'blog_bposts', 3);

        Configuration::updateValue($this->name.'blog_catbl_order', 'time_add');
        Configuration::updateValue($this->name.'blog_catbl_ad', 'desc');

        Configuration::updateValue($this->name.'blog_bposts', 5);
        Configuration::updateValue($this->name.'blog_bposts', 5);

        Configuration::updateValue($this->name.'blog_tr_b', 100);

        Configuration::updateValue($this->name.'bposts_slider', 0);
        Configuration::updateValue($this->name.'bposts_sl', 2);


        Configuration::updateValue($this->name.'bcat_slider', 0);
        Configuration::updateValue($this->name.'bcat_sl', 3);

        Configuration::updateValue($this->name.'blog_postbl_order', 'time_add');
        Configuration::updateValue($this->name.'blog_postbl_ad', 'desc');

        Configuration::updateValue($this->name.'blog_postblh_order', 'time_add');
        Configuration::updateValue($this->name.'blog_postblh_ad', 'desc');





        Configuration::updateValue($this->name.'bcom_slider', 0);
        Configuration::updateValue($this->name.'bcom_sl', 3);

        Configuration::updateValue($this->name.'rating_bllc', 1);


		Configuration::updateValue($this->name.'rsson', 1);
		Configuration::updateValue($this->name.'number_rssitems', 10);

		$languages = Language::getLanguages(false);
    	foreach ($languages as $language){
    		$i = $language['id_lang'];

    		$rssname = Configuration::get('PS_SHOP_NAME');
    		Configuration::updateValue($this->name.'rssname_'.$i, $rssname);
			$rssdesc = Configuration::get('PS_SHOP_NAME');
			Configuration::updateValue($this->name.'rssdesc_'.$i, $rssdesc);
		}




	 	$this->createAdminTabs15();


		if (!$this->registerHook('displayLeftColumn')
			OR !$this->registerHook('displayRightColumn')
			OR !$this->registerHook('displayHeader')
			OR !$this->registerHook('displayFooter')
			OR !$this->registerHook('displayProductTabContent')
			OR !$this->registerHook('displayProductTab')
            OR !$this->registerHook('displayProductfooter')
			OR !$this->_installDB()
            OR !$this->createLikePostTable()
			OR !$this->registerHook('displayHome')

            OR !$this->createGalleryTable()
            OR !($this->_is_cloud? true : $this->createFolderAndSetPermissionsGallery())

            OR !$this->registerHook('displayCustomerAccount')
            OR !$this->registerHook('displayMyAccountBlock')
            OR !$this->installUserTable()
            OR !($this->_is_cloud? true : $this->createFolderAndSetPermissionsAvatar())


            OR !$this->registerHook('ModuleRoutes')

			OR !($this->_is_cloud? true : $this->_createFolderAndSetPermissions())

			OR !$this->registerHook('DisplayBackOfficeHeader')

            OR !$this->registerHook('blogCategoriesSPM')
            OR !$this->registerHook('blogPostsSPM')
            OR !$this->registerHook('blogCommentsSPM')
            OR !$this->registerHook('blogSearchSPM')
            OR !$this->registerHook('blogArchivesSPM')
            OR !$this->registerHook('blogTagsSPM')
            OR !$this->registerHook('blogTopAuthorsSPM')
            OR !$this->registerHook('blogGallerySPM')


            OR !$this->installLoyalityProgramTables()

            OR !$this->registerHook('displayOrderConfirmation')
            OR !$this->registerHook('actionValidateOrder')
            OR !$this->registerHook('displayPaymentReturn')
            OR !$this->registerHook('actionBeforeAddOrder')

            OR !$this->registerHook('actionOrderStatusUpdate')

            OR !$this->registerHook('actionCustomerAccountAdd')


            OR !$this->registerHook('displayCustomerAccountForm')

            OR !((version_compare(_PS_VERSION_, '1.7', '>'))? $this->registerHook('displayProductButtons') : $this->registerHook('productActions'))


            //GDPR
            OR !$this->registerHook('registerGDPRConsent')
            OR !$this->registerHook('actionDeleteGDPRCustomer')
            OR !$this->registerHook('actionExportGDPRData')
            //GDPR
			 )
			return false;


		return true;
	}


    public function hookActionDeleteGDPRCustomer ($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {

            include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
            $obj_blog = new blogspm();
            $return = $obj_blog->deleteGDPRCustomerData($customer['email']);

            if ($return) {
                return json_encode(true);
            }
            return json_encode($this->displayName . ' : ' .$this->l('Unable to delete customer using email.'));
        }
    }


    public function hookActionExportGDPRData ($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {

            $return = array();
            include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
            $obj_blog = new blogspm();
            $return_blog = $obj_blog->getGDPRCustomerData($customer['email']);

            if(count($return_blog)>0)
                $return[0] = $return_blog;


            if (count($return)>0) {
                return json_encode($return);
            }
            return json_encode($this->displayName . ' : ' .$this->l('Unable to export customer using email.'));
        }
    }




    public function hookModuleRoutes()
    {

        $id_lang = $this->context->language->id;

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        $data_seo_uls_set = $obj_blog->getSeoUrlsSettings(array('id_lang'=>$id_lang));

        $blog_alias = $data_seo_uls_set['blog_alias'];
        $blog_cat_alias = $data_seo_uls_set['blog_cat_alias'];
        $blog_allcom_alias = $data_seo_uls_set['blog_allcom_alias'];
        $blog_alltags_alias = $data_seo_uls_set['blog_alltags_alias'];
        $blog_tag_alias = $data_seo_uls_set['blog_tag_alias'];
        $blog_authors_alias = $data_seo_uls_set['blog_authors_alias'];
        $blog_author_alias = $data_seo_uls_set['blog_author_alias'];
        $blog_gallery_alias = $data_seo_uls_set['blog_gallery_alias'];
        $blog_mava_alias = $data_seo_uls_set['blog_mava_alias'];
        $blog_mbposts_alias = $data_seo_uls_set['blog_mbposts_alias'];
        $blog_mbcom_alias = $data_seo_uls_set['blog_mbcom_alias'];

        $blog_rss_alias = $data_seo_uls_set['blog_rss_alias'];
        $blog_mbloyalty_alias = $data_seo_uls_set['blog_mbloyality_alias'];



        return array(





            ## category ##
            /*'blockblog-blog-category' => array(
                'controller' =>	'blog',
                'rule' =>		$blog_alias.'/c-{category_id}',
                'keywords' => array(
                    'category_id'		=>	array('regexp' => '[0-9a-zA-Z-_]+','param'=>'category_id'),

                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),*/
            ## category ##

            ## categories ##
            'blockblog-blog-categories' => array(
                'controller' =>	'categories',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_cat_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## categories ##

            /// old post page
            /*'blockblog-blog-post-post_id' => array(
                'controller' =>	'post',
                'rule' =>		$blog_alias.'/{controller}/{post_id}',
                'keywords' => array(
                    'post_id'		=>	array('regexp' => '[0-9a-zA-Z-_]+','param'=>'post_id'),
                    'controller'	=>	array('regexp' => 'post', 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),*/
            /// old post page




            ## comments ##
            'blockblog-blog-comments' => array(
                'controller' =>	'comments',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_allcom_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),


            ## comments ##


            ## tags ##

            'blockblog-blog-tag-tag_id' => array(
                'controller' =>	'tag',
                'rule' =>		$blog_alias.'/{controller}/{tag_id}',
                'keywords' => array(
                    'tag_id'		=>	array('regexp' => '[0-9a-zA-Z-_\s\pL_]+','param'=>'tag_id'),
                    'controller'	=>	array('regexp' => $blog_tag_alias, 'param' => 'controller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),

            'blockblog-blog-tags' => array(
                'controller' =>	'tags',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_alltags_alias, 'param' => 'controller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),

            ## tags ##

            ## authors ##

            'blockblog-blog-author-author_id' => array(
                'controller' =>	'author',
                'rule' =>		$blog_alias.'/{controller}/{author_id}',
                'keywords' => array(
                    'author_id'		=>	array('regexp' => '[0-9a-zA-Z-_\s\pL_]+','param'=>'author_id'),
                    'controller'	=>	array('regexp' => $blog_author_alias, 'param' => 'controller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),

            'blockblog-blog-authors' => array(
                'controller' =>	'authors',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_authors_alias, 'param' => 'controller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## authors ##


            ## gallery ##
            'blockblog-blog-gallery' => array(
                'controller' =>	'gallery',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_gallery_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## gallery ##


            ## useraccount ##
            'blockblog-blog-useraccount' => array(
                'controller' =>	'useraccount',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_mava_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## useraccount ##


            ## myblogposts ##
            'blockblog-blog-myblogposts' => array(
                'controller' =>	'myblogposts',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_mbposts_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## myblogposts ##


            ## myblogcomments ##
            'blockblog-blog-myblogcomments' => array(
                'controller' =>	'myblogcomments',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_mbcom_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## myblogcomments ##

            ## loyalty ##
            'blockblog-blog-loyalty' => array(
                'controller' =>	'loyalty',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_mbloyalty_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## loyalty ##



            ## rss ##
            'blockblog-blog-rss' => array(
                'controller' =>	'rss',
                'rule' =>		$blog_alias.'/{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_rss_alias, 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),
            ## rss ##






           'blockblog-blog' => array(
                'controller' =>	'blog',
                'rule' =>		'{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => $blog_alias, 'param' => 'controller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),


            'blockblog-blog-p' => array(
                'controller' =>	'blog',
                'rule' =>		'{controller}/{p}',
                'keywords' => array(
                    'p'		=>	array('regexp' => '[0-9a-zA-Z-_]+','param'=>'p'),
                    'controller'	=>	array('regexp' => $blog_alias, 'param' => 'controller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockblog'
                )
            ),





        );


    }




    public function hookDisplayBackOfficeHeader()
	{

        $this->context->controller->addCSS($this->_path.'views/css/tab.css');


	}
	
	public function uninstall()
	{

        Configuration::deleteByName($this->name.'urlrewrite_on');

        Configuration::deleteByName($this->name.'btabs_type');


        Configuration::deleteByName($this->name.'blog_tags');
        Configuration::deleteByName($this->name.'blog_authors');

        // comments //
        Configuration::deleteByName($this->name.'blog_bp_sl');
        Configuration::deleteByName($this->name.'blog_com');
        Configuration::deleteByName($this->name.'blog_com_tr');
        Configuration::deleteByName($this->name.'perpage_com');
        Configuration::deleteByName($this->name.'pperpage_com');
        Configuration::deleteByName($this->name.'rating_post');
        Configuration::deleteByName($this->name.'rating_acom');
        Configuration::deleteByName($this->name.'blog_com_effect');
        Configuration::deleteByName($this->name.'blog_comp_effect');
        // comments //

        //fb comm
        Configuration::deleteByName($this->name.'number_fc');


        // left
        Configuration::deleteByName($this->name.'cat_left');
        Configuration::deleteByName($this->name.'posts_left');
        Configuration::deleteByName($this->name.'com_left');
        Configuration::deleteByName($this->name.'tag_left');
        Configuration::deleteByName($this->name.'author_left');
        // left

        Configuration::deleteByName($this->name.'blog_h');
        Configuration::deleteByName($this->name.'blog_bp_h');
        Configuration::deleteByName($this->name.'posts_w_h');
        Configuration::deleteByName($this->name.'blog_p_tr');

        /*Configuration::deleteByName($this->name.'search_left');
        Configuration::deleteByName($this->name.'arch_left');

        Configuration::deleteByName($this->name.'cat_footer');
        Configuration::deleteByName($this->name.'posts_footer');
        Configuration::deleteByName($this->name.'search_footer');
        Configuration::deleteByName($this->name.'arch_footer');
        Configuration::deleteByName($this->name.'com_footer');
        Configuration::deleteByName($this->name.'tag_footer');*/

        Configuration::deleteByName($this->name.'search_right');
        Configuration::deleteByName($this->name.'arch_right');
        Configuration::deleteByName($this->name.'com_right');


        Configuration::deleteByName($this->name.'cat_list_display_date');
        Configuration::deleteByName($this->name.'perpage_catblog');

        Configuration::deleteByName($this->name.'blog_cat_effect');

        Configuration::deleteByName($this->name.'clists_img_width');
        Configuration::deleteByName($this->name.'cat_img_width');

        Configuration::deleteByName($this->name.'blog_catl_tr');


        Configuration::deleteByName($this->name.'perpage_posts');
        Configuration::deleteByName($this->name.'p_list_displ_date');
        Configuration::deleteByName($this->name.'postl_views');
        Configuration::deleteByName($this->name.'rating_postl');

        Configuration::deleteByName($this->name.'postbl_views');
        Configuration::deleteByName($this->name.'rating_bl');
        Configuration::deleteByName($this->name.'rating_blh');

        Configuration::deleteByName($this->name.'block_display_date');
        Configuration::deleteByName($this->name.'block_display_img');
        Configuration::deleteByName($this->name.'posts_block_img_width');


        Configuration::deleteByName($this->name.'tab_blog_pr');

        Configuration::deleteByName($this->name.'blog_cat_order');
        Configuration::deleteByName($this->name.'blog_cat_ad');
        Configuration::deleteByName($this->name.'blog_catp_order');
        Configuration::deleteByName($this->name.'blog_catp_ad');

        Configuration::deleteByName($this->name.'authors_home');
        Configuration::deleteByName($this->name.'block_last_home');
        Configuration::deleteByName($this->name.'postblh_views');
        Configuration::deleteByName($this->name.'lists_img_width');
        //Configuration::deleteByName($this->name.'lists_img_height');
        Configuration::deleteByName($this->name.'blog_pl_tr');
        Configuration::deleteByName($this->name.'blog_post_effect');
        Configuration::deleteByName($this->name.'blog_post_order');
        Configuration::deleteByName($this->name.'blog_post_ad');
        Configuration::deleteByName($this->name.'blog_rp_order');
        Configuration::deleteByName($this->name.'blog_rp_ad');


        Configuration::deleteByName($this->name.'post_display_date');
        Configuration::deleteByName($this->name.'post_img_width');
        //Configuration::deleteByName($this->name.'is_soc_buttons');

        Configuration::deleteByName($this->name.'is_blog_f_share');
        Configuration::deleteByName($this->name.'is_blog_g_share');
        Configuration::deleteByName($this->name.'is_blog_t_share');
        Configuration::deleteByName($this->name.'is_blog_p_share');
        Configuration::deleteByName($this->name.'is_blog_l_share');
        Configuration::deleteByName($this->name.'is_blog_tu_share');
        Configuration::deleteByName($this->name.'is_blog_w_share');



        Configuration::deleteByName($this->name.'post_views');
        Configuration::deleteByName($this->name.'rating_postp');
        Configuration::deleteByName($this->name.'rating_postrp');

        Configuration::deleteByName($this->name.'is_tags_bp');
        Configuration::deleteByName($this->name.'is_cat_bp');
        Configuration::deleteByName($this->name.'is_author_bp');


        Configuration::deleteByName($this->name.'img_size_rp');
        Configuration::deleteByName($this->name.'blog_rp_tr');

        Configuration::deleteByName($this->name.'rp_img_width');
        Configuration::deleteByName($this->name.'postrel_views');

        Configuration::deleteByName($this->name.'relpr_slider');
        Configuration::deleteByName($this->name.'relp_slider');

        Configuration::deleteByName($this->name.'np_slider');
        Configuration::deleteByName($this->name.'np_slider');


        Configuration::deleteByName($this->name.'noti');
        Configuration::deleteByName($this->name.'mail');
        Configuration::deleteByName($this->name.'blog_bcat');
        Configuration::deleteByName($this->name.'blog_bposts');

        Configuration::deleteByName($this->name.'bposts_slider');
        Configuration::deleteByName($this->name.'bposts_sl');

        Configuration::deleteByName($this->name.'bcat_slider');
        Configuration::deleteByName($this->name.'bcat_sl');
        Configuration::deleteByName($this->name.'bcom_slider');
        Configuration::deleteByName($this->name.'bcom_sl');

        Configuration::deleteByName($this->name.'rating_bllc');


        Configuration::deleteByName($this->name.'blog_tr_b');

        Configuration::deleteByName($this->name.'blog_catbl_order');
        Configuration::deleteByName($this->name.'blog_catbl_ad');
        Configuration::deleteByName($this->name.'blog_postbl_order');
        Configuration::deleteByName($this->name.'blog_postbl_ad');
        Configuration::deleteByName($this->name.'blog_postblh_order');
        Configuration::deleteByName($this->name.'blog_postblh_ad');



        Configuration::deleteByName($this->name.'rsson');
        Configuration::deleteByName($this->name.'number_rssitems');


        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];

            Configuration::deleteByName($this->name.'rssname_'.$i);
            Configuration::deleteByName($this->name.'rssdesc_'.$i);

        }


		$this->uninstallTab15();

		
			
		if (!parent::uninstall() || !$this->_uninstallDB())
			return false;
		return true;
	}
	
	public function uninstallTab15(){



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


        $tab_id = Tab::getIdFromClassName("AdminBlockblogauthors");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminBlockblogstatisticsspm");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminBlockblogajax");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }



		
		@unlink(_PS_ROOT_DIR_."/img/t/AdminBlockblog.gif");
	}
	

	
	public function getTabNameByLangISO(){
        return array(

          'AdminBlockblog' => array(
              'fr' => 'Blog',
              'en' => 'Blog',
              'es' => 'Blog',
              'it' => 'Blog',
              'nl' => 'Blog',
              'pt' => 'Blog',
              'ru' => 'Блог',
              'bg' => 'Блог',
              'br' => 'Blog',
              'de' => 'Blog',
          ),

          'AdminBlockblogtest' => array(
              'fr' => 'Blog',
              'en' => 'Blog',
              'es' => 'Blog',
              'it' => 'Blog',
              'nl' => 'Blog',
              'pt' => 'Blog',
              'ru' => 'Блог',
              'bg' => 'Блог',
              'br' => 'Blog',
              'de' => 'Blog',
            ),

            'AdminBlockblogcategories' => array(
                'fr' => 'Catégories',
                'en' => 'Categories',
                'es' => 'Categorías',
                'it' => 'Categorie',
                'nl' => 'Categorieën',
                'pt' => 'Categorias',
                'ru' => 'Категории',
                'bg' => 'Категории',
                'br' => 'Categorias',
                'de' => 'Kategorien',
            ),

            'AdminBlockblogposts' => array(
                'fr' => 'Messages',
                'en' => 'Posts',
                'es' => 'Mensajes',
                'it' => 'Messaggi',
                'nl' => 'Posts',
                'pt' => 'Posts',
                'ru' => 'Посты',
                'bg' => 'Публикации',
                'br' => 'Posts',
                'de' => 'Beiträge',
            ),

            'AdminBlockblogcomments' => array(
                'fr' => 'Commentaires',
                'en' => 'Comments',
                'es' => 'Comentarios',
                'it' => 'Commenti',
                'nl' => 'Comments',
                'pt' => 'Comentários',
                'ru' => 'Комментарии',
                'bg' => 'Коментари',
                'br' => 'Comentários',
                'de' => 'Kommentare',
            ),

            'AdminBlockbloggallery' => array(
                'fr' => 'Galerie',
                'en' => 'Gallery',
                'es' => 'Galería',
                'it' => 'Galleria',
                'nl' => 'Galerij',
                'pt' => 'Galeria',
                'ru' => 'Галерея',
                'bg' => 'Галерия',
                'br' => 'Galeria',
                'de' => 'Galerie',
            ),


            'AdminBlockblogpolls' => array(
                'fr' => 'Sondages',
                'en' => 'Polls',
                'es' => 'Encuestas',
                'it' => 'Sondaggi',
                'nl' => 'Polls',
                'pt' => 'Pesquisas',
                'ru' => 'Опросы',
                'bg' => 'Анкети',
                'br' => 'Pesquisas',
                'de' => 'Umfragen',
            ),

            'AdminBlockblogauthors' => array(
                'fr' => 'Auteurs',
                'en' => 'Authors',
                'es' => 'Autores',
                'it' => 'Autori',
                'nl' => 'Auteurs',
                'pt' => 'Autores',
                'ru' => 'Авторы',
                'bg' => 'Автори',
                'br' => 'Autores',
                'de' => 'Autoren',
            ),

            'AdminBlockblogstatisticsspm' => array(
                'en' => 'Customer loyalty points',
                'fr' => 'Points de fidélité client',
                'es' => 'Puntos de fidelización de clientes',
                'it' => 'Punti fedeltà del cliente',
                'nl' => 'Klant loyaliteits punten',
                'pt' => 'Pontos de fidelização de clientes',
                'ru' => 'Очки лояльности клиентов',
                'bg' => 'Точки за лоялност на клиентите',
                'br' => 'Pontos de fidelização de clientes',
                'de' => 'Kunden bindungs punkte',
            ),


        );
    }
	
	public function createAdminTabs15(){


            $aTabNameByLang = $this->getTabNameByLangISO();

		 	$langs = Language::getLanguages(true);


            $tab0 = new Tab();
            $tab0->class_name = "AdminBlockblog";
            $tab0->module = $this->name;
            $tab0->id_parent = 0;
            $tab0->active = 1;
            foreach($langs as $l){
                    $tab0->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockblog"][$l['iso_code']])?$aTabNameByLang["AdminBlockblog"][$l['iso_code']]:$aTabNameByLang["AdminBlockblog"]['en'];
            }
            $tab0->save();
            $main_tab_id = $tab0->id;

            unset($tab0);

            if(version_compare(_PS_VERSION_, '1.7', '>')) {
                // create subtab //

                $tab0 = new Tab();
                $tab0->class_name = "AdminBlockblogtest";
                $tab0->module = $this->name;
                $tab0->id_parent = $main_tab_id;
                $tab0->active = 1;
                foreach ($langs as $l) {
                    $tab0->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockblogtest"][$l['iso_code']])?$aTabNameByLang["AdminBlockblogtest"][$l['iso_code']]:$aTabNameByLang["AdminBlockblogtest"]['en'];
                }
                $tab0->save();
                $main_tab_id = $tab0->id;

                unset($tab0);
                // create subtab //
            }

            $tab1 = new Tab();
            $tab1->class_name = "AdminBlockblogcategories";
            $tab1->module = $this->name;
            $tab1->id_parent = $main_tab_id;
            $tab1->active = 1;
            foreach($langs as $l){
                    $tab1->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockblogcategories"][$l['iso_code']])?$aTabNameByLang["AdminBlockblogcategories"][$l['iso_code']]:$aTabNameByLang["AdminBlockblogcategories"]['en'];
            }
            $tab1->save();



            unset($tab1);

            $tab2 = new Tab();
            $tab2->class_name = "AdminBlockblogposts";
            $tab2->module = $this->name;
            $tab2->id_parent = $main_tab_id;
            $tab2->active = 1;
            foreach($langs as $l){
                    $tab2->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockblogposts"][$l['iso_code']])?$aTabNameByLang["AdminBlockblogposts"][$l['iso_code']]:$aTabNameByLang["AdminBlockblogposts"]['en'];
            }
            $tab2->save();

            unset($tab2);

            $tab3 = new Tab();
            $tab3->class_name = "AdminBlockblogcomments";
            $tab3->module = $this->name;
            $tab3->id_parent = $main_tab_id;
            $tab3->active = 1;
            foreach($langs as $l){
                    $tab3->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockblogcomments"][$l['iso_code']])?$aTabNameByLang["AdminBlockblogcomments"][$l['iso_code']]:$aTabNameByLang["AdminBlockblogcomments"]['en'];
            }
            $tab3->save();

            unset($tab3);

            $tab4 = new Tab();
            $tab4->class_name = "AdminBlockbloggallery";
            $tab4->module = $this->name;
            $tab4->id_parent = $main_tab_id;
            $tab4->active = 1;
            foreach($langs as $l){
                $tab4->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockbloggallery"][$l['iso_code']])?$aTabNameByLang["AdminBlockbloggallery"][$l['iso_code']]:$aTabNameByLang["AdminBlockbloggallery"]['en'];
            }
            $tab4->save();

            unset($tab4);




            $tab7 = new Tab();
            $tab7->class_name = "AdminBlockblogauthors";
            $tab7->module = $this->name;
            $tab7->id_parent = $main_tab_id;
            $tab7->active = 1;
            foreach($langs as $l){
                $tab7->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockblogauthors"][$l['iso_code']])?$aTabNameByLang["AdminBlockblogauthors"][$l['iso_code']]:$aTabNameByLang["AdminBlockblogauthors"]['en'];
            }
            $tab7->save();

            unset($tab7);


            $tab8 = new Tab();
            $tab8->class_name = "AdminBlockblogstatisticsspm";
            $tab8->module = $this->name;
            $tab8->id_parent = $main_tab_id;
            $tab8->active = 1;
            foreach($langs as $l){
                $tab8->name[$l['id_lang']] = isset($aTabNameByLang["AdminBlockblogstatisticsspm"][$l['iso_code']])?$aTabNameByLang["AdminBlockblogstatisticsspm"][$l['iso_code']]:$aTabNameByLang["AdminBlockblogstatisticsspm"]['en'];
            }
            $tab8->save();

            unset($tab8);


            $tab_ajax = new Tab();
            $tab_ajax->module = $this->name;
            $tab_ajax->active = 0;
            $tab_ajax->class_name = 'AdminBlockblogajax';
            $tab_ajax->id_parent = (int)Tab::getIdFromClassName($this->name);
            foreach (Language::getLanguages(true) as $lang)
                $tab_ajax->name[$lang['id_lang']] = 'Blockblogajax';
            $tab_ajax->add();



	}


    public function createFolderAndSetPermissionsAvatar(){

        $prev_cwd = getcwd();

        $module_dir = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
        @chdir($module_dir);
        //folder avatars
        $module_dir_img = $module_dir.$this->name.DIRECTORY_SEPARATOR;
        @mkdir($module_dir_img, 0777);

        @chdir($module_dir_img);

        $module_dir_img_avatar = $module_dir.$this->name.DIRECTORY_SEPARATOR."avatar".DIRECTORY_SEPARATOR;
        @mkdir($module_dir_img_avatar, 0777);

        @chdir($prev_cwd);

        return true;
    }

    public function createFolderAndSetPermissionsGallery(){

        $prev_cwd = getcwd();

        $module_dir = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
        @chdir($module_dir);
        //folder avatars
        $module_dir_img = $module_dir.$this->name.DIRECTORY_SEPARATOR;
        @mkdir($module_dir_img, 0777);

        @chdir($module_dir_img);

        $module_dir_img_avatar = $module_dir.$this->name.DIRECTORY_SEPARATOR."gallery".DIRECTORY_SEPARATOR;
        @mkdir($module_dir_img_avatar, 0777);

        @chdir($prev_cwd);

        return true;
    }


	
	private function _createFolderAndSetPermissions(){
		
		$prev_cwd = getcwd();
		
		$module_dir = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
		@chdir($module_dir);
		//folder avatars
		$module_dir_img = $module_dir.$this->name.DIRECTORY_SEPARATOR;
		@mkdir($module_dir_img, 0777);

		@chdir($prev_cwd);
		
		return true;
	}


    public function installLoyalityProgramTables(){
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj = new loyalityblog();
        return $obj->installLoyalityProgramTables();
    }


    private function _installDB(){
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_category` (
							  `id` int(11) NOT NULL auto_increment,
							  `img` text,
							  `ids_shops` varchar(1024) NOT NULL default \'0\',
							  `ids_groups` varchar(1024) NOT NULL default \'1,2,3\',
							  `position` int(10) NOT NULL default \'0\',
							  `status` int(11) NOT NULL default \'1\',
							  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
							  PRIMARY KEY  (`id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		if (!Db::getInstance()->Execute($sql))
			return false;
			
		$query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_category_data` (
							  `id_item` int(11) NOT NULL,
							  `id_lang` int(11) NOT NULL,
							  `title` varchar(1024) default NULL,
							  `seo_description` text,
							  `seo_keywords` varchar(1024) default NULL,
							  `seo_url` varchar(1024) default NULL,
							  `content` LONGTEXT,
							  KEY `id_item` (`id_item`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8';
		if (!Db::getInstance()->Execute($query))
			return false;
			
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_post` (
					  `id` int(11) NOT NULL auto_increment,
					  `img` text,
					  `author` varchar(1024) NOT NULL default \'admin\',
					  `author_id` int(11) NOT NULL default \'0\',
					  `ids_shops` varchar(1024) NOT NULL default \'0\',
					  `status` int(11) NOT NULL default \'1\',
					  `is_comments` int(11) NOT NULL default \'1\',
					  `related_products` varchar(1024) NOT NULL default \'0\',
					  `related_posts` varchar(1024) NOT NULL default \'0\',
					  `ids_groups` varchar(1024) NOT NULL default \'1,2,3\',
					  `position` int(10) NOT NULL default \'0\',
					  `count_views` int(10) NOT NULL default \'0\',
					  `after_time_add` int(10) NOT NULL default \'0\',
					  `publish_date` varchar(256) default \'0000-00-00 00:00:00\',
					  `avg_rating` int(11) NULL,
					  `is_slider` int(11) NOT NULL default \'0\',
					  `is_rss` int(10) NOT NULL default \'1\',
					  `is_fbcomments` int(10) NOT NULL default \'0\',
					  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
					  PRIMARY KEY  (`id`)
					) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		if (!Db::getInstance()->Execute($sql))
			return false;
			
		$query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_post_data` (
							  `id_item` int(11) NOT NULL,
							  `id_lang` int(11) NOT NULL,
							  `title` varchar(1024) default NULL,
							  `seo_keywords` varchar(1024) default NULL,
							  `seo_description` text,
							  `content` LONGTEXT,
							  `seo_url` varchar(1024) default NULL,
							  `tags` varchar(512) default NULL,
							  KEY `id_item` (`id_item`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8';
		if (!Db::getInstance()->Execute($query))
			return false;
		
			
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_comments` (
							  `id` int(11) NOT NULL auto_increment,
							  `id_lang` int(11) NOT NULL,
							  `name` varchar(5000) default NULL,
							  `email` varchar(500) default NULL,
							  `id_customer` int(11) NOT NULL default \'0\',
							  `comment` text,
							  `response` text,
							  `rating` int(11) NOT NULL,
							  `status` int(11) NOT NULL default \'0\',
							  `id_post` int(11) NOT NULL,
							  `id_shop` int(11) NOT NULL default \'0\',
							  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
							  PRIMARY KEY  (`id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		if (!Db::getInstance()->Execute($sql))
			return false;

		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_category2post` (
							  `category_id` int(11) NOT NULL,
							  `post_id` int(11) NOT NULL,
							  KEY `category_id` (`category_id`),
							  KEY `post_id` (`post_id`),
							  KEY `category2post` (`category_id`,`post_id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		if (!Db::getInstance()->Execute($sql))
			return false;


        ### create statistics table ###
        if(!$this->createViewsStatisticsTable())
            return false;
        ### create statistics table ###
			
		return true;
	}

    public function createLikePostTable(){
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_post_like` (
					  `id` int(11) NOT NULL auto_increment,
					  `post_id` int(11) NOT NULL,
					  `like` int(11) NOT NULL,
					  `ip` varchar(255) default NULL,
					  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
					  PRIMARY KEY  (`id`)
					) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
        if (!Db::getInstance()->Execute($sql))
            return false;

        return true;
    }

    public function createViewsStatisticsTable(){
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_views_statistics` (
					  `id` int(11) NOT NULL auto_increment,
					  `post_id` int(11) NOT NULL,
					  `ip` varchar(255) default NULL,
					  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
					  PRIMARY KEY  (`id`)
					) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
        if (!Db::getInstance()->Execute($sql))
            return false;

        return true;
    }

    public function installUserTable(){

        $db = Db::getInstance();
        $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_avatar2customer` (
							  `id_customer` int(11) NOT NULL,
							  `avatar_thumb` text,
							  `info` text,
							  `is_show` int(11) NOT NULL default \'1\',
							  `id_shop` int(11) NOT NULL,
							  `status` int(11) NOT NULL default \'1\',
							  KEY `id_customer` (`id_customer`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8';
        $db->Execute($query);
        return true;

    }

    public function createGalleryTable(){
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_gallery` (
							  `id` int(11) NOT NULL auto_increment,
							  `img` text,
							  `ids_shops` varchar(1024) NOT NULL default \'0\',
							  `position` int(10) NOT NULL default \'0\',
							  `status` int(11) NOT NULL default \'1\',
							  `is_featured` int(11) NOT NULL default \'1\',
							  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
							  PRIMARY KEY  (`id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
        if (!Db::getInstance()->Execute($sql))
            return false;

        $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blog_gallery_data` (
							  `id_item` int(11) NOT NULL,
							  `id_lang` int(11) NOT NULL,
							  `title` varchar(1024) default NULL,
							  `content` text,
							  KEY `id_item` (`id_item`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8';
        if (!Db::getInstance()->Execute($query))
            return false;
        return true;
    }


    public function isInstallandEnableModules(){

        $is_enable = 0;
        $is_install = 0;

        $modules = array('spmgsnipreview','spmreviewsadv','spmprodstorerev');

        foreach($modules as $name) {

            /*if (version_compare(_PS_VERSION_, '1.7.5', '>')) {

                $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                $moduleManager = $moduleManagerBuilder->build();
                $is_install = ($is_install == 1)?1:$moduleManager->isInstalled($name);
                $is_enable = ($is_enable == 1)?1:$moduleManager->isEnabled($name);
            } else {*/
                $is_install = ($is_install == 1)?1:Module::isInstalled($name);
                $is_enable = ($is_enable == 1)?1:Module::isEnabled($name);
            //}


        }


        return array('is_install'=>$is_install,'is_enable'=>$is_enable);
    }


	private function _uninstallDB() {
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_category');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_category_data');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_post');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_post_data');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_comments');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_category2post');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_post_like');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_views_statistics');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_avatar2customer');

        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_gallery');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blog_gallery_data');

        /*$data = $this->isInstallandEnableModules();
        $is_install = $data['is_install'];

        if(!$is_install) {*/
            include_once(_PS_MODULE_DIR_ . $this->name . '/classes/loyalityblog.class.php');
            $obj_loyalty = new loyalityblog();
            $obj_loyalty->uninstallTable();
        //}


        return true;
	}
	


    public function setSEOUrls(){
        $smarty = $this->context->smarty;
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $is_show_customer_data = $obj_blog->getGroupPermissionsForBlog();
        $is_show_customer = $is_show_customer_data['is_show'];


        $data_url = $obj_blog->getSEOURLs();
        $category_url = $data_url['category_url'];
        $categories_url = $data_url['categories_url'];
        $posts_url = $data_url['posts_url'];
        $post_url = $data_url['post_url'];
        $comments_url = $data_url['comments_url'];
        $tags_url = $data_url['tags_url'];
        $tag_url = $data_url['tag_url'];
        $ajax_url = $data_url['ajax_url'];
        $captcha_url = $data_url['captcha_url'];
        $rss_url = $data_url['rss_url'];
        $author_url = $data_url['author_url'];
        $authors_url = $data_url['authors_url'];
        $my_account = $data_url['my_account'];
        $useraccount_url = $data_url['useraccount_url'];
        $myblogposts_url = $data_url['myblogposts_url'];
        $myblogcomments_url = $data_url['myblogcomments_url'];
        $gallery_url = $data_url['gallery_url'];
        $loyalty_account_url = $data_url['loyalty_account_url'];



        $smarty->assign(
                        array($this->name.'category_url' => $category_url,
                              $this->name.'categories_url' => $categories_url,
                              $this->name.'posts_url' => $posts_url,
                              $this->name.'post_url' => $post_url,
                              $this->name.'comments_url' => $comments_url,
                              $this->name.'tags_url'=>$tags_url,
                              $this->name.'tag_url'=>$tag_url,
                              $this->name.'ajax_url'=>$ajax_url,
                              $this->name.'captcha_url'=>$captcha_url,
                              $this->name.'rss_url'=>$rss_url,
                              $this->name.'author_url'=>$author_url,
                              $this->name.'authors_url'=>$authors_url,
                              $this->name.'my_account'=>$my_account,
                              $this->name.'useraccount_url'=>$useraccount_url,
                              $this->name.'myblogposts_url'=>$myblogposts_url,
                              $this->name.'myblogcomments_url'=>$myblogcomments_url,
                              $this->name.'gallery_url'=>$gallery_url,

                              $this->name.'is_show_customer'=>$is_show_customer,
                              $this->name.'loyalty_account_url'=>$loyalty_account_url,


                            )
        );




    }

    public function hookblogCommentsSPM($params)
    {
        $comm_custom_hook = Configuration::get($this->name.'comm_custom_hook');

        if($comm_custom_hook == 1) {

            $id_post = isset($params['id_post']) ? $params['id_post'] : '';

            $is_mobile = $this->getIsMobile();

            if ((Configuration::get(($this->name . 'mcomm_custom_hook')) == 1 && $is_mobile == 1)
                ||
                (Configuration::get(($this->name . 'scomm_custom_hook')) == 1 && $is_mobile == 0)
            ) {

                $name_template = "blogcommentsspm.tpl";

                $cache_id = $this->name . '|' . $name_template . $id_post.$is_mobile;

                if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                    $smarty = $this->context->smarty;

                    $this->setMainSettings();

                    $smarty->assign($this->name . 'is16', $this->_is16);

                    include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');
                    $obj_blog = new blogspm();
                    $_data_com = $obj_blog->getLastComments(array('id_post' => $id_post));

                    $smarty->assign(array(
                            $this->name . 'comments' => $_data_com['comments']
                        )
                    );

                    $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());


                    $smarty->assign($this->name . 'block_display_date', Configuration::get($this->name . 'block_display_date'));
                    $smarty->assign($this->name . 'block_display_img', Configuration::get($this->name . 'block_display_img'));
                    $smarty->assign($this->name.'ava_on', Configuration::get($this->name.'ava_on'));


                    $ps15 = 0;
                    if ($this->_is15) {
                        $ps15 = 1;
                    }
                    $smarty->assign($this->name . 'is_ps15', $ps15);

                    $smarty->assign($this->name . 'rsson', Configuration::get($this->name . 'rsson'));

                    if ($this->_is_friendly_url) {
                        $smarty->assign($this->name . 'iso_lng', $this->_iso_lng);
                    } else {
                        $smarty->assign($this->name . 'iso_lng', '');
                    }

                    $smarty->assign($this->name . 'pic', $this->path_img_cloud);

                    $this->setSEOUrls();

                    $smarty->assign($this->name . 'blog_com_tr', Configuration::get($this->name . 'blog_com_tr'));
                    $smarty->assign($this->name . 'blog_p_tr', Configuration::get($this->name . 'blog_p_tr'));

                    $smarty->assign($this->name . 'rating_bllc', Configuration::get($this->name . 'rating_bllc'));

                }
                return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));

            }
        }

    }

    public function hookblogPostsSPM($params)
    {
        $posts_custom_hook = Configuration::get($this->name.'posts_custom_hook');

        if($posts_custom_hook == 1) {

            $id_category = isset($params['id_category']) ? $params['id_category'] : '';


            $is_mobile = $this->getIsMobile();

            if ((Configuration::get(($this->name . 'mposts_custom_hook')) == 1 && $is_mobile == 1)
                ||
                (Configuration::get(($this->name . 'sposts_custom_hook')) == 1 && $is_mobile == 0)
            ) {

                $name_template = "blogpostsspm.tpl";

                $cache_id = $this->name . '|' . $name_template . $id_category . $is_mobile;

                if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                    $smarty = $this->context->smarty;

                    $this->setMainSettings();

                    $this->_setPositionsBlocksSettings();

                    $smarty->assign($this->name . 'is16', $this->_is16);

                    include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');
                    $obj_blog = new blogspm();
                    $_data_post = $obj_blog->getRecentsPosts(array('id_category' => $id_category));


                    $smarty->assign(array(
                            $this->name . 'posts' => $_data_post['posts'],
                        )
                    );

                    $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());


                    $smarty->assign($this->name . 'postbl_views', Configuration::get($this->name . 'postbl_views'));
                    $smarty->assign($this->name . 'rating_bl', Configuration::get($this->name . 'rating_bl'));
                    $smarty->assign($this->name . 'block_display_date', Configuration::get($this->name . 'block_display_date'));
                    $smarty->assign($this->name . 'block_display_img', Configuration::get($this->name . 'block_display_img'));


                    $ps15 = 0;
                    if ($this->_is15) {
                        $ps15 = 1;
                    }
                    $smarty->assign($this->name . 'is_ps15', $ps15);

                    $smarty->assign($this->name . 'rsson', Configuration::get($this->name . 'rsson'));

                    if ($this->_is_friendly_url) {
                        $smarty->assign($this->name . 'iso_lng', $this->_iso_lng);
                    } else {
                        $smarty->assign($this->name . 'iso_lng', '');
                    }

                    $smarty->assign($this->name . 'pic', $this->path_img_cloud);

                    $this->setSEOUrls();

                    $smarty->assign($this->name . 'blog_com_tr', Configuration::get($this->name . 'blog_com_tr'));
                    $smarty->assign($this->name . 'blog_p_tr', Configuration::get($this->name . 'blog_p_tr'));


                }
                return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));
            }

        }
    }

    public function hookblogCategoriesSPM($params)
    {
        $cat_custom_hook = Configuration::get($this->name.'cat_custom_hook');

        if($cat_custom_hook == 1){

            $is_mobile = $this->getIsMobile();

            if((Configuration::get(($this->name.'mcat_custom_hook')) == 1 && $is_mobile == 1)
            ||
            (Configuration::get(($this->name.'scat_custom_hook')) == 1 && $is_mobile == 0)){

            $name_template = "blogcategoriesspm.tpl";

            $cache_id = $this->name.'|' . $name_template.$is_mobile;

            if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {

                $smarty = $this->context->smarty;

                $smarty->assign($this->name . 'is16', $this->_is16);

                include_once(_PS_MODULE_DIR_.$this->name . '/classes/blogspm.class.php');
                $obj_blog = new blogspm();
                $_data_cat = $obj_blog->getCategoriesBlock();

                $smarty->assign(array($this->name . 'categories' => $_data_cat['categories'],

                    )
                );

                $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());


                $smarty->assign($this->name . 'block_display_date', Configuration::get($this->name . 'block_display_date'));
                $smarty->assign($this->name . 'block_display_img', Configuration::get($this->name . 'block_display_img'));


                $ps15 = 0;
                if ($this->_is15) {
                    $ps15 = 1;
                }
                $smarty->assign($this->name . 'is_ps15', $ps15);

                $smarty->assign($this->name . 'rsson', Configuration::get($this->name . 'rsson'));

                if ($this->_is_friendly_url) {
                    $smarty->assign($this->name . 'iso_lng', $this->_iso_lng);
                } else {
                    $smarty->assign($this->name . 'iso_lng', '');
                }

                $smarty->assign($this->name . 'pic', $this->path_img_cloud);

                $this->setSEOUrls();

                $smarty->assign($this->name . 'blog_com_tr', Configuration::get($this->name . 'blog_com_tr'));
                $smarty->assign($this->name . 'blog_p_tr', Configuration::get($this->name . 'blog_p_tr'));

            }
            return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));

        }
        }

    }


    public function hookblogSearchSPM($params)
    {
        $search_custom_hook = Configuration::get($this->name.'search_custom_hook');

        if($search_custom_hook == 1) {

            $is_mobile = $this->getIsMobile();

            if ((Configuration::get(($this->name . 'msearch_custom_hook')) == 1 && $is_mobile == 1)
                ||
                (Configuration::get(($this->name . 'ssearch_custom_hook')) == 1 && $is_mobile == 0)
            ) {


                $name_template = "blogsearchspm.tpl";


                $cache_id = $this->name . '|' . $name_template.$is_mobile;

                if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                    $smarty = $this->context->smarty;

                    include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');
                    $obj_blog = new blogspm();

                    $_data_post = $obj_blog->getRecentsPosts();


                    $smarty->assign(array(
                            $this->name . 'posts' => $_data_post['posts'],

                        )
                    );


                    $smarty->assign($this->name . 'isdisablebl', $this->_disable_blocks_if_on_the_front_office_no_blog_messages);

                    $smarty->assign($this->name . 'is16', $this->_is16);

                    $ps15 = 0;
                    if ($this->_is15) {
                        $ps15 = 1;
                    }
                    $smarty->assign($this->name . 'is_ps15', $ps15);

                    $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());

                    $this->setSEOUrls();


                }
                return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));

            }
        }
    }

    public function hookblogArchivesSPM($params)
    {
        $arch_custom_hook = Configuration::get($this->name.'arch_custom_hook');

        if($arch_custom_hook == 1){

            $is_mobile = $this->getIsMobile();

            if ((Configuration::get(($this->name . 'march_custom_hook')) == 1 && $is_mobile == 1)
                ||
                (Configuration::get(($this->name . 'sarch_custom_hook')) == 1 && $is_mobile == 0)
            ) {

            $name_template = "blogarchivesspm.tpl";
            $cache_id = $this->name.'|' . $name_template.$is_mobile;

            if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {

                $smarty = $this->context->smarty;

                include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
                $obj_blog = new blogspm();

                $_data_arch = $obj_blog->getArchives();


                $smarty->assign(array(
                        $this->name.'arch' => $_data_arch['posts'],
                    )
                );


                $smarty->assign($this->name . 'isdisablebl', $this->_disable_blocks_if_on_the_front_office_no_blog_messages);

                $smarty->assign($this->name . 'is16', $this->_is16);

                $ps15 = 0;
                if ($this->_is15) {
                    $ps15 = 1;
                }
                $smarty->assign($this->name . 'is_ps15', $ps15);

                $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());

                $this->setSEOUrls();


            }
            return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));

        }
        }
    }

    public function hookblogTagsSPM($params)
    {

        $tags_custom_hook = Configuration::get($this->name.'tags_custom_hook');

        if($tags_custom_hook == 1) {

            $is_mobile = $this->getIsMobile();

            if ((Configuration::get(($this->name . 'mtags_custom_hook')) == 1 && $is_mobile == 1)
                ||
                (Configuration::get(($this->name . 'stags_custom_hook')) == 1 && $is_mobile == 0)
            ) {

                $name_template = "blogtagsspm.tpl";
                $cache_id = $this->name . '|' . $name_template.$is_mobile;

                if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                    $smarty = $this->context->smarty;

                    include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');
                    $obj_blog = new blogspm();

                    $_data_tags = $obj_blog->getTags(array('limit' => (int)Configuration::get($this->name . 'blog_tags')));


                    $smarty->assign(array(
                            $this->name . 'tags' => $_data_tags,
                        )
                    );


                    $smarty->assign($this->name . 'isdisablebl', $this->_disable_blocks_if_on_the_front_office_no_blog_messages);

                    $smarty->assign($this->name . 'is16', $this->_is16);

                    $ps15 = 0;
                    if ($this->_is15) {
                        $ps15 = 1;
                    }
                    $smarty->assign($this->name . 'is_ps15', $ps15);

                    $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());

                    $this->setSEOUrls();


                }
                return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));

            }
        }

    }

    public function hookblogTopAuthorsSPM($params)
    {

        $auth_custom_hook = Configuration::get($this->name.'auth_custom_hook');

        if($auth_custom_hook == 1) {

            $is_mobile = $this->getIsMobile();

            if ((Configuration::get(($this->name . 'mauth_custom_hook')) == 1 && $is_mobile == 1)
                ||
                (Configuration::get(($this->name . 'sauth_custom_hook')) == 1 && $is_mobile == 0)
            ) {


                $name_template = "blogtopauthors.tpl";
                $cache_id = $this->name . '|' . $name_template . $is_mobile;

                if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                    $smarty = $this->context->smarty;

                    include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');
                    $obj_blog = new blogspm();

                    include_once(_PS_MODULE_DIR_ . $this->name . '/classes/userprofileblockblog.class.php');
                    $obj_userprofileblockblog = new userprofileblockblog();

                    $info_customers = $obj_userprofileblockblog->getShoppersBlock(
                        array(
                            'start' => 0,
                            'step' => (int)Configuration::get($this->name . 'blog_authors')
                        )
                    );

                    $smarty->assign(array(
                        $this->name . 'customers_block' => $info_customers['customers']
                    ));


                    $smarty->assign($this->name . 'isdisablebl', $this->_disable_blocks_if_on_the_front_office_no_blog_messages);

                    $smarty->assign($this->name . 'is16', $this->_is16);

                    $ps15 = 0;
                    if ($this->_is15) {
                        $ps15 = 1;
                    }
                    $smarty->assign($this->name . 'is_ps15', $ps15);

                    $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());

                    $this->setSEOUrls();


                }
                return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));

            }
        }

    }

    public function hookblogGallerySPM($params)
    {
        $gallery_custom_hook = Configuration::get($this->name.'gallery_custom_hook');

        if($gallery_custom_hook == 1) {

            $is_mobile = $this->getIsMobile();

            if ((Configuration::get(($this->name . 'mgallery_custom_hook')) == 1 && $is_mobile == 1)
                ||
                (Configuration::get(($this->name . 'sgallery_custom_hook')) == 1 && $is_mobile == 0)
            ) {
                $name_template = "bloggallery.tpl";
                $cache_id = $this->name . '|' . $name_template . $is_mobile;

                if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                    $smarty = $this->context->smarty;

                    include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');
                    $obj_blog = new blogspm();

                    $_data_gallery = $obj_blog->getGallery(array('is_featured' => 1, 'is_home' => 1));


                    $smarty->assign(array(
                            $this->name . 'galleryblockhook' => $_data_gallery['gallery'],
                        )
                    );


                    $smarty->assign($this->name . 'isdisablebl', $this->_disable_blocks_if_on_the_front_office_no_blog_messages);

                    $smarty->assign($this->name . 'is16', $this->_is16);

                    $ps15 = 0;
                    if ($this->_is15) {
                        $ps15 = 1;
                    }
                    $smarty->assign($this->name . 'is_ps15', $ps15);

                    $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());

                    $this->setSEOUrls();


                }
                return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));

            }
        }
    }

    public function hookdisplayCustomerAccount($params)
    {
        $cookie = $this->context->cookie;
        $is_logged = isset($cookie->id_customer)?$cookie->id_customer:0;

        if($is_logged) {
            $name_template = "my-account.tpl";
            $cache_id = $this->name.'|' . $name_template;

            if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                $smarty = $this->context->smarty;

                $smarty->assign($this->name . 'islogged', $is_logged);

                $smarty->assign($this->name . 'is16', $this->_is16);

                $smarty->assign($this->name . 'is_ps15', $this->_is15);

                $smarty->assign($this->name.'ava_on', Configuration::get($this->name.'ava_on'));
                $smarty->assign($this->name.'myblogposts_on', Configuration::get($this->name.'myblogposts_on'));
                $smarty->assign($this->name.'myblogcom_on', Configuration::get($this->name.'myblogcom_on'));


                $this->setSEOUrls();

                $this->setLoyalitySettings();


            }
            return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));
        }
    }

    public function hookdisplayMyAccountBlock($params)
    {
        $cookie = $this->context->cookie;
        $is_logged = isset($cookie->id_customer) ? $cookie->id_customer : 0;

        if($is_logged) {
            $name_template = "my-account-block.tpl";
            $cache_id = $this->name.'|' . $name_template;

            if (!$this->isCached($this->_template_name . $name_template, $this->getCacheId($cache_id))) {

                $smarty = $this->context->smarty;

                $smarty->assign($this->name . 'is16', $this->_is16);

                $smarty->assign($this->name . 'is_ps15', $this->_is15);


                $smarty->assign($this->name . 'islogged', $is_logged);

                $smarty->assign($this->name.'ava_on', Configuration::get($this->name.'ava_on'));
                $smarty->assign($this->name.'myblogposts_on', Configuration::get($this->name.'myblogposts_on'));
                $smarty->assign($this->name.'myblogcom_on', Configuration::get($this->name.'myblogcom_on'));


                $this->setSEOUrls();

                $this->setLoyalitySettings();
            }
            return $this->display(__FILE__, $this->_template_path . $name_template, $this->getCacheId($cache_id));
        }
    }

    public function hookActionCustomerAccountAdd($params){

        $new_customer = $params['newCustomer'];
        $id_customer = $new_customer->id;

        // loyality program //
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->saveLoyalityAction(array('id_loyalty_status'=>1,'id_customer'=>$id_customer,'type'=>'loyality_registration'));

        $ava_on = Configuration::get($this->name.'ava_on');
        if($ava_on)
            $loyality->saveLoyalityAction(array('id_loyalty_status' => 1, 'id_customer' => $id_customer, 'type' => 'loyality_user_my_show'));
        // loyality program //

    }

    public function hookdisplayCustomerAccountForm($params){

        $cookie = $this->context->cookie;
        $smarty = $this->context->smarty;
        $is_logged = isset($cookie->id_customer) ? 1 : 0;

        $name_template = "displayCustomerAccountForm.tpl";
        $cache_id = $this->name.'|'.$name_template.$is_logged;

        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {


            $smarty->assign($this->name . 'islogged', $is_logged);
            // loyality program
            $this->setLoyalitySettings();
            // loyality program
        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));
    }

    public function hookActionOrderStatusUpdate($params){

        $id_order = (int)$params['id_order'];
        $order = new Order($id_order);

        $id_customer = $order->id_customer;

        $new_order = $params['newOrderStatus'];
        $new_order_status_id = $new_order->id;

        // loyality program //
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();



        $id_loyalty_status = 0;
        $orderstatuses = Configuration::get($this->name.'lorderstatuses');
        $orderstatuses = explode(",",$orderstatuses);


        if(in_array($new_order_status_id,$orderstatuses)) {
            $id_loyalty_status = 1;
        }

        //var_dump($new_order_status_id);var_dump($orderstatuses);var_dump($id_loyalty_status);exit;

        $loyality->setLoyalityStatus(array('id_loyalty_status'=>$id_loyalty_status,'id_customer'=>$id_customer,'id_order'=>$id_order,'type'=>'loyality_order'));
        // loyality program //

    }


    public function hookactionBeforeAddOrder($params){
        return (
        $this->hookdisplayOrderConfirmation($params)
        );
    }

    public function hookdisplayPaymentReturn($params){
        return (
        $this->hookdisplayOrderConfirmation($params)
        );
    }

    public function hookNewOrder($params)
    {
        return (
        $this->hookdisplayOrderConfirmation($params)
        );
    }

    public function hookActionValidateOrder($params)
    {
        return (
        $this->hookdisplayOrderConfirmation($params)
        );
    }

    public function hookdisplayOrderConfirmation($params){

        $cookie = $this->context->cookie;

        if (version_compare(_PS_VERSION_, '1.7', '>')) {

            $id_lang = $cookie->id_lang;
            $id_order = Tools::getValue('id_order');
            $params_obj_order = new Order($id_order, $id_lang);

            if (empty($params_obj_order) && !is_object($params_obj_order)) {
                $params_obj_order = isset($params['order']) ? $params['order'] : null;
            }
            //$params_obj_order = isset($params['order'])?$params['order']:null;

        } else {

            $id_lang = $cookie->id_lang;
            $id_order = Tools::getValue('id_order');
            $params_obj_order = new Order($id_order, $id_lang);

            if (empty($params_obj_order) && !is_object($params_obj_order)) {
                $params_obj_order = isset($params['objOrder']) ? $params['objOrder'] : null;
            }
            //$params_obj_order = isset($params['objOrder'])?$params['objOrder']:null;

        }

        // loyality program //
        if (!empty($params_obj_order) && is_object($params_obj_order)) {
            $new_order_status_id = $params_obj_order->getCurrentState();
        } else {
            $new_order_status_id = 0;
        }

        $id_loyalty_status = 0;
        $orderstatuses = Configuration::get($this->name.'lorderstatuses');
        $orderstatuses = explode(",",$orderstatuses);


        if(in_array($new_order_status_id,$orderstatuses)) {
            $id_loyalty_status = 1;
        }

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->saveLoyalityAction(array('id_loyalty_status'=>$id_loyalty_status,'current_state'=>$params_obj_order->current_state,'id_customer'=>$params_obj_order->id_customer,'id_order'=>$params_obj_order->id,'type'=>'loyality_order'));
        // loyality program //
    }

    public function setMainSettings(){
        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;
        $id_lang =  $cookie->id_lang;

        $smarty->assign($this->name.'text_readmore', Configuration::get($this->name.'text_readmore_'.$id_lang));
        $smarty->assign($this->name.'bgcolor_main', Configuration::get($this->name.'bgcolor_main'));
        $smarty->assign($this->name.'bgcolor_hover', Configuration::get($this->name.'bgcolor_hover'));
        $smarty->assign($this->name.'blog_date', Configuration::get($this->name.'blog_date'));
        //$smarty->assign($this->name.'blog_layout_type', Configuration::get($this->name.'blog_layout_type'));
    }


    public function hookdisplayProductButtons($params)
    {

        return $this->hookproductActions($params);

    }

    public function hookproductActions($params){


        $id_product = (int)Tools::getValue('id_product');
        $name_template = "productactions.tpl";
        $cache_id = $this->name.'|'.$name_template.$id_product;

        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {


            // loyality program
            $this->setLoyalitySettings();
            // loyality program
        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));
    }

    private function _setBlocksinTheHooksSettings(){
        //$smarty = $this->context->smarty;


        /*$smarty->assign($this->name.'cat_left', Configuration::get($this->name.'cat_left'));
        $smarty->assign($this->name.'posts_left', Configuration::get($this->name.'posts_left'));
        $smarty->assign($this->name.'arch_left', Configuration::get($this->name.'arch_left'));
        $smarty->assign($this->name.'search_left', Configuration::get($this->name.'search_left'));
        $smarty->assign($this->name.'com_left', Configuration::get($this->name.'com_left'));
        $smarty->assign($this->name.'tag_left', Configuration::get($this->name.'tag_left'));
        $smarty->assign($this->name.'author_left', Configuration::get($this->name.'author_left'));
        $smarty->assign($this->name.'gallery_left', Configuration::get($this->name.'gallery_left'));



        $smarty->assign($this->name . 'cat_right', Configuration::get($this->name . 'cat_right'));
        $smarty->assign($this->name . 'posts_right', Configuration::get($this->name . 'posts_right'));
        $smarty->assign($this->name . 'arch_right', Configuration::get($this->name . 'arch_right'));
        $smarty->assign($this->name . 'search_right', Configuration::get($this->name . 'search_right'));
        $smarty->assign($this->name . 'com_right', Configuration::get($this->name . 'com_right'));
        $smarty->assign($this->name . 'tag_right', Configuration::get($this->name . 'tag_right'));
        $smarty->assign($this->name . 'author_right', Configuration::get($this->name . 'author_right'));
        $smarty->assign($this->name . 'gallery_right', Configuration::get($this->name . 'gallery_right'));



        $smarty->assign($this->name . 'cat_footer', Configuration::get($this->name . 'cat_footer'));
        $smarty->assign($this->name . 'posts_footer', Configuration::get($this->name . 'posts_footer'));
        $smarty->assign($this->name . 'arch_footer', Configuration::get($this->name . 'arch_footer'));
        $smarty->assign($this->name . 'search_footer', Configuration::get($this->name . 'search_footer'));
        $smarty->assign($this->name . 'com_footer', Configuration::get($this->name . 'com_footer'));
        $smarty->assign($this->name . 'tag_footer', Configuration::get($this->name . 'tag_footer'));
        $smarty->assign($this->name . 'author_footer', Configuration::get($this->name . 'author_footer'));
        $smarty->assign($this->name . 'gallery_footer', Configuration::get($this->name . 'gallery_footer'));*/

        $this->_setPositionsBlocksSettings();




    }

    private function _setPositionsBlocksSettings(){
        $smarty = $this->context->smarty;

        $data_connects_array_prefix = $this->getBlocksArrayPrefix();


        include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();
        $_data_cat = $obj_blog->getCategoriesBlock();
        $_data_post = $obj_blog->getRecentsPosts();
        $_data_arch = $obj_blog->getArchives();
        $_data_com = $obj_blog->getLastComments();
        $_data_tags = $obj_blog->getTags(array('limit'=>(int)Configuration::get($this->name.'blog_tags')));
        $_data_gallery = $obj_blog->getGallery(array('is_featured'=>1, 'is_left_right_footer'=>1));

        $smarty->assign(array(
                $this->name.'categories' => $_data_cat['categories'],
                $this->name.'posts' => $_data_post['posts'],
                $this->name.'arch' => $_data_arch['posts'],
                $this->name.'comments' => $_data_com['comments'],
                $this->name.'tags' => $_data_tags,
                $this->name.'galleryblock' => $_data_gallery['gallery'],
                $this->name.'is_mobile' => $this->getIsMobile(),
                $this->name.'isdisablebl' => $this->_disable_blocks_if_on_the_front_office_no_blog_messages,
            )
        );

        $this->setMainSettings();

        $smarty->assign($this->name.'is16', $this->_is16);

        $ps15 = 0;
        if($this->_is15){
            $ps15 = 1;
        }
        $smarty->assign($this->name.'is_ps15', $ps15);

        $smarty->assign($this->name.'rsson', Configuration::get($this->name.'rsson'));

        if($this->_is_friendly_url){
            $smarty->assign($this->name.'iso_lng', $this->_iso_lng);
        } else {
            $smarty->assign($this->name.'iso_lng', '');
        }

        $smarty->assign($this->name.'pic', $this->path_img_cloud);

        $this->setSEOUrls();

        $smarty->assign($this->name.'blog_com_tr', Configuration::get($this->name.'blog_com_tr'));
        $smarty->assign($this->name.'blog_p_tr', Configuration::get($this->name.'blog_p_tr'));

        $smarty->assign($this->name.'bposts_slider', Configuration::get($this->name.'bposts_slider'));
        $smarty->assign($this->name.'bposts_sl', Configuration::get($this->name.'bposts_sl'));
        $smarty->assign($this->name.'blog_tr_b', Configuration::get($this->name.'blog_tr_b'));

        $smarty->assign($this->name.'bcat_slider', Configuration::get($this->name.'bcat_slider'));
        $smarty->assign($this->name.'bcat_sl', Configuration::get($this->name.'bcat_sl'));

        $smarty->assign($this->name.'bcom_slider', Configuration::get($this->name.'bcom_slider'));
        $smarty->assign($this->name.'bcom_sl', Configuration::get($this->name.'bcom_sl'));

        $smarty->assign($this->name.'rating_bllc', Configuration::get($this->name.'rating_bllc'));

        $this->setGallerySettings();

        $smarty->assign($this->name.'gallery_on', Configuration::get($this->name.'gallery_on'));


        /* home */

        /* gallery home */

        if(Configuration::get($this->name.'gallery_on') == 1) {
            $_data_gallery_home = $obj_blog->getGallery(array('is_featured'=>1, 'is_home'=>1));
            $smarty->assign(array(
                    $this->name.'galleryblockhome' => $_data_gallery_home['gallery'],
                )
            );


            $smarty->assign($this->name . 'gallery_home', Configuration::get($this->name . 'gallery_home'));
        }
        /* gallery home */


        /* avatars home */
        $smarty->assign($this->name.'ava_on', Configuration::get($this->name.'ava_on'));

        if(Configuration::get($this->name.'ava_on') == 1) {

            include_once(_PS_MODULE_DIR_ . $this->name . '/classes/userprofileblockblog.class.php');
            $obj_userprofileblockblog = new userprofileblockblog();

            $info_customers = $obj_userprofileblockblog->getShoppersBlock(
                array(
                    'start' => 0,
                    'step' => (int)Configuration::get($this->name . 'blog_authors')
                )
            );

            $smarty->assign(array(
                $this->name . 'customers_block' => $info_customers['customers']
            ));

            $smarty->assign($this->name . 'sr_slideru', (int)Configuration::get($this->name . 'sr_slideru'));
            $smarty->assign($this->name . 'sr_slu', (int)Configuration::get($this->name . 'sr_slu'));


            if(Configuration::get($this->name . 'authors_home') == 1) {
                // home page
                $info_customers = $obj_userprofileblockblog->getShoppersBlock(
                    array(
                        'start' => 0,
                        'step' => (int)Configuration::get($this->name . 'blog_authorsh')
                    )
                );

                $smarty->assign(array(
                    $this->name . 'customers_blockh' => $info_customers['customers']
                ));


                $smarty->assign($this->name . 'sr_sliderhu', (int)Configuration::get($this->name . 'sr_sliderhu'));
                $smarty->assign($this->name . 'sr_slhu', (int)Configuration::get($this->name . 'sr_slhu'));
            }
            $smarty->assign($this->name . 'authors_home', Configuration::get($this->name . 'authors_home'));
            // home page
        }
        /* avatars home */


        /* blog posts recents home */
        $_data_post = $obj_blog->getRecentsPosts(array('is_home' => 1));


        foreach ($_data_post['posts'] as $k => $val) {
            foreach ($val['data'] as $_k => $_item) {
                $_data_post['posts'][$k]['data'][$_k]['content'] = strip_tags(html_entity_decode($_item['content']));
            }
        }

        $smarty->assign(
            array($this->name . 'postsh' => $_data_post['posts'])
        );


        $smarty->assign($this->name . 'block_last_home', Configuration::get($this->name . 'block_last_home'));


        $smarty->assign($this->name . 'postblh_views', Configuration::get($this->name . 'postblh_views'));
        $smarty->assign($this->name . 'rating_blh', Configuration::get($this->name . 'rating_blh'));
        $smarty->assign($this->name . 'blog_bp_sl', Configuration::get($this->name . 'blog_bp_sl'));

        $blog_h = (int)Configuration::get($this->name . 'blog_h');
        if ($blog_h == 0)
            $blog_h = 1;

        $smarty->assign($this->name . 'blog_h', $blog_h);
        /* blog posts recents home */




        /* home */


        $smarty->assign($this->name.'urlrewrite_on', $obj_blog->isURLRewriting());

        $smarty->assign($this->name.'postbl_views', Configuration::get($this->name.'postbl_views'));
        $smarty->assign($this->name.'rating_bl', Configuration::get($this->name.'rating_bl'));
        $smarty->assign($this->name.'block_display_date', Configuration::get($this->name.'block_display_date'));
        $smarty->assign($this->name.'block_display_img', Configuration::get($this->name.'block_display_img'));


        $this->setLoyalitySettings();

        ### handle positions ##
         foreach ($this->getAllAvailablePositionsBlog() as $alias_position => $item_position) {


            //foreach($data_connects_array_prefix as $prefix_short => $_data_item) {

                //$smarty->assign($this->name . '_' . $alias_position . $prefix_short, Configuration::get($this->name . '_' . $alias_position . $prefix_short));

                //$smarty->assign($this->name . '_' . $alias_position . $prefix_short . "mobile", Configuration::get($this->name . '_' . $alias_position . $prefix_short . "mobile"));

                //mobile positions
                //$smarty->assign($this->name . $alias_position . "mobile", Configuration::get($this->name . $alias_position . "mobile"));
                //desktop positions
                //$smarty->assign($this->name . $alias_position, Configuration::get($this->name . $alias_position));


            //}

             $smarty->assign($this->name.'allblocks'.$alias_position, $data_connects_array_prefix);
             $smarty->assign($this->name.'alias', $alias_position);



             $data_positions_connects_forcurrent_position = Configuration::get($this->name . '_orderby' . $alias_position);
             $data_positions_connects_forcurrent_position = json_decode($data_positions_connects_forcurrent_position,true);

             if(empty($data_positions_connects_forcurrent_position)){
                 $data_positions_connects_forcurrent_position = array();
             }


             if(
                 (Configuration::get(($this->name.$alias_position.'mobile')) == 1 && $this->getIsMobile() == 1)
                 ||
                 (Configuration::get(($this->name.$alias_position)) == 1 && $this->getIsMobile() == 0)
             ) {
                 $blocks = '';

                 foreach($data_positions_connects_forcurrent_position as $name_item => $order){
                     $pos_name = explode("_",$name_item);
                     $name = end($pos_name);

                     if((Configuration::get($this->name . '_' . $alias_position . $name) && $this->getIsMobile() == 0)
                         ||
                        (Configuration::get($this->name . '_' . $alias_position . $name . "mobile") && $this->getIsMobile() == 1)
                     ) {



                         $blocks_tpl = $this->display(__FILE__, 'views/templates/hooks/blocks/block_' . $name .($alias_position == "home"?'_home':''). '.tpl');
                         //$blocks_tpl = $this->display(__FILE__, 'views/templates/hooks/blocks/block_' . $name . '.tpl');
                         $blocks_tpl = str_replace("\n", "", $blocks_tpl);


                         $blocks .= $blocks_tpl;
                     }
                 }
             } else {
                 $blocks = '';
             }

             $smarty->assign($this->name . $alias_position.'blocks', $blocks);


        }
        ### handle positions ##





    }


    public function hookdisplayFooter($params)
    {

        $is_mobile = $this->getIsMobile();
        $name_template = "footer.tpl";


        $cache_id = $this->name.'|' . $name_template.$is_mobile;

        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {

            $this->_setBlocksinTheHooksSettings();

        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));


    }

	public function hookdisplayLeftColumn($params)
	{


        $is_mobile = $this->getIsMobile();
        $name_template = "left.tpl";
        $cache_id = $this->name.'|' . $name_template.$is_mobile;

        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {

            $this->_setBlocksinTheHooksSettings();

        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));

		
	}
	
	public function hookdisplayRightColumn($params)
	{

        $is_mobile = $this->getIsMobile();
        $name_template = "right.tpl";
        $cache_id = $this->name.'|' . $name_template.$is_mobile;

        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {


            $this->_setBlocksinTheHooksSettings();


        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));

		
	}
	
	
	public function hookdisplayHome($params)
	{


        $is_mobile = $this->getIsMobile();
        $name_template = "home.tpl";
        $cache_id = $this->name.'|' . $name_template.$is_mobile;

        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {

            $this->_setBlocksinTheHooksSettings();


            ## slider ##
            $smarty = $this->context->smarty;
            if(Configuration::get($this->name . 'slider_h_on')) {
                $this->setSliderSettings();
            }
            $smarty->assign($this->name . 'slider_h_on', Configuration::get($this->name . 'slider_h_on'));

            ## slider ##


        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));
		

	
	}
	
	public function hookdisplayHeader($params){


        include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');

        $obj = new blogspm();

        $this->setSEOUrls();

        // CSS and JS - NEED NO CACHED!!!//

            $this->context->controller->addCSS(($this->_path) . 'views/css/blog.css', 'all');
            $this->context->controller->addCSS(($this->_path) . 'views/css/font-custom.min.css', 'all');

            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $this->context->controller->addJS($this->_path . 'views/js/jquery.scrollTo.js');
            }

            $this->context->controller->addJS($this->_path . 'views/js/blog.js');


        if(Configuration::get($this->name.'ava_on')){
            $this->context->controller->addCSS(($this->_path) . 'views/css/users.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/users.js');
        }


        $this->context->controller->addCSS(($this->_path) . 'views/css/users.css', 'all');

        if (version_compare(_PS_VERSION_, '1.7', '>'))
            $this->context->controller->addCSS(($this->_path) . 'views/css/blog17.css', 'all');



        if ($obj->isURLRewriting()) {
            $item_id = Tools::getValue('p');
            $is_category = Tools::substr($item_id,0,2);

            if($is_category == 'c-') {
                $item_id = null;
            } else {
                $item_id = Tools::substr($item_id, 2);
            }
            //$item_id = str_replace("p-", "", $item_id);

        } else {
            $item_id = Tools::getValue('post_id');
        }



        ### owl carousel ###
        $blog_h = Configuration::get($this->name . 'blog_h');
        $bposts_slider = Configuration::get($this->name . 'bposts_slider');
        $bcat_slider = Configuration::get($this->name . 'bcat_slider');
        $bcom_slider = Configuration::get($this->name . 'bcom_slider');


        if ($blog_h == 3 || $bposts_slider == 1 || $bcat_slider == 1 || $bcom_slider == 1) {

            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $this->name . '/views/js/owl.carousel.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $this->name . '/views/css/owl.carousel.css');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $this->name . '/views/css/owl.theme.default.css');
        }
        ### owl carousel ###





        if ($item_id) {
            // add rating functional only for blog post page//
            $this->context->controller->addJS($this->_path . 'views/js/bootstrap-rating-input.js');
            // add rating functional only for blog post page//

        }
        // CSS and JS - NEED NO CACHED!!!//


        ## slider ##

        ## home page ##
        ## prestashop 1.7 ##
        $smarty = $this->context->smarty;
        if(version_compare(_PS_VERSION_, '1.7', '>')){
            $page_name = isset($smarty->tpl_vars['page']->value['page_name'])?$smarty->tpl_vars['page']->value['page_name']:'';
            ## prestashop 1.7 ##
        } else {
            if (defined('_MYSQL_ENGINE_')) {
                $page_name = isset($smarty->tpl_vars['page_name']->value)?$smarty->tpl_vars['page_name']->value:'';
            } else {
                $page_name = isset($smarty->_tpl_vars['page_name'])?$smarty->_tpl_vars['page_name']:'';
            }
        }
        $is_home = 0;
        if ($page_name == 'index') {
            $is_home = 1;
        }
        ## home page ##


        // home page //
        if((Configuration::get($this->name . 'slider_h_on') && $is_home) || Configuration::get($this->name . 'slider_b_on')){
            $count_sliders = $obj->getCountSliders();
            if($count_sliders)
                $this->context->controller->addJS($this->_path . 'views/js/fancytr/jqFancyTransitions.js');
        }


        ## slider ##


        // gallery jquery.prettyPhoto.js //
        if(Configuration::get($this->name . 'gallery_on')){
            $this->context->controller->addJS($this->_path . 'views/js/jquery.prettyPhoto.js');
            $this->context->controller->addJS($this->_path . 'views/js/prettyPhoto.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $this->name . '/views/css/prettyPhoto.css');
        }
        // gallery jquery.prettyPhoto.js //



        $name_template = "head.tpl";

        $cache_id = $this->name.'|' . $name_template;



        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {



            $cookie = $this->context->cookie;

            $smarty->assign($this->name . 'rsson', Configuration::get($this->name . 'rsson'));
            $smarty->assign($this->name . 'is15', $this->_is15);


            $is_ps14 = 0;
            $smarty->assign($this->name . 'is_ps14', $is_ps14);





            $is_wow = 0;
            if (Configuration::get($this->name . 'blog_cat_effect') != "disable_all_effects" ||
                Configuration::get($this->name . 'blog_post_effect') != "disable_all_effects" ||
                Configuration::get($this->name . 'blog_com_effect') != "disable_all_effects" ||
                Configuration::get($this->name . 'blog_comp_effect') != "disable_all_effects"
            ) {
                $is_wow = 1;

            }
            $smarty->assign($this->name . 'is_wow', $is_wow);


            $blog_h = (int)Configuration::get($this->name . 'blog_h');
            if ($blog_h == 0)
                $blog_h = 1;

            $smarty->assign($this->name . 'blog_h', $blog_h);


            $smarty->assign($this->name . 'is_cloud', $this->_is_cloud);





            $is_blog_page = 0;
            if ($item_id) {
                $is_blog_page = 1;


                $item_id = $obj->getTransformSEOURLtoIDPost(array('id' => $item_id));

                $_info_cat = $obj->getPostItem(array('id' => $item_id, 'site' => 1));


                $name = isset($_info_cat['post'][0]['title']) ? $_info_cat['post'][0]['title'] : '';
                $seo_description = isset($_info_cat['post'][0]['seo_description']) ? $_info_cat['post'][0]['seo_description'] : '';
                $img = isset($_info_cat['post'][0]['img_orig']) ? $_info_cat['post'][0]['img_orig'] : '';
                $seo_url = isset($_info_cat['post'][0]['seo_url']) ? $_info_cat['post'][0]['seo_url'] : '';

                $smarty->assign($this->name . 'appid', Configuration::get($this->name . 'appid'));
                $smarty->assign($this->name . 'appadmin', Configuration::get($this->name . 'appadmin'));

                // google captcha //
                $smarty->assign($this->name.'is_captcha_com', Configuration::get($this->name.'is_captcha_com'));
                $smarty->assign($this->name.'bcaptcha_type', Configuration::get($this->name.'bcaptcha_type'));
                $smarty->assign($this->name.'bcaptcha_site_key', Configuration::get($this->name.'bcaptcha_site_key'));
                $smarty->assign($this->name.'bcaptcha_secret_key', Configuration::get($this->name.'bcaptcha_secret_key'));


                include_once(_PS_MODULE_DIR_.$this->name . '/classes/blogspm.class.php');
                $obj_blog = new blogspm();
                $data_fb = $obj_blog->getfacebooklib($cookie->id_lang);
                $lang_iso = $data_fb['lng_iso'];
                $lang_data = explode("_",$lang_iso);
                $lng_custom = current($lang_data);
                $smarty->assign($this->name . 'lang_captcha', $lng_custom);

                // google captcha //

                $smarty->assign($this->name . 'name', $name);
                $smarty->assign($this->name . 'seod', $seo_description);
                $smarty->assign($this->name . 'img', $img);
                $smarty->assign($this->name . 'postseo_url', $seo_url);
                $smarty->assign($this->name . 'postid', $item_id);
                $smarty->assign($this->name . 'urlrewrite_on', $obj->isURLRewriting());
            }

            $smarty->assign($this->name . 'is_blog', $is_blog_page);


            if(Configuration::get($this->name . 'slider_h_on') || Configuration::get($this->name . 'slider_b_on')) {
                $this->setSliderSettings(array('is_home'=>$is_home));
            }
            $smarty->assign($this->name . 'slider_b_on', Configuration::get($this->name . 'slider_b_on'));
            $smarty->assign($this->name . 'slider_h_on', Configuration::get($this->name . 'slider_h_on'));


            // gallery settings //
            $this->setGallerySettings();
            // gallery settings //


            $this->setMainSettings();


        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));



    
    }

    public function setGallerySettings(){
        $smarty = $this->context->smarty;
        $smarty->assign($this->name . 'slider_effect', Configuration::get($this->name . 'slider_effect'));
        $smarty->assign($this->name . 'gallery_autoplay', Configuration::get($this->name . 'gallery_autoplay'));
        $smarty->assign($this->name . 'gallery_speed', Configuration::get($this->name . 'gallery_speed'));
        $smarty->assign($this->name . 'gallery_on', Configuration::get($this->name . 'gallery_on'));

        $smarty->assign($this->name . 'gallery_height', Configuration::get($this->name . 'gallery_height'));
        $smarty->assign($this->name . 'gallery_w_h', Configuration::get($this->name . 'gallery_w_h'));

    }

    public function setSliderSettings($data = null){
        $smarty = $this->context->smarty;

        include_once(_PS_MODULE_DIR_ . $this->name . '/classes/blogspm.class.php');

        $obj = new blogspm();


        $is_mobile = 0;

        if(version_compare(_PS_VERSION_, '1.5', '>') && version_compare(_PS_VERSION_, '1.7', '<')){

            require_once(_PS_TOOL_DIR_.'mobile_Detect/Mobile_Detect.php');
            $mobile_detect = new Mobile_Detect();

            if ($mobile_detect->isMobile()){
                $is_mobile = 1;
            }

        }

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $mobile_detect = new \Mobile_Detect();
            if ($mobile_detect->isMobile()) {
                $is_mobile = 1;
            }
        }

        // test
        //$is_mobile = 1;
        // test


        $slides = $obj->getSliderForPosts(array('is_mobile'=>$is_mobile));



        $smarty->assign($this->name . 'slides',$slides['slides']);

        $smarty->assign($this->name . 'slider_type', Configuration::get($this->name . 'slider_type'));
        $smarty->assign($this->name . 'slider_h_on', Configuration::get($this->name . 'slider_h_on'));
        $smarty->assign($this->name . 'slider_b_on', Configuration::get($this->name . 'slider_b_on'));

        if($is_mobile){
            $slider_widthfancytr  = $this->_img_slider_width_mobile;
            $slider_heightfancytr = $this->_img_slider_height_mobile;
            $slider_width = $this->_img_slider_width_mobile;
            $slider_height = $this->_img_slider_height_mobile;

        } else {
            $slider_widthfancytr  = Configuration::get($this->name . 'slider_widthfancytr');
            $slider_heightfancytr = Configuration::get($this->name . 'slider_heightfancytr');
            $slider_width = Configuration::get($this->name . 'slider_widthfancytr');
            $slider_height = Configuration::get($this->name . 'slider_heightfancytr');

        }
        $smarty->assign($this->name . 'slider_widthfancytr', $slider_widthfancytr);
        $smarty->assign($this->name . 'slider_heightfancytr', $slider_heightfancytr);
        $smarty->assign($this->name . 'slider_titlefancytr', Configuration::get($this->name . 'slider_titlefancytr'));





        $slider_type = Configuration::get($this->name . 'slider_type');
        $slider_position = isset($data['is_home'])?$data['is_home']:0;

        $slider_pn_on = Configuration::get($this->name . 'slider_pn_on');

        $name_module = $this->name;


        ob_start();
        include(dirname(__FILE__) . '/views/templates/hooks/'.$this->name.'/scripts.phtml');
        $scripts = ob_get_clean();

        $smarty->assign($this->name.'_scripts', $scripts);


    }




    public function hookdisplayFooterProduct($params){
        $btabs_type = Configuration::get($this->name . 'btabs_type');
        if(version_compare(_PS_VERSION_, '1.7', '>') && $btabs_type != 3) {
            return $this->hookdisplayProductTabContent($params);
        }
    }

    public function hookdisplayProductFooter($params){
        $btabs_type = Configuration::get($this->name . 'btabs_type');
        if(version_compare(_PS_VERSION_, '1.7', '>') && $btabs_type != 3) {
            return $this->hookdisplayProductTabContent($params);
        }
    }

     public function hookdisplayProductTabContent($params)
	{

        $id_product = (int)Tools::getValue('id_product');

        $name_template = "TabContent.tpl";
        //$cache_id = $this->name.$name_template.$id_product;

        $cache_id = $this->name.'|' . $id_product;




        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {

            $smarty = $this->context->smarty;

            $this->setLoyalitySettings();

            $smarty->assign($this->name . 'is16', $this->_is16);


            $this->setMainSettings();

            include_once(_PS_MODULE_DIR_.$this->name . '/classes/blogspm.class.php');
            $obj_blog = new blogspm();

            $data_posts = $obj_blog->getRelatedPostsForProduct(array('id_product'=>$id_product));
            $smarty->assign(
                array($this->name . 'related_posts' => $data_posts['related_posts']
                )
            );


            $smarty->assign($this->name . 'urlrewrite_on', $obj_blog->isURLRewriting());

            $smarty->assign($this->name . 'tab_blog_pr', Configuration::get($this->name . 'tab_blog_pr'));
            $smarty->assign($this->name . 'btabs_type', Configuration::get($this->name . 'btabs_type'));

            $smarty->assign($this->name . 'postrel_viewsp', Configuration::get($this->name . 'postrel_viewsp'));
            $smarty->assign($this->name . 'rating_postrpp', Configuration::get($this->name . 'rating_postrpp'));

            $smarty->assign($this->name . 'relp_sliderp', Configuration::get($this->name . 'relp_sliderp'));
            $smarty->assign($this->name . 'np_sliderp', Configuration::get($this->name . 'np_sliderp'));

            $smarty->assign($this->name.'pic', $this->getCloudImgPath());

            $this->setSEOUrls();


        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));
		

	
	}	
	
	public function hookdisplayProductTab($params)
	{
        $name_template = "tab.tpl";
        $id_product = (int)Tools::getValue('id_product');


        $cache_id = $this->name.'|' . $id_product;


        if (!$this->isCached($this->_template_name.$name_template, $this->getCacheId($cache_id))) {

            $smarty = $this->context->smarty;

            $smarty->assign($this->name . 'is16', $this->_is16);


            include_once(_PS_MODULE_DIR_.$this->name . '/classes/blogspm.class.php');
            $obj_blog = new blogspm();
            $data_posts = $obj_blog->getRelatedPostsForProduct(array('id_product'=>$id_product));
            $smarty->assign(
                array($this->name . 'related_posts' => $data_posts['related_posts']
                )
            );
            $smarty->assign($this->name . 'tab_blog_pr', Configuration::get($this->name . 'tab_blog_pr'));
            $smarty->assign($this->name . 'btabs_type', Configuration::get($this->name . 'btabs_type'));

            $this->setSEOUrls();


        }
        return $this->display(__FILE__, $this->_template_path.$name_template, $this->getCacheId($cache_id));

		
	}

    protected function addBackOfficeMedia()
    {
        $this->context->controller->addCSS($this->_path.'views/css/font-custom.min.css');
        //CSS files
        $this->context->controller->addCSS($this->_path.'views/css/blog.css');
        $this->context->controller->addCSS($this->_path.'views/css/admin.css');

        // JS files
        $this->context->controller->addJs($this->_path.'views/js/menu16.js');

        $this->context->controller->addJqueryUI('ui.sortable');


    }

    public function clearSmartyCacheBlog(){

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/cacheblockblog.class.php');
        $cacheblockblog = new cacheblockblog();


        $cacheblockblog->clearSmartyCacheModule();
    }


	
    public function getContent()
    {

        $cookie = $this->context->cookie;

        $currentIndex = $this->context->currentindex;

    	include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
		$obj_blog = new blogspm();
    	


        $errors = array();
		
    	$_html = '';

        $this->addBackOfficeMedia();


        $_prefix_loyality = $this->getLoyalityPrefix();
        $loyalitysettingsset = Tools::getValue("loyalitysettingsset");
        if (Tools::strlen($loyalitysettingsset)>0) {

            ob_start();
            $number_tab = 60;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }

        if (Tools::isSubmit('loyalitysettings')) {



            Configuration::updateValue($this->name.'loyality_on'.$_prefix_loyality, Tools::getValue('loyality_on'.$_prefix_loyality));

            foreach (Tools::getValue($_prefix_loyality.'amount') AS $id => $value) {
                Configuration::updateValue($_prefix_loyality.'amount_' . (int)($id), (float)($value));
            }

            // orderstatuses
            $orderstatuses = Tools::getValue('orderstatuses');
            $orderstatuses = implode(",",$orderstatuses);
            Configuration::updateValue($this->name.$_prefix_loyality.'orderstatuses', $orderstatuses);

            // orderstatuses
            /*$orderstatusescan = Tools::getValue('orderstatusescan');
            $orderstatusescan = implode(",",$orderstatusescan);
            Configuration::updateValue($this->name.$_prefix_loyality.'orderstatusescan', $orderstatusescan);*/



            if(!ctype_digit(Tools::getValue('validity_period'.$_prefix_loyality)) || Tools::getValue('validity_period'.$_prefix_loyality) == NULL) {
                $errors[] = $this->l('Validity period of a points').' '.$this->l('must be digit!');
            } else {
                Configuration::updateValue($this->name.'validity_period'.$_prefix_loyality, (int)Tools::getValue('validity_period'.$_prefix_loyality));
            }



            if (count($errors) == 0) {
                // clear cache //
                $this->clearSmartyCacheBlog();
                // clear cache //
                $url = $currentIndex . '&conf=6&tab=AdminModules&loyalitysettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {

                ob_start();
                $number_tab = 60;
                include(dirname(__FILE__) . '/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }
        }


        // voucher settings

        $vouchersetl = Tools::getValue("voucherset".$_prefix_loyality);
        if (Tools::strlen($vouchersetl)>0) {
            ob_start();
            $number_tab = 61;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }

        if (Tools::isSubmit('vouchersettings'.$_prefix_loyality))
        {


            Configuration::updateValue($this->name.'vis_on'.$_prefix_loyality, Tools::getValue('vis_on'.$_prefix_loyality));

            $languages = Language::getLanguages(false);
            foreach ($languages as $language){
                $i = $language['id_lang'];
                Configuration::updateValue($this->name.'coupondesc'.$_prefix_loyality.'_'.$i, Tools::getValue('coupondesc'.$_prefix_loyality.'_'.$i));
            }

            if(Tools::getValue('vouchercode'.$_prefix_loyality) == NULL || Tools::strlen(Tools::getValue('vouchercode'.$_prefix_loyality))<3) {
                $errors[] = $this->l('Voucher code cannot be empty or must be at least 3 letters long!');
            } else {
                Configuration::updateValue($this->name . 'vouchercode'.$_prefix_loyality, Tools::getValue('vouchercode'.$_prefix_loyality));
            }


            if(!ctype_digit(Tools::getValue('sdvvalid'.$_prefix_loyality)) || Tools::getValue('sdvvalid'.$_prefix_loyality) == NULL) {
                $errors[] = $this->l('Voucher validity').' '.$this->l('must be digit!');
            } else {
                Configuration::updateValue($this->name . 'sdvvalid'.$_prefix_loyality, Tools::getValue('sdvvalid'.$_prefix_loyality));
            }

            if(Tools::getValue($this->name.'isminamount'.$_prefix_loyality) == true){
                foreach (Tools::getValue('sdminamount'.$_prefix_loyality) AS $id => $value){
                    Configuration::updateValue('sdminamount'.$_prefix_loyality.'_'.(int)($id), (float)($value));
                }
            }

            Configuration::updateValue($this->name.'isminamount'.$_prefix_loyality, Tools::getValue($this->name.'isminamount'.$_prefix_loyality));

            Configuration::updateValue($this->name.'tax'.$_prefix_loyality, Tools::getValue('tax'.$_prefix_loyality));

            // category
            $categoryBox = Tools::getValue('categoryBox'.$_prefix_loyality);


            if($categoryBox) {
                $categoryBox = implode(",", $categoryBox);
            } else{
                $categoryBox = '';
            }

            Configuration::updateValue($this->name.'catbox'.$_prefix_loyality, $categoryBox);

            // cumulable
            Configuration::updateValue($this->name.'cumulativeother'.$_prefix_loyality, Tools::getValue('cumulativeother'.$_prefix_loyality));
            Configuration::updateValue($this->name.'cumulativereduc'.$_prefix_loyality, Tools::getValue('cumulativereduc'.$_prefix_loyality));

            Configuration::updateValue($this->name.'highlight'.$_prefix_loyality, Tools::getValue('highlight'.$_prefix_loyality));

            if(sizeof($errors)==0) {
                // clear cache //
                $this->clearSmartyCacheBlog();
                // clear cache //
                $url = $currentIndex . '&conf=6&tab=AdminModules&voucherset'.$_prefix_loyality.'=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 61;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }
        }
        // voucher settings


        // points and actions
        $pointsactionssettingsset = Tools::getValue("pointsactionssettingsset");
        if (Tools::strlen($pointsactionssettingsset)>0) {
            ob_start();
            $number_tab = 62;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }

        if (Tools::isSubmit('pointsactionssettings'))
        {

            $points_and_actions = $this->getAvailablePoints();

            foreach($points_and_actions as $k=> $item){

                if(!ctype_digit(Tools::getValue($k.'_points')) || Tools::getValue($k.'_points') == NULL) {
                    $errors[] = $item['name'] .$this->l('points').' '.$this->l('must be digit!');
                } else {
                    Configuration::updateValue($this->name.$k.'_points', (int)Tools::getValue($k.'_points'));
                }

                $is_times = isset($item['count_times'])?$item['count_times']:1;
                if($is_times) {


                    $count_times = Tools::getValue($k . '_times')?(int)Tools::getValue($k . '_times'):1;


                    Configuration::updateValue($this->name . $k . '_times', (int)$count_times);

                }

                Configuration::updateValue($this->name.$k.'_status', Tools::getValue($k));

                $is_product = isset($item['is_product'])?$item['is_product']:0;
                if($is_product) {

                    Configuration::updateValue($this->name . $k . '_product', Tools::getValue($k . '_product'));
                }

            }

            if(sizeof($errors)==0) {
                // clear cache //
                $this->clearSmartyCacheBlog();
                // clear cache //
                $url = $currentIndex . '&conf=6&tab=AdminModules&pointsactionssettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 62;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }
        }
        // points and actions


        // points and actions
        $loyaltymessagesset = Tools::getValue("loyaltymessagesset");
        if (Tools::strlen($loyaltymessagesset)>0) {
            ob_start();
            $number_tab = 63;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }

        if (Tools::isSubmit('loyaltymessagessettings'))
        {



            $points_and_actions = $this->getAvailablePoints();

            foreach($points_and_actions as $k=> $item) {

                $languages = Language::getLanguages(false);
                foreach ($languages as $language){
                    $i = $language['id_lang'];
                    Configuration::updateValue($this->name.$k.'description_'.$i, Tools::getValue($k.'description_'.$i));
                }



            }

            if(sizeof($errors)==0) {
                // clear cache //
                $this->clearSmartyCacheBlog();
                // clear cache //
                $url = $currentIndex . '&conf=6&tab=AdminModules&loyaltymessagesset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 63;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }

        }



        $loyalitymysettingsset = Tools::getValue("loyalitymysettingsset");
        if (Tools::strlen($loyalitymysettingsset)>0) {

            ob_start();
            $number_tab = 64;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }

        if (Tools::isSubmit('loyalitymysettings')) {

            Configuration::updateValue($this->name.'d_eff_loyalty_my'.$_prefix_loyality, Tools::getValue('d_eff_loyalty_my'.$_prefix_loyality));
            if(!ctype_digit(Tools::getValue('perpagemy'.$_prefix_loyality)) || Tools::getValue('perpagemy'.$_prefix_loyality) == NULL) {
                $errors[] = $this->l('My Loaylty Points per Page in the My account').' '.$this->l('must be digit!');
            } else {
                Configuration::updateValue($this->name.'perpagemy'.$_prefix_loyality, (int)Tools::getValue('perpagemy'.$_prefix_loyality));
            }

            if (count($errors) == 0) {
                // clear cache //
                $this->clearSmartyCacheBlog();
                // clear cache //
                $url = $currentIndex . '&conf=6&tab=AdminModules&loyalitymysettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {

                ob_start();
                $number_tab = 64;
                include(dirname(__FILE__) . '/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }
        }


        $loyaltyemailssettingsset = Tools::getValue("loyaltyemailssettingsset");
        if (Tools::strlen($loyaltyemailssettingsset)>0) {

            ob_start();
            $number_tab = 65;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }

        if (Tools::isSubmit('loyaltyemailssettings')) {

            Configuration::updateValue($this->name.'noti'.$_prefix_loyality, Tools::getValue('noti'.$_prefix_loyality));


            if (count($errors) == 0) {
                // clear cache //
                $this->clearSmartyCacheBlog();
                // clear cache //
                $url = $currentIndex . '&conf=6&tab=AdminModules&loyaltyemailssettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {

                ob_start();
                $number_tab = 65;
                include(dirname(__FILE__) . '/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }
        }


        $loyaltyemailssubjectssettingsset = Tools::getValue("loyaltyemailssubjectssettingsset");
        if (Tools::strlen($loyaltyemailssubjectssettingsset)>0) {

            ob_start();
            $number_tab = 66;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }

        if (Tools::isSubmit('loyaltyemailssubjectssettings')) {

            Configuration::updateValue($this->name.'is_points'.$_prefix_loyality, Tools::getValue('is_points'.$_prefix_loyality));
            Configuration::updateValue($this->name.'is_loyaltyvouch'.$_prefix_loyality, Tools::getValue('is_loyaltyvouch'.$_prefix_loyality));

            $languages = Language::getLanguages(false);
            foreach ($languages as $language){
                $i = $language['id_lang'];
                Configuration::updateValue($this->name.'points'.$_prefix_loyality.'_'.$i, Tools::getValue('points'.$_prefix_loyality.'_'.$i));
                Configuration::updateValue($this->name.'loyaltyvouch'.$_prefix_loyality.'_'.$i, Tools::getValue('loyaltyvouch'.$_prefix_loyality.'_'.$i));
            }

            if (count($errors) == 0) {
                // clear cache //
                $this->clearSmartyCacheBlog();
                // clear cache //
                $url = $currentIndex . '&conf=6&tab=AdminModules&loyaltyemailssubjectssettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {

                ob_start();
                $number_tab = 66;
                include(dirname(__FILE__) . '/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }
        }




        foreach($this->getAvailablePages() as $available_pages_array) {

            $alias_position = $available_pages_array['alias'];
            $is_layout = $available_pages_array['is_layout'];

            $submitdesignpositions = Tools::getValue("sidebarmenu" . $alias_position . "settings");
            if (Tools::strlen($submitdesignpositions) > 0) {
                ob_start();
                $number_tab = 19;
                $is_subtab = 1;
                $number_subtab = "sidebarmenu_" . $alias_position;
                include(dirname(__FILE__) . '/views/templates/hooks/' . $this->name . '/js.phtml');
                $_html .= ob_get_clean();


            }


            if (Tools::isSubmit('sidebarmenusettings' . $alias_position)) {

                Configuration::updateValue($this->name.'sidebar_pos' . $alias_position, Tools::getValue('sidebar_pos' . $alias_position));

                if($is_layout) {
                    Configuration::updateValue($this->name . 'blog_layout_type' . $alias_position, Tools::getValue('blog_layout_type' . $alias_position));
                }



                if(sizeof($errors)==0) {
                    $url = $currentIndex . '&conf=6&tab=AdminModules&sidebarmenu' . $alias_position . 'settings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                    Tools::redirectAdmin($url);
                } else {
                    ob_start();
                    $number_tab = 19;
                    $is_subtab = 1;
                    $number_subtab = "sidebarmenu_".$alias_position;
                    include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                    $_html .= ob_get_clean();

                }
            }
        }


        ### url rewrite settings ###
        $blogurlrewritesettings = Tools::getValue("blogurlrewritesettings");
        if (Tools::strlen($blogurlrewritesettings)>0) {

            ob_start();
            $number_tab = 1;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();


        }


        if (Tools::isSubmit('urlrewritesettings'))
        {


            Configuration::updateValue($this->name.'urlrewrite_on', Tools::getValue('urlrewrite_on'));



            $languages = Language::getLanguages(false);
            foreach ($languages as $language){
                $i = $language['id_lang'];
                Configuration::updateValue($this->name.'title_allcat_'.$i, Tools::getValue('title_allcat_'.$i));
                Configuration::updateValue($this->name.'desc_allcat_'.$i, Tools::getValue('desc_allcat_'.$i));
                Configuration::updateValue($this->name.'key_allcat_'.$i, Tools::getValue('key_allcat_'.$i));

                if(Tools::getValue('blog_cat_alias_'.$i) == NULL || !Tools::getValue('blog_cat_alias_'.$i)) {
                    $txt = $this->l('All Blog Categories page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_cat_alias_'.$i, Tools::getValue('blog_cat_alias_'.$i));
                }



                Configuration::updateValue($this->name.'title_allposts_'.$i, Tools::getValue('title_allposts_'.$i));
                Configuration::updateValue($this->name.'desc_allposts_'.$i, Tools::getValue('desc_allposts_'.$i));
                Configuration::updateValue($this->name.'key_allposts_'.$i, Tools::getValue('key_allposts_'.$i));

                if(Tools::getValue('blog_alias_'.$i) == NULL || !Tools::getValue('blog_alias_'.$i)) {
                    $txt = $this->l('Blog main page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_alias_'.$i, Tools::getValue('blog_alias_'.$i));
                }



                Configuration::updateValue($this->name.'title_allcom_'.$i, Tools::getValue('title_allcom_'.$i));
                Configuration::updateValue($this->name.'desc_allcom_'.$i, Tools::getValue('desc_allcom_'.$i));
                Configuration::updateValue($this->name.'key_allcom_'.$i, Tools::getValue('key_allcom_'.$i));

                if(Tools::getValue('blog_allcom_alias_'.$i) == NULL || !Tools::getValue('blog_allcom_alias_'.$i)) {
                    $txt = $this->l('Blog All Comments page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_allcom_alias_'.$i, Tools::getValue('blog_allcom_alias_'.$i));
                }


                Configuration::updateValue($this->name.'title_alltags_'.$i, Tools::getValue('title_alltags_'.$i));
                Configuration::updateValue($this->name.'desc_alltags_'.$i, Tools::getValue('desc_alltags_'.$i));
                Configuration::updateValue($this->name.'key_alltags_'.$i, Tools::getValue('key_alltags_'.$i));

                if(Tools::getValue('blog_alltags_alias_'.$i) == NULL || !Tools::getValue('blog_alltags_alias_'.$i)) {
                    $txt = $this->l('Blog All Tags page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_alltags_alias_'.$i, Tools::getValue('blog_alltags_alias_'.$i));
                }


                if(Tools::getValue('blog_tag_alias_'.$i) == NULL || !Tools::getValue('blog_tag_alias_'.$i)) {
                    $txt = $this->l('Blog Tag page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_tag_alias_'.$i, Tools::getValue('blog_tag_alias_'.$i));
                }


                Configuration::updateValue($this->name.'title_allauthors_'.$i, Tools::getValue('title_allauthors_'.$i));
                Configuration::updateValue($this->name.'desc_allauthors_'.$i, Tools::getValue('desc_allauthors_'.$i));
                Configuration::updateValue($this->name.'key_allauthors_'.$i, Tools::getValue('key_allauthors_'.$i));

                if(Tools::getValue('blog_authors_alias_'.$i) == NULL || !Tools::getValue('blog_authors_alias_'.$i)) {
                    $txt = $this->l('Blog Authors page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_authors_alias_'.$i, Tools::getValue('blog_authors_alias_'.$i));
                }


                if(Tools::getValue('blog_author_alias_'.$i) == NULL || !Tools::getValue('blog_author_alias_'.$i)) {
                    $txt = $this->l('Blog Author page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_author_alias_'.$i, Tools::getValue('blog_author_alias_'.$i));
                }

                Configuration::updateValue($this->name.'title_myava_'.$i, Tools::getValue('title_myava_'.$i));
                Configuration::updateValue($this->name.'desc_myava_'.$i, Tools::getValue('desc_myava_'.$i));
                Configuration::updateValue($this->name.'key_myava_'.$i, Tools::getValue('key_myava_'.$i));

                if(Tools::getValue('blog_mava_alias_'.$i) == NULL || !Tools::getValue('blog_mava_alias_'.$i)) {
                    $txt = $this->l('Blog author avatar page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_mava_alias_'.$i, Tools::getValue('blog_mava_alias_'.$i));
                }

                Configuration::updateValue($this->name.'title_myposts_'.$i, Tools::getValue('title_myposts_'.$i));
                Configuration::updateValue($this->name.'desc_myposts_'.$i, Tools::getValue('desc_myposts_'.$i));
                Configuration::updateValue($this->name.'key_myposts_'.$i, Tools::getValue('key_myposts_'.$i));

                if(Tools::getValue('blog_mbposts_alias_'.$i) == NULL || !Tools::getValue('blog_mbposts_alias_'.$i)) {
                    $txt = $this->l('My Blog Posts page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_mbposts_alias_'.$i, Tools::getValue('blog_mbposts_alias_'.$i));
                }

                Configuration::updateValue($this->name.'title_mycom_'.$i, Tools::getValue('title_mycom_'.$i));
                Configuration::updateValue($this->name.'desc_mycom_'.$i, Tools::getValue('desc_mycom_'.$i));
                Configuration::updateValue($this->name.'key_mycom_'.$i, Tools::getValue('key_mycom_'.$i));

                if(Tools::getValue('blog_mbcom_alias_'.$i) == NULL || !Tools::getValue('blog_mbcom_alias_'.$i)) {
                    $txt = $this->l('My Blog Comments page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_mbcom_alias_'.$i, Tools::getValue('blog_mbcom_alias_'.$i));
                }

                Configuration::updateValue($this->name.'title_gallery_'.$i, Tools::getValue('title_gallery_'.$i));
                Configuration::updateValue($this->name.'desc_gallery_'.$i, Tools::getValue('desc_gallery_'.$i));
                Configuration::updateValue($this->name.'key_gallery_'.$i, Tools::getValue('key_gallery_'.$i));

                if(Tools::getValue('blog_gallery_alias_'.$i) == NULL || !Tools::getValue('blog_gallery_alias_'.$i)) {
                    $txt = $this->l('Blog Gallery page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_gallery_alias_'.$i, Tools::getValue('blog_gallery_alias_'.$i));
                }






                Configuration::updateValue($this->name.'title_rss_'.$i, Tools::getValue('title_rss_'.$i));
                Configuration::updateValue($this->name.'desc_rss_'.$i, Tools::getValue('desc_rss_'.$i));
                Configuration::updateValue($this->name.'key_rss_'.$i, Tools::getValue('key_rss_'.$i));

                if(Tools::getValue('blog_rss_alias_'.$i) == NULL || !Tools::getValue('blog_rss_alias_'.$i)) {
                    $txt = $this->l('Blog RSS page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {
                    Configuration::updateValue($this->name.'blog_rss_alias_'.$i, Tools::getValue('blog_rss_alias_'.$i));
                }



                Configuration::updateValue($this->name.'title_loyalty_'.$i, Tools::getValue('title_loyalty_'.$i));
                Configuration::updateValue($this->name.'desc_loyalty_'.$i, Tools::getValue('desc_loyalty_'.$i));
                Configuration::updateValue($this->name.'key_loyalty_'.$i, Tools::getValue('key_loyalty_'.$i));



                if(Tools::getValue('blog_loyalty_alias_'.$i) === NULL || !Tools::getValue('blog_loyalty_alias_'.$i)) {

                    $txt = $this->l('Blog My Loyalty Program page alias').' '.$this->l('must be filled!');
                    if(!in_array($txt,$errors))
                        $errors[] = $txt;
                } else {

                    Configuration::updateValue($this->name.'blog_loyalty_alias_'.$i, Tools::getValue('blog_loyalty_alias_'.$i));
                }




            }


            if(count($errors)==0) {

                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&blogurlrewritesettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';

                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 1;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }

        }
        ### url rewrite settings ###

        ### main settings ###
        $mainsettingsblogset = Tools::getValue("mainsettingsblogset");
        if (Tools::strlen($mainsettingsblogset)>0) {

            ob_start();
            $number_tab = 17;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();


        }


        if (Tools::isSubmit('mainsettingsblog'))
        {


            Configuration::updateValue($this->name.'bgcolor_main', Tools::getValue($this->name.'bgcolor_main'));
            Configuration::updateValue($this->name.'bgcolor_hover', Tools::getValue($this->name.'bgcolor_hover'));

            if(Tools::getValue('blog_date') == NULL || !Tools::getValue('blog_date')) {
                $txt = $this->l('Date Format').' '.$this->l('must be filled!');
                if(!in_array($txt,$errors))
                    $errors[] = $txt;
            } else {
                Configuration::updateValue($this->name . 'blog_date', Tools::getValue('blog_date'));
            }

            //Configuration::updateValue($this->name.'blog_layout_type', Tools::getValue('blog_layout_type'));




            $languages = Language::getLanguages(false);
            foreach ($languages as $language){
                $i = $language['id_lang'];

                Configuration::updateValue($this->name.'text_readmore_'.$i, Tools::getValue('text_readmore_'.$i));
            }


            if(count($errors)==0) {

                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&mainsettingsblogset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';

                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 17;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }

        }
        ### main settings ###

        ### categoriessettings ###
        $blogcategoriessettings = Tools::getValue("blogcategoriessettings");
        if (Tools::strlen($blogcategoriessettings)>0) {

            ob_start();
            $number_tab = 2;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();



        }


        if (Tools::isSubmit('categoriessettings'))
        {
            Configuration::updateValue($this->name.'cat_list_display_date', Tools::getValue('cat_list_display_date'));

            if(!ctype_digit(Tools::getValue('perpage_catblog'))) {
                $errors[] = $this->l('"Categories per Page value" must be digit!');
            } else {
                Configuration::updateValue($this->name . 'perpage_catblog', Tools::getValue('perpage_catblog'));
            }

            Configuration::updateValue($this->name.'blog_cat_effect', Tools::getValue('blog_cat_effect'));


            if(!ctype_digit(Tools::getValue('cat_img_width'))) {
                $errors[] = $this->l('"Image width on the category page value" must be digit!');
            } else {
               Configuration::updateValue($this->name . 'cat_img_width', Tools::getValue('cat_img_width'));
            }


            if(!ctype_digit(Tools::getValue('clists_img_width'))) {
                $errors[] = $this->l('"Image width in lists categories page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'clists_img_width', Tools::getValue('clists_img_width'));
            }

            if(!ctype_digit(Tools::getValue('blog_catl_tr'))) {
                $errors[] = $this->l('"Truncate category description in the list view"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_catl_tr', Tools::getValue('blog_catl_tr'));
            }


            Configuration::updateValue($this->name.'blog_cat_order', Tools::getValue('blog_cat_order'));
            Configuration::updateValue($this->name.'blog_cat_ad', Tools::getValue('blog_cat_ad'));



            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //
                $url = $currentIndex . '&conf=6&tab=AdminModules&blogcategoriessettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);

            } else {
                ob_start();
                $number_tab = 2;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }

        }
        ### categoriessettings ###


        ### postssettings ###
        $blogpostssettings = Tools::getValue("blogpostssettings");
        if (Tools::strlen($blogpostssettings)>0) {

            ob_start();
            $number_tab = 3;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();



        }


        if (Tools::isSubmit('postssettings'))
        {


            if(!ctype_digit(Tools::getValue('perpage_posts'))) {
                $errors[] = $this->l('"Posts per Page in the list view"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'perpage_posts', Tools::getValue('perpage_posts'));
            }


            Configuration::updateValue($this->name.'ava_list_displ', Tools::getValue('ava_list_displ'));
            Configuration::updateValue($this->name.'ava_post_displ', Tools::getValue('ava_post_displ'));



            Configuration::updateValue($this->name.'p_list_displ_date', Tools::getValue('p_list_displ_date'));
            Configuration::updateValue($this->name.'postl_views', Tools::getValue('postl_views'));
            Configuration::updateValue($this->name.'rating_postl', Tools::getValue('rating_postl'));

            if(!ctype_digit(Tools::getValue('lists_img_width'))) {
                $errors[] = $this->l('"Image width in the lists posts"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'lists_img_width', Tools::getValue('lists_img_width'));
            }

           /* if(!ctype_digit(Tools::getValue('lists_img_height'))) {
                $errors[] = $this->l('"Image height in the lists posts"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'lists_img_height', Tools::getValue('lists_img_height'));
            }*/

            if(!ctype_digit(Tools::getValue('blog_pl_tr'))) {
                $errors[] = $this->l('"Truncate posts content in the list view"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_pl_tr', Tools::getValue('blog_pl_tr'));
            }

            Configuration::updateValue($this->name.'img_size_rp', Tools::getValue('img_size_rp'));

            if(!ctype_digit(Tools::getValue('blog_rp_tr'))) {
                $errors[] = $this->l('"Truncate product description"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_rp_tr', Tools::getValue('blog_rp_tr'));
            }


            Configuration::updateValue($this->name.'post_display_date', Tools::getValue('post_display_date'));

            if(!ctype_digit(Tools::getValue('post_img_width'))) {
                $errors[] = $this->l('"Image width on the post page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'post_img_width', Tools::getValue('post_img_width'));
            }


            //Configuration::updateValue($this->name.'is_soc_buttons', Tools::getValue('is_soc_buttons'));

            Configuration::updateValue($this->name.'is_blog_f_share', Tools::getValue('is_blog_f_share'));
            Configuration::updateValue($this->name.'is_blog_g_share', Tools::getValue('is_blog_g_share'));
            Configuration::updateValue($this->name.'is_blog_t_share', Tools::getValue('is_blog_t_share'));
            Configuration::updateValue($this->name.'is_blog_p_share', Tools::getValue('is_blog_p_share'));
            Configuration::updateValue($this->name.'is_blog_l_share', Tools::getValue('is_blog_l_share'));
            Configuration::updateValue($this->name.'is_blog_tu_share', Tools::getValue('is_blog_tu_share'));
            Configuration::updateValue($this->name.'is_blog_w_share', Tools::getValue('is_blog_w_share'));

            Configuration::updateValue($this->name.'post_views', Tools::getValue('post_views'));
            Configuration::updateValue($this->name.'rating_postp', Tools::getValue('rating_postp'));
            Configuration::updateValue($this->name.'rating_postrp', Tools::getValue('rating_postrp'));

            Configuration::updateValue($this->name.'is_tags_bp', Tools::getValue('is_tags_bp'));
            Configuration::updateValue($this->name.'is_cat_bp', Tools::getValue('is_cat_bp'));
            Configuration::updateValue($this->name.'is_author_bp', Tools::getValue('is_author_bp'));

            if(!ctype_digit(Tools::getValue('rp_img_width'))) {
                $errors[] = $this->l('"Image width in the related posts block on the post page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'rp_img_width', Tools::getValue('rp_img_width'));
            }

            Configuration::updateValue($this->name.'postrel_views', Tools::getValue('postrel_views'));

            if(!ctype_digit(Tools::getValue('npr_slider'))) {
                $errors[] = $this->l('"Number Products in the Related Products slider"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'npr_slider', Tools::getValue('npr_slider'));
            }

            if(!ctype_digit(Tools::getValue('np_slider'))) {
                $errors[] = $this->l('"Number Posts in the Related Posts slider"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'np_slider', Tools::getValue('np_slider'));
            }

            Configuration::updateValue($this->name.'relpr_slider', Tools::getValue('relpr_slider'));
            Configuration::updateValue($this->name.'relp_slider', Tools::getValue('relp_slider'));

            Configuration::updateValue($this->name.'blog_post_effect', Tools::getValue('blog_post_effect'));

            Configuration::updateValue($this->name.'blog_post_order', Tools::getValue('blog_post_order'));
            Configuration::updateValue($this->name.'blog_post_ad', Tools::getValue('blog_post_ad'));

            Configuration::updateValue($this->name.'blog_rp_order', Tools::getValue('blog_rp_order'));
            Configuration::updateValue($this->name.'blog_rp_ad', Tools::getValue('blog_rp_ad'));



            Configuration::updateValue($this->name.'tab_blog_pr', Tools::getValue('tab_blog_pr'));

            Configuration::updateValue($this->name.'btabs_type', Tools::getValue('btabs_type'));

            Configuration::updateValue($this->name.'blog_catp_order', Tools::getValue('blog_catp_order'));
            Configuration::updateValue($this->name.'blog_catp_ad', Tools::getValue('blog_catp_ad'));


            if(!ctype_digit(Tools::getValue('rp_img_widthp'))) {
                $errors[] = $this->l('"Image width in the posts on the Product Page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'rp_img_widthp', Tools::getValue('rp_img_widthp'));
            }

            Configuration::updateValue($this->name.'postrel_viewsp', Tools::getValue('postrel_viewsp'));
            Configuration::updateValue($this->name.'rating_postrpp', Tools::getValue('rating_postrpp'));

            Configuration::updateValue($this->name.'relp_sliderp', Tools::getValue('relp_sliderp'));

            if(!ctype_digit(Tools::getValue('np_sliderp'))) {
                $errors[] = $this->l('"Number Posts in the posts slider on the Product Page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'np_sliderp', Tools::getValue('np_sliderp'));
            }


            if(!ctype_digit(Tools::getValue('blog_relposts'))) {
                $errors[] = $this->l('"The number of items in the "Related Blog Posts" on the Product Page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_relposts', Tools::getValue('blog_relposts'));
            }

            if(count($errors)==0){
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&blogpostssettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 3;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();


            }

        }
        ### postssettings ###


        ### commentssettings ##
        $blogcommentssettings = Tools::getValue("blogcommentssettings");
        if (Tools::strlen($blogcommentssettings)>0) {


            ob_start();
            $number_tab = 4;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();



        }


        if (Tools::isSubmit('commentssettings'))
        {



            Configuration::updateValue($this->name.'whocanaddc', Tools::getValue('whocanaddc'));
            Configuration::updateValue($this->name.'ava_list_displ_call', Tools::getValue('ava_list_displ_call'));
            Configuration::updateValue($this->name.'ava_list_displ_cpost', Tools::getValue('ava_list_displ_cpost'));

            if(!ctype_digit(Tools::getValue('perpage_com'))) {
                $errors[] = $this->l('"Comments per Page on the All Comments page in the list view"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'perpage_com', Tools::getValue('perpage_com'));
            }

            if(!ctype_digit(Tools::getValue('pperpage_com'))) {
                $errors[] = $this->l('"Comments per Page on the post page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'pperpage_com', Tools::getValue('pperpage_com'));
            }

            Configuration::updateValue($this->name.'rating_post', Tools::getValue('rating_post'));
            Configuration::updateValue($this->name.'rating_acom', Tools::getValue('rating_acom'));
            Configuration::updateValue($this->name.'blog_com_effect', Tools::getValue('blog_com_effect'));
            Configuration::updateValue($this->name.'blog_comp_effect', Tools::getValue('blog_comp_effect'));

            Configuration::updateValue($this->name.'is_captcha_com', Tools::getValue('is_captcha_com'));

            Configuration::updateValue($this->name.'bcaptcha_type', Tools::getValue('bcaptcha_type'));

            if(Configuration::get($this->name.'bcaptcha_type') != 1) {

                if(!Tools::getValue('bcaptcha_site_key')) {
                    $errors[] = $this->l('"Site Key"').' '.$this->l('value must be filled!');
                } else {
                    Configuration::updateValue($this->name . 'bcaptcha_site_key', Tools::getValue('bcaptcha_site_key'));
                }



                if(!Tools::getValue('bcaptcha_secret_key')) {
                    $errors[] = $this->l('"Secret Key"').' '.$this->l('value must be filled!');
                } else {
                    Configuration::updateValue($this->name . 'bcaptcha_secret_key', Tools::getValue('bcaptcha_secret_key'));
                }

             }

            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&blogcommentssettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 4;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }

        }
        ### commentssettings ###


        ### commentssettings ##
        $blogfbcommentssettings = Tools::getValue("blogfbcommentssettings");
        if (Tools::strlen($blogfbcommentssettings)>0) {

            ob_start();
            $number_tab = 10;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();


        }


        if (Tools::isSubmit('fbcommentssettings'))
        {
            if(!ctype_digit(Tools::getValue('number_fc'))) {
                $errors[] = $this->l('"Numbers of comments visible"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'number_fc', Tools::getValue('number_fc'));
            }

            Configuration::updateValue($this->name.'appid', Tools::getValue('appid'));
            Configuration::updateValue($this->name.'appadmin', Tools::getValue('appadmin'));

            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&blogfbcommentssettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 10;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();

            }

        }
        ### commentssettings ###



        ### myaccountsettings ###
        $myaccountsettingsset = Tools::getValue("myaccountsettingsset");
        if (Tools::strlen($myaccountsettingsset)>0) {

            ob_start();
            $number_tab = 16;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();



        }


        if (Tools::isSubmit('myaccountsettings'))
        {

            Configuration::updateValue($this->name.'myblogposts_on', Tools::getValue('myblogposts_on'));
            Configuration::updateValue($this->name.'is_addmyblogposts', Tools::getValue('is_addmyblogposts'));
            Configuration::updateValue($this->name.'is_editmyblogposts', Tools::getValue('is_editmyblogposts'));
            Configuration::updateValue($this->name.'is_delmyblogposts', Tools::getValue('is_delmyblogposts'));
            Configuration::updateValue($this->name.'d_eff_shopmyposts', Tools::getValue('d_eff_shopmyposts'));

            $blog_ids_groups = Tools::getValue("blog_ids_groups");
            if(!$blog_ids_groups) {
                $errors[] = $this->l('Please select the Customer groups');
            } else{
                $blog_ids_groups = implode(",",$blog_ids_groups);

                Configuration::updateValue($this->name . 'blog_ids_groups', $blog_ids_groups);
                //$blog_ids_groups1 = Configuration::get($this->name.'blog_ids_groups');
                //var_dump($blog_ids_groups1);exit;
            }


            $ids_categories = Tools::getValue("ids_categories");
            $ids_categories = implode(",",$ids_categories);
            Configuration::updateValue($this->name . 'ids_categories', $ids_categories);


            if(!ctype_digit(Tools::getValue('perpage_myposts'))) {
                $errors[] = $this->l('"Blog posts per page in the My account -> My Blog Posts"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'perpage_myposts', Tools::getValue('perpage_myposts'));
            }


            Configuration::updateValue($this->name.'myblogcom_on', Tools::getValue('myblogcom_on'));
            Configuration::updateValue($this->name.'is_editmyblogcom', Tools::getValue('is_editmyblogcom'));
            Configuration::updateValue($this->name.'is_delmyblogcom', Tools::getValue('is_delmyblogcom'));
            Configuration::updateValue($this->name.'d_eff_shopmycom', Tools::getValue('d_eff_shopmycom'));

            if(!ctype_digit(Tools::getValue('perpage_mycom'))) {
                $errors[] = $this->l('"Blog Comments per page in the My account -> My Blog Comments"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'perpage_mycom', Tools::getValue('perpage_mycom'));
            }


            if(count($errors)==0){
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&myaccountsettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 16;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();


            }

        }
        ### myaccountsettings ###

        ### slidersettings settings ###
        $slidersettingsset = Tools::getValue("slidersettingsset");
        if (Tools::strlen($slidersettingsset)>0) {

            ob_start();
            $number_tab = 13;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();

        }


        if (Tools::isSubmit('slidersettings'))
        {
            Configuration::updateValue($this->name.'slider_type', Tools::getValue('slider_type'));
            Configuration::updateValue($this->name.'slider_h_on', Tools::getValue('slider_h_on'));
            Configuration::updateValue($this->name.'slider_b_on', Tools::getValue('slider_b_on'));

            Configuration::updateValue($this->name.'slider_pn_on', Tools::getValue('slider_pn_on'));

            if(!ctype_digit(Tools::getValue('slider_widthfancytr'))) {
                $errors[] = $this->l('"Slider Width for Desktop devices"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'slider_widthfancytr', Tools::getValue('slider_widthfancytr'));
            }

            if(!ctype_digit(Tools::getValue('slider_heightfancytr'))) {
                $errors[] = $this->l('"Slider Height for Desktop devices"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'slider_heightfancytr', Tools::getValue('slider_heightfancytr'));
            }



            if(!ctype_digit(Tools::getValue('slider_widthfancytr_mobile'))) {
                $errors[] = $this->l('"Slider Width for Mobile devices"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'slider_widthfancytr_mobile', Tools::getValue('slider_widthfancytr_mobile'));
            }

            if(!ctype_digit(Tools::getValue('slider_heightfancytr_mobile'))) {
                $errors[] = $this->l('"Slider Height for Mobile devices"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'slider_heightfancytr_mobile', Tools::getValue('slider_heightfancytr_mobile'));
            }




            if(!ctype_digit(Tools::getValue('slider_titlefancytr'))) {
                $errors[] = $this->l('"Title length"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'slider_titlefancytr', Tools::getValue('slider_titlefancytr'));
            }

            if(!ctype_digit(Tools::getValue('slide_limit'))) {
                $errors[] = $this->l('"Number of slide to display"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'slide_limit', Tools::getValue('slide_limit'));
            }


            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //


                $url = $currentIndex . '&conf=6&tab=AdminModules&slidersettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 13;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }



        }
        ### slidersettings settings ###


        ### gallerysettings settings ###
        $slidersettingsset = Tools::getValue("gallerysettingsset");
        if (Tools::strlen($slidersettingsset)>0) {

            ob_start();
            $number_tab = 14;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }


        if (Tools::isSubmit('gallerysettings'))
        {
            Configuration::updateValue($this->name.'gallery_on', Tools::getValue('gallery_on'));


            if(!ctype_digit(Tools::getValue('gallery_width'))) {
                $errors[] = $this->l('"Image Width"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'gallery_width', Tools::getValue('gallery_width'));
            }

            if(!ctype_digit(Tools::getValue('gallery_height'))) {
                $errors[] = $this->l('"Image Height"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'gallery_height', Tools::getValue('gallery_height'));
            }

            if(!ctype_digit(Tools::getValue('gallery_per_page'))) {
                $errors[] = $this->l('"Number of Images per page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'gallery_per_page', Tools::getValue('gallery_per_page'));
            }

            if(!ctype_digit(Tools::getValue('gallery_speed'))) {
                $errors[] = $this->l('"Slideshow speed"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'gallery_speed', Tools::getValue('gallery_speed'));
            }

            Configuration::updateValue($this->name.'slider_effect', Tools::getValue('slider_effect'));
            Configuration::updateValue($this->name.'gallery_autoplay', Tools::getValue('gallery_autoplay'));

            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //


                $url = $currentIndex . '&conf=6&tab=AdminModules&gallerysettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 14;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }



        }
        ### gallerysettings settings ###


        ### avatar settings ###
        $avasettingsset = Tools::getValue("avasettingsset");
        if (Tools::strlen($avasettingsset)>0) {

            ob_start();
            $number_tab = 12;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }


        if (Tools::isSubmit('avasettings'))
        {
            Configuration::updateValue($this->name.'ava_on', Tools::getValue('ava_on'));

            if(!ctype_digit(Tools::getValue('perpage_authors'))) {
                $errors[] = $this->l('"Authors per page in the list view"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'perpage_authors', Tools::getValue('perpage_authors'));
            }

            Configuration::updateValue($this->name.'d_eff_shopu', Tools::getValue('d_eff_shopu'));

            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //


                $url = $currentIndex . '&conf=6&tab=AdminModules&avasettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 12;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }



        }
        ### avatar settings ###



        ### blockssettings ###
        $blogblockssettings = Tools::getValue("blogblockssettings");
        if (Tools::strlen($blogblockssettings)>0) {

            ob_start();
            $number_tab = 5;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();

        }


        if (Tools::isSubmit('blockssettings'))
        {
            if(!ctype_digit(Tools::getValue('blog_bcat'))) {
                $errors[] = $this->l('"The number of items in the "Blog categories""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_bcat', Tools::getValue('blog_bcat'));
            }

            if(!ctype_digit(Tools::getValue('blog_bposts'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Posts recents""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_bposts', Tools::getValue('blog_bposts'));
            }


            Configuration::updateValue($this->name.'postbl_views', Tools::getValue('postbl_views'));
            Configuration::updateValue($this->name.'rating_bl', Tools::getValue('rating_bl'));
            Configuration::updateValue($this->name.'block_display_date', Tools::getValue('block_display_date'));
            Configuration::updateValue($this->name.'block_display_img', Tools::getValue('block_display_img'));



            if(!ctype_digit(Tools::getValue('posts_block_img_width'))) {
                $errors[] = $this->l('"Image width in the block "Blog Posts recents""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'posts_block_img_width', Tools::getValue('posts_block_img_width'));
            }



            Configuration::updateValue($this->name.'bposts_slider', Tools::getValue('bposts_slider'));


            if(!ctype_digit(Tools::getValue('bposts_sl'))) {
                $errors[] = $this->l('"Displayed number of items in the slider in the block "Blog Posts recents""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'bposts_sl', Tools::getValue('bposts_sl'));
            }


            if(!ctype_digit(Tools::getValue('blog_tr_b'))) {
                $errors[] = $this->l('"Truncate posts in the block "Blog Posts recents""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_tr_b', Tools::getValue('blog_tr_b'));
            }



            Configuration::updateValue($this->name.'bcat_slider', Tools::getValue('bcat_slider'));



            if(!ctype_digit(Tools::getValue('bcat_sl'))) {
                $errors[] = $this->l('"Displayed number of items in the slider in the block "Blog categories""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'bcat_sl', Tools::getValue('bcat_sl'));
            }

            Configuration::updateValue($this->name.'bcom_slider', Tools::getValue('bcom_slider'));



            if(!ctype_digit(Tools::getValue('bcom_sl'))) {
                $errors[] = $this->l('"Displayed number of items in the slider in the block "Blog Last Comments""').' '.$this->l('value must be digit!');
            } else {
               Configuration::updateValue($this->name . 'bcom_sl', Tools::getValue('bcom_sl'));
            }



            Configuration::updateValue($this->name.'rating_bllc', Tools::getValue('rating_bllc'));
            Configuration::updateValue($this->name.'blog_h', Tools::getValue('blog_h'));


            if(!ctype_digit(Tools::getValue('blog_bp_h'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Posts recents" on the home page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_bp_h', Tools::getValue('blog_bp_h'));
            }


            if(!ctype_digit(Tools::getValue('posts_w_h'))) {
                $errors[] = $this->l('"Image width in the block "Blog Posts recents" on the home page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'posts_w_h', Tools::getValue('posts_w_h'));
            }




            Configuration::updateValue($this->name.'postblh_views', Tools::getValue('postblh_views'));
            Configuration::updateValue($this->name.'rating_blh', Tools::getValue('rating_blh'));




            if(!ctype_digit(Tools::getValue('blog_p_tr'))) {
                $errors[] = $this->l('"Truncate posts in the block "Blog Posts recents" on the home page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_p_tr', Tools::getValue('blog_p_tr'));
            }


            if(!ctype_digit(Tools::getValue('blog_tags'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Tags""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_tags', Tools::getValue('blog_tags'));
            }


            if(!ctype_digit(Tools::getValue('blog_authors'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Top Authors""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_authors', Tools::getValue('blog_authors'));
            }



            Configuration::updateValue($this->name.'sr_slideru', Tools::getValue('sr_slideru'));

            if(!ctype_digit(Tools::getValue('sr_slu'))) {
                $errors[] = $this->l('"Displayed number of items in the slider in the block "Blog Top Authors""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'sr_slu', Tools::getValue('sr_slu'));
            }


            if(!ctype_digit(Tools::getValue('blog_authorsh'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Top Authors" on the home page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_authorsh', Tools::getValue('blog_authorsh'));
            }



            Configuration::updateValue($this->name.'sr_sliderhu', Tools::getValue('sr_sliderhu'));

            if(!ctype_digit(Tools::getValue('sr_slhu'))) {
                $errors[] = $this->l('"Displayed number of items in the slider in the block "Blog Top Authors" on the home page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'sr_slhu', Tools::getValue('sr_slhu'));
            }


            if(!ctype_digit(Tools::getValue('blog_com'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Last Comments""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_com', Tools::getValue('blog_com'));
            }


            if(!ctype_digit(Tools::getValue('blog_com_tr'))) {
                $errors[] = $this->l('"Truncate Comments in the block "Blog Last Comments""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_com_tr', Tools::getValue('blog_com_tr'));
            }


            if(!ctype_digit(Tools::getValue('blog_bp_sl'))) {
                $errors[] = $this->l('"Displayed number of items in the slider on the home page in the block "Blog Posts recents""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_bp_sl', Tools::getValue('blog_bp_sl'));
            }


            Configuration::updateValue($this->name.'blog_catbl_order', Tools::getValue('blog_catbl_order'));
            Configuration::updateValue($this->name.'blog_catbl_ad', Tools::getValue('blog_catbl_ad'));

            Configuration::updateValue($this->name.'blog_postbl_order', Tools::getValue('blog_postbl_order'));
            Configuration::updateValue($this->name.'blog_postbl_ad', Tools::getValue('blog_postbl_ad'));

            Configuration::updateValue($this->name.'blog_postblh_order', Tools::getValue('blog_postblh_order'));
            Configuration::updateValue($this->name.'blog_postblh_ad', Tools::getValue('blog_postblh_ad'));


            if(!ctype_digit(Tools::getValue('blog_gallery'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Gallery""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_gallery', Tools::getValue('blog_gallery'));
            }

            if(!ctype_digit(Tools::getValue('gallery_w'))) {
                $errors[] = $this->l('"Image width in the block "Blog Gallery""').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'gallery_w', Tools::getValue('gallery_w'));
            }

            if(!ctype_digit(Tools::getValue('blog_galleryh'))) {
                $errors[] = $this->l('"The number of items in the block "Blog Gallery" on the home page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'blog_galleryh', Tools::getValue('blog_galleryh'));
            }

            if(!ctype_digit(Tools::getValue('gallery_w_h'))) {
                $errors[] = $this->l('"Image width in the block "Blog Gallery" on the home page"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'gallery_w_h', Tools::getValue('gallery_w_h'));
            }



            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //


                $url = $currentIndex . '&conf=6&tab=AdminModules&blogblockssettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 5;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }

        }
        ### blockssettings ###


        ### blockpositions ###
        $blogblockpositions = Tools::getValue("blogblockpositions");
        if (Tools::strlen($blogblockpositions)>0) {


            ob_start();
            $number_tab = 6;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();


        }


        if (Tools::isSubmit('blockpositions'))
        {
            /*// footer
            Configuration::updateValue($this->name.'cat_footer', Tools::getValue('cat_footer'));
            Configuration::updateValue($this->name.'posts_footer', Tools::getValue('posts_footer'));
            Configuration::updateValue($this->name.'arch_footer', Tools::getValue('arch_footer'));
            Configuration::updateValue($this->name.'search_footer', Tools::getValue('search_footer'));
            Configuration::updateValue($this->name.'com_footer', Tools::getValue('com_footer'));
            Configuration::updateValue($this->name.'tag_footer', Tools::getValue('tag_footer'));
            Configuration::updateValue($this->name.'author_footer', Tools::getValue('author_footer'));
            Configuration::updateValue($this->name.'gallery_footer', Tools::getValue('gallery_footer'));
            // footer

            // right
            Configuration::updateValue($this->name.'cat_right', Tools::getValue('cat_right'));
            Configuration::updateValue($this->name.'posts_right', Tools::getValue('posts_right'));
            Configuration::updateValue($this->name.'arch_right', Tools::getValue('arch_right'));
            Configuration::updateValue($this->name.'search_right', Tools::getValue('search_right'));
            Configuration::updateValue($this->name.'com_right', Tools::getValue('com_right'));
            Configuration::updateValue($this->name.'tag_right', Tools::getValue('tag_right'));
            Configuration::updateValue($this->name.'author_right', Tools::getValue('author_right'));
            Configuration::updateValue($this->name.'gallery_right', Tools::getValue('gallery_right'));
            // right


            // left
            Configuration::updateValue($this->name.'cat_left', Tools::getValue('cat_left'));
            Configuration::updateValue($this->name.'posts_left', Tools::getValue('posts_left'));
            Configuration::updateValue($this->name.'arch_left', Tools::getValue('arch_left'));
            Configuration::updateValue($this->name.'search_left', Tools::getValue('search_left'));
            Configuration::updateValue($this->name.'com_left', Tools::getValue('com_left'));
            Configuration::updateValue($this->name.'tag_left', Tools::getValue('tag_left'));
            Configuration::updateValue($this->name.'author_left', Tools::getValue('author_left'));
            Configuration::updateValue($this->name.'gallery_left', Tools::getValue('gallery_left'));
            // left

            Configuration::updateValue($this->name.'block_last_home', Tools::getValue('block_last_home'));
            Configuration::updateValue($this->name.'authors_home', Tools::getValue('authors_home'));
            Configuration::updateValue($this->name.'gallery_home', Tools::getValue('gallery_home'));*/


            Configuration::updateValue($this->name.'cat_custom_hook', Tools::getValue('cat_custom_hook'));
            Configuration::updateValue($this->name.'posts_custom_hook', Tools::getValue('posts_custom_hook'));
            Configuration::updateValue($this->name.'comm_custom_hook', Tools::getValue('comm_custom_hook'));
            Configuration::updateValue($this->name.'search_custom_hook', Tools::getValue('search_custom_hook'));
            Configuration::updateValue($this->name.'arch_custom_hook', Tools::getValue('arch_custom_hook'));
            Configuration::updateValue($this->name.'tags_custom_hook', Tools::getValue('tags_custom_hook'));
            Configuration::updateValue($this->name.'auth_custom_hook', Tools::getValue('auth_custom_hook'));
            Configuration::updateValue($this->name.'gallery_custom_hook', Tools::getValue('gallery_custom_hook'));


            foreach(array("m","s") as $val) {

                Configuration::updateValue($this->name.$val.'cat_custom_hook', Tools::getValue($val.'cat_custom_hook'));
                Configuration::updateValue($this->name.$val.'posts_custom_hook', Tools::getValue($val.'posts_custom_hook'));
                Configuration::updateValue($this->name.$val.'comm_custom_hook', Tools::getValue($val.'comm_custom_hook'));
                Configuration::updateValue($this->name.$val.'search_custom_hook', Tools::getValue($val.'search_custom_hook'));
                Configuration::updateValue($this->name.$val.'arch_custom_hook', Tools::getValue($val.'arch_custom_hook'));
                Configuration::updateValue($this->name.$val.'tags_custom_hook', Tools::getValue($val.'tags_custom_hook'));
                Configuration::updateValue($this->name.$val.'auth_custom_hook', Tools::getValue($val.'auth_custom_hook'));
                Configuration::updateValue($this->name.$val.'gallery_custom_hook', Tools::getValue($val.'gallery_custom_hook'));
            }


            // clear cache only for blog //
            $this->clearSmartyCacheBlog();
            // clear cache only for blog //


            $url = $currentIndex.'&conf=6&tab=AdminModules&blogblockpositions=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
            Tools::redirectAdmin($url);

        }
        ### blockpositions ###


        ### rssfeedsettings ###

        $blogrssfeedsettings = Tools::getValue("blogrssfeedsettings");
        if (Tools::strlen($blogrssfeedsettings)>0) {



            ob_start();
            $number_tab = 7;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();


        }


        if (Tools::isSubmit('rssfeedsettings'))
        {
            Configuration::updateValue($this->name.'rsson', Tools::getValue('rsson'));

            if(!ctype_digit(Tools::getValue('number_rssitems'))) {
                $errors[] = $this->l('"Number of items in RSS Feed"').' '.$this->l('value must be digit!');
            } else {
                Configuration::updateValue($this->name . 'number_rssitems', Tools::getValue('number_rssitems'));
            }


            $languages = Language::getLanguages(false);
            foreach ($languages as $language){
                $i = $language['id_lang'];
                Configuration::updateValue($this->name.'rssname_'.$i, Tools::getValue('rssname_'.$i));
                Configuration::updateValue($this->name.'rssdesc_'.$i, Tools::getValue('rssdesc_'.$i));
            }

            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&blogrssfeedsettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 7;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }

        }
        ### rssfeedsettings ###


        ### emailsettings ###
        $blogemailsettings = Tools::getValue("blogemailsettings");
        if (Tools::strlen($blogemailsettings)>0) {




            ob_start();
            $number_tab = 8;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();


        }


        if (Tools::isSubmit('emailsettings'))
        {

            Configuration::updateValue($this->name.'noti', Tools::getValue('noti'));

            if(!Validate::isEmail(Tools::getValue('mail'))) {
                $errors[] = $this->l('"Admin email" is incorrect!');
            } else {
                Configuration::updateValue($this->name . 'mail', Tools::getValue('mail'));
            }


            Configuration::updateValue($this->name.'is_newblogc', Tools::getValue('is_newblogc'));
            Configuration::updateValue($this->name.'is_responseblogc', Tools::getValue('is_responseblogc'));

            Configuration::updateValue($this->name.'is_deleteblogcomcust', Tools::getValue('is_deleteblogcomcust'));
            Configuration::updateValue($this->name.'is_editblogcomcust', Tools::getValue('is_editblogcomcust'));
            Configuration::updateValue($this->name.'is_editblogcomtocust', Tools::getValue('is_editblogcomtocust'));

            Configuration::updateValue($this->name.'is_deleteblogpostcust', Tools::getValue('is_deleteblogpostcust'));
            Configuration::updateValue($this->name.'is_editblogpostcust', Tools::getValue('is_editblogpostcust'));
            Configuration::updateValue($this->name.'is_addblogpostcust', Tools::getValue('is_addblogpostcust'));
            Configuration::updateValue($this->name.'is_editblogposttocust', Tools::getValue('is_editblogposttocust'));
            Configuration::updateValue($this->name.'is_addblogposttocust', Tools::getValue('is_addblogposttocust'));

            Configuration::updateValue($this->name.'is_changestatustocust', Tools::getValue('is_changestatustocust'));
            Configuration::updateValue($this->name.'is_deleteallpoststocust', Tools::getValue('is_deleteallpoststocust'));

            $languages = Language::getLanguages(false);
            foreach ($languages as $language){
                $i = $language['id_lang'];
                Configuration::updateValue($this->name.'newblogc_'.$i, Tools::getValue('newblogc_'.$i));
                Configuration::updateValue($this->name.'responseblogc_'.$i, Tools::getValue('responseblogc_'.$i));

                Configuration::updateValue($this->name.'deleteblogcomcust_'.$i, Tools::getValue('deleteblogcomcust_'.$i));
                Configuration::updateValue($this->name.'editblogcomcust_'.$i, Tools::getValue('editblogcomcust_'.$i));
                Configuration::updateValue($this->name.'editblogcomtocust_'.$i, Tools::getValue('editblogcomtocust_'.$i));

                Configuration::updateValue($this->name.'deleteblogpostcust_'.$i, Tools::getValue('deleteblogpostcust_'.$i));
                Configuration::updateValue($this->name.'editblogpostcust_'.$i, Tools::getValue('editblogpostcust_'.$i));
                Configuration::updateValue($this->name.'addblogpostcust_'.$i, Tools::getValue('addblogpostcust_'.$i));
                Configuration::updateValue($this->name.'editblogposttocust_'.$i, Tools::getValue('editblogposttocust_'.$i));
                Configuration::updateValue($this->name.'addblogposttocust_'.$i, Tools::getValue('addblogposttocust_'.$i));

                Configuration::updateValue($this->name.'changestatustocust_'.$i, Tools::getValue('changestatustocust_'.$i));
                Configuration::updateValue($this->name.'deleteallpoststocust_'.$i, Tools::getValue('deleteallpoststocust_'.$i));


            }


            if(count($errors)==0) {
                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

                $url = $currentIndex . '&conf=6&tab=AdminModules&blogemailsettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            } else {
                ob_start();
                $number_tab = 8;
                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
                $_html .= ob_get_clean();
            }

        }
        ### emailsettings ###


        ### sitemapsettings ###
        $sitemapvalsettingsset = Tools::getValue("sitemapvalsettingsset");
        if (Tools::strlen($sitemapvalsettingsset)>0) {

            ob_start();
            $number_tab = 18;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();

        }


        if (Tools::isSubmit('sitemapvalsettings'))
        {

            Configuration::updateValue($this->name.'sitemapon', Tools::getValue('sitemapon'));

            Configuration::updateValue($this->name.'pages_main_blog', Tools::getValue('pages_main_blog'));
            Configuration::updateValue($this->name.'pages_blog_allcat', Tools::getValue('pages_blog_allcat'));
            Configuration::updateValue($this->name.'pages_blog_cat', Tools::getValue('pages_blog_cat'));

            Configuration::updateValue($this->name.'pages_blog_post', Tools::getValue('pages_blog_post'));


            Configuration::updateValue($this->name.'pages_authors', Tools::getValue('pages_authors'));
            Configuration::updateValue($this->name.'pages_author', Tools::getValue('pages_author'));
            Configuration::updateValue($this->name.'pages_allcom', Tools::getValue('pages_allcom'));
            Configuration::updateValue($this->name.'pages_alltags', Tools::getValue('pages_alltags'));
            Configuration::updateValue($this->name.'pages_tag', Tools::getValue('pages_tag'));
            Configuration::updateValue($this->name.'pages_gallery', Tools::getValue('pages_gallery'));

            //Configuration::updateValue($this->name.'sitemap_limit', Tools::getValue('sitemap_limit'));


            // clear cache only for blog //
            $this->clearSmartyCacheBlog();
            // clear cache only for blog //

            $url = $currentIndex . '&conf=6&tab=AdminModules&sitemapvalsettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
            Tools::redirectAdmin($url);


        }
        ### sitemapsettings ###



        if(Tools::isSubmit('submitsitemap')){
        	$obj_blog->generateSitemap();

            // clear cache only for blog //
            $this->clearSmartyCacheBlog();
            // clear cache only for blog //
            ob_start();
            $number_tab = 9;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();

        }



        $wordpresssettingsset = Tools::getValue("wordpresssettingsset");
        if (Tools::strlen($wordpresssettingsset)>0 || Tools::isSubmit('wordpresssettings')) {
            ob_start();
            $number_tab = 11;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }



        if(Tools::isSubmit('wordpresssettings')){
            //$obj_blog->generateSitemap();
            $error = 0;


            $cat_shop_association = Tools::getValue("cat_shop_association");

            $group_association = Tools::getValue("group_association");
            $select_language = Tools::getValue("select_language");


            //$files = isset($_FILES['xml_file'])?$_FILES['xml_file']:null;





            /*if(!empty($files['name'])){
                $upload_max_filesize = ini_get('upload_max_filesize');

                // convert to bytes
                preg_match('/[0-9]+/', $upload_max_filesize, $match);
                $upload_max_filesize_in_bytes = $match[0] * 1024 * 1024;
                // convert to bytes

                if($files['size'] > $upload_max_filesize_in_bytes){

                    $errors[] = $this->l('Max Upload XML file size in php.ini').' - '.$upload_max_filesize_in_bytes.' ('.$upload_max_filesize.'). '.$this->l('Your filesize').': '.$files['size'];

                }
            } else {
                $errors[] = $this->l('Please select the Upload file');
            }*/


            //if(!$this->is_demo) {
                if (!$select_language)
                    $errors[] = $this->l('Please select the Language');

                if (!$group_association)
                    $errors[] = $this->l('Please select the Group permissions');

                if (!$cat_shop_association)
                    $errors[] = $this->l('Please select the Shop');
            /*} else {
                if (!$cat_shop_association)
                    $errors[] = $this->l('Feature disabled on the demo mode');
            }*/


            //var_dump($errors);exit;


            if(sizeof($errors) == 0){

                $data_xml = array(
                                  'cat_shop_association'=>$cat_shop_association,
                                  'group_association'=>$group_association,
                                  'select_language'=>$select_language
                                );

                $data_wordpress_xml = $obj_blog->parseWordpressXml($data_xml);

                $error = $data_wordpress_xml['error'];
                $error_text = $data_wordpress_xml['error_text'];

                if($error != 0){
                    $errors[] = $this->l($error_text);
                } else {

                    // clear cache only for blog //
                    $this->clearSmartyCacheBlog();
                    // clear cache only for blog //

                   Tools::redirectAdmin($currentIndex . '&conf=18&wordpresssettingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '');
                }
            }

        }



        $twset = Tools::getValue("twset");
        if (Tools::strlen($twset)>0) {
            ob_start();
            $number_tab = 15;
            include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/js.phtml');
            $_html .= ob_get_clean();
        }
        if (Tools::isSubmit('pstwitterpostsettings'))
        {

            $array_wall_posts_items = array('tw',
                //'i',
                //'p'
            );

            foreach($array_wall_posts_items as $wall_post_item) {
                Configuration::updateValue($this->name . $wall_post_item.'post_on', Tools::getValue($wall_post_item.'post_on'));


                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $i = $language['id_lang'];
                    Configuration::updateValue($this->name . $wall_post_item.'desc_' . $i, Tools::getValue($wall_post_item.'desc_' . $i));
                }
            }

            // clear cache only for blog //
            $this->clearSmartyCacheBlog();
            // clear cache only for blog //


            $url = $currentIndex.'&conf=6&tab=AdminModules&twset=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
            Tools::redirectAdmin($url);
        }
        #### posts api ###







        $_html .= $this->_displayForm16(array('errors'=>$errors));


        return $_html;
    }

    private function _is_gd_installed() {
        if  (function_exists('gd_info')) {
            return true;
        }
        else {
            return false;
        }
    }

private function _welcome(){

    $this->_data_translate_custom['_welcome16_w'] = $this->l('Welcome');
    $this->_data_translate_custom['_welcome16_w_and_thank_y'] = $this->l('Welcome and thank you for purchasing the module.');
    $this->_data_translate_custom['_welcome16_to_conf'] = $this->l('To configure module please read');
    $this->_data_translate_custom['_welcome16_h_doc'] = $this->l('Help / Documentation');

    $this->_data_translate_custom['_welcome16_pre_check'] = $this->l('Prerequisites Check');
    $this->_data_translate_custom['_welcome16_fr_url'] = $this->l('Friendly URL is DISABLED in your SHOP');

    ob_start();
    include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
    $_html = ob_get_clean();


    return $_html;



    }

    private function _help_documentation(){
        $this->_data_translate_custom['_help_documentation_hd'] = $this->l('Help / Documentation');
        $this->_data_translate_custom['_help_documentation_mod_doc'] = $this->l('MODULE DOCUMENTATION ');
        $this->_data_translate_custom['_help_documentation_how_to_conf_cron'] = $this->l('How to configure CRON ');
        $this->_data_translate_custom['_help_documentation_cron_help'] = $this->l('CRON HELP');
        $this->_data_translate_custom['_help_documentation_g_rich_snip'] = $this->l('GOOGLE RICH SNIPPETS TEST TOOL ');
        $this->_data_translate_custom['_help_documentation_cust_hook_help'] = $this->l('CUSTOM HOOK HELP for BLOG PRO Positions Blocks ');
        $this->_data_translate_custom['_help_documentation_how_i_can_show'] = $this->l('How I can show Blog Categories, Posts, Comments, etc. on a single page (CMS or other places for example)');

        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();


        return $_html;
    }

    private function _displayForm16($data = null){

        $_html = '';
        $this->_data_translate_custom['_displayForm16_errors'] = $this->l('errors');
        $this->_data_translate_custom['_displayForm16_welcome'] = $this->l('Welcome');
        $this->_data_translate_custom['_displayForm16_blog_set'] = $this->l('Blog Settings');
        $this->_data_translate_custom['_displayForm16_rewards'] = $this->l('Loyalty program');
        $this->_data_translate_custom['_displayForm16_aut_post_int'] = $this->l('Automatically posts Integration');
        $this->_data_translate_custom['_displayForm16_help_doc'] = $this->l('Help / Documentation');
        $this->_data_translate_custom['_displayForm16_oth_spm_mod'] = $this->l('Other SPM Modules');

        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();


        return $_html;

    }

    private function _loyalty(){


        $this->_data_translate_custom['_loyalty_main_set'] = $this->l('Settings');
        $this->_data_translate_custom['_loyalty_voucher_set_when_add_rev'] = $this->l('Voucher settings');
        $this->_data_translate_custom['_loyalty_points_actions'] = $this->l('Loyalty Points per Action');
        $this->_data_translate_custom['_loyalty_msg'] = $this->l('Messages for Actions');
        $this->_data_translate_custom['_loyalty_my'] = $this->l('My account -> Loyalty Program');
        $this->_data_translate_custom['_loyalty_em_set'] = $this->l('Loyalty program emails settings');
        $this->_data_translate_custom['_loyalty_em_subj_set'] = $this->l('Emails subjects settings');


        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();
        return $_html;
    }


    private function _loyalitysettings(){

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();

        $data = array(
            'prefix'=>$this->getLoyalityPrefix(),

            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'title'=>$this->l('Settings'),
            'yes_title' =>$this->l('Yes'),
            'no_title'=>$this->l('No'),
            'loyality_on_title'=>$this->l('Enable loyalty program'),
            'loyality_on_desc'=>$this->l('Loyalty program only for registered customers!'),

            'loyality_points_desc'=>$this->l('Customer can convert collected points into coupon with discount in my account'),

            'sel_statuses_title'=> $this->l('Points are awarded when the order is'),
            'sel_statuses_desc'=>$this->l('You must choose at least one status.'),


            'validity_period_title'=> $this->l('Validity period of a points'),

            'days'=> $this->l('days'),


            //'cancel_statuses_title'=> $this->l('Points are cancelled when the order is'),
            //'cancel_statuses_desc'=>$this->l('You must choose at least one status.'),



            'update_title'=>$this->l('Update Settings'),
        );
        return $obj_loyality->loyalitysettings($data);
    }

    private function _voucherloyality(){
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();



        $_prefix = $this->getLoyalityPrefix();

        // select categories
        $_selected_cat = Configuration::get($this->name.'catbox'.$_prefix);
        $select_categories = explode(",",$_selected_cat);


        $categories = array();
        foreach ($select_categories as $key => $category) {
            $categories[] = $category;
        }

        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('associated-categories-tree-'.$_prefix, 'Associated categories');
        $tree->setTemplate('tree_associated_categories.tpl')
            ->setHeaderTemplate('tree_associated_header.tpl')
            ->setTemplateDirectory(dirname(__FILE__).'/views/templates/admin/_configure/helpers/tree/')
            ->setRootCategory((int)$root->id)
            ->setUseCheckBox(true)
            ->setInputName('categoryBox'.$_prefix)
            ->setUseSearch(true)
            ->setSelectedCategories($categories);
        $selected_categories = $tree->render();

        $data = array(

            'prefix'=>$_prefix,

            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'selected_categories'=>$selected_categories,

            'title'=>$this->l('Voucher settings'),
            'vis_on_title'=>$this->l('Enable or Disable Voucher'),
            'vis_on_desc'=>$this->l('Enable or Disable Voucher'),
            'yes_title'=>$this->l('Yes'),
            'no_title'=>$this->l('No'),
            'coupondesc_title'=>$this->l('Coupon Description'),
            'coupondesc_desc'=>$this->l('The description is displayed in cart once your customers use their voucher.'),
            'vouchercode_title'=>$this->l('Voucher code'),
            'vouchercode_desc'=>$this->l('Voucher code prefix. It must be at least 3 letters long. Prefix voucher code will be used in the first part of the coupon code, which the user will use to get a discount'),
            'tax_title'=>$this->l('Tax'),
            'tax1'=>$this->l('Tax Excluded'),
            'tax2'=>$this->l('Tax Included'),

            'isminamount'=>$this->l('Minimum checkout'),
            'select_cat_title'=>$this->l('Select categories'),
            'select_cat_desc'=>$this->l('Check all box(es) of categories to which the discount is to be applied. No categories checked will apply the voucher on all of them.'),
            'sdvvalid_title'=>$this->l('Voucher validity'),
            'sdvvalid_desc'=>$this->l('Voucher term of validity in days'),
            'highlight_title'=>$this->l('Highlight'),
            'highlight_desc'=>$this->l('If the voucher is not yet in the cart, it will be displayed in the cart summary.'),
            'cumulativeother_title'=>$this->l('Cumulative with others vouchers'),
            'cumulativereduc_title'=>$this->l('Cumulative with price reductions'),

            'save_title'=>$this->l('Save'),

        );

        return $obj_loyality->voucherloyality($data);
    }



    private function _pointsactions(){
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();


        $data = array(
            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),


            'title_main'=>$this->l('Loyalty Points per Action'),

            'loyality_title'=>$this->l('Loyalty Points per Action'),

            'loyality_points' =>$this->getAvailablePoints(),



            'update_title'=>$this->l('Update Settings'),
        );

        return $obj_loyality->pointsactions($data);
    }

    private function _loyaltymessages(){

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();


        $data = array(
            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'available_points' => $this->getAvailablePoints(),

            'title_main'=>$this->l('Messages for Actions'),

            'label1'=>$this->l('Message'),
            'label2'=>$this->l('before'),

            'desc1'=>$this->l('Available data-tags:'),
            'desc2'=>$this->l('If you do not want see messsage - just leave this field empty'),




            'update_title'=>$this->l('Update Settings'),
        );

        return $obj_loyality->loyaltymessages($data);

    }

    private function _loyaltymy(){

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();

        $data = array(
            'prefix'=>$this->getLoyalityPrefix(),

            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'title'=>$this->l('My account - Loyalty Program'),


            'my_account'=> $this->l('My Loaylty Points per Page in the My account'),

            'd_eff_loyalty_val'=>$this->_effects(array('value' => 'd_eff_loyalty_my'.$this->getLoyalityPrefix())),

            'd_eff_loyalty_my_title'=>$this->l('Display effect in the My account'),

            'update_title'=>$this->l('Update Settings'),
        );
        return $obj_loyality->loyaltymy($data);

    }


    private function _loyaltyemails(){

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();

        $data = array(

            'prefix'=>$this->getLoyalityPrefix(),

            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'title'=>$this->l('Loyalty program emails settings'),
            'noti_title'=>$this->l('E-mail notification:'),

            'update_title'=>$this->l('Update Settings'),

        );

        return $obj_loyality->loyaltyemails($data);
    }


    private function _loyaltyemailssubjects(){

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();

        $data = array(
            'prefix'=>$this->getLoyalityPrefix(),

            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'title'=>$this->l('Emails subjects settings'),
            'yes_title' =>$this->l('Yes'),
            'no_title'=>$this->l('No'),

            'em_desc1'=>$this->l('You can customize the subject of the e-mail here.'),
            'em_desc2'=>$this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the'),
            'em_desc3'=>$this->l(' folder inside the'),
            'em_desc4'=>$this->l('module folder, for each language, both the text and the HTML version each time. '),
            'em_desc5'=>$this->l('FTP Location'),

            'is_loyaltyvouch'=>$this->l('Enable or Disable Send voucher with discount by email, when customer transform points into voucher'),
            'loyaltyvouch'=>$this->l('Subject of the email - Send voucher with discount by email, when customer transform points into voucher'),


            'is_points'=>$this->l('Enable or Disable Sending a email with the number of points each time when user receives points'),
            'points'=>$this->l('Subject of the email - Sending a email with the number of points each time when user receives points'),



            'update_title'=>$this->l('Update Settings'),
        );
        return $obj_loyality->loyaltyemailssubjects($data);

    }


    public function setLoyalitySettings(){


        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;

        $prefix = $this->getLoyalityPrefix();

        $smarty->assign($this->name.'loyality_on'.$prefix, Configuration::get($this->name.'loyality_on'.$prefix));

        $available_points = $this->getAvailablePoints();
        foreach($available_points as $k => $item){

            $smarty->assign($this->name.$k.'_points'.$prefix, '<b>'.Configuration::get($this->name.$k.'_points').'</b>');
            $smarty->assign($this->name.$k.'_status'.$prefix, Configuration::get($this->name.$k.'_status'));
            $smarty->assign($this->name.$k.'_times'.$prefix, (int)Configuration::get($this->name.$k .'_times'));
            $smarty->assign($this->name.$k.'_description'.$prefix, Configuration::get($this->name.$k.'description_'.$this->getIdLang()));


            $smarty->assign($this->name.$k.'_timesu'.$prefix, 0);
        }

        $smarty->assign($this->name.'is_productmsg'.$prefix, $this->l('Reward points apply only to purchased products'));

        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/loyalityblog.class.php');
        $obj_loyality = new loyalityblog();

        $times_array = $obj_loyality->countAllTimes(array('id_customer'=>$id_customer));
        foreach($times_array as $item){
            $type = $item['type'];
            $count = $item['count'];
            $smarty->assign($this->name.$type.'_timesu'.$prefix, $count);
        }




    }

    public function getAvailablePoints(){

        $convert_msg = $this->l('Your can convert [points] points in your account into a voucher for a future purchase.');
        $appear_points_post = $this->l('Points will be appear in your account, when administrator has approved your post');
        $appear_points_comment = $this->l('Points will be appear in your account, when administrator has approved your comment');

        return array(
            'loyality_order'=>array('name'=>$this->l('Customer has made order'),'points'=>20,'count_times'=>1000, 'desc_points'=>$this->l('Buying this product you will collect [points] points with our loyalty program.').' '.$convert_msg), // ok
            'loyality_registration'=>array('name'=>$this->l('Customer has made registration in the shop'),'points'=>15, 'desc_points'=>$this->l('Registering in our store you will collect [points] points with our loyalty program.').' '.$convert_msg), //ok

            'loyality_add_blog_post'=>array('name'=>$this->l('Customer has add blog post'),'points'=>10,'is_product'=>0,'count_times'=>20,'desc_points'=>$this->l('Add blog post you will collect [points] points with our loyalty program.').' '.$convert_msg.' '.$appear_points_post),//ok
            'loyality_add_blog_post_logo' => array('name'=>$this->l('Customer has upload logo for blog post'),'points'=>1,'is_product'=>0,'count_times'=>20,'desc_points'=>$this->l('Upload logo for blog post you will collect [points] points with our loyalty program.').' '.$convert_msg),//ok*/
            'loyality_like_blog_post'=>array('name'=>$this->l('Customer click on the like button for blog post'),'points'=>1,'is_product'=>1,'count_times'=>3,'desc_points'=>$this->l('Click on the like button for blog post you will collect [points] points with our loyalty program.').' '.$convert_msg), //ok

            'loyality_add_blog_comment'=>array('name'=>$this->l('Customer has add blog comment'),'points'=>10,'is_product'=>0,'count_times'=>10,'desc_points'=>$this->l('Add blog comment you will collect [points] points with our loyalty program.').' '.$convert_msg.' '.$appear_points_comment),//ok

            'loyality_user_my_avatar' => array('name'=>$this->l('Customer upload own avatar'),'points'=>5,'is_product'=>0,'desc_points'=>$this->l('Upload own avatar you will collect [points] points with our loyalty program.').' '.$convert_msg), // ok
            'loyality_user_my_show' => array('name'=>$this->l('Customer select option \'Show my profile on the site\' in my account - for avatar settings'),'points'=>2,'is_product'=>0,'desc_points'=>$this->l('Select option [Show my profile on the site] you will collect [points] points with our loyalty program.').' '.$convert_msg), // ok

        );
    }

    public function psvkform16($data_in){
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/postshelp.class.php');
        $postshelp = new postshelp();

        $data = array(
            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'translate'=>$data_in,
            'yes_title'=>$this->l('Yes'),
            'no_title'=>$this->l('No'),

            'update_title'=>$this->l('Update Settings'),
        );
        return $postshelp->psvkform16($data);
    }

    public function pstwitterform16($data_in){
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/postshelp.class.php');
        $postshelp = new postshelp();

        $data = array(
            'table' => $this->table,
            'this'=>$this,
            'identifier' => $this->identifier,
            'tab' => $this->tab,
            'uri' => $this->getPathUri(),

            'translate'=>$data_in,
            'yes_title'=>$this->l('Yes'),
            'no_title'=>$this->l('No'),

            'update_title'=>$this->l('Update Settings'),
        );
        return $postshelp->pstwitterform16($data);
    }


    private function _autoposts16(){
        #### posts api ###
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/postshelp.class.php');
        $postshelp = new postshelp();

        return $postshelp->postsSettings(array('translate'=>$this->_translate));
        #### posts api ###
    }


    private function _faqblockblog16(){
        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();



        return $_html;
    }

    private function _blogsettings16($data = null){
        $this->_data_translate_custom['_blogsettings16_seo_url'] = $this->l('SEO & URL Rewriting');
        $this->_data_translate_custom['_blogsettings16_main_set'] = $this->l('Main settings');
        $this->_data_translate_custom['_blogsettings16_cat_set'] = $this->l('Categories settings');
        $this->_data_translate_custom['_blogsettings16_posts_set'] = $this->l('Posts settings');
        $this->_data_translate_custom['_blogsettings16_com_set'] = $this->l('Comments settings');
        $this->_data_translate_custom['_blogsettings16_f_com'] = $this->l('Facebook Comments settings');
        $this->_data_translate_custom['_blogsettings16_my_acc_set'] = $this->l('My account settings');
        $this->_data_translate_custom['_blogsettings16_slider_set'] = $this->l('Slider settings');
        $this->_data_translate_custom['_blogsettings16_gal_set'] = $this->l('Gallery settings');
        $this->_data_translate_custom['_blogsettings16_blog_auth'] = $this->l('Blog Author settings');
        $this->_data_translate_custom['_blogsettings16_blocks_set'] = $this->l('Blocks settings');
        $this->_data_translate_custom['_blogsettings16_pos_blocks'] = $this->l('Positions Blocks');
        $this->_data_translate_custom['_blogsettings16_sid_design'] = $this->l('Blog pages design (Sidebars/Layouts)');
        $this->_data_translate_custom['_blogsettings16_em_set'] = $this->l('Email settings');
        $this->_data_translate_custom['_blogsettings16_rss_feed'] = $this->l('RSS Feed');
        $this->_data_translate_custom['_blogsettings16_sit'] = $this->l('Sitemap');
        $this->_data_translate_custom['_blogsettings16_imp_wordpress'] = $this->l('Import Wordpress XML');
        $this->_data_translate_custom['_blogsettings16_name_page'] = $this->l('Name of the page');


        $this->_data_translate_custom['_blogsettings16_name_pos'] = $this->l('Name of the position');

        $this->_data_translate_custom['_blogsettings16_enable'] = $this->l('Enable');
        $this->_data_translate_custom['_blogsettings16_disable'] = $this->l('Disable');

        $this->_data_translate_custom['_blogsettings16_mobile'] = $this->l('Mobile');
        $this->_data_translate_custom['_blogsettings16_desktop'] = $this->l('Desktop');



        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();




        return $_html;
    }


    private function _gallerysettings(){

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $data_url = $obj_blog->getSEOURLs();
        $gallery_url = $data_url['gallery_url'];


        $txt1 = '';
        $this->_data_translate_custom['_gallerysettings_txt_fr_url'] = $this->l('Blog Gallery page');
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_txt.phtml');


        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Gallery settings'),
                    'icon' => 'fa fa-image fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable gallery'),
                        'name' => 'gallery_on',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of Images per page'),
                        'name' => 'gallery_per_page',
                        'id' => 'gallery_per_page',
                        'lang' => FALSE,
                      ),


                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image Width on the Gallery page'),
                        'name' => 'gallery_width',
                        'value' => (int)Configuration::get($this->name.'gallery_width'),
                        'desc'=>$txt1,
                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image Height on the Gallery page'),
                        'name' => 'gallery_height',
                        'value' => (int)Configuration::get($this->name.'gallery_height'),
                        'desc'=>$txt1,
                    ),


                    array(
                        'type' => 'select',
                        'label' => $this->l('Gallery slideshow effect'),
                        'name' => 'slider_effect',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'default',
                                    'name' =>  'Default'
                                ),

                                array(
                                    'id' => 'dark_square',
                                    'name' => 'Dark Square',
                                ),

                                array(
                                    'id' => 'dark_rounded',
                                    'name' => 'Dark Rounded',
                                ),

                                array(
                                    'id' => 'light_square',
                                    'name' => 'Light Square',
                                ),

                                array(
                                    'id' => 'light_rounded',
                                    'name' => 'Light Rounded',
                                ),



                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),



                    array(
                        'type' => 'switch',
                        'label' => $this->l('Auto play gallery slideshow'),
                        'name' => 'gallery_autoplay',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'custom_ms',
                        'label' => $this->l('Slideshow speed'),
                        'name' => 'gallery_speed',
                        'value' => (int)Configuration::get($this->name.'gallery_speed'),
                    ),


                ),

            ),


        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'gallerysettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesGallerySettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesGallerySettings(){

        $data_config = array(
            'gallery_on' => (int)Configuration::get($this->name.'gallery_on'),
            'gallery_per_page'=> (int)Configuration::get($this->name.'gallery_per_page'),
            'gallery_autoplay'=> (int)Configuration::get($this->name.'gallery_autoplay'),
            'slider_effect'=> Configuration::get($this->name.'slider_effect'),

        );

        return $data_config;

    }


    private function _myaccountsettings(){

        $cookie = $this->context->cookie;
        $id_lang =  $cookie->id_lang;
        $blog_ids_groups = Configuration::get($this->name.'blog_ids_groups');
        $blog_ids_groups = $blog_ids_groups?explode(",",$blog_ids_groups):array();



        $ids_categories = Configuration::get($this->name.'ids_categories');
        $ids_categories = $ids_categories?explode(",",$ids_categories):array();


        require_once(_PS_MODULE_DIR_ . ''.$this->name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $data_url = $obj_blog->getSEOURLs();
        $category_url =$data_url['category_url'];


        // is cloud ?? //
//        if(defined('_PS_HOST_MODE_')){
//            $logo_img_path_ralated = '../modules/'.$this->name.'/upload/';
//        } else {
//            $logo_img_path_ralated = '../upload/'.$this->name.'/';
//        }
        $logo_img_path_ralated = '../modules/'.$this->name.'/upload/';
        // is cloud ?? //

        $related_categories  = $obj_blog->getCategories(array('admin'=>1));


        if($obj_blog->isURLRewriting()){
            $is_rewrite = 1;
        } else {
            $is_rewrite = 0;
        }



        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('My account -> My Blog Posts settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable My account -> My Blog Posts functional'),
                        'name' => 'myblogposts_on',
                        'desc' => $this->l('Enable or Disable My account -> My Blog Posts functional'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable for customers possibility add Blog Posts in the My account'),
                        'name' => 'is_addmyblogposts',
                        'desc' => $this->l('Enable or Disable for customers possibility add Blog Posts in the My account'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable for customers possibility edit Blog Posts in the My account'),
                        'name' => 'is_editmyblogposts',
                        'desc' => $this->l('Enable or Disable for customers possibility edit Blog Posts in the My account'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable for customers possibility delete Blog Posts in the My account'),
                        'name' => 'is_delmyblogposts',
                        'desc' => $this->l('Enable or Disable for customers possibility delete Blog Posts in the My account'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),


                    array(
                        'type' => 'group_association_author',
                        'label' => $this->l('Customer groups'),
                        'name' => 'blog_ids_groups',
                        'values'=>Group::getGroups($id_lang),
                        'selected_data'=>$blog_ids_groups,
                        'required' => TRUE,
                        'desc' => $this->l('Select customers group who can submit posts'),
                    ),

                    array(
                        'type' => 'related_categories_my',
                        'label' => $this->l('Select categories'),
                        'name' => 'ids_categories_my',
                        'values'=>$related_categories['categories'],
                        'selected_data'=>$ids_categories,
                        'required' => false,
                        'name_field_custom'=>'ids_categories',
                        'logo_img_path' => $logo_img_path_ralated,
                        'is_post'=>0,

                        'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,
                        'item_url'=>$category_url,
                        'is_rewrite' => $is_rewrite,
                        'desc'=>$this->l('Select categories in which customer can submit blog posts')

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog posts per page in the My account -> My Blog Posts'),
                        'name' => 'perpage_myposts',
                        'id' => 'perpage_myposts',
                        'lang' => FALSE,

                    ),

                    array(
                        'type' => 'blog_display_effect',
                        'label' => $this->l('Display effect for Blog posts in the My account -> My Blog Posts'),
                        'name' => 'd_eff_shopmyposts',
                        'id' => 'd_eff_shopmyposts',
                        'lang' => FALSE,
                        'value' => $this->_effects(array('value' => 'd_eff_shopmyposts'))

                    ),


                ),

            ),
        );

        $fields_form2 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('My account -> My Blog Comments settings'),
                    'icon' => 'fa fa-comments-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable My account -> My Blog Comments functional'),
                        'name' => 'myblogcom_on',
                        'desc' => $this->l('Enable or Disable My account -> My Blog Comments functional'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),



                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable for customers possibility edit Blog Comments in the My account'),
                        'name' => 'is_editmyblogcom',
                        'desc' => $this->l('Enable or Disable for customers possibility edit Blog Comments in the My account'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable for customers possibility delete Blog Comments in the My account'),
                        'name' => 'is_delmyblogcom',
                        'desc' => $this->l('Enable or Disable for customers possibility delete Blog Comments in the My account'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog Comments per page in the My account -> My Blog Comments'),
                        'name' => 'perpage_mycom',
                        'id' => 'perpage_mycom',
                        'lang' => FALSE,

                    ),

                    array(
                        'type' => 'blog_display_effect',
                        'label' => $this->l('Display effect for Blog Comments in the My account -> My Blog Comments'),
                        'name' => 'd_eff_shopmycom',
                        'id' => 'd_eff_shopmycom',
                        'lang' => FALSE,
                        'value' => $this->_effects(array('value' => 'd_eff_shopmycom'))

                    ),


                ),

            ),
        );


        $fields_form6 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'myaccountsettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesMyaccountSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );



        return  $helper->generateForm(array($fields_form,$fields_form2,$fields_form6));
    }

    public function getConfigFieldsValuesMyaccountSettings(){

        $data_config = array(

            'myblogposts_on'=>Configuration::get($this->name.'myblogposts_on'),
            'is_addmyblogposts'=>Configuration::get($this->name.'is_addmyblogposts'),
            'is_editmyblogposts'=>Configuration::get($this->name.'is_editmyblogposts'),
            'is_delmyblogposts'=>Configuration::get($this->name.'is_delmyblogposts'),
            'd_eff_shopmyposts'=>Configuration::get($this->name.'d_eff_shopmyposts'),
            'perpage_myposts'=>Configuration::get($this->name.'perpage_myposts'),


            'myblogcom_on'=>Configuration::get($this->name.'myblogcom_on'),
            'is_editmyblogcom'=>Configuration::get($this->name.'is_editmyblogcom'),
            'is_delmyblogcom'=>Configuration::get($this->name.'is_delmyblogcom'),
            'd_eff_shopmycom'=>Configuration::get($this->name.'d_eff_shopmycom'),
            'perpage_mycom'=>Configuration::get($this->name.'perpage_mycom'),

        );

        return $data_config;
    }

    private function _slidersettings(){

        $text_17 = '';
        $this->_data_translate_custom['_slidersettings_txt_read_step'] = $this->l('Read Step 16 "How to add blog posts with images in the slider?" in the');
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_txt.phtml');

        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Slider settings'),
                    'icon' => 'fa fa-puzzle-piece fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'select',
                        'label' => $this->l('Type of the slider'),
                        'desc' => $this->l('Just play with types').$text_17,
                        'name' => 'slider_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 1,
                                    'name' => 1
                                ),

                                array(
                                    'id' => 2,
                                    'name' => 2,
                                ),

                                array(
                                    'id' => 3,
                                    'name' => 3,
                                ),
                                array(
                                    'id' => 4,
                                    'name' => 4
                                ),

                                array(
                                    'id' => 5,
                                    'name' => 5,
                                ),

                                array(
                                    'id' => 6,
                                    'name' => 6,
                                ),
                                array(
                                    'id' => 7,
                                    'name' => 7
                                ),

                                array(
                                    'id' => 8,
                                    'name' => 8,
                                ),

                                array(
                                    'id' => 9,
                                    'name' => 9,
                                ),

                                array(
                                    'id' => 10,
                                    'name' => 10
                                ),

                                array(
                                    'id' => 11,
                                    'name' =>11,
                                ),

                                array(
                                    'id' => 12,
                                    'name' => 12,
                                ),
                                array(
                                    'id' => 13,
                                    'name' => 13
                                ),

                                array(
                                    'id' => 14,
                                    'name' => 14,
                                ),

                                array(
                                    'id' => 15,
                                    'name' => 15,
                                ),
                                array(
                                    'id' => 16,
                                    'name' => 16
                                ),


                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Slider on homepage'),
                        'name' => 'slider_h_on',
                        'desc' => $this->l('The slide will be displayed in the center column of the home page of your shop.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Slider on blogpage'),
                        'name' => 'slider_b_on',
                        'desc' => $this->l('The slide will be displayed in the top of first page articles list of blog.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of slide to display'),
                        'name' => 'slide_limit',
                        'id' => 'slide_limit',
                        'lang' => FALSE,

                    ),


                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Slider Width for Desktop devices'),
                        'name' => 'slider_widthfancytr',
                        'value' => (int)Configuration::get($this->name.'slider_widthfancytr'),
                        'hint'=>$this->l('Enter width of the slider for Desktop devices'),

                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Slider Height for Desktop devices'),
                        'name' => 'slider_heightfancytr',
                        'value' => (int)Configuration::get($this->name.'slider_heightfancytr'),
                        'hint'=>$this->l('Enter height of the slider for Desktop devices'),

                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Slider Width for Mobile devices'),
                        'name' => 'slider_widthfancytr_mobile',
                        'value' => (int)Configuration::get($this->name.'slider_widthfancytr_mobile'),
                        'hint'=>$this->l('Enter width of the slider for Mobile devices'),

                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Slider Height for Mobile devices'),
                        'name' => 'slider_heightfancytr_mobile',
                        'value' => (int)Configuration::get($this->name.'slider_heightfancytr_mobile'),
                        'hint'=>$this->l('Enter height of the slider for Mobile devices'),

                    ),

                    array(
                        'type' => 'text_truncate',
                        'label' => $this->l('Title length'),
                        'name' => 'slider_titlefancytr',
                        'id' => 'slider_titlefancytr',
                        'lang' => FALSE,
                        'value' => (int)Configuration::get($this->name.'slider_titlefancytr'),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable back and next buttons in the slider'),
                        'name' => 'slider_pn_on',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),



                ),



            ),


        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'slidersettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesSliderSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesSliderSettings(){

        $data_config = array(
            'slider_h_on' => (int)Configuration::get($this->name.'slider_h_on'),
            'slider_b_on' => (int)Configuration::get($this->name.'slider_b_on'),
            'slider_type'=> Configuration::get($this->name.'slider_type'),
            'slide_limit'=> (int)Configuration::get($this->name.'slide_limit'),
            'slider_pn_on'=> (int)Configuration::get($this->name.'slider_pn_on'),

        );

        return $data_config;

    }

    private function _avasettings(){

        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog Author settings'),
                    'icon' => 'fa fa-users fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Blog Author Avatar functional'),
                        'name' => 'ava_on',
                        'desc' => $this->l('Enable or Disable Blog Author Avatar functional'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Authors per page in the list view on the All Authors page'),
                        'name' => 'perpage_authors',
                        'id' => 'perpage_authors',
                        'lang' => FALSE,

                    ),

                    array(
                        'type' => 'blog_display_effect',
                        'label' => $this->l('Display effect for Blog Authors in the list view on the All Authors page'),
                        'name' => 'd_eff_shopu',
                        'id' => 'd_eff_shopu',
                        'lang' => FALSE,
                        'value' => $this->_effects(array('value' => 'd_eff_shopu'))

                    ),






                ),



            ),


        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'avasettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesAvaSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesAvaSettings(){

        $data_config = array(
            'ava_on' => (int)Configuration::get($this->name.'ava_on'),
            'perpage_authors'=> (int)Configuration::get($this->name.'perpage_authors'),

        );

        return $data_config;

    }

    private function _wordpress(){


            $is_demo = '';

        $cookie = $this->context->cookie;
        $id_lang = (int)$cookie->id_lang;

        $text_17 = '';
        $this->_data_translate_custom['_wordpress_txt_read_step'] = $this->l('Read Step 11 "How to get Wordpress XML file and Import Wordpress XML file?" in the');

        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_txt.phtml');


        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Import Wordpress XML'),
                    'icon' => 'fa fa-upload fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'post_image_custom',
                        'label' => $this->l('Upload file'),
                        'name' => 'xml_file',
                        'required' => TRUE,
                        'desc' => $text_17.$this->l('Allow format *.xml'),
                        'is_demo' => $is_demo,
                        'max_upload_info' => ini_get('upload_max_filesize'),
                        'post_max_size'=>ini_get('post_max_size'),
                    ),

                    array(
                        'type' => 'select_language',
                        'label' => $this->l('Select language'),
                        'name' => 'lang_association',
                        'values'=>Language::getLanguages(false),
                        'required' => TRUE,
                    ),

                    array(
                        'type' => 'cms_pages',
                        'label' => $this->l('Select Shop'),
                        'name' => 'cat_shop_association',
                        'values'=>Shop::getShops(),
                        'required' => TRUE,
                    ),

                    array(
                        'type' => 'group_association',
                        'label' => $this->l('Select Group permissions'),
                        'name' => 'group_association',
                        'values'=>Group::getGroups($id_lang),
                        'required' => TRUE,
                    ),

                ),
            ),
        );

        $fields_form1 = array(
            'form' => array(
                'submit' => array(
                    'title' => $this->l('Import XML File'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'wordpresssettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesWordpressXMLSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesWordpressXMLSettings(){

        $data_config = array(
            'mail' => Configuration::get($this->name.'mail'),
        );

        return $data_config;
    }




    private function _sitemap(){
        $sitemap_url = '';

        $this->_data_translate_custom['_sitemap_sit'] = $this->l('Sitemap');
        $this->_data_translate_custom['_sitemap_your_cron'] = $this->l('Your CRON URL to call');
        $this->_data_translate_custom['_sitemap_reg_sit'] = $this->l('Regenerate Google sitemap');
        $this->_data_translate_custom['_sitemap_sit_for_shop'] = $this->l('Sitemap for shop ');
        $this->_data_translate_custom['_sitemap_to_decl'] = $this->l('To declare blog sitemap xml, add this line at the end of your robots.txt file');

        $this->_data_translate_custom['_sitemap_use_main'] = $this->l('Use main sitemap to regroup all items related with blog');
        $this->_data_translate_custom['_sitemap_all_sitemaps'] = $this->l('All sitemaps can be crawled individually');

        $this->_data_translate_custom['_sitemap_way'] = $this->l('Way');

        $this->_data_translate_custom['_sitemap_main_sitemap'] = $this->l('Main sitemap for shop');
        $this->_data_translate_custom['_sitemap_incl_separated'] = $this->l('include all Separated sitemaps');
        $this->_data_translate_custom['_sitemap_sitemap_not_exists'] = $this->l('Sitemaps not exists. Try press button');

        $this->_data_translate_custom['_sitemap_sep_sitemap'] = $this->l('Separated sitemaps for shop');





        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();





        $_html .= $this->_cronhelp(array('url'=>$sitemap_url));



        $fields_form = array(
            'form'=> array(

                'legend' => array(
                    'title' => $this->l('Sitemap configuration'),
                    'icon' => 'fa fa-sitemap fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Sitemap'),
                        'name' => 'sitemapon',
                         'desc'=>$this->l('Here you can disable generation sitemap by CRON'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Pages to be included in sitemap'),
                        'name' => 'sitemap_pages',
                        'hint' => $this->l('Pages to be included in sitemap'),
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'pages_main_blog',
                                    'name' => $this->l('Main Blog page'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'pages_blog_allcat',
                                    'name' => $this->l('Blog All Categories page'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'pages_blog_cat',
                                    'name' => $this->l('Blog Category pages'),
                                    'val' => '1'
                                ),


                                array(
                                    'id' => 'pages_blog_post',
                                    'name' => $this->l('Blog Post pages'),
                                    'val' => '1'
                                ),


                                array(
                                    'id' => 'pages_authors',
                                    'name' => $this->l('Blog All Authors page'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'pages_author',
                                    'name' => $this->l('Blog Author page'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'pages_allcom',
                                    'name' => $this->l('Blog All Comments page'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'pages_alltags',
                                    'name' => $this->l('Blog All Tags page'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'pages_tag',
                                    'name' => $this->l('Blog Tag pages'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'pages_gallery',
                                    'name' => $this->l('Blog Gallery page'),
                                    'val' => '1'
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),

                   /* array(
                        'type' => 'sitemap_limit',
                        'label' => $this->l('Limit url number per xml file'),
                        'name' => 'sitemap_limit',
                        'value' => (int)Configuration::get($this->name.'sitemap_limit')


                    ),*/


                ),

            ),


        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'sitemapvalsettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesSitemapSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1)).$_html;
    }

    public function getConfigFieldsValuesSitemapSettings(){



        $data_config = array(
            'sitemapon' => Configuration::get($this->name.'sitemapon'),


            'pages_main_blog' => Configuration::get($this->name.'pages_main_blog'),

            'pages_blog_allcat' => Configuration::get($this->name.'pages_blog_allcat'),
            'pages_blog_cat' => Configuration::get($this->name.'pages_blog_cat'),


            'pages_blog_post' => Configuration::get($this->name.'pages_blog_post'),

            'pages_authors' => Configuration::get($this->name.'pages_authors'),
            'pages_author' => Configuration::get($this->name.'pages_author'),
            'pages_allcom' => Configuration::get($this->name.'pages_allcom'),

            'pages_alltags' => Configuration::get($this->name.'pages_alltags'),
            'pages_tag' => Configuration::get($this->name.'pages_tag'),

            'pages_gallery' => Configuration::get($this->name.'pages_gallery'),
        );

        return $data_config;

    }




    private function _cronhelp($data = null){
        $url_cron = isset($data['url'])?$data['url']:'';
        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();



        return $_html;
    }


    private function _emailsubjects16_desc($data){

        $desc = isset($data['desc'])?$data['desc']:'';
        $name_template = isset($data['name'])?$data['name']:'';

        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();


        return $_html;
    }

    private function _emailsettings(){
        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Email settings'),
                    'icon' => 'fa fa-envelope-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('E-mail notification:'),
                        'name' => 'noti',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'noti')
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Admin email:'),
                        'name' => 'mail',
                        'id' => 'mail',
                        'lang' => FALSE,

                    ),



                ),



            ),


        );


        $fields_form1 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Emails subjects settings '),
                    'icon' => 'fa fa-envelope-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable New Comment from Your Blog email'),
                        'name' => 'is_newblogc',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable New Comment from Your Blog email'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Email New Comment from Your Blog subject'),
                        'name' => 'newblogc',
                        'id' => 'newblogc',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'comment','desc'=>$this->l('FTP Location')))

                    ),




                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Response on the Blog Comment email'),
                        'name' => 'is_responseblogc',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable Response on the Blog Comment email'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Email Response on the Blog Comment subject'),
                        'name' => 'responseblogc',
                        'id' => 'responseblogc',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'response-blogcomment','desc'=>$this->l('FTP Location')))

                    ),



                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email, when customer has delete own blog comment'),
                        'name' => 'is_deleteblogcomcust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email, when customer has delete own blog comment'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email, when customer has delete own blog comment subject'),
                        'name' => 'deleteblogcomcust',
                        'id' => 'deleteblogcomcust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'admin-customer-deleteblogcomment','desc'=>$this->l('FTP Location')))

                    ),




                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email, when customer edited own blog comment'),
                        'name' => 'is_editblogcomcust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email, when customer edited own blog comment'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email, when customer edited own blog comment subject'),
                        'name' => 'editblogcomcust',
                        'id' => 'editblogcomcust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'admin-customer-editblogcomment','desc'=>$this->l('FTP Location')))

                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email to customer, when admin reviewed edited customer blog comment'),
                        'name' => 'is_editblogcomtocust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email to customer, when admin reviewed edited customer blog comment'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email to customer, when admin reviewed edited customer blog comment subject'),
                        'name' => 'editblogcomtocust',
                        'id' => 'editblogcomtocust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'customer-approveeditblogcomment','desc'=>$this->l('FTP Location')))

                    ),






                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email, when customer has delete own blog post'),
                        'name' => 'is_deleteblogpostcust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email, when customer has delete own blog post'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email, when customer has delete own blog post subject'),
                        'name' => 'deleteblogpostcust',
                        'id' => 'deleteblogpostcust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'admin-customer-deleteblogpost','desc'=>$this->l('FTP Location')))

                    ),




                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email, when customer edited own blog post'),
                        'name' => 'is_editblogpostcust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email, when customer edited own blog post'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email, when customer edited own blog post subject'),
                        'name' => 'editblogpostcust',
                        'id' => 'editblogpostcust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'admin-customer-editblogpost','desc'=>$this->l('FTP Location')))

                    ),



                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email, when customer added own blog post'),
                        'name' => 'is_addblogpostcust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email, when customer added own blog post'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email, when customer added own blog post subject'),
                        'name' => 'addblogpostcust',
                        'id' => 'addblogpostcust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'admin-customer-addblogpost','desc'=>$this->l('FTP Location')))

                    ),




                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email to customer, when admin reviewed edited customer blog post'),
                        'name' => 'is_editblogposttocust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email to customer, when admin reviewed edited customer blog post'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email to customer, when admin reviewed edited customer blog post subject'),
                        'name' => 'editblogposttocust',
                        'id' => 'editblogposttocust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'customer-approveeditblogpost','desc'=>$this->l('FTP Location')))

                    ),



                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email to customer, when admin reviewed added customer blog post'),
                        'name' => 'is_addblogposttocust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email to customer, when admin reviewed added customer blog post'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email to customer, when admin reviewed added customer blog post subject'),
                        'name' => 'addblogposttocust',
                        'id' => 'addblogposttocust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'customer-approveaddblogpost','desc'=>$this->l('FTP Location')))

                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email to customer, when admin has changed status of the customer'),
                        'name' => 'is_changestatustocust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email to customer, when admin has changed status of the customer'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email to customer, when admin has changed status of the customer subject'),
                        'name' => 'changestatustocust',
                        'id' => 'changestatustocust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'customer-changestatustocust','desc'=>$this->l('FTP Location')))

                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable notification email to customer, when admin has deleted all customers blog posts'),
                        'name' => 'is_deleteallpoststocust',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable notification email to customer, when admin has deleted all customers blog posts'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Notification email to customer, when admin has deleted all customers blog posts'),
                        'name' => 'deleteallpoststocust',
                        'id' => 'deleteallpoststocust',
                        'lang' => TRUE,
                        'desc' => $this->l('You can customize the subject of the e-mail here.').' '
                            . $this->l('If you wish to customize the e-mail message itself, you will need to manually edit the files in the')
                            .'  "mails" '
                            . $this->l(' folder inside the')
                            .' "'.$this->name.'" '
                            .$this->l('module folder, for each language, both the text and the HTML version each time. ')
                            .$this->_emailsubjects16_desc(array('name'=>'customer-deleteallpoststocust','desc'=>$this->l('FTP Location')))

                    ),



                ),



            ),


        );

        $fields_form2 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'emailsettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesEmailsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1,$fields_form2));
    }

    public function getConfigFieldsValuesEmailsSettings(){


        $languages = Language::getLanguages(false);
        $fields_newblogc = array();
        $fields_responseblogc = array();

        $fields_deleteblogcomcust = array();
        $fields_editblogcomcust = array();
        $fields_editblogcomtocust = array();

        $fields_deleteblogpostcust = array();
        $fields_editblogpostcust = array();
        $fields_addblogpostcust = array();
        $fields_editblogposttocust = array();
        $fields_addblogposttocust = array();

        $fields_changestatustocust = array();
        $fields_deleteallpoststocust = array();


        foreach ($languages as $lang)
        {
            $fields_newblogc[$lang['id_lang']] =  Configuration::get($this->name.'newblogc_'.$lang['id_lang']);
            $fields_responseblogc[$lang['id_lang']] =  Configuration::get($this->name.'responseblogc_'.$lang['id_lang']);

            $fields_deleteblogcomcust[$lang['id_lang']] =  Configuration::get($this->name.'deleteblogcomcust_'.$lang['id_lang']);
            $fields_editblogcomcust[$lang['id_lang']] =  Configuration::get($this->name.'editblogcomcust_'.$lang['id_lang']);
            $fields_editblogcomtocust[$lang['id_lang']] =  Configuration::get($this->name.'editblogcomtocust_'.$lang['id_lang']);


            $fields_deleteblogpostcust[$lang['id_lang']] =  Configuration::get($this->name.'deleteblogpostcust_'.$lang['id_lang']);
            $fields_editblogpostcust[$lang['id_lang']] =  Configuration::get($this->name.'editblogpostcust_'.$lang['id_lang']);
            $fields_addblogpostcust[$lang['id_lang']] =  Configuration::get($this->name.'addblogpostcust_'.$lang['id_lang']);
            $fields_editblogposttocust[$lang['id_lang']] =  Configuration::get($this->name.'editblogposttocust_'.$lang['id_lang']);
            $fields_addblogposttocust[$lang['id_lang']] =  Configuration::get($this->name.'addblogposttocust_'.$lang['id_lang']);

            $fields_changestatustocust[$lang['id_lang']] =  Configuration::get($this->name.'changestatustocust_'.$lang['id_lang']);
            $fields_deleteallpoststocust[$lang['id_lang']] =  Configuration::get($this->name.'deleteallpoststocust_'.$lang['id_lang']);


        }

        $data_config = array(
            'mail' => Configuration::get($this->name.'mail'),

            'newblogc' => $fields_newblogc,
            'responseblogc' => $fields_responseblogc,

            'deleteblogcomcust' => $fields_deleteblogcomcust,
            'editblogcomcust' => $fields_editblogcomcust,
            'editblogcomtocust' => $fields_editblogcomtocust,

            'deleteblogpostcust' => $fields_deleteblogpostcust,
            'editblogpostcust' => $fields_editblogpostcust,
            'addblogpostcust' => $fields_addblogpostcust,
            'editblogposttocust' => $fields_editblogposttocust,
            'addblogposttocust' => $fields_addblogposttocust,

            'changestatustocust' => $fields_changestatustocust,
            'deleteallpoststocust' => $fields_deleteallpoststocust,


            'is_newblogc' => Configuration::get($this->name.'is_newblogc'),
            'is_responseblogc' => Configuration::get($this->name.'is_responseblogc'),

            'is_deleteblogcomcust' => Configuration::get($this->name.'is_deleteblogcomcust'),
            'is_editblogcomcust' => Configuration::get($this->name.'is_editblogcomcust'),
            'is_editblogcomtocust'=> Configuration::get($this->name.'is_editblogcomtocust'),

            'is_deleteblogpostcust' => Configuration::get($this->name.'is_deleteblogpostcust'),
            'is_editblogpostcust' => Configuration::get($this->name.'is_editblogpostcust'),
            'is_addblogpostcust' => Configuration::get($this->name.'is_addblogpostcust'),
            'is_editblogposttocust'=> Configuration::get($this->name.'is_editblogposttocust'),
            'is_addblogposttocust'=> Configuration::get($this->name.'is_addblogposttocust'),

            'is_changestatustocust'=> Configuration::get($this->name.'is_changestatustocust'),
            'is_deleteallpoststocust'=> Configuration::get($this->name.'is_deleteallpoststocust'),



        );

        return $data_config;
    }

    private function _rssfeed(){


        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();


        $fields_form = array(
            'form'=> array(

                'legend' => array(
                    'title' => $this->l('RSS Feed'),
                    'icon' => 'fa fa-rss fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable RSS Feed'),
                        'name' => 'rsson',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'custom_rss_ursl',
                        'label' => $this->l('RSS Feed Urls'),
                        'name' => 'rssurls',
                        'id' => 'rssurls',
                        'html'=>$_html

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Title of your RSS Feed'),
                        'name' => 'rssname',
                        'id' => 'rssname',
                        'lang' => TRUE,
                        //'required' => TRUE,
                        'size' => 50,
                        //'maxlength' => 50,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Description of your RSS Feed'),
                        'name' => 'rssdesc',
                        'id' => 'rssdesc',
                        'lang' => TRUE,
                        //'required' => TRUE,
                        'size' => 50,
                        //'maxlength' => 50,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of items in RSS Feed'),
                        'name' => 'number_rssitems',
                        'class' => ' fixed-width-sm',

                    ),


                ),



            ),


        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'rssfeedsettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesRssfeedSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesRssfeedSettings(){
        $languages = Language::getLanguages(false);
        $fields_rssname = array();
        $fields_rssdesc = array();

        foreach ($languages as $lang)
        {
            $fields_rssname[$lang['id_lang']] =  Configuration::get($this->name.'rssname_'.$lang['id_lang']);

            $fields_rssdesc[$lang['id_lang']] =  Configuration::get($this->name.'rssdesc_'.$lang['id_lang']);
        }


        $data_config = array(
            'rsson' => Configuration::get($this->name.'rsson'),
            'rssname' => $fields_rssname,
            'rssdesc' => $fields_rssdesc,
            'number_rssitems' => (int)Configuration::get($this->name.'number_rssitems'),

        );

        return $data_config;

    }


    private function _blockpositions(){
        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Positions Blocks'),
                    'icon' => 'fa fa-th fa-lg'
                ),
                'input' => array(

                    /*array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Left column'),
                        'name' => 'pos_left_col',
                        'hint' => $this->l('Blocks in the Left column'),
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'cat_left',
                                    'name' => $this->l('Blog Categories'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'posts_left',
                                    'name' => $this->l('Blog Posts recents'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'arch_left',
                                    'name' => $this->l('Block Archives'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'search_left',
                                    'name' => $this->l('Block Search'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'com_left',
                                    'name' => $this->l('Blog Last Comments'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'tag_left',
                                    'name' => $this->l('Blog Tags'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'author_left',
                                    'name' => $this->l('Blog Top Authors'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'gallery_left',
                                    'name' => $this->l('Blog Gallery'),
                                    'val' => '1'
                                ),



                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),



                    array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Right column'),
                        'name' => 'pos_right_col',
                        'hint' => $this->l('Blocks in the Right column'),
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'cat_right',
                                    'name' => $this->l('Blog Categories'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'posts_right',
                                    'name' => $this->l('Blog Posts recents'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'arch_right',
                                    'name' => $this->l('Block Archives'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'search_right',
                                    'name' => $this->l('Block Search'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'com_right',
                                    'name' => $this->l('Blog Last Comments'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'tag_right',
                                    'name' => $this->l('Blog Tags'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'author_right',
                                    'name' => $this->l('Blog Top Authors'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'gallery_right',
                                    'name' => $this->l('Blog Gallery'),
                                    'val' => '1'
                                ),


                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),


                    array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Footer'),
                        'name' => 'pos_footer_col',
                        'hint' => $this->l('Blocks in the Footer'),
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'cat_footer',
                                    'name' => $this->l('Blog Categories'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'posts_footer',
                                    'name' => $this->l('Blog Posts recents'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'arch_footer',
                                    'name' => $this->l('Block Archives'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'search_footer',
                                    'name' => $this->l('Block Search'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'com_footer',
                                    'name' => $this->l('Blog Last Comments'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'tag_footer',
                                    'name' => $this->l('Blog Tags'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'author_footer',
                                    'name' => $this->l('Blog Top Authors'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'gallery_footer',
                                    'name' => $this->l('Blog Gallery'),
                                    'val' => '1'
                                ),


                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),


                    array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Home page'),
                        'name' => 'pos_footer_col',
                        'hint' => $this->l('Blocks on the Home page'),

                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'block_last_home',
                                    'name' => $this->l('Blog Posts recents'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'authors_home',
                                    'name' => $this->l('Blog Top Authors'),
                                    'val' => '1'
                                ),

                                array(
                                    'id' => 'gallery_home',
                                    'name' => $this->l('Blog Gallery'),
                                    'val' => '1'
                                ),


                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),*/

                    array(
                        'type' => 'checkbox_custom_blocks_custom',
                        'label' => $this->l('Blocks in the CUSTOM HOOKS'),
                        'name' => 'pos_footer_col',
                        'hint' => $this->l('Blocks in the CUSTOM HOOKS'),
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'cat_custom_hook',
                                    'name' => $this->l('Blog Categories in CUSTOM HOOK (blogCategoriesSPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'mcat_custom_hook'),
                                    'site'=>Configuration::get($this->name.'scat_custom_hook'),
                                ),
                                array(
                                    'id' => 'posts_custom_hook',
                                    'name' => $this->l('Blog Posts in CUSTOM HOOK (blogPostsSPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'mposts_custom_hook'),
                                    'site'=>Configuration::get($this->name.'sposts_custom_hook'),
                                ),
                                array(
                                    'id' => 'comm_custom_hook',
                                    'name' => $this->l('Block Blog Comments in CUSTOM HOOK (blogCommentsSPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'mcomm_custom_hook'),
                                    'site'=>Configuration::get($this->name.'scomm_custom_hook'),
                                ),

                                array(
                                    'id' => 'search_custom_hook',
                                    'name' => $this->l('Block Blog Search in CUSTOM HOOK (blogSearchSPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'msearch_custom_hook'),
                                    'site'=>Configuration::get($this->name.'ssearch_custom_hook'),
                                ),
                                array(
                                    'id' => 'arch_custom_hook',
                                    'name' => $this->l('Block Blog Archives in CUSTOM HOOK (blogArchivesSPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'march_custom_hook'),
                                    'site'=>Configuration::get($this->name.'sarch_custom_hook'),
                                ),
                                array(
                                    'id' => 'tags_custom_hook',
                                    'name' => $this->l('Block Blog Tags in CUSTOM HOOK (blogTagsSPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'mtags_custom_hook'),
                                    'site'=>Configuration::get($this->name.'stags_custom_hook'),
                                ),
                                array(
                                    'id' => 'auth_custom_hook',
                                    'name' => $this->l('Block Blog Top Authors in CUSTOM HOOK (blogTopAuthorsSPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'mauth_custom_hook'),
                                    'site'=>Configuration::get($this->name.'sauth_custom_hook'),
                                ),
                                array(
                                    'id' => 'gallery_custom_hook',
                                    'name' => $this->l('Block Gallery in CUSTOM HOOK (blogGallerySPM)'),
                                    'val' => '1',
                                    'mobile'=>Configuration::get($this->name.'mgallery_custom_hook'),
                                    'site'=>Configuration::get($this->name.'sgallery_custom_hook'),
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),



                ),



            ),


        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'blockpositions';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesPositionsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesPositionsSettings(){

        $data_config = array(
            /*'cat_left' => Configuration::get($this->name.'cat_left'),
            'posts_left' => Configuration::get($this->name.'posts_left'),
            'arch_left' => Configuration::get($this->name.'arch_left'),
            'search_left' => Configuration::get($this->name.'search_left'),
            'com_left' => Configuration::get($this->name.'com_left'),
            'tag_left' => Configuration::get($this->name.'tag_left'),
            'author_left' => Configuration::get($this->name.'author_left'),
            'gallery_left' => Configuration::get($this->name.'gallery_left'),

            'cat_right' => Configuration::get($this->name.'cat_right'),
            'posts_right' => Configuration::get($this->name.'posts_right'),
            'arch_right' => Configuration::get($this->name.'arch_right'),
            'search_right' => Configuration::get($this->name.'search_right'),
            'com_right' => Configuration::get($this->name.'com_right'),
            'tag_right' => Configuration::get($this->name.'tag_right'),
            'author_right' => Configuration::get($this->name.'author_right'),
            'gallery_right' => Configuration::get($this->name.'gallery_right'),

            'cat_footer' => Configuration::get($this->name.'cat_footer'),
            'posts_footer' => Configuration::get($this->name.'posts_footer'),
            'arch_footer' => Configuration::get($this->name.'arch_footer'),
            'search_footer' => Configuration::get($this->name.'search_footer'),
            'com_footer' => Configuration::get($this->name.'com_footer'),
            'tag_footer' => Configuration::get($this->name.'tag_footer'),
            'author_footer' => Configuration::get($this->name.'author_footer'),
            'gallery_footer' => Configuration::get($this->name.'gallery_footer'),

            'block_last_home'=> Configuration::get($this->name.'block_last_home'),
            'authors_home'=> Configuration::get($this->name.'authors_home'),
            'gallery_home'=> Configuration::get($this->name.'gallery_home'),*/

            'cat_custom_hook' => Configuration::get($this->name.'cat_custom_hook'),
            'posts_custom_hook' => Configuration::get($this->name.'posts_custom_hook'),
            'comm_custom_hook' => Configuration::get($this->name.'comm_custom_hook'),
            'search_custom_hook' => Configuration::get($this->name.'search_custom_hook'),
            'arch_custom_hook' => Configuration::get($this->name.'arch_custom_hook'),
            'tags_custom_hook' => Configuration::get($this->name.'tags_custom_hook'),
            'auth_custom_hook' => Configuration::get($this->name.'auth_custom_hook'),
            'gallery_custom_hook' => Configuration::get($this->name.'gallery_custom_hook'),



        );

        return $data_config;
    }


    public function getBlocksArrayPrefix(){
        return array(
            'cat'=>array('prefix'=>'cat','name'=>$this->l('Blog Categories'),'name_chook'=>$this->l('Blog Categories in CUSTOM HOOK (blogCategoriesSPM)')),
            'posts'=>array('prefix'=>'posts','name'=>$this->l('Blog Posts recents'),'name_chook'=>$this->l('Blog Posts in CUSTOM HOOK (blogPostsSPM)')),
            'comm'=>array('prefix'=>'comm','name'=>$this->l('Blog Last Comments'),'name_chook'=>$this->l('Block Blog Comments in CUSTOM HOOK (blogCommentsSPM)')),
            'search'=>array('prefix'=>'comm','name'=>$this->l('Block Search'),'name_chook'=>$this->l('Block Blog Search in CUSTOM HOOK (blogSearchSPM)')),
            'arch'=>array('prefix'=>'arch','name'=>$this->l('Block Archives'),'name_chook'=>$this->l('Block Blog Archives in CUSTOM HOOK (blogArchivesSPM)')),
            'tags'=>array('prefix'=>'tags','name'=>$this->l('Blog Tags'),'name_chook'=>$this->l('Block Blog Tags in CUSTOM HOOK (blogTagsSPM)')),
            'auth'=>array('prefix'=>'auth','name'=>$this->l('Blog Top Authors'),'name_chook'=>$this->l('Block Blog Top Authors in CUSTOM HOOK (blogTopAuthorsSPM)')),
            'gallery'=>array('prefix'=>'gallery','name'=>$this->l('Blog Gallery'),'name_chook'=>$this->l('Block Gallery in CUSTOM HOOK (blogGallerySPM)')),
        );
    }

    public function positionsForBlog($data_out = null){

        $fields_form_array = array();

        $alias_position = isset($data_out['alias'])?$data_out['alias']:'';



        ## handle types , when customer does not have email ##
        $_all_social_types = $this->getBlocksArrayPrefix();


        // positions
        $data_positions_blocks_forcurrent_position = Configuration::get($this->name . '_orderby' . $alias_position);

        $data_positions_blocks_forcurrent_position = json_decode($data_positions_blocks_forcurrent_position,true);



        // fixed bug if module does not correctly updated and data orderby is empty
        if(empty($data_positions_blocks_forcurrent_position)) {
            $positions = array();
            $i = 1;
            foreach ($this->getBlocksArrayPrefix() as $_key_type => $_values_type) {

                $key = $alias_position . "_" . $_key_type;
                $position_item = $i;
                $positions[$key] = $position_item;
                $i++;

            }
            $data_positions_blocks_forcurrent_position = $positions;
        }
        // fixed bug if module does not correctly updated and data orderby is empty


        //echo "<pre>"; var_dump($data_positions_blocks_forcurrent_position);exit;

        $_all_social_types_tmp = array();

        $data_blocks_for_home_page = array('posts','auth','gallery');

        foreach($_all_social_types as $_key_type => $_values_type) {


            if($alias_position == "home") {
                if (!in_array($_key_type, $data_blocks_for_home_page))
                    continue;
            }

            $_all_social_types_tmp_item = array();
            $_all_social_types_tmp_item['prefix'] = $_key_type;

            $_all_social_types_tmp_item['name'] = ($alias_position=="chook")?$_values_type['name_chook']:$_values_type['name'];


            ############# positions #############

            $prefix = $_key_type;

            $_all_social_types_tmp_item['alias_position'] = $alias_position.$prefix;


            $hooks_array_pre = $this->getAllAvailablePositionsBlog();

            foreach($hooks_array_pre as $k=>$val){
                if($alias_position == $k) {

                    $_all_social_types_tmp_item['position_current'] = Configuration::get($this->name . '_' . $k . $prefix);
                    $_all_social_types_tmp_item['position_current_mobile'] = Configuration::get($this->name . '_' . $k . $prefix."mobile");

                    $_all_social_types_tmp_item['position_current_pos'] = $k;
                    $_all_social_types_tmp_item['position_current_pos_mobile'] = $k;


                    $_all_social_types_tmp_item['position_current_blog'] = Configuration::get($this->name.'_'.$k.$prefix);
                    $_all_social_types_tmp_item['position_current_blog_mobile'] = Configuration::get($this->name .'_'. $k.$prefix ."mobile");

                    $_all_social_types_tmp_item['orderby'] = $data_positions_blocks_forcurrent_position[$alias_position."_".$prefix];

                }
            }


            $prefix_short = $_key_type;


            $_all_social_types_tmp[$data_positions_blocks_forcurrent_position[$alias_position."_".$prefix_short]] = $_all_social_types_tmp_item;

        }
        //echo "<pre>";var_dump($_all_social_types_tmp);exit;

        ksort($_all_social_types_tmp);

        ## handle types , when customer does not have email ##

        $ajax_url = $this->context->link->getAdminLink('AdminBlockblogajax');

        $fields_form_array[0] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Blocks in the selected hook'),
                    'icon' => 'fa fa-th fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'type'=>'positions_blocks_order_by',
                        'positions_blocks_blog'=>$_all_social_types_tmp,
                        'position_key'=>$alias_position,
                        'mobile_txt' => 'mobile',

                        'ajax_url' => $ajax_url,
                        'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),
                        'is_demo'=>$this->is_demo,

                        'success_message'=>$this->l('Settings updated'),
                        'error_message'=>$this->l('Unable to update settings'),



                        'is17'=> version_compare(_PS_VERSION_, '1.7', '>'),

                    ),

                ),
            ),
        );


        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitpositions';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => array(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        //echo "<pre>"; var_dump($fields_form_array);exit;

        return $helper->generateForm($fields_form_array);
    }

    private function _customhookhelp(){
        ob_start();
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();



        return $_html;
    }

    private function _blockssettings(){
        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog categories" settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the "Blog categories":'),
                        'name' => 'blog_bcat',
                        'id' => 'blog_bcat',
                        'lang' => FALSE,
                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for block "Blog categories"'),
                        'name' => 'bcat_slider',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Displayed number of items in the slider in the block "Blog categories"'),
                        'name' => 'bcat_sl',
                        'id' => 'bcat_sl',
                        'lang' => FALSE,
                    ),


                    array(
                        'type' => 'order_items',
                        'label' => $this->l('Sort items in the "Blog categories" block by'),
                        'lang' => FALSE,

                        'name' => 'blog_catbl_order',
                        'id' => 'blog_catbl_order',
                        'value' => Configuration::get($this->name.'blog_catbl_order'),
                        'data_order' => array('time_add'=>$this->l('Date'),'position'=>$this->l('Position')),

                        'name_ad' => 'blog_catbl_ad',
                        'id_ad' => 'blog_catbl_ad',
                        'value_ad' => Configuration::get($this->name.'blog_catbl_ad'),
                        'data_ad' => array('asc'=>$this->l('Asc'),'desc'=>$this->l('Desc')),

                    ),




                ),



            ),


        );


        $fields_form1 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Posts recents" settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Posts recents"'),
                        'name' => 'blog_bposts',
                        'id' => 'blog_bposts',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display date in the block "Blog Posts recents"'),
                        'name' => 'block_display_date',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'block_display_date')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Views counter in the block "Blog Posts recents"'),
                        'name' => 'postbl_views',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'postbl_views')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating in the block "Blog Posts recents"'),
                        'name' => 'rating_bl',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_bl')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display images in the block "Blog Posts recents"'),
                        'name' => 'block_display_img',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'block_display_img')
                        ),
                    ),



                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in the block "Blog Posts recents"'),
                        'name' => 'posts_block_img_width',
                        'value' => (int)Configuration::get($this->name.'posts_block_img_width')
                    ),

                    array(
                        'type' => 'text_truncate',
                        'label' => $this->l('Truncate posts in the block "Blog Posts recents"'),
                        'name' => 'blog_tr_b',
                        'id' => 'blog_tr_b',
                        'lang' => FALSE,
                        'value' => (int)Configuration::get($this->name.'blog_tr_b'),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for block "Blog Posts recents"'),
                        'name' => 'bposts_slider',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Displayed number of items in the slider in the block "Blog Posts recents"'),
                        'name' => 'bposts_sl',
                        'id' => 'bposts_sl',
                        'lang' => FALSE,
                    ),


                    array(
                        'type' => 'order_items',
                        'label' => $this->l('Sort items in the "Blog Posts recents" block by'),
                        'lang' => FALSE,

                        'name' => 'blog_postbl_order',
                        'id' => 'blog_postbl_order',
                        'value' => Configuration::get($this->name.'blog_postbl_order'),
                        'data_order' => array('time_add'=>$this->l('Date'),'position'=>$this->l('Position'),'count_views'=>$this->l('Views')),

                        'name_ad' => 'blog_postbl_ad',
                        'id_ad' => 'blog_postbl_ad',
                        'value_ad' => Configuration::get($this->name.'blog_postbl_ad'),
                        'data_ad' => array('asc'=>$this->l('Asc'),'desc'=>$this->l('Desc')),

                    ),




                ),



            ),


        );


        $fields_form2 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Posts recents" on the Home Page settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(





                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Views counter in the block "Blog Posts recents" on the Home Page'),
                        'name' => 'postblh_views',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'postblh_views')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating in the block "Blog Posts recents" on the Home Page'),
                        'name' => 'rating_blh',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_blh')
                        ),
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('"Blog Posts recents" on the Home Page'),
                        'name' => 'blog_h',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 1,
                                    'name' => $this->l('Horizontal view Posts on the home page')),

                                array(
                                    'id' => 2,
                                    'name' => $this->l('Posts on home page in Blocks'),
                                ),

                                array(
                                    'id' => 3,
                                    'name' => $this->l('Slider for Posts on the home page'),
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Displayed number of items in the slider on the home page in the block "Blog Posts recents"'),
                        'name' => 'blog_bp_sl',
                        'id' => 'blog_bp_sl',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Posts recents" on the home page'),
                        'name' => 'blog_bp_h',
                        'id' => 'blog_bp_h',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in the block "Blog Posts recents" on the home page'),
                        'name' => 'posts_w_h',
                        'value' => (int)Configuration::get($this->name.'posts_w_h')
                    ),

                    array(
                        'type' => 'text_truncate',
                        'label' => $this->l('Truncate posts in the block "Blog Posts recents" on the home page'),
                        'name' => 'blog_p_tr',
                        'id' => 'blog_p_tr',
                        'lang' => FALSE,
                        'value' => (int)Configuration::get($this->name.'blog_p_tr'),
                    ),


                    array(
                        'type' => 'order_items',
                        'label' => $this->l('Sort items in the "Blog Posts recents" block on the home page by'),
                        'lang' => FALSE,

                        'name' => 'blog_postblh_order',
                        'id' => 'blog_postblh_order',
                        'value' => Configuration::get($this->name.'blog_postblh_order'),
                        'data_order' => array('time_add'=>$this->l('Date'),'position'=>$this->l('Position'),'count_views'=>$this->l('Views')),

                        'name_ad' => 'blog_postblh_ad',
                        'id_ad' => 'blog_postblh_ad',
                        'value_ad' => Configuration::get($this->name.'blog_postblh_ad'),
                        'data_ad' => array('asc'=>$this->l('Asc'),'desc'=>$this->l('Desc')),

                    ),


                ),



            ),


        );


        $fields_form3 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Last Comments" settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Last Comments"'),
                        'name' => 'blog_com',
                        'id' => 'blog_com',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'text_truncate',
                        'label' => $this->l('Truncate Comments in the block "Blog Last Comments"'),
                        'name' => 'blog_com_tr',
                        'id' => 'blog_com_tr',
                        'lang' => FALSE,
                        'value' => (int)Configuration::get($this->name.'blog_com_tr'),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating in the block "Blog Last Comments"'),
                        'name' => 'rating_bllc',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_bllc')
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for block "Blog Last Comments"'),
                        'name' => 'bcom_slider',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Displayed number of items in the slider in the block "Blog Last Comments"'),
                        'name' => 'bcom_sl',
                        'id' => 'bcom_sl',
                        'lang' => FALSE,
                    ),





                ),



            ),


        );


        $fields_form4 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Tags" settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Tags"'),
                        'name' => 'blog_tags',
                        'id' => 'blog_tags',
                        'lang' => FALSE,
                    ),
                ),
            ),
        );


        $fields_form5 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Top Authors" settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Top Authors"'),
                        'name' => 'blog_authors',
                        'id' => 'blog_authors',
                        'lang' => FALSE,
                    ),



                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for block "Blog Top Authors"'),
                        'name' => 'sr_slideru',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Displayed number of items in the slider in the block "Blog Top Authors"'),
                        'name' => 'sr_slu',
                        'id' => 'sr_slu',
                        'lang' => FALSE,
                    ),


                ),
            ),
        );

        $fields_form6 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Top Authors" on the home page settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Top Authors" on the home page'),
                        'name' => 'blog_authorsh',
                        'id' => 'blog_authorsh',
                        'lang' => FALSE,
                    ),



                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for block "Blog Top Authors" on the home page'),
                        'name' => 'sr_sliderhu',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Displayed number of items in the slider in the block "Blog Top Authors" on the home page'),
                        'name' => 'sr_slhu',
                        'id' => 'sr_slhu',
                        'lang' => FALSE,
                    ),


                ),
            ),
        );




        $fields_form7 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Gallery" settings'),
                    'icon' => 'fa fa-image fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Gallery"'),
                        'name' => 'blog_gallery',
                        'id' => 'blog_gallery',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in the block "Blog Gallery"'),
                        'name' => 'gallery_w',
                        'value' => (int)Configuration::get($this->name.'gallery_w')
                    ),



                ),
            ),
        );

        $fields_form8 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Block "Blog Gallery" on the home page settings'),
                    'icon' => 'fa fa-image fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the block "Blog Gallery" on the home page'),
                        'name' => 'blog_galleryh',
                        'id' => 'blog_galleryh',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in the block "Blog Gallery" on the home page'),
                        'name' => 'gallery_w_h',
                        'value' => (int)Configuration::get($this->name.'gallery_w_h')
                    ),




                ),
            ),
        );



        $fields_form99 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'blockssettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesBlockSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        $_html = '';
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_js.phtml');



        return  $_html.$helper->generateForm(array($fields_form,$fields_form1, $fields_form2, $fields_form3, $fields_form4, $fields_form5,$fields_form6,
            $fields_form7,$fields_form8,$fields_form99));
    }

    public function getConfigFieldsValuesBlockSettings(){

        $data_config = array(
            'blog_bcat' => (int)Configuration::get($this->name.'blog_bcat'),
            'blog_bposts' => (int)Configuration::get($this->name.'blog_bposts'),
            'blog_h' => (int)Configuration::get($this->name.'blog_h'),
            'blog_bp_h' => (int)Configuration::get($this->name.'blog_bp_h'),
            'blog_com'  => (int)Configuration::get($this->name.'blog_com'),

            'blog_bp_sl' => (int)Configuration::get($this->name.'blog_bp_sl'),

            'bposts_slider'=> (int)Configuration::get($this->name.'bposts_slider'),
            'bposts_sl'=> (int)Configuration::get($this->name.'bposts_sl'),

            'bcat_slider'=> (int)Configuration::get($this->name.'bcat_slider'),
            'bcat_sl'=> (int)Configuration::get($this->name.'bcat_sl'),

            'bcom_slider'=> (int)Configuration::get($this->name.'bcom_slider'),
            'bcom_sl'=> (int)Configuration::get($this->name.'bcom_sl'),

            'blog_tags'=>(int)Configuration::get($this->name.'blog_tags'),
            'blog_authors'=>(int)Configuration::get($this->name.'blog_authors'),
            'sr_slideru'=>(int)Configuration::get($this->name.'sr_slideru'),
            'sr_slu'=>(int)Configuration::get($this->name.'sr_slu'),

            'blog_authorsh'=>(int)Configuration::get($this->name.'blog_authorsh'),
            'sr_sliderhu'=>(int)Configuration::get($this->name.'sr_sliderhu'),
            'sr_slhu'=>(int)Configuration::get($this->name.'sr_slhu'),

            'blog_gallery'=>(int)Configuration::get($this->name.'blog_gallery'),
            'blog_galleryh'=>(int)Configuration::get($this->name.'blog_galleryh'),

        );

        return $data_config;
    }


    private function _fbcommentssettings(){
        $admin_img = "";
        $admin_explode = explode(",", Configuration::get($this->name.'appadmin'));
        if(sizeof($admin_explode)>0) {
            foreach ($admin_explode as $admin_explode) {
                if(Tools::strlen($admin_explode)==0) continue;

                include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_a.phtml');
            }
        }

        $_desc1 = '';
        $this->_data_translate_custom['_fbcommentssettings_desc1_to_conf'] = $this->l('To configure the Facebook Application Id read');
        $this->_data_translate_custom['_fbcommentssettings_desc1_which_is_loc'] = $this->l('which is located in the folder  with the module.');
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_desc1.phtml');


        $_desc2 = '';
        $this->_data_translate_custom['_fbcommentssettings_desc2_id'] = $this->l('Id administrators, separated by commas');
        $this->_data_translate_custom['_fbcommentssettings_desc2_add'] = $this->l('Add a facebook accounts ID for beeing comments moderators. ID Can be found on');
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_desc2.phtml');


        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Facebook Comments settings'),
                    'icon' => 'fa fa-facebook fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'text',
                        'label' => $this->l('Numbers of comments visible'),
                        'name' => 'number_fc',
                        'id' => 'number_fc',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook Application Id'),
                        'name' => 'appid',
                        'id' => 'appid',
                        'lang' => FALSE,
                        'desc' => $_desc1,
                    ),

                    array(
                        'type' => 'text_fcom',
                        'label' => $this->l('My App'),
                        'name' => 'fcom_text',
                        'id' => 'fcom_text',
                        'lang' => FALSE,
                        'f_appid' => Configuration::get($this->name.'appid'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Moderators'),
                        'name' => 'appadmin',
                        'id' => 'appadmin',
                        'lang' => FALSE,
                        'desc' => $_desc2.$admin_img,
                    ),

                    array(
                        'type' => 'text_fcom_set',
                        'label' => $this->l('Moderate Facebook comments'),
                        'name' => 'fcom_text',
                        'id' => 'fcom_text',
                        'lang' => FALSE,
                        'f_appid' => Configuration::get($this->name.'appid'),

                    ),
                ),

            ),


        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );

        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'fbcommentssettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesFbcommentsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1));
    }


    public function getConfigFieldsValuesFbcommentsSettings(){


        $data_config = array(

            'number_fc' => Configuration::get($this->name.'number_fc'),
            'appid' => Configuration::get($this->name.'appid'),
            'appadmin' => Configuration::get($this->name.'appadmin'),

        );

        return $data_config;

    }


    private function _commentssettings(){
        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Comments on the All Comments page settings'),
                    'icon' => 'fa fa-comments-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Comments per Page on the All Comments page in the list view'),
                        'name' => 'perpage_com',
                        'id' => 'perpage_com',
                        'lang' => FALSE,

                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating on the All Comments page in the list view'),
                        'name' => 'rating_acom',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_acom')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Blog Author Avatar on the All Comments page in the list view'),
                        'name' => 'ava_list_displ_call',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'ava_list_displ_call')
                        ),
                        'desc' => $this->l('Must be enabled Blog Author Avatar functional'),
                    ),

                    array(
                        'type' => 'blog_display_effect',
                        'label' => $this->l('Display effect for All Comments page in the list view'),
                        'name' => 'blog_com_effect',
                        'id' => 'blog_com_effect',
                        'lang' => FALSE,
                        'value' => $this->_effects(array('value' => 'blog_com_effect'))

                    ),


                ),



            ),


        );

        $fields_form1 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Comments on the post page settings'),
                    'icon' => 'fa fa-comments-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'block_radio_buttons_blog_comments_custom',
                        'label' => $this->l('Who can add comments?'),

                        'name' => 'block_radio_buttons_reviews_custom',
                        'values'=> array(
                            'value' => Configuration::get($this->name.'whocanaddc')
                        ),

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Comments per Page on the post page'),
                        'name' => 'pperpage_com',
                        'id' => 'pperpage_com',
                        'lang' => FALSE,

                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating in the Comments on the post page'),
                        'name' => 'rating_post',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_post')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Blog Author Avatar in the Comments on the post page'),
                        'name' => 'ava_list_displ_cpost',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'ava_list_displ_cpost')
                        ),
                        'desc' => $this->l('Must be enabled Blog Author Avatar functional'),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Captcha in the Leave a Comment form'),
                        'name' => 'is_captcha_com',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('CAPTCHA type'),
                        'name' => 'bcaptcha_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 1,
                                    'name' => $this->l('Image CAPTCHA')),

                                array(
                                    'id' => 2,
                                    'name' => $this->l('Google reCAPTCHA2'),
                                ),
                                array(
                                    'id' => 3,
                                    'name' => $this->l('Google reCAPTCHA3'),
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Site Key'),
                        'name' => 'bcaptcha_site_key',
                        'id' => 'bcaptcha_site_key',
                        'lang' => FALSE,
                        'required'=>true,

                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Secret key'),
                        'name' => 'bcaptcha_secret_key',
                        'id' => 'bcaptcha_secret_key',
                        'lang' => FALSE,
                        'required'=>true,

                    ),

                    array(
                        'type' => 'blog_display_effect',
                        'label' => $this->l('Display effect for Comments on the post page'),
                        'name' => 'blog_comp_effect',
                        'id' => 'blog_comp_effect',
                        'lang' => FALSE,
                        'value' => $this->_effects(array('value' => 'blog_comp_effect'))

                    ),


                ),



            ),


        );




        $fields_form3 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'commentssettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesCommentsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        $_html = '';

        $bcaptcha_type = (int)Configuration::get($this->name.'bcaptcha_type');
        $this->_data_translate_custom['_commentssettings_txt_read2'] = $this->l('Read Step 17 "How to get Site key and Secret key for reCAPTCHA – v2 ?" in the');
        $this->_data_translate_custom['_commentssettings_txt_read3'] = $this->l('Read Step 18 "How to get Site key and Secret key for reCAPTCHA – v3 ?" in the');

        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_txt.phtml');


        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_js.phtml');



        return  $_html . $helper->generateForm(array($fields_form,$fields_form1,$fields_form3));
    }

    public function getConfigFieldsValuesCommentsSettings(){
        $data_config = array(
            'perpage_com' => (int)Configuration::get($this->name.'perpage_com'),
            'pperpage_com' => (int)Configuration::get($this->name.'pperpage_com'),
            'is_captcha_com'=> (int)Configuration::get($this->name.'is_captcha_com'),
            'bcaptcha_type'=> (int)Configuration::get($this->name.'bcaptcha_type'),
            'bcaptcha_site_key'=> Configuration::get($this->name.'bcaptcha_site_key'),
            'bcaptcha_secret_key'=> Configuration::get($this->name.'bcaptcha_secret_key'),
        );

        return $data_config;
    }



    private function _postssettings(){
        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Posts in the list view settings'),
                    'icon' => 'fa fa-newspaper-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Posts per Page in the list view'),
                        'name' => 'perpage_posts',
                        'id' => 'perpage_posts',
                        'lang' => FALSE,

                    ),


                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display date in the list posts view'),
                        'name' => 'p_list_displ_date',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'p_list_displ_date')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Views counter in the list posts view'),
                        'name' => 'postl_views',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'postl_views')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating in the list posts view'),
                        'name' => 'rating_postl',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_postl')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Blog Author Avatar in the list posts view'),
                        'name' => 'ava_list_displ',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'ava_list_displ')
                        ),
                        'desc' => $this->l('Must be enabled Blog Author Avatar functional'),
                    ),




                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in the lists posts'),
                        'name' => 'lists_img_width',
                        'value' => (int)Configuration::get($this->name.'lists_img_width')


                    ),

                    /*array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image height in the lists posts'),
                        'name' => 'lists_img_height',
                        'value' => (int)Configuration::get($this->name.'lists_img_height')


                    ),*/

                    array(
                        'type' => 'text_truncate',
                        'label' => $this->l('Truncate posts content in the list view'),
                        'name' => 'blog_pl_tr',
                        'id' => 'blog_pl_tr',
                        'lang' => FALSE,
                        'value' => (int)Configuration::get($this->name.'blog_pl_tr'),
                    ),


                    array(
                        'type' => 'blog_display_effect',
                        'label' => $this->l('Display effect for Blog Posts in the list view'),
                        'name' => 'blog_post_effect',
                        'id' => 'blog_post_effect',
                        'lang' => FALSE,
                        'value' => $this->_effects(array('value' => 'blog_post_effect'))

                    ),

                    array(
                        'type' => 'order_items',
                        'label' => $this->l('Sort Blog Posts in the list view by'),
                        'lang' => FALSE,

                        'name' => 'blog_post_order',
                        'id' => 'blog_post_order',
                        'value' => Configuration::get($this->name.'blog_post_order'),
                        'data_order' => array('time_add'=>$this->l('Date'),'position'=>$this->l('Position'),'count_views'=>$this->l('Views')),

                        'name_ad' => 'blog_post_ad',
                        'id_ad' => 'blog_post_ad',
                        'value_ad' => Configuration::get($this->name.'blog_post_ad'),
                        'data_ad' => array('asc'=>$this->l('Asc'),'desc'=>$this->l('Desc')),

                    ),




                ),



            ),


        );


        $fields_form1 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Post page settings'),
                    'icon' => 'fa fa-newspaper-o fa-lg'
                ),
                'input' => array(





                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display date on the post page'),
                        'name' => 'post_display_date',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'post_display_date')
                        ),
                    ),

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width on the post page'),
                        'name' => 'post_img_width',
                        'value' => (int)Configuration::get($this->name.'post_img_width')
                    ),

                   /* array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Active Social share buttons'),
                        'name' => 'is_soc_buttons',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'is_soc_buttons')
                        ),
                    ),*/

                    array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Active Social share buttons'),
                        'name' => 'blog_soc_buttons',
                        'hint' => $this->l('Active Social share buttons'),
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'is_blog_f_share',
                                    'name' => $this->l('Facebook').' '.$this->l('Share Button'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'is_blog_g_share',
                                    'name' => $this->l('Google').' '.$this->l('Share Button'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'is_blog_t_share',
                                    'name' => $this->l('Twitter').' '.$this->l('Share Button'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'is_blog_p_share',
                                    'name' => $this->l('Pinterest').' '.$this->l('Share Button'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'is_blog_l_share',
                                    'name' => $this->l('Linkedin').' '.$this->l('Share Button'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'is_blog_tu_share',
                                    'name' => $this->l('Tumblr').' '.$this->l('Share Button'),
                                    'val' => '1'
                                ),
                                array(
                                    'id' => 'is_blog_w_share',
                                    'name' => $this->l('Whatsapp').' '.$this->l('Share Button'),
                                    'val' => '1'
                                ),


                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Views counter on the post page'),
                        'name' => 'post_views',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'post_views')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating on the post page'),
                        'name' => 'rating_postp',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_postp')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Blog Blog Tags the post page'),
                        'name' => 'is_tags_bp',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'is_tags_bp')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Blog Categories the post page'),
                        'name' => 'is_cat_bp',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'is_cat_bp')
                        ),
                    ),


                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Blog Author the post page'),
                        'name' => 'is_author_bp',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'is_author_bp')
                        ),
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Blog Author Avatar on the post page'),
                        'name' => 'ava_post_displ',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'ava_post_displ')
                        ),
                        'desc' => $this->l('Must be enabled Blog Author Avatar functional'),
                    ),

                ),



            ),


        );


        $data_img_sizes = array();

        $available_types = ImageType::getImagesTypes('products');

        foreach ($available_types as $type){

            $id = $type['name'];
            $name = $type['name'].' ('.$type['width'].' x '.$type['height'].')';

            $data_item_size = array(
                'id' => $id,
                'name' => $name,
            );

            array_push($data_img_sizes,$data_item_size);


        }



        $fields_form2 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Related products on post page settings'),
                    'icon' => 'fa fa-book fa-lg'
                ),
                'input' => array(



                    array(
                        'type' => 'select',
                        'label' => $this->l('Image size for related products'),
                        'name' => 'img_size_rp',
                        'options' => array(
                            'query' => $data_img_sizes,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),

                    array(
                        'type' => 'text_truncate',
                        'label' => $this->l('Truncate product description'),
                        'name' => 'blog_rp_tr',
                        'id' => 'blog_rp_tr',
                        'lang' => FALSE,
                        'value' => (int)Configuration::get($this->name.'blog_rp_tr'),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for Related Products on the post page'),
                        'name' => 'relpr_slider',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Number Products in the Related Products slider'),
                        'name' => 'npr_slider',
                        'id' => 'npr_slider',
                        'lang' => FALSE,

                    ),


                ),



            ),


        );

        $fields_form3 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Related Posts on post page settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in the related posts block on the post page'),
                        'name' => 'rp_img_width',
                        'value' => (int)Configuration::get($this->name.'rp_img_width')
                    ),


                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Views counter in the related posts block on the post page'),
                        'name' => 'postrel_views',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'postrel_views')
                        ),
                    ),


                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating in the related posts block on the post page'),
                        'name' => 'rating_postrp',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_postrp')
                        ),
                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for Related Posts on the post page'),
                        'name' => 'relp_slider',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Number Posts in the Related Posts slider'),
                        'name' => 'np_slider',
                        'id' => 'np_slider',
                        'lang' => FALSE,

                    ),





                    array(
                        'type' => 'order_items',
                        'label' => $this->l('Sort Related Posts on the post page by'),
                        'lang' => FALSE,

                        'name' => 'blog_rp_order',
                        'id' => 'blog_rp_order',
                        'value' => Configuration::get($this->name.'blog_rp_order'),
                        'data_order' => array('time_add'=>$this->l('Date'),'position'=>$this->l('Position'),'count_views'=>$this->l('Views')),

                        'name_ad' => 'blog_rp_ad',
                        'id_ad' => 'blog_rp_ad',
                        'value_ad' => Configuration::get($this->name.'blog_rp_ad'),
                        'data_ad' => array('asc'=>$this->l('Asc'),'desc'=>$this->l('Desc')),

                    ),
                ),


            ),


        );



        $fields_form4 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Posts on Product Page settings'),
                    'icon' => 'fa fa-list-alt fa-lg'
                ),
                'input' => array(



                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the "Related Blog Posts" on the Product Page'),
                        'name' => 'blog_relposts',
                        'id' => 'blog_relposts',
                        'lang' => FALSE,
                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display tab "Related Blog Posts" on the Product Page:'),
                        'name' => 'tab_blog_pr',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'tab_blog_pr')
                        ),
                    ),


                    array(
                        'type' => 'select',
                        'label' => $this->l('Product tabs'),
                        'name' => 'btabs_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 1,
                                    'name' => $this->l('Standard theme without Tabs')),

                                array(
                                    'id' => 2,
                                    'name' => $this->l('Custom theme with tabs on product page for Prestashop 1.6'),
                                ),
                                array(
                                    'id' => 3,
                                    'name' => $this->l('Custom theme with tabs on product page for Prestashop 1.7'),
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('On a standard PrestaShop 1.6 and PrestaShop 1.7 theme, the product page no longer has tabs for the various sections.').
                            '&nbsp;'.$this->l('But some custom themes have added back tabs on the product page. ')
                    ),



                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in the posts on the Product Page'),
                        'name' => 'rp_img_widthp',
                        'value' => (int)Configuration::get($this->name.'rp_img_widthp')
                    ),


                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display Views counter in the posts on the Product Page'),
                        'name' => 'postrel_viewsp',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'postrel_viewsp')
                        ),
                    ),


                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display rating in the posts on the Product Page'),
                        'name' => 'rating_postrpp',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'rating_postrpp')
                        ),
                    ),


                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable Slider for posts on the Product Page'),
                        'name' => 'relp_sliderp',
                        'desc'=> $this->l('We do not guarantee the correct display slider in the Product tabs!'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Number Posts in the posts slider on the Product Page'),
                        'name' => 'np_sliderp',
                        'id' => 'np_sliderp',
                        'lang' => FALSE,


                    ),

                    array(
                        'type' => 'order_items',
                        'label' => $this->l('Sort Blog Posts on the Product Page by'),
                        'lang' => FALSE,

                        'name' => 'blog_catp_order',
                        'id' => 'blog_catp_order',
                        'value' => Configuration::get($this->name.'blog_catp_order'),
                        'data_order' => array('time_add'=>$this->l('Date'),'position'=>$this->l('Position'),'count_views'=>$this->l('Views')),

                        'name_ad' => 'blog_catp_ad',
                        'id_ad' => 'blog_catp_ad',
                        'value_ad' => Configuration::get($this->name.'blog_catp_ad'),
                        'data_ad' => array('asc'=>$this->l('Asc'),'desc'=>$this->l('Desc')),

                    ),


                ),



            ),


        );


        $fields_form6 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'postssettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesPostsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );



        $_html = '';


        $btabs_type = (int)Configuration::get($this->name.'btabs_type');
        $this->_data_translate_custom['_postssettings_txt_read'] = $this->l('Read Step 14 "How to configure product tabs for Prestashop 1.7.x.x ?" in the');

        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_txt.phtml');


        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_js.phtml');



        return  $_html.$helper->generateForm(array($fields_form,$fields_form1,$fields_form2,$fields_form3,$fields_form4,$fields_form6));
    }

    public function getConfigFieldsValuesPostsSettings(){

        $data_config = array(
            'blog_relposts' => (int)Configuration::get($this->name.'blog_relposts'),
            'perpage_posts' => (int)Configuration::get($this->name.'perpage_posts'),
            'img_size_rp' => Configuration::get($this->name.'img_size_rp'),

            'npr_slider' => Configuration::get($this->name.'npr_slider'),
            'np_slider' => Configuration::get($this->name.'np_slider'),

            'relp_slider'=> Configuration::get($this->name.'relp_slider'),

            'relpr_slider'=> Configuration::get($this->name.'relpr_slider'),

            'btabs_type' => Configuration::get($this->name.'btabs_type'),
            'relp_sliderp'=>Configuration::get($this->name.'relp_sliderp'),
            'np_sliderp'=>Configuration::get($this->name.'np_sliderp'),


            'is_blog_f_share'=>Configuration::get($this->name.'is_blog_f_share'),
            'is_blog_g_share'=>Configuration::get($this->name.'is_blog_g_share'),
            'is_blog_t_share'=>Configuration::get($this->name.'is_blog_t_share'),
            'is_blog_p_share'=>Configuration::get($this->name.'is_blog_p_share'),
            'is_blog_l_share'=>Configuration::get($this->name.'is_blog_l_share'),
            'is_blog_tu_share'=>Configuration::get($this->name.'is_blog_tu_share'),
            'is_blog_w_share'=>Configuration::get($this->name.'is_blog_w_share'),




        );

        return $data_config;
    }

    private function _categoriessettings(){
        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Categories settings'),
                    'icon' => 'fa fa-list fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Categories per Page:'),
                        'name' => 'perpage_catblog',
                        'id' => 'perpage_catblog',
                        'lang' => FALSE,

                    ),


                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('Display date in the list Categories page:'),
                        'name' => 'cat_list_display_date',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'cat_list_display_date')
                        ),

                    ),


                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width in lists categories'),
                        'name' => 'clists_img_width',
                        'value' => (int)Configuration::get($this->name.'clists_img_width')


                    ),

                    array(
                        'type' => 'text_truncate',
                        'label' => $this->l('Truncate category description in the list view'),
                        'name' => 'blog_catl_tr',
                        'id' => 'blog_catl_tr',
                        'lang' => FALSE,
                        'value' => (int)Configuration::get($this->name.'blog_catl_tr'),
                    ),

                    array(
                        'type' => 'blog_display_effect',
                        'label' => $this->l('Display effect for Blog Categories in the list view'),
                        'name' => 'blog_cat_effect',
                        'id' => 'blog_cat_effect',
                        'lang' => FALSE,
                        'value' => $this->_effects(array('value' => 'blog_cat_effect'))

                    ),


                    array(
                        'type' => 'order_items',
                        'label' => $this->l('Sort Categories in the list view by'),
                        'lang' => FALSE,

                        'name' => 'blog_cat_order',
                        'id' => 'blog_cat_order',
                        'value' => Configuration::get($this->name.'blog_cat_order'),
                        'data_order' => array('time_add'=>$this->l('Date'),'position'=>$this->l('Position')),

                        'name_ad' => 'blog_cat_ad',
                        'id_ad' => 'blog_cat_ad',
                        'value_ad' => Configuration::get($this->name.'blog_cat_ad'),
                        'data_ad' => array('asc'=>$this->l('Asc'),'desc'=>$this->l('Desc')),

                    ),



                ),



            ),


        );

        $fields_form1 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Category page settings'),
                    'icon' => 'fa fa-newspaper-o fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'image_custom_px',
                        'label' => $this->l('Image width on the category page'),
                        'name' => 'cat_img_width',
                        'value' => (int)Configuration::get($this->name.'cat_img_width')
                    ),


                ),



            ),


        );




        $fields_form3 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'categoriessettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesCategoriesSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );






        return  $helper->generateForm(array($fields_form,$fields_form1,$fields_form3));
    }

    public function getConfigFieldsValuesCategoriesSettings(){

        $data_config = array(
            'perpage_catblog' => (int)Configuration::get($this->name.'perpage_catblog'),



        );

        return $data_config;


    }


    private function _mainsettingsblog(){

        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Main settings'),
                    'icon' => 'fa fa-cogs fa-lg'
                ),
                'input' => array(

                    /*array(
                        'type' => 'select',
                        'label' => $this->l('Blog layout'),
                        'name' => 'blog_layout_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 1,
                                    'name' => $this->l('List')),

                                array(
                                    'id' => 2,
                                    'name' => $this->l('Grid type 1'),
                                ),

                                array(
                                    'id' => 3,
                                    'name' => $this->l('Grid type 2'),
                                ),


                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'desc'=>$this->l('Layout type for pages such as blog page, blog category pages, author pages, all comments pages.')

                    ),*/

                    array(
                        'type' => 'color',
                        'lang' => true,
                        'label' => $this->l('Main Color'),
                        'name' => $this->name.'bgcolor_main',
                        'desc' => $this->l('Used for buttons. You can enter Hexadecimal color code, like #000000')
                    ),

                    array(
                        'type' => 'color',
                        'lang' => true,
                        'label' => $this->l('Hover Color'),
                        'name' => $this->name.'bgcolor_hover',
                        'desc' => $this->l('Used for buttons. You can enter Hexadecimal color code, like #000000')
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Date Format'),
                        'name' => 'blog_date',
                        'id' => 'blog_date',
                        'lang' => FALSE,
                        'required' => TRUE,
                        'desc'=>$this->l('Default: "d/m/Y". For more reference, please check http://php.net/manual/en/function.date.php')

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('"Read more" text'),
                        'name' => 'text_readmore',
                        'id' => 'text_readmore',
                        'lang' => TRUE,

                        'size' => 50,
                        'desc'=>$this->l('Leave blank to hide the "Read more" link for blog posts')
                    ),

                ),

            ),
        );


        $fields_form3 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );


        $helper = new HelperForm();
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'mainsettingsblog';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesMainblogsettingsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        return  $helper->generateForm(array($fields_form,$fields_form3));
    }

    public function getConfigFieldsValuesMainblogsettingsSettings(){

        $languages = Language::getLanguages(false);
        $fields_text_readmore = array();

        foreach ($languages as $lang)
        {
            $fields_text_readmore[$lang['id_lang']] =  Configuration::get($this->name.'text_readmore_'.$lang['id_lang']);


        }



        $data_config = array(
            $this->name.'bgcolor_main'=>Configuration::get($this->name.'bgcolor_main'),
            $this->name.'bgcolor_hover'=>Configuration::get($this->name.'bgcolor_hover'),
            'blog_date'=>Configuration::get($this->name.'blog_date'),
            'text_readmore' => $fields_text_readmore,
            'blog_layout_type'=>Configuration::get($this->name.'blog_layout_type'),



        );

        return $data_config;


    }

    private function _urlrewrite(){

        if(version_compare(_PS_VERSION_, '1.7', '>')){
            $html = ' ( Shop Parameters -> Traffic & SEO -> SEO & URLs -> Set up URLs )';
        } else {
            $html = ' ( Preferences -> SEO & URLs -> Set up URLs )';
        }


        $txt = '';
        $this->_data_translate_custom['_urlrewrite_txt_fr_url'] = $this->l('Friendly URL is DISABLED in your SHOP');
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_txt.phtml');


        include_once(_PS_MODULE_DIR_.$this->name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $data_url = $obj_blog->getSEOURLs();
        $posts_url = $data_url['posts_url'];

        $txt1 = '';
        $this->_data_translate_custom['_urlrewrite_txt1_fr_url'] = $this->l('Blog main page:');
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'_txt1.phtml');


        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('URL Rewriting'),
                    'icon' => 'fa fa-link fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable URL rewriting:'),
                        'name' => 'urlrewrite_on',
                        'desc' => $this->l('Enable only if your server allows URL rewriting (recommended).').

                            (!Configuration::get('PS_REWRITING_SETTINGS')?$txt:''),

                         'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),





                ),
            ),
        );


        $fields_form1 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog categories page'),
                    'icon' => 'fa fa-list fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('All Blog Categories page alias'),
                        'name' => 'blog_cat_alias',
                        'id' => 'blog_cat_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "categories"'),

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for Blog categories page'),
                        'name' => 'title_allcat',
                        'id' => 'title_allcat',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for Blog categories page'),
                        'name' => 'key_allcat',
                        'id' => 'key_allcat',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for Blog categories page'),
                        'name' => 'desc_allcat',
                        'id' => 'desc_allcat',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form2 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog main page'),
                    'icon' => 'fa fa-newspaper-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog main page alias'),
                        'name' => 'blog_alias',
                        'id' => 'blog_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=> $txt1,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for Blog main page'),
                        'name' => 'title_allposts',
                        'id' => 'title_allposts',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for Blog main page'),
                        'name' => 'key_allposts',
                        'id' => 'key_allposts',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for Blog main page'),
                        'name' => 'desc_allposts',
                        'id' => 'desc_allposts',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );



        $fields_form3 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog All Comments page'),
                    'icon' => 'fa fa-comments-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog All Comments page alias'),
                        'name' => 'blog_allcom_alias',
                        'id' => 'blog_allcom_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "comments"'),

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for Blog All Comments page'),
                        'name' => 'title_allcom',
                        'id' => 'title_allcom',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for Blog All Comments page'),
                        'name' => 'key_allcom',
                        'id' => 'key_allcom',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for Blog All Comments page'),
                        'name' => 'desc_allcom',
                        'id' => 'desc_allcom',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form4 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog All Tags page'),
                    'icon' => 'fa fa-tags fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog All Tags page alias'),
                        'name' => 'blog_alltags_alias',
                        'id' => 'blog_alltags_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "tags"'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for Blog All Tags page'),
                        'name' => 'title_alltags',
                        'id' => 'title_alltags',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for Blog All Tags page'),
                        'name' => 'key_alltags',
                        'id' => 'key_alltags',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for Blog All Tags page'),
                        'name' => 'desc_alltags',
                        'id' => 'desc_alltags',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form41 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog Tag page'),
                    'icon' => 'fa fa-tags fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog Tag page alias'),
                        'name' => 'blog_tag_alias',
                        'id' => 'blog_tag_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "tag"'),
                    ),


                ),
            ),
        );

        $fields_form5 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog Authors page'),
                    'icon' => 'fa fa-users fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog Authors page alias'),
                        'name' => 'blog_authors_alias',
                        'id' => 'blog_authors_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "authors"'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for Blog Authors page'),
                        'name' => 'title_allauthors',
                        'id' => 'title_allauthors',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for Blog Authors page'),
                        'name' => 'key_allauthors',
                        'id' => 'key_allauthors',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for Blog Authors page'),
                        'name' => 'desc_allauthors',
                        'id' => 'desc_allauthors',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form51 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog Author page'),
                    'icon' => 'fa fa-users fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog Author page alias'),
                        'name' => 'blog_author_alias',
                        'id' => 'blog_author_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "author"'),
                    ),



                ),
            ),
        );



        $fields_form6 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog Gallery page'),
                    'icon' => 'fa fa-image fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog Gallery page alias'),
                        'name' => 'blog_gallery_alias',
                        'id' => 'blog_gallery_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "gallery"'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for Blog Gallery page'),
                        'name' => 'title_gallery',
                        'id' => 'title_gallery',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for Blog Gallery page'),
                        'name' => 'key_gallery',
                        'id' => 'key_gallery',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for Blog Gallery page'),
                        'name' => 'desc_gallery',
                        'id' => 'desc_gallery',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );




        $fields_form7 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('My account -> Blog author avatar page'),
                    'icon' => 'fa fa-user fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog author avatar page alias'),
                        'name' => 'blog_mava_alias',
                        'id' => 'blog_mava_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for My account -> Blog author avatar page'),
                        'name' => 'title_myava',
                        'id' => 'title_myava',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for My account -> Blog author avatar page'),
                        'name' => 'key_myava',
                        'id' => 'key_myava',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for My account -> Blog author avatar page'),
                        'name' => 'desc_myava',
                        'id' => 'desc_myava',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form8 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('My account -> My Blog Posts page'),
                    'icon' => 'fa fa-user fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'text',
                        'label' => $this->l('My Blog Posts page alias'),
                        'name' => 'blog_mbposts_alias',
                        'id' => 'blog_mbposts_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for My account -> My Blog Posts page'),
                        'name' => 'title_myposts',
                        'id' => 'title_myposts',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for My account -> My Blog Posts page'),
                        'name' => 'key_myposts',
                        'id' => 'key_myposts',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for My account -> My Blog Posts page'),
                        'name' => 'desc_myposts',
                        'id' => 'desc_myposts',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form9 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('My account -> My Blog Comments page'),
                    'icon' => 'fa fa-user fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $this->l('My Blog Comments page alias'),
                        'name' => 'blog_mbcom_alias',
                        'id' => 'blog_mbcom_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for My account -> My Blog Comments page'),
                        'name' => 'title_mycom',
                        'id' => 'title_mycom',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for My account -> My Blog Comments page'),
                        'name' => 'key_mycom',
                        'id' => 'key_mycom',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for My account -> My Blog Comments page'),
                        'name' => 'desc_mycom',
                        'id' => 'desc_mycom',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form10 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('My Loyalty Program page'),
                    'icon' => 'fa fa-sitemap fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'text',
                        'label' => $this->l('My Loyalty Program page alias'),
                        'name' => 'blog_loyalty_alias',
                        'id' => 'blog_loyalty_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "loyalty"'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for My Loyalty Program page'),
                        'name' => 'title_loyalty',
                        'id' => 'title_loyalty',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for My Loyalty Program page'),
                        'name' => 'key_loyalty',
                        'id' => 'key_loyalty',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for My Loyalty Program page'),
                        'name' => 'desc_loyalty',
                        'id' => 'desc_loyalty',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );


        $fields_form11 = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Blog RSS page'),
                    'icon' => 'fa fa-rss fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'text',
                        'label' => $this->l('Blog RSS page alias'),
                        'name' => 'blog_rss_alias',
                        'id' => 'blog_rss_alias',
                        'lang' => TRUE,
                        'required' =>TRUE,
                        'desc'=>$this->l('Default: "rss"'),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title for Blog RSS page'),
                        'name' => 'title_rss',
                        'id' => 'title_rss',
                        'lang' => TRUE,
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords for Blog RSS page'),
                        'name' => 'key_rss',
                        'id' => 'key_rss',
                        'lang' => TRUE,
                        'desc'=>$this->l('Keywords must be comma-separated')
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Description for Blog RSS page'),
                        'name' => 'desc_rss',
                        'id' => 'desc_rss',
                        'required' => FALSE,
                        'autoload_rte' => FALSE,
                        'lang' => TRUE,
                        'rows' => 8,
                        'cols' => 40,

                    ),



                ),
            ),
        );




        $fields_form15 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );




        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'urlrewritesettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesUrlrewriteSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );




        return  $helper->generateForm(array($fields_form,$fields_form1,$fields_form2,$fields_form3,$fields_form4,$fields_form41,$fields_form5,$fields_form51,
            $fields_form6,
            $fields_form7,$fields_form8,$fields_form9,
            $fields_form10,$fields_form11,
            $fields_form15));
    }

    public function getConfigFieldsValuesUrlrewriteSettings(){

        $languages = Language::getLanguages(false);



        $fields_title_allcat = array();
        $fields_desc_allcat = array();
        $fields_key_allcat = array();
        $fields_blog_cat_alias = array();

        $fields_title_allposts = array();
        $fields_desc_allposts = array();
        $fields_key_allposts = array();
        $fields_blog_alias = array();

        $fields_title_allcom = array();
        $fields_desc_allcom = array();
        $fields_key_allcom = array();
        $fields_blog_allcom_alias = array();

        $fields_title_alltags = array();
        $fields_desc_alltags = array();
        $fields_key_alltags = array();
        $fields_blog_alltags_alias = array();

        $fields_blog_tag_alias = array();

        $fields_title_allauthors = array();
        $fields_desc_allauthors = array();
        $fields_key_allauthors = array();
        $fields_blog_authors_alias = array();

        $fields_blog_author_alias = array();

        $fields_title_myava = array();
        $fields_desc_myava = array();
        $fields_key_myava = array();
        $fields_blog_gallery_alias = array();





        $fields_title_myposts = array();
        $fields_desc_myposts = array();
        $fields_key_myposts = array();
        $fields_blog_mava_alias = array();

        $fields_title_mycom = array();
        $fields_desc_mycom = array();
        $fields_key_mycom = array();
        $fields_blog_mbposts_alias = array();

        $fields_title_gallery = array();
        $fields_desc_gallery = array();
        $fields_key_gallery = array();
        $fields_blog_mbcom_alias = array();


        $fields_title_rss = array();
        $fields_desc_rss = array();
        $fields_key_rss = array();
        $fields_blog_rss_alias = array();


        $fields_title_loyalty = array();
        $fields_desc_loyalty = array();
        $fields_key_loyalty = array();
        $fields_blog_loyalty_alias = array();


        foreach ($languages as $lang)
        {


            $fields_title_allcat[$lang['id_lang']] =  Configuration::get($this->name.'title_allcat_'.$lang['id_lang']);
            $fields_desc_allcat[$lang['id_lang']] =  Configuration::get($this->name.'desc_allcat_'.$lang['id_lang']);
            $fields_key_allcat[$lang['id_lang']] =  Configuration::get($this->name.'key_allcat_'.$lang['id_lang']);
            $fields_blog_cat_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_cat_alias_'.$lang['id_lang']);


            $fields_title_allposts[$lang['id_lang']] =  Configuration::get($this->name.'title_allposts_'.$lang['id_lang']);
            $fields_desc_allposts[$lang['id_lang']] =  Configuration::get($this->name.'desc_allposts_'.$lang['id_lang']);
            $fields_key_allposts[$lang['id_lang']] =  Configuration::get($this->name.'key_allposts_'.$lang['id_lang']);
            $fields_blog_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_alias_'.$lang['id_lang']);

            $fields_title_allcom[$lang['id_lang']] =  Configuration::get($this->name.'title_allcom_'.$lang['id_lang']);
            $fields_desc_allcom[$lang['id_lang']] =  Configuration::get($this->name.'desc_allcom_'.$lang['id_lang']);
            $fields_key_allcom[$lang['id_lang']] =  Configuration::get($this->name.'key_allcom_'.$lang['id_lang']);
            $fields_blog_allcom_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_allcom_alias_'.$lang['id_lang']);

            $fields_title_alltags[$lang['id_lang']] =  Configuration::get($this->name.'title_alltags_'.$lang['id_lang']);
            $fields_desc_alltags[$lang['id_lang']] =  Configuration::get($this->name.'desc_alltags_'.$lang['id_lang']);
            $fields_key_alltags[$lang['id_lang']] =  Configuration::get($this->name.'key_alltags_'.$lang['id_lang']);
            $fields_blog_alltags_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_alltags_alias_'.$lang['id_lang']);

            $fields_blog_tag_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_tag_alias_'.$lang['id_lang']);

            $fields_title_allauthors[$lang['id_lang']] =  Configuration::get($this->name.'title_allauthors_'.$lang['id_lang']);
            $fields_desc_allauthors[$lang['id_lang']] =  Configuration::get($this->name.'desc_allauthors_'.$lang['id_lang']);
            $fields_key_allauthors[$lang['id_lang']] =  Configuration::get($this->name.'key_allauthors_'.$lang['id_lang']);
            $fields_blog_authors_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_authors_alias_'.$lang['id_lang']);

            $fields_blog_author_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_author_alias_'.$lang['id_lang']);

            $fields_title_gallery[$lang['id_lang']] =  Configuration::get($this->name.'title_gallery_'.$lang['id_lang']);
            $fields_desc_gallery[$lang['id_lang']] =  Configuration::get($this->name.'desc_gallery_'.$lang['id_lang']);
            $fields_key_gallery[$lang['id_lang']] =  Configuration::get($this->name.'key_gallery_'.$lang['id_lang']);
            $fields_blog_gallery_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_gallery_alias_'.$lang['id_lang']);





            $fields_title_myava[$lang['id_lang']] =  Configuration::get($this->name.'title_myava_'.$lang['id_lang']);
            $fields_desc_myava[$lang['id_lang']] =  Configuration::get($this->name.'desc_myava_'.$lang['id_lang']);
            $fields_key_myava[$lang['id_lang']] =  Configuration::get($this->name.'key_myava_'.$lang['id_lang']);
            $fields_blog_mava_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_mava_alias_'.$lang['id_lang']);

            $fields_title_myposts[$lang['id_lang']] =  Configuration::get($this->name.'title_myposts_'.$lang['id_lang']);
            $fields_desc_myposts[$lang['id_lang']] =  Configuration::get($this->name.'desc_myposts_'.$lang['id_lang']);
            $fields_key_myposts[$lang['id_lang']] =  Configuration::get($this->name.'key_myposts_'.$lang['id_lang']);
            $fields_blog_mbposts_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_mbposts_alias_'.$lang['id_lang']);

            $fields_title_mycom[$lang['id_lang']] =  Configuration::get($this->name.'title_mycom_'.$lang['id_lang']);
            $fields_desc_mycom[$lang['id_lang']] =  Configuration::get($this->name.'desc_mycom_'.$lang['id_lang']);
            $fields_key_mycom[$lang['id_lang']] =  Configuration::get($this->name.'key_mycom_'.$lang['id_lang']);
            $fields_blog_mbcom_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_mbcom_alias_'.$lang['id_lang']);


            $fields_title_rss[$lang['id_lang']] =  Configuration::get($this->name.'title_rss_'.$lang['id_lang']);
            $fields_desc_rss[$lang['id_lang']] =  Configuration::get($this->name.'desc_rss_'.$lang['id_lang']);
            $fields_key_rss[$lang['id_lang']] =  Configuration::get($this->name.'key_rss_'.$lang['id_lang']);
            $fields_blog_rss_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_rss_alias_'.$lang['id_lang']);


            $fields_title_loyalty[$lang['id_lang']] =  Configuration::get($this->name.'title_loyalty_'.$lang['id_lang']);
            $fields_desc_loyalty[$lang['id_lang']] =  Configuration::get($this->name.'desc_loyalty_'.$lang['id_lang']);
            $fields_key_loyalty[$lang['id_lang']] =  Configuration::get($this->name.'key_loyalty_'.$lang['id_lang']);
            $fields_blog_loyalty_alias[$lang['id_lang']] =  Configuration::get($this->name.'blog_loyalty_alias_'.$lang['id_lang']);



        }


        $data_config = array(
            'urlrewrite_on' => (int)Configuration::get($this->name.'urlrewrite_on'),


            'title_allcat' => $fields_title_allcat,
            'desc_allcat' => $fields_desc_allcat,
            'key_allcat' => $fields_key_allcat,
            'blog_cat_alias' => $fields_blog_cat_alias,

            'title_allposts' => $fields_title_allposts,
            'desc_allposts' => $fields_desc_allposts,
            'key_allposts' => $fields_key_allposts,
            'blog_alias' => $fields_blog_alias,

            'title_allcom' => $fields_title_allcom,
            'desc_allcom' => $fields_desc_allcom,
            'key_allcom' => $fields_key_allcom,
            'blog_allcom_alias'=>$fields_blog_allcom_alias,

            'title_alltags' => $fields_title_alltags,
            'desc_alltags' => $fields_desc_alltags,
            'key_alltags' => $fields_key_alltags,
            'blog_alltags_alias'=>$fields_blog_alltags_alias,

            'blog_tag_alias'=>$fields_blog_tag_alias,

            'title_allauthors' => $fields_title_allauthors,
            'desc_allauthors' => $fields_desc_allauthors,
            'key_allauthors' => $fields_key_allauthors,
            'blog_authors_alias' => $fields_blog_authors_alias,

            'blog_author_alias' => $fields_blog_author_alias,

            'title_gallery' => $fields_title_gallery,
            'desc_gallery' => $fields_desc_gallery,
            'key_gallery' => $fields_key_gallery,
            'blog_gallery_alias' => $fields_blog_gallery_alias,




            'title_myava' => $fields_title_myava,
            'desc_myava' => $fields_desc_myava,
            'key_myava' => $fields_key_myava,
            'blog_mava_alias' => $fields_blog_mava_alias,

            'title_myposts' => $fields_title_myposts,
            'desc_myposts' => $fields_desc_myposts,
            'key_myposts' => $fields_key_myposts,
            'blog_mbposts_alias'=> $fields_blog_mbposts_alias,

            'title_mycom' => $fields_title_mycom,
            'desc_mycom' => $fields_desc_mycom,
            'key_mycom' => $fields_key_mycom,
            'blog_mbcom_alias' => $fields_blog_mbcom_alias,

            'title_rss' => $fields_title_rss,
            'desc_rss' => $fields_desc_rss,
            'key_rss' => $fields_key_rss,
            'blog_rss_alias' => $fields_blog_rss_alias,

            'title_loyalty' => $fields_title_loyalty,
            'desc_loyalty' => $fields_desc_loyalty,
            'key_loyalty' => $fields_key_loyalty,
            'blog_loyalty_alias' => $fields_blog_loyalty_alias,



        );

        return $data_config;

    }



    
private function _sidebarmenu($data = null){


    $alias_pos = $data['alias'];
    $name_pos = $data['name'];
    $icon = $data['icon'];
    $is_layout = $data['is_layout'];


    $fields_form = array(
        'form'=> array(
            'legend' => array(
                'title' => $name_pos,
                'icon' => 'fa '.$icon.' fa-lg'
            ),
            'input' => array(

                array(
                    'type' => 'select',
                    'label' => $this->l('Sidebar position'),
                    'name' => 'sidebar_pos'.$alias_pos,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 1,
                                'name' => $this->l('Left')),

                            array(
                                'id' => 2,
                                'name' => $this->l('Right'),
                            ),

                            array(
                                'id' => 3,
                                'name' => $this->l('No sidebar'),
                            ),


                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    //'desc'=>$this->l('Layout type for pages such as blog page, blog category pages, author pages, all comments pages.')

                )



            ),
        ),
    );

    if($is_layout == 1) {

        array_push(
            $fields_form['form']['input'],
            array(
                'type' => 'select',
                'label' => $this->l('Layout'),
                'name' => 'blog_layout_type' . $alias_pos,
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 1,
                            'name' => $this->l('List')),

                        array(
                            'id' => 2,
                            'name' => $this->l('Grid type 1'),
                        ),

                        array(
                            'id' => 3,
                            'name' => $this->l('Grid type 2'),
                        ),


                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Layout type for') . ' ' . $name_pos

            )

    );

    }


    if($is_layout == 2) {

        array_push(
            $fields_form['form']['input'],
            array(
                'type' => 'select',
                'label' => $this->l('Layout'),
                'name' => 'blog_layout_type' . $alias_pos,
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 1,
                            'name' => $this->l('List')),

                        array(
                            'id' => 2,
                            'name' => $this->l('Grid'),
                        ),




                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Layout type for') . ' ' . $name_pos

            )

        );

    }



    $fields_form2 = array(
        'form' => array(


            'submit' => array(
                'title' => $this->l('Update Settings'),
            )
        ),
    );


    $helper = new HelperForm();



    $helper->table = $this->table;
    $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    $helper->default_form_language = $lang->id;
    $helper->module = $this;
    $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    $helper->identifier = $this->identifier;
    $helper->submit_action = 'sidebarmenusettings'.$alias_pos;
    $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->tpl_vars = array(
        'uri' => $this->getPathUri(),
        'fields_value' => $this->getConfigFieldsValuesSidebarmenuSettings($data),
        'languages' => $this->context->controller->getLanguages(),
        'id_language' => $this->context->language->id
    );




    return  $helper->generateForm(array($fields_form,$fields_form2));
}

    public function getConfigFieldsValuesSidebarmenuSettings($data = null){


        $alias_pos = $data['alias'];



        $data_config = array(
            'sidebar_pos'.$alias_pos=>Configuration::get($this->name.'sidebar_pos'.$alias_pos),
            'blog_layout_type'.$alias_pos=>Configuration::get($this->name.'blog_layout_type'.$alias_pos),



        );

        return $data_config;


    }


    public function getAvailablePages(){

        $data = array(
            1 => array('name'=>$this->l('Blog categories page'),'alias'=>'blog_cat_alias','icon'=>'fa-list','is_layout'=>2), //
            2 => array('name'=>$this->l('Blog category page'),'alias'=>'blog_cat_item_alias','icon'=>'fa-file-text-o','is_layout'=>1),
            3 => array('name'=>$this->l('Blog main page'),'alias'=>'blog_alias','icon'=>'fa-newspaper-o','is_layout'=>1),
            4 => array('name'=>$this->l('Blog post page'),'alias'=>'blog_post_alias','icon'=>'fa-file-text-o','is_layout'=>0),
            5 => array('name'=>$this->l('Blog All Comments page'),'alias'=>'blog_allcom_alias','icon'=>'fa-comments-o','is_layout'=>2), //
            6 => array('name'=>$this->l('Blog All Tags page'),'alias'=>'blog_alltags_alias','icon'=>'fa-tags','is_layout'=>0),
            7 => array('name'=>$this->l('Blog Tag page'),'alias'=>'blog_tag_alias','icon'=>'fa-tag','is_layout'=>1),
            8 => array('name'=>$this->l('Blog Authors page'),'alias'=>'blog_authors_alias','icon'=>'fa-users','is_layout'=>2), //
            9 => array('name'=>$this->l('Blog Author page'),'alias'=>'blog_author_alias','icon'=>'fa-user','is_layout'=>1),
            10 => array('name'=>$this->l('Blog Gallery page'),'alias'=>'blog_gallery_alias','icon'=>'fa-image','is_layout'=>0),
            11 => array('name'=>$this->l('My account -> Blog author avatar page'),'alias'=>'blog_mava_alias','icon'=>'fa-user','is_layout'=>0),
            12 => array('name'=>$this->l('My account -> My Blog Posts page'),'alias'=>'blog_mbposts_alias','icon'=>'fa-newspaper-o','is_layout'=>0),
            13 => array('name'=>$this->l('My account -> My Blog Comments page'),'alias'=>'blog_mbcom_alias','icon'=>'fa-comments-o','is_layout'=>0),
            //12 => array('name'=>$this->l('Blog RSS page'),'alias'=>'blog_rss_alias'),

        );

        return $data;
    }

   public function renderTplCategories(){
   		
    	return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/categories.tpl');
    }

    public function renderTplListCategories(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_categories.tpl');
    }
    
    public function renderTplCategory(){
    	return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/category.tpl');
    }

    public function renderTplListCategory(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_posts.tpl');
    }
    
    public function renderTplAllPosts(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/all-posts.tpl');
    }

    public function renderTplListAllPosts(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_posts.tpl');
    }
    
    public function renderTplAllComments(){
    	return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/all-comments.tpl');
    }

    public function renderTplListAllComments(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_allcomments.tpl');
    }

    public function renderTplListPostComments(){

        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_postcomments.tpl');
    }

    public function renderTplListAllAuthors(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_authors.tpl');
    }

    public function renderTplListMyBlogPosts(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_myblogposts.tpl');
    }

    public function renderTplListMyBlogComments(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_myblogcomments.tpl');
    }

    public function renderTplListGallery(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_gallery.tpl');
    }

    public function renderListMyLoyalty(){
        return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/list_mypoints.tpl');

    }

    
    public function translateItems(){
    	return array('page'=>$this->l('Page'),
    				 'email_subject' =>  $this->l('New Comment from Your Blog'),
                     'email_subject_response' =>  $this->l('Response on the Blog Comment'),
    				 'meta_title_categories' => $this->l('Blog categories'),
    			     'meta_description_categories' => $this->l('Blog categories'),
    				 'meta_keywords_categories' => $this->l('Blog categories'),
    				 'meta_title_all_posts' => $this->l('Blog All Posts'),
    				 'meta_description_all_posts' => $this->l('Blog All Posts'),
    				 'meta_keywords_all_posts' => $this->l('Blog All Posts'),
    				 'add_new' => $this->l('Add New'),
    				 'title_home' => $this->l('Blog'),
    				 'title_categories' => $this->l('Categories'),
    				 'title_posts' => $this->l('Posts'),
    				 'title_comments' => $this->l('Comments'),
    				 'meta_title_all_comments' => $this->l('Blog All Comments'),
    				 'meta_description_all_comments' => $this->l('Blog All Comments'),
    				 'meta_keywords_all_comments' => $this->l('Blog All Comments'),
                     'message_like' => $this->l('You have already voted for this post!'),


                    'msg_cap'=>$this->l('Please, enter the security code'),
                    'msg_comm'=>$this->l('Please, enter the comment'),
                    'msg_name'=>$this->l('Please, enter the name'),
                    'msg_rate'=>$this->l('Please, enter the rating'),
                    'msg_em'=>$this->l('Please, enter the email. For example johndoe@domain.com'),


                    'meta_title_tag' => $this->l('Tag:'),
                    'meta_description_tag' => $this->l('Tag:'),
                    'meta_keywords_tag' => $this->l('Tag:'),

                    'meta_title_tags' => $this->l('Blog Tags'),
                    'meta_description_tags' => $this->l('Blog Tags'),
                    'meta_keywords_tags' => $this->l('Blog Tags'),


                    'meta_title_author' => $this->l('Author:'),
                    'meta_description_author' => $this->l('Author:'),
                    'meta_keywords_author' => $this->l('Author:'),

                    'meta_title_authors'=>$this->l('Blog Authors'),
                    'meta_description_authors'=>$this->l('Blog Authors'),
                    'meta_keywords_authors'=>$this->l('Blog Authors'),

                    'meta_title_myaccount'=>$this->l('My account - Blog Author Avatar'),
                    'meta_description_myaccount'=>$this->l('My account - Blog Author Avatar'),
                    'meta_keywords_myaccount'=>$this->l('My account - Blog Author Avatar'),
                    'ava_msg8'=>$this->l('Invalid file type, please try again!'),
                    'ava_msg9'=>$this->l('Wrong file format, please try again!'),
                    'male'=>$this->l('Male'),
                    'female'=>$this->l('Female'),

                    'meta_title_myblogposts'=>$this->l('My account - My Blog Posts'),
                    'meta_description_myblogposts'=>$this->l('My account - My Blog Posts'),
                    'meta_keywords_myblogposts'=>$this->l('My account - My Blog Posts'),



                    'meta_title_myblogcomments'=>$this->l('My account - My Blog Comments'),
                    'meta_description_myblogcomments'=>$this->l('My account - My Blog Comments'),
                    'meta_keywords_myblogcomments'=>$this->l('My account - My Blog Comments'),

                    'meta_title_myblogcommentsedit'=>$this->l('My account - Edit Blog Comment'),
                    'meta_description_myblogcommentsedit'=>$this->l('My account - Edit Blog Comment'),
                    'meta_keywords_myblogcommentsedit'=>$this->l('My account - Edit Blog Comment'),


                    'old_comment'=>$this->l('Old Comment'),
                    'old_rating'=>$this->l('Old Rating'),

                    'meta_title_myblogpostedit'=>$this->l('My account - Edit Blog Post'),
                    'meta_description_myblogpostedit'=>$this->l('My account - Edit Blog Post'),
                    'meta_keywords_myblogpostedit'=>$this->l('My account - Edit Blog Post'),

                    'meta_title_myblogpostadd'=>$this->l('My account - Add Blog Post'),
                    'meta_description_myblogpostadd'=>$this->l('My account - Add Blog Post'),
                    'meta_keywords_myblogpostadd'=>$this->l('My account - Add Blog Post'),


                    'msg_title'=>$this->l('Please, enter the Blog Post Title'),
                    'msg_content'=>$this->l('Please, enter the Blog Post Content'),

                    'selected_categories_for_email'=>$this->l('Selected categories'),

                    'old_blog_post_title'=>$this->l('Old Blog post title'),
                    'old_blog_post_content'=>$this->l('Old Blog post content'),
                    'old_selected_categories'=>$this->l('Old Selected categories'),


                    'meta_title_gallery' => $this->l('Blog gallery'),
                    'meta_description_gallery' => $this->l('Blog gallery'),
                    'meta_keywords_gallery' => $this->l('Blog gallery'),

                    'c_disabled' => $this->l('Disabled'),
                    'c_enabled' => $this->l('Enabled'),
                    'c_disabled_hidden' => $this->l('Disabled and hidden posts'),



        );
    }


    public function setControllersSettings(){
        $smarty = $this->context->smarty;

        $this->setMainSettings();



        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $smarty->assign($this->name.'is16' , 1);
        } else {
            $smarty->assign($this->name.'is16' , 0);
        }

        if(version_compare(_PS_VERSION_, '1.5', '<')){
            $smarty->assign($this->name.'is14' , 1);
        } else {
            $smarty->assign($this->name.'is14' , 0);
        }

        if(version_compare(_PS_VERSION_, '1.7', '>')){
            $smarty->assign($this->name.'is17' , 1);
        } else {
            $smarty->assign($this->name.'is17' , 0);
        }

        // blog post page
        $smarty->assign($this->name.'urlrewrite_on', Configuration::get($this->name.'urlrewrite_on'));
        $smarty->assign($this->name.'post_display_date', Configuration::get($this->name.'post_display_date'));
        //$smarty->assign($this->name.'is_soc_buttons', Configuration::get($this->name.'is_soc_buttons'));

        $smarty->assign($this->name.'is_blog_f_share', Configuration::get($this->name.'is_blog_f_share'));
        $smarty->assign($this->name.'is_blog_g_share', Configuration::get($this->name.'is_blog_g_share'));
        $smarty->assign($this->name.'is_blog_t_share', Configuration::get($this->name.'is_blog_t_share'));
        $smarty->assign($this->name.'is_blog_p_share', Configuration::get($this->name.'is_blog_p_share'));
        $smarty->assign($this->name.'is_blog_l_share', Configuration::get($this->name.'is_blog_l_share'));
        $smarty->assign($this->name.'is_blog_tu_share', Configuration::get($this->name.'is_blog_tu_share'));
        $smarty->assign($this->name.'is_blog_w_share', Configuration::get($this->name.'is_blog_w_share'));

        $smarty->assign($this->name.'post_views', Configuration::get($this->name.'post_views'));

        $smarty->assign($this->name.'blog_comp_effect', Configuration::get($this->name.'blog_comp_effect'));

        $smarty->assign($this->name.'npr_slider', Configuration::get($this->name.'npr_slider'));
        $smarty->assign($this->name.'np_slider', Configuration::get($this->name.'np_slider'));

        $smarty->assign($this->name.'relpr_slider', Configuration::get($this->name.'relpr_slider'));
        $smarty->assign($this->name.'relp_slider', Configuration::get($this->name.'relp_slider'));

        $smarty->assign($this->name.'rating_post', Configuration::get($this->name.'rating_post'));
        $smarty->assign($this->name.'rating_postp', Configuration::get($this->name.'rating_postp'));
        $smarty->assign($this->name.'rating_postrp', Configuration::get($this->name.'rating_postrp'));

        $smarty->assign($this->name.'is_tags_bp', Configuration::get($this->name.'is_tags_bp'));
        $smarty->assign($this->name.'is_cat_bp', Configuration::get($this->name.'is_cat_bp'));
        $smarty->assign($this->name.'is_author_bp', Configuration::get($this->name.'is_author_bp'));

        $smarty->assign($this->name.'is_captcha_com', Configuration::get($this->name.'is_captcha_com'));

        $smarty->assign($this->name.'bcaptcha_type', Configuration::get($this->name.'bcaptcha_type'));
        $smarty->assign($this->name.'bcaptcha_site_key', Configuration::get($this->name.'bcaptcha_site_key'));
        $smarty->assign($this->name.'bcaptcha_secret_key', Configuration::get($this->name.'bcaptcha_secret_key'));




        // blog post page


        // blog list view
        $smarty->assign($this->name.'p_list_displ_date', Configuration::get($this->name.'p_list_displ_date'));
        $smarty->assign($this->name.'rsson', Configuration::get($this->name.'rsson'));
        $smarty->assign($this->name.'blog_pl_tr', Configuration::get($this->name.'blog_pl_tr'));
        $smarty->assign($this->name.'postl_views', Configuration::get($this->name.'postl_views'));
        $smarty->assign($this->name.'rating_postl', Configuration::get($this->name.'rating_postl'));
        $smarty->assign($this->name.'blog_post_effect', Configuration::get($this->name.'blog_post_effect'));
        // blog list view

        $smarty->assign($this->name.'pic', $this->getCloudImgPath());

        // blog list categories view
        $smarty->assign($this->name.'cat_list_display_date', Configuration::get($this->name.'cat_list_display_date'));
        $smarty->assign($this->name.'blog_cat_effect', Configuration::get($this->name.'blog_cat_effect'));
        $smarty->assign($this->name.'blog_catl_tr', Configuration::get($this->name.'blog_catl_tr'));
        // blog list categories view



        $smarty->assign($this->name.'blog_com_effect', Configuration::get($this->name.'blog_com_effect'));
        $smarty->assign($this->name.'rating_acom', Configuration::get($this->name.'rating_acom'));


        $smarty->assign($this->name.'ava_on', Configuration::get($this->name.'ava_on'));
        $smarty->assign($this->name.'ava_list_displ', Configuration::get($this->name.'ava_list_displ'));
        $smarty->assign($this->name.'ava_post_displ', Configuration::get($this->name.'ava_post_displ'));

        $smarty->assign($this->name.'whocanaddc', (int)Configuration::get($this->name.'whocanaddc'));

        $smarty->assign($this->name.'ava_list_displ_call', Configuration::get($this->name.'ava_list_displ_call'));
        $smarty->assign($this->name.'ava_list_displ_cpost', Configuration::get($this->name.'ava_list_displ_cpost'));

        $is_mobile = 0;

        if(version_compare(_PS_VERSION_, '1.5', '>') && version_compare(_PS_VERSION_, '1.7', '<')){

            require_once(_PS_TOOL_DIR_.'mobile_Detect/Mobile_Detect.php');
            $mobile_detect = new Mobile_Detect();

            if ($mobile_detect->isMobile()){
                $is_mobile = 1;
            }

        }

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $mobile_detect = new \Mobile_Detect();
            if ($mobile_detect->isMobile()) {
                $is_mobile = 1;
            }
        }



        $smarty->assign($this->name.'is_mobile', $is_mobile);


        foreach($this->getAvailablePages() as $available_pages_array) {
            $alias_position = $available_pages_array['alias'];

            $smarty->assign($this->name.'sidebar_pos'.$alias_position, Configuration::get($this->name . 'sidebar_pos' . $alias_position));
            $smarty->assign($this->name.'blog_layout_type'.$alias_position, Configuration::get($this->name . 'blog_layout_type' . $alias_position));
        }

        // test
        //$is_mobile = 1;
        // test

        $this->setLoyalitySettings();

    }


    public function renderTplPost(){
    	return Module::display(_PS_MODULE_DIR_.$this->name.'/blockblog.php', 'views/templates/front/post.tpl');
    }


    private function _effects($data){

        $value = $data['value'];

        $current_value = Configuration::get($this->name.$value);

        $all_effects = array();





        $all_effects[$this->l('Disable All Effects')] = array('disable_all_effects');

        $all_effects[$this->l('Attention Seekers')] = array('bounce','flash','pulse','rubberBand','shake','swing','tada','wobble','jello');
        $all_effects[$this->l('Bouncing Entrances')] = array('bounceIn','bounceInDown','bounceInLeft','bounceInRight','bounceInUp');
        $all_effects[$this->l('Bouncing Exits')] = array('bounceOut','bounceOutDown','bounceOutLeft','bounceOutRight','bounceOutUp');
        $all_effects[$this->l('Fading Entrances')] = array('fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig');
        $all_effects[$this->l('Flippers')] = array('flip','flipInX','flipInY','flipOutX','flipOutY');
        $all_effects[$this->l('Lightspeed')] = array('lightSpeedIn','lightSpeedOut');
        $all_effects[$this->l('Rotating Entrances')] = array('rotateIn','rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight');
        $all_effects[$this->l('Rotating Exits')] = array('rotateOut','rotateOutDownLeft','rotateOutDownRight','rotateOutUpLeft','rotateOutUpRight');
        $all_effects[$this->l('Sliding Entrances')] = array('slideInUp','slideInDown','slideInLeft','slideInRight');
        $all_effects[$this->l('Sliding Exits')] = array('slideOutUp','slideOutDown','slideOutLeft','slideOutRight');
        $all_effects[$this->l('Zoom Entrances')] = array('zoomIn','zoomInDown','zoomInLeft','zoomInRight','zoomInUp');
        $all_effects[$this->l('Zoom Exits')] = array('zoomOut','zoomOutDown','zoomOutLeft','zoomOutRight','zoomOutUp');
        $all_effects[$this->l('Specials')] = array('hinge','rollIn','rollOut');

        $_html = '';
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');


        return $_html;
    }


    public function getIdLang(){
        $cookie = $this->context->cookie;
        $id_lang = (int)($cookie->id_lang);
        return $id_lang;
    }


    private function _linkAHtml($data){

        $title_a = $data['title'];
        $href_a = $data['href'];
        $is_b = isset($data['is_b'])?$data['is_b']:0;
        $onclick = isset($data['onclick'])?$data['onclick']:0;
        $disable_target_blank = isset($data['disable_target_blank'])?$data['disable_target_blank']:0;
        $br_after_count = isset($data['br_after_count'])?$data['br_after_count']:0;

        $_html = '';
        include(dirname(__FILE__).'/views/templates/hooks/'.$this->name.'/'.__FUNCTION__.'.phtml');


        return $_html;
    }

    public function recurseCategoryIds($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL)
    {
        $done = $this->context->done;
        // set variables
        static $_idsCat;

        if ($id_category == 1) {
            $_idsCat = null;
        }
        /*if (!isset($done[$current['infos']['id_parent']]))
            $done[$current['infos']['id_parent']] = 0;
        $done[$current['infos']['id_parent']] += 1;*/
        $_idsCat[] = (string)$id_category;
        if (isset($categories[$id_category]))
            foreach ($categories[$id_category] AS $key => $row)
                if ($key != 'infos')
                    $this->recurseCategoryIds($indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $row);
        return $_idsCat;
    }

    public function getIdsCategories(){
        /// get all category ids ///
        $cookie = $this->context->cookie;
        $cat = new Category();
        $list_cat = $cat->getCategories($cookie->id_lang);
        $current_cat = Category::getRootCategory()->id;
        $cat_ids = $this->recurseCategoryIds($list_cat, $list_cat, $current_cat);
        $cat_ids = implode(",",$cat_ids);
        return $cat_ids;
        /// get all category ids ///
    }

    public function translateCustom(){

        return array(

            'firsttext' => $this->l('You get voucher for discount'),
            'secondtext' => $this->l('Here is you voucher code'),
            'threetext' => $this->l('It is valid until'),
            'tax_included' => $this->l('Tax Included'),
            'tax_excluded' => $this->l('Tax Excluded'),



            'my_loyalty_meta_title' => $this->l('My account - Loyalty Program'),
            'my_loyalty_meta_description' => $this->l('My account - Loyalty Program'),
            'my_loyalty_meta_keywords' => $this->l('My account - Loyalty Program'),

        );
    }

    public function hookRegisterGDPRConsent()
    {
        /* registerGDPRConsent is a special kind of hook that doesn't need a listener, see :
           https://build.prestashop.com/howtos/module/how-to-make-your-module-compliant-with-prestashop-official-gdpr-compliance-module/
          However since Prestashop 1.7.8, modules must implement a listener for all the hooks they register: a check is made
          at module installation.
        */
    }


}



// stub for prestashop 1.7.4.0

if (!function_exists('idn_to_ascii'))
{
    function idn_to_ascii($email)
    {
        return $email;
    }
}