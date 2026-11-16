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

{if $blockblogslider_h_on == 1}
    {if count($blockblogslides)>0}
        <div class="text-align-center">
            <div id="fancytr-blockblog">
                <div id="fancytr">

                    {foreach from=$blockblogslides item=slide name=myLoop}
                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$slide.img|escape:'htmlall':'UTF-8'}" alt="{$slide.title|escape:'htmlall':'UTF-8'}" />
                        <a href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$slide.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$slide.id|escape:'htmlall':'UTF-8'}
                          {/if}" title="{$slide.title|escape:'htmlall':'UTF-8'}"></a>
                    {/foreach}
                </div>
            </div>
        </div>
    {/if}
{/if}

{$blockbloghomeblocks nofilter}

{*

{if $blockblogblock_last_home == 1}

    {if $blockblogisdisablebl == 0 || count($blockblogpostsh) > 0}



<div {if $blockblogis_ps15 !=0  && $blockblogis17 == 0}id="left_column"{/if}>
	<div id="blockblogblock_block_left"
         class="block
         {if $blockblogblog_h == 3}owl_blog_home_recents_posts_type_carousel{/if}
         {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}
         blockblog-block
         {if $blockblogis_ps15 == 0}margin-top-10{/if}
         {if $blockblogis17 == 1}block-categories{/if}"
         >
		<h4 class="title_block {if $blockblogis17 == 1}text-uppercase h6{/if}">
			
			<div {if $blockblogrsson == 1}class="float-left"{/if}>
				<a href="{$blockblogposts_url|escape:'htmlall':'UTF-8'}" title="{l s='Blog Posts recents' mod='blockblog'}"
					>{l s='Blog Posts recents' mod='blockblog'}</a>

			</div>
			{if $blockblogrsson == 1}
			<div class="float-left margin-left-left-10">
				<a href="{$blockblogrss_url|escape:'htmlall':'UTF-8'}" title="{l s='RSS Feed' mod='blockblog'}" target="_blank">
					<img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/feed.png" alt="{l s='RSS Feed' mod='blockblog'}" />
				</a>
			</div>
			{/if}
			
			<div class="clear"></div>
		</h4>
		<div class="block_content block-items-data">
		{if count($blockblogpostsh) > 0}
            {if $blockblogblog_h == 1}
                <div class="items-articles-block">
            {else}
                <ul {if $blockblogblog_h == 3}class="owl-carousel owl-theme"{/if}>
            {/if}
	     {foreach from=$blockblogpostsh item=items name=myLoop1}
	    	{foreach from=$items.data item=blog name=myLoop}
	    	
	    	{if $blockblogblog_h == 1}


                <div class="current-item-block">

                    {if $blockblogblock_display_img == 1}
                        {if strlen($blog.img)>0}
                            <div class="block-side float-left margin-right-10">
                                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blog.img|escape:'htmlall':'UTF-8'}"
                                     title="{$blog.title|escape:'htmlall':'UTF-8'}" alt="{$blog.title|escape:'htmlall':'UTF-8'}"  />
                            </div>
                        {/if}
                    {/if}

                    <div class="block-content">
                        <a class="item-article" title="{$blog.title|escape:'htmlall':'UTF-8'}"
                           href="{if $blockblogurlrewrite_on == 1}
                                    {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}
                                 {else}
                                    {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}
                                 {/if}
                                "
                                >{$blog.title|escape:'htmlall':'UTF-8'}</a>

                            <br/><br/>
                                <div>{$blog.content|strip_tags|mb_substr:0:$blockblogblog_p_tr|escape:'htmlall':'UTF-8'}{if strlen($blog.content)>$blockblogblog_p_tr}...{/if}</div>

                        <div class="clr"></div>

                        {if $blockblogblock_display_date == 1}
                            <span class="float-left block-blog-date"><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$blog.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</span>
                        {/if}

                        <span class="float-right comment block-blog-like">

                            {if $blockblograting_blh == 1}
                                {if $blog.avg_rating != 0}
                                    <span class="rating-input float-left margin-right-10">

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

                            {if $blockblogpostblh_views}
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
                            &nbsp;
                            <a title="{$blog.title|escape:'htmlall':'UTF-8'}"
                               href="{if $blockblogurlrewrite_on == 1}
                                        {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}#blogcomments
                                     {else}
                                        {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}#blogcomments
                                     {/if}"><i
                                        class="fa fa-comments-o fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_comments|escape:'htmlall':'UTF-8'}</span>)</a>
                        </span>


                        <div class="clr"></div>
                    </div>
                </div>



	    	
	    	{elseif $blockblogblog_h == 2}


		   <li class="vertical-blocks-blog {if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
	    		
		    	<table width="100%">
		    				
		    				{if strlen($blog.img)>0}
		    				<tr>
		    					<td align="center" class="text-align-center">
		    						{if $blockblogurlrewrite_on == 1}
			    						<a href="{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}"
			    		  				title="{$blog.title|escape:'htmlall':'UTF-8'}">
			    					{else}
			    						<a href="{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}"
			    		  				title="{$blog.title|escape:'htmlall':'UTF-8'}">
			    		  			{/if}
			    						<img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blog.img|escape:'htmlall':'UTF-8'}"
			    						     title="{$blog.title|escape:'htmlall':'UTF-8'}"
			    						     alt="{$blog.title|escape:'htmlall':'UTF-8'}" />
			    						</a>
			    					
		    					</td>
		    					</tr>
		    				{/if}
		    				<tr>
		    					<td class="v-b-title {if strlen($blog.img)==0}v-b-bottom{/if}">
		    						
		    							{if $blockblogurlrewrite_on == 1}
	    								<a href="{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}"
		    		  						   title="{$blog.title|escape:'htmlall':'UTF-8'}">
	    								{else}
	    									<a href="{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}"
		    		  						   title="{$blog.title|escape:'htmlall':'UTF-8'}">
		    		  					{/if}
		    		  					<b>
		    		  						{$blog.title|escape:'htmlall':'UTF-8'}
		    		  					</b>
		    		  					</a>



		    		  					{if $blockblograting_blh == 1}
		    		  					{if $blog.avg_rating != 0}
		    		  					<div class="clr"></div>
		    		  					<br/>
                                            <span class="rating-input ">

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

		    					</td>
		    				</tr>
		    				<tr>
		    				<td class="v-footer">
		    				{if $blockblogblock_display_date == 1}
                                <span class="float-left block-blog-date"><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$blog.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</span>
                            {/if}

                                <span class="float-right comment block-blog-like">

                                {if $blockblogpostblh_views}
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
                                    &nbsp;
                                    <a title="{$blog.title|escape:'htmlall':'UTF-8'}"
                                       href="{if $blockblogurlrewrite_on == 1}
                                                {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}#blogcomments
                                             {else}
                                                {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}#blogcomments
                                             {/if}"><i
                                                class="fa fa-comments-o fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_comments|escape:'htmlall':'UTF-8'}</span>)</a>
                                </span>
		    		        </td>
		    				</tr>
		    		</table>
		   </li>


		   {elseif $blockblogblog_h == 3}


            <div class="current-item-block">

                    {if $blockblogblock_display_img == 1}
                        {if strlen($blog.img)>0}
                            <div class="text-align-center">
                                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blog.img|escape:'htmlall':'UTF-8'}"
                                     title="{$blog.title|escape:'htmlall':'UTF-8'}" alt="{$blog.title|escape:'htmlall':'UTF-8'}" class="img-responsive" />
                            </div>
                        {/if}
                    {/if}
                    <br/>
                    <div class="block-content text-align-center">
                        <a class="item-article" title="{$blog.title|escape:'htmlall':'UTF-8'}"
                           href="{if $blockblogurlrewrite_on == 1}
                                    {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}
                                 {else}
                                    {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}
                                 {/if}
                                "
                                >{$blog.title|escape:'htmlall':'UTF-8'}</a>

                            <br/><br/>
                                <div>{$blog.content|strip_tags|mb_substr:0:$blockblogblog_p_tr|escape:'htmlall':'UTF-8'}{if strlen($blog.content)>$blockblogblog_p_tr}...{/if}</div>


                        <div class="clr"></div>
                        {if $blockblograting_blh == 1}
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
                            {if $blockblogpostblh_views}
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
                               href="{if $blockblogurlrewrite_on == 1}
                                        {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}#blogcomments
                                     {else}
                                        {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}#blogcomments
                                     {/if}"><i
                                        class="fa fa-comments-o fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_comments|escape:'htmlall':'UTF-8'}</span>)</a>

                            {/if}
                           </span>

                        <div class="clr"></div>
                    </div>
                </div>

		   {/if}
	    	
	    	{/foreach}
	    {/foreach}
        {if $blockblogblog_h == 1}
	        </div>
        {else}
            </ul>


        {/if}
	    <div class="clear"></div>
	    {else}
		<div class="block-no-items">
			{l s='There are not posts yet.' mod='blockblog'}
		</div>
		{/if}
		</div>
	</div>
</div>


{if $blockblogblog_h == 3}
{literal}
<script type="text/javascript">
    var blockblog_number_home_recents_posts_slider = {/literal}{$blockblogblog_bp_sl|escape:'htmlall':'UTF-8'}{literal};
</script>
{/literal}
{/if}


    {/if}


{/if}








{if $blockblogava_on == 1}
    {if $blockblogauthors_home == 1 && count($blockblogcustomers_blockh)>0}

        {if $blockblogis17 == 1}<br/>{/if}

        {if $blockblogis16 == 1 && $blockblogis17 == 0}
            <div {if $blockblogis_ps15 !=0  && $blockblogis17 == 0}id="left_column"{/if}>
        {/if}

        <div id="blockblog_block_home_users"
             class="block
                    {if $blockblogsr_sliderhu == 1}owl_users_home_type_carousel_blockblog{/if}
                    {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} {if $blockblogis17 == 1}block-categories{/if}
                    ">
            <h4  class="title_block {if $blockblogis17 == 1}text-uppercase h6{/if}" {if $blockblogis16 != 1}align="center"{/if}>
                <a href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}"
                        >{l s='Blog Top Authors' mod='blockblog'}</a>
            </h4>
            <div class="block_content">
                {if count($blockblogcustomers_blockh)>0}
                    <ul class="users-block-items {if $blockblogsr_sliderhu == 1}owl-carousel owl-theme{else}home-shoppers{/if}">

                        {foreach from=$blockblogcustomers_blockh item=customer name=myLoop}



                            <li class="float-left border-bottom-none {if $blockblogsr_sliderhu == 1}current-item-block"{/if}">
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




                        {/foreach}

                    </ul>
                    <div class="clr"></div>




                    <p class="block-view-all float-right">
                        <a class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                           href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}" title="{l s='View all authors' mod='blockblog'}">
                            <span>{l s='View all authors' mod='blockblog'}</span>
                        </a>
                    </p>

                    <div class="clr"></div>
                {else}
                    <div class="padding-10">
                        {l s='There are not authors yet.' mod='blockblog'}
                    </div>
                {/if}

            </div>
        </div>

        {if $blockblogis16 == 1 && $blockblogis17 == 0}
            </div>
        {/if}


        {literal}
            <script type="text/javascript">
                var blockblog_number_users_home_slider = {/literal}{$blockblogsr_slhu|escape:'htmlall':'UTF-8'}{literal};
            </script>
        {/literal}

    {/if}


{/if}





{if $blockbloggallery_on == 1}
    {if $blockbloggallery_home == 1 && count($blockbloggalleryblockhome)>0}

        {if $blockblogis17 == 1}<br/>{/if}

    {if $blockblogis16 == 1 && $blockblogis17 == 0}
        <div {if $blockblogis_ps15 !=0  && $blockblogis17 == 0}id="left_column"{/if}>
            {/if}

            <div id="blockblog_block_home_gallery"
                 class="block
                    {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} {if $blockblogis17 == 1}block-categories{/if}
                    ">
                <h4  class="title_block {if $blockblogis17 == 1}text-uppercase h6{/if}" {if $blockblogis16 != 1}align="center"{/if}>
                    <a href="{$blockbloggallery_url|escape:'htmlall':'UTF-8'}"
                            >{l s='Blog Gallery' mod='blockblog'}</a>
                </h4>
                <div class="block_content gallery-block-blockblog-home">
                    {if count($blockbloggalleryblockhome)>0}
                        <ul>

                            {foreach from=$blockbloggalleryblockhome item=item name=myLoop}

                                <li>

                                    <a class="gallery_item" title="{$item.content|escape:'htmlall':'UTF-8'}"
                                       rel="prettyPhotoGalleryBlockHome[gallery]"
                                       href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}">
                                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img_thumb_block_home|escape:'htmlall':'UTF-8'}"
                                             title="{$item.title|escape:'htmlall':'UTF-8'}" alt="{$item.title|escape:'htmlall':'UTF-8'}"
                                             data-original="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}"
                                             class="lazyload">
                                    </a>



                                </li>
                            {/foreach}

                        </ul>
                        <div class="clear"></div>




                        <p class="block-view-all float-right">
                            <a class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                               href="{$blockbloggallery_url|escape:'htmlall':'UTF-8'}" title="{l s='View all gallery' mod='blockblog'}">
                                <span>{l s='View all gallery' mod='blockblog'}</span>
                            </a>
                        </p>

                        <div class="clear"></div>
                    {else}
                        <div class="padding-10">
                            {l s='There are not items yet.' mod='blockblog'}
                        </div>
                    {/if}

                </div>
            </div>

            {if $blockblogis16 == 1 && $blockblogis17 == 0}
        </div>
    {/if}

    {/if}


{/if}*}


