{*
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockblog
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{$blockblogleftblocks nofilter}

{*
{if $blockblogcat_left == 1}


{if $blockblogisdisablebl == 0 || count($blockblogcategories) > 0}

	<div id="blockblogcat_block_left"
         class="block
         {if $blockblogbcat_slider == 1}owl_blog_cat_type_carousel{/if}
         margin-top-10
         {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
         {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
		<h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">
			{l s='Blog Categories' mod='blockblog'}
		</h4>
		<div class="block_content">

            {if count($blockblogcategories) > 0}



            <div class="items-cat-block">

            {if $blockblogbcat_slider == 1 && (count($blockblogcategories) > $blockblogbcat_sl)}<ul class="owl-carousel owl-theme">{/if}

                {foreach from=$blockblogcategories item=items name=myLoop1}
                    {foreach from=$items.data item=blog name=myLoop}

                        {if $blockblogbcat_slider == 1}

                            {if ($smarty.foreach.myLoop1.index % $blockblogbcat_sl == 0) || $smarty.foreach.myLoop1.first}
                                <div>
                            {/if}

                        {/if}

                        <div class="name-category">
                            <a title="{$blog.title|escape:'htmlall':'UTF-8'}"
                               href="{if $blockblogurlrewrite_on == 1}{$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}{/if}"
                                    >{$blog.title|escape:'htmlall':'UTF-8'} ({$blog.count_posts|escape:'htmlall':'UTF-8'})</a>
                        </div>

                        {if $blockblogbcat_slider == 1}

                            {if ($smarty.foreach.myLoop1.index % $blockblogbcat_sl == $blockblogbcat_sl - 1) || $smarty.foreach.myLoop1.last}
                                </div>
                            {/if}
                        {/if}


                    {/foreach}
                {/foreach}

                    {if $blockblogbcat_slider == 1 && (count($blockblogcategories) > $blockblogbcat_sl)}</ul>{/if}

                <p class="block-view-all category-button-view-all">
                    <a title="{l s='View all categories' mod='blockblog'}"  class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                       href="{$blockblogcategories_url|escape:'htmlall':'UTF-8'}"><b>{l s='View all categories' mod='blockblog'}</b></a>
                </p>
            </div>




	    {else}
		<div class="block-no-items">
			{l s='There are not categories yet.' mod='blockblog'}
		</div>
		{/if}
		</div>
	</div>


{/if}

{/if}


{if $blockblogposts_left == 1}


{if $blockblogisdisablebl == 0 || count($blockblogposts) > 0}

	<div id="blockblogposts_block_left"
         class="block
         {if $blockblogbposts_slider == 1}owl_blog_recents_posts_type_carousel{/if}
         {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
         {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} blockblog-block" >
		<h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">
			
				{l s='Blog Posts recents' mod='blockblog'}

			{if $blockblogrsson == 1}
				<a  class="margin-left-left-10" href="{$blockblogrss_url|escape:'htmlall':'UTF-8'}" title="{l s='RSS Feed' mod='blockblog'}" target="_blank">
					<img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/feed.png" alt="{l s='RSS Feed' mod='blockblog'}" />
				</a>
			{/if}
			
		</h4>
		<div class="block_content">

            {if count($blockblogposts) > 0}
            <div class="items-articles-block">


                {if $blockblogbposts_slider == 1 && (count($blockblogposts) > $blockblogbposts_sl)}<ul class="owl-carousel owl-theme">{/if}



                {foreach from=$blockblogposts item=items name=myLoop1}
                {foreach from=$items.data item=blog name=myLoop}

                {if $blockblogbposts_slider == 1}

                    {if ($smarty.foreach.myLoop1.index % $blockblogbposts_sl == 0) || $smarty.foreach.myLoop1.first}
                        <div>
                    {/if}

                {/if}

                    <div class="current-item-block">


                    {if $blockblogblock_display_img == 1}
                        {if strlen($blog.img)>0}
                            <div class="block-side">
                                  <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blog.img|escape:'htmlall':'UTF-8'}"
                                       title="{$blog.title|escape:'htmlall':'UTF-8'}" alt="{$blog.title|escape:'htmlall':'UTF-8'}"  />
                            </div>
                        {/if}
                    {/if}

                    <div class="block-content">
                        <a class="item-article" title="{$blog.title|escape:'htmlall':'UTF-8'}"
                           href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}{/if}"
                                >{$blog.title|escape:'htmlall':'UTF-8'}</a>

                        <div class="blog-block-content">{$blog.content|strip_tags|mb_substr:0:$blockblogblog_tr_b nofilter}{if strlen($blog.content)>$blockblogblog_tr_b}...{/if}</div>

                        {if $blockblograting_bl == 1}
                            {if $blog.avg_rating != 0}
                            <span class="rating-input">

                                {for $foo=0 to 4}
                                    {if $foo < $blog.avg_rating}
                                        <i class="fa fa-star" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>

                                    {else}
                                        <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                    {/if}

                                {/for}


                        </span>
                            {/if}
                        {/if}

                        <div class="clr"></div>
                        {if $blockblogblock_display_date == 1}
                        <span class="float-left block-blog-date"><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$blog.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</span>
                        {/if}
                        <span class="float-right comment block-blog-like">
                            {if $blockblogpostbl_views}
                                <i class="fa fa-eye fa-lg"></i>&nbsp;<span class="blockblog-views">({$blog.count_views|escape:'htmlall':'UTF-8'})</span>&nbsp;&nbsp;
                            {/if}

                            {if $blog.is_liked_post}
                                <i class="fa fa-thumbs-up fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_like|escape:'htmlall':'UTF-8'}</span>)
                            {else}
                                <span class="post-like-{$blog.id|escape:'htmlall':'UTF-8'}">
                                <a onclick="blockblog_like_post({$blog.id|escape:'htmlall':'UTF-8'},1)"
                                   href="javascript:void(0)"><i class="fa fa-thumbs-o-up fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_like|escape:'htmlall':'UTF-8'}</span>)</a>
                                </span>
                            {/if}


                            {if $blog.is_comments || $blog.count_comments > 0}
                            &nbsp;
                            <a title="{$blog.title|escape:'htmlall':'UTF-8'}"
                               href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}#blogcomments{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}#blogcomments{/if}">
                                <i class="fa fa-comments-o fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_comments|escape:'htmlall':'UTF-8'}</span>)</a>
                            {/if}

                        </span>

                        <div class="clr"></div>
                    </div>


                        {if $blockblogbposts_slider == 1}

                            {if ($smarty.foreach.myLoop1.index % $blockblogbposts_sl == $blockblogbposts_sl - 1) || $smarty.foreach.myLoop1.last}
                                </div>
                            {/if}

                        {/if}

                    </div>



                {/foreach}
                {/foreach}


                {if $blockblogbposts_slider == 1  && (count($blockblogposts) > $blockblogbposts_sl)}</ul>{/if}



                <p class="block-view-all">
                        <a href="{$blockblogposts_url|escape:'htmlall':'UTF-8'}" title="{l s='View all posts' mod='blockblog'}" class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                                ><b>{l s='View all posts' mod='blockblog'}</b></a>
                </p>

            </div>
            {else}
                <div class="block-no-items">
                    {l s='There are not posts yet.' mod='blockblog'}
                </div>
            {/if}

		</div>
	</div>

{/if}
{/if}

{if $blockblogcom_left == 1}

{if $blockblogisdisablebl == 0 || count($blockblogcomments) > 0}

<div id="blockblogcomm_block_left"
     class="last-comments-block block
     {if $blockblogbcom_slider == 1}owl_blog_com_type_carousel{/if}
     {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
     {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
	<h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Last Comments' mod='blockblog'}</h4>
	
       	<div class="block_content">
               {if count($blockblogcomments) > 0}
                <div class="items-articles-block">

                    {if $blockblogbcom_slider == 1 && (count($blockblogcomments) > $blockblogbcom_sl)}<ul class="owl-carousel owl-theme">{/if}

                    {foreach from=$blockblogcomments item=comment name=myLoop}

                        {if $blockblogbcom_slider == 1}

                            {if ($smarty.foreach.myLoop.index % $blockblogbcom_sl == 0) || $smarty.foreach.myLoop.first}
                                <div>
                            {/if}

                        {/if}

                        <div class="current-item-block">
                            <a class="item-comm" title="{$comment.comment|escape:'htmlall':'UTF-8'}"

                               href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$comment.post_seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$comment.post_id|escape:'htmlall':'UTF-8'}{/if}"
                                    >
                                {$comment.comment|strip_tags|mb_substr:0:$blockblogblog_com_tr|escape:'htmlall':'UTF-8'}{if strlen($comment.comment)>$blockblogblog_com_tr}...{/if}
                            </a>

                            {if $blockblograting_bllc == 1}
                                <div class="clr"></div>
                                <br/>
                                <span class="rating-input">
                                    {if $comment.rating != 0}
                                        {for $foo=0 to 4}
                                            {if $foo < $comment.rating}
                                                <i class="fa fa-star" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>

                                            {else}
                                                <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                            {/if}

                                        {/for}

                                    {else}

                                        {for $foo=0 to 4}
                                        <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                    {/for}
                                    {/if}
                                </span>
                            {/if}

                            <div class="clr"></div>
                            <small class="float-left block-blog-date"><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$comment.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</small>
                            <small class="float-right block-blog-date">

                                {if $comment.is_show_ava == 0 || ($blockblogava_on == 0)}<i class="fa fa-user"></i>{/if}&nbsp;{if $comment.is_show_ava == 1 && ($blockblogava_on == 1)}<span class="avatar-block-rev">
                                    <img alt="{$comment.name|escape:'htmlall':'UTF-8'}" src="{$comment.avatar|escape:'htmlall':'UTF-8'}" />
                                </span>&nbsp;<a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$comment.author_id|escape:'htmlall':'UTF-8'}-{$comment.author|escape:'htmlall':'UTF-8'}"
                                                title="{$comment.name|escape:'htmlall':'UTF-8'}"
                                                class="blog_post_author">{/if}{if strlen($comment.name)>10}{$comment.name|mb_substr:0:10}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}{if $comment.is_show_ava == 1 && ($blockblogava_on == 1)}</a>{/if}


                            </small>
                            <div class="clr"></div>
                        </div>


                        {if $blockblogbcom_slider == 1}

                            {if ($smarty.foreach.myLoop.index % $blockblogbcom_sl == $blockblogbcom_sl - 1) || $smarty.foreach.myLoop.last}
                                </div>
                            {/if}
                        {/if}

                    {/foreach}

                    {if $blockblogbcom_slider == 1 && (count($blockblogcomments) > $blockblogbcom_sl)}</ul>{/if}

                    <p class="block-view-all">
                        <a title="{l s='View all comments' mod='blockblog'}"  class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                           href="{$blockblogcomments_url|escape:'htmlall':'UTF-8'}"><b>{l s='View all comments' mod='blockblog'}</b></a>
                    </p>
               </div>

			    {else}
				<div class="block-no-items">
					{l s='There are not comments yet.' mod='blockblog'}
				</div>
				{/if}
     		</div>
</div>
{/if}
{/if}





{if $blockblogava_on == 1}
    {if $blockblogauthor_left == 1 && count($blockblogcustomers_block)>0}

        <div id="blockblog_block_left_users"
             class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
                    {if $blockblogsr_slideru == 1 && (count($blockblogcustomers_block) > $blockblogsr_slu)}owl_users_type_carousel_blockblog{/if}
                    blockmanufacturer16 {if $blockblogis17 == 1}block-categories{/if}
                    ">
            <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">
                <a href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}"
                        >{l s='Blog Top Authors' mod='blockblog'}</a>

            </h4>
            <div class="block_content">
                {if count($blockblogcustomers_block)>0}
                    <ul class="users-block-items {if $blockblogsr_slideru == 1 && (count($blockblogcustomers_block) > $blockblogsr_slu)}owl-carousel owl-theme{/if}">


                        {foreach from=$blockblogcustomers_block item=customer name=myLoop}


                            {if $blockblogsr_slideru == 1}

                                {if ($smarty.foreach.myLoop.index % $blockblogsr_slu == 0) || $smarty.foreach.myLoop.first}
                                    <div>
                                {/if}

                            {/if}


                            <li>
                                <img src="{$customer.avatar_thumb|escape:'htmlall':'UTF-8'}"
                                     class="user-img-blockblog"
                                     title="{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                     alt = "{$customer.customer_name|escape:'htmlall':'UTF-8'}" />

                                <div class="float-left">
                                <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                   title="{$customer.customer_name|escape:'htmlall':'UTF-8'}">
                                    {$customer.customer_name|escape:'htmlall':'UTF-8'}
                                </a>
                                    <br/>
                                <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                   title="{l s='View all posts by' mod='blockblog'} {$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                        class="author_count_posts">
                                    {l s='View' mod='blockblog'} {$customer.count_posts|escape:'htmlall':'UTF-8'} {l s='posts' mod='blockblog'}
                                </a>
                                </div>
                                <div class="clr"></div>
                            </li>


                            {if $blockblogsr_slideru == 1}

                                {if ($smarty.foreach.myLoop.index % $blockblogsr_slu == $blockblogsr_slu - 1) || $smarty.foreach.myLoop.last}
                                    </div>
                                {/if}

                            {/if}


                        {/foreach}
                    </ul>
                {else}
                    <div class="padding-10">
                        {l s='There are not authors yet.' mod='blockblog'}
                    </div>
                {/if}


                <p class="block-view-all">
                    <a class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                       href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}" title="{l s='View all authors' mod='blockblog'}">
                        <span>{l s='View all authors' mod='blockblog'}</span>
                    </a>
                </p>


            </div>
        </div>

    {/if}
{/if}




{if $blockblogarch_left == 1}

{if $blockblogisdisablebl == 0 || sizeof($blockblogarch)>0}

<div id="blockblogarch_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} search_blog" >
	<h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Archives' mod='blockblog'}</h4>
	
       	<div class="block_content">
                {if sizeof($blockblogarch)>0}
          			<ul class="bullet">
          				{foreach from=$blockblogarch item=items key=year name=myarch}
          				<li><a class="arch-category" href="javascript:void(0)" 
          					   onclick="show_arch({$smarty.foreach.myarch.index|escape:'htmlall':'UTF-8'},'left')">{$year|escape:'htmlall':'UTF-8'}</a></li>
          				<div id="arch{$smarty.foreach.myarch.index|escape:'htmlall':'UTF-8'}left" 
    						{if $smarty.foreach.myarch.first}{else}class="display-none"{/if}
    						>
          					{foreach from=$items item=item name=myLoop1}
    					<li class="arch-subcat">
    						<a class="arch-subitem" href="{$blockblogposts_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}y={$year|escape:'htmlall':'UTF-8'}&m={$item.month|escape:'htmlall':'UTF-8'}">
    							{$item.time_add|date_format:"%B"|escape:'htmlall':'UTF-8'}&nbsp;({$item.total|escape:'htmlall':'UTF-8'})
    						</a>

    					</li>
    						{/foreach}
    					</div>
    					{/foreach}
    				</ul>
                   {else}
    				{l s='There are not archives yet.' mod='blockblog'}
    				{/if}
                   
            </div>
	
</div>

{/if}
{/if}

{if $blockblogsearch_left == 1}

{if $blockblogisdisablebl == 0 || count($blockblogposts) > 0}
<div id="blockblogsearch_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} search_blog" >
	<h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Search in Blog' mod='blockblog'}</h4>
	 <form method="{if $blockblogurlrewrite_on == 1}get{else}post{/if}" action="{$blockblogposts_url|escape:'htmlall':'UTF-8'}">
       	<div class="block_content">
                    <input type="text" value="" class="search-blog {if $blockblogis17 == 1}search-blog17{/if}" name="search" {if $blockblogis_ps15 == 0}class="search_text"{/if}>
                    <input type="submit" value="go" class="{if $blockblogis17 == 1}button-mini-blockblog{/if} button_mini {if $blockblogis_ps15 == 0}search_go{/if}"/>
                    {if $blockblogis_ps15 == 0}<div class="clear"></div>{/if}
           </div>
	</form>
</div>

{/if}
{/if}


{if $blockblogtag_left == 1}

{if $blockblogisdisablebl == 0 || sizeof($blockblogtags)>0}
    <div id="blockblogtag_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Tags' mod='blockblog'}</h4>
        <div class="block_content tags-block-blockblog">

            {if sizeof($blockblogtags)>0}

            {foreach from=$blockblogtags item=tags name=myLoop}
                <a href="{$blockblogtag_url|escape:'htmlall':'UTF-8'}{$tags.query|escape:'htmlall':'UTF-8'}"
                   class="tag-blockblog"
                        >{$tags.query|escape:'htmlall':'UTF-8'}</a>

            {/foreach}

            <div class="clear"></div>

            <p class="block-view-all">
                <a href="{$blockblogtags_url|escape:'htmlall':'UTF-8'}" title="{l s='View all tags' mod='blockblog'}" class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                        ><b>{l s='View all tags' mod='blockblog'}</b></a>
            </p>
            {else}
                {l s='There are not tags yet.' mod='blockblog'}
            {/if}
        </div>



    </div>

{/if}
{/if}


{if $blockbloggallery_left == 1}

    {if $blockblogisdisablebl == 0 || sizeof($blockbloggalleryblock)>0}
        <div id="blockbloggallery_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
            <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Gallery' mod='blockblog'}</h4>
            <div class="block_content gallery-block-blockblog">

                {if sizeof($blockbloggalleryblock)>0}
                    <ul>
                    {foreach from=$blockbloggalleryblock item=item name=myLoop}
                        <li>

                            <a class="gallery_item" title="{$item.content|escape:'htmlall':'UTF-8'}"
                               rel="prettyPhotoGalleryBlockLeft[gallery]"
                               href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}">
                                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img_thumb_block|escape:'htmlall':'UTF-8'}"
                                     title="{$item.title|escape:'htmlall':'UTF-8'}" alt="{$item.title|escape:'htmlall':'UTF-8'}"
                                     data-original="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}"
                                     class="lazyload">
                            </a>



                        </li>
                    {/foreach}
                    </ul>
                    <div class="clear"></div>

                    <p class="block-view-all">
                        <a href="{$blockbloggallery_url|escape:'htmlall':'UTF-8'}" title="{l s='View all gallery' mod='blockblog'}" class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                                ><b>{l s='View all gallery' mod='blockblog'}</b></a>
                    </p>
                {else}
                    {l s='There are not items yet.' mod='blockblog'}
                {/if}
            </div>



        </div>

    {/if}
{/if}
*}