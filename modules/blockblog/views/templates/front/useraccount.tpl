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

{literal}
    <script type="text/javascript">
        //<![CDATA[
        var baseDir = '{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}';
        //]]>
    </script>
{/literal}

    {if $blockblogis16 == 1 && $blockblogis17 ==0}
        {capture name=path}<a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
            <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{l s='Blog Author Avatar' mod='blockblog'}{/capture}
    {/if}

    {if $blockblogis17 == 1}
        <a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
        <span class="navigation-pipe"> > </span>{l s='Blog Author Avatar' mod='blockblog'}
    {/if}

    {if $blockblogis16 == 1}
        <h3 class="page-product-heading">{l s='Blog Author Avatar' mod='blockblog'}</h3>

    {else}

        {l s='Blog Author Avatar' mod='blockblog'}

    {/if}


{if ($blockblogstatus_author == 0 || $blockblogstatus_author == 2) && $blockblogstatus_author !== ""}
<div class="advertise-text-blockblog advertise-text-blockblog-text-align">
    {l s='Customer disabled by admin' mod='blockblog'}
</div>
{else}


    <form method="post" action="#" enctype="multipart/form-data"
          id="user_avatar_img" name="user_avatar_img"
            >
        <input type="hidden" name="action" value="addavatar" />

        <div class="b-info-block {if $blockblogis17 == 1}block-categories{/if}">
            <div class="b-body">
                <dl>
                    <dt class="check-box">
                        <label class="check-box">{l s='Show my profile on the site' mod='blockblog'}</label>
                    </dt>
                    <dd class="padding-top-5">
                        <input type="checkbox" name="show_my_profile" id="show_my_profile" class="check-box"
                               {if $blockblogis_show == 1}checked="checked"{/if}
                                />

                        {* loyalty program *}
                        {if $blockblogloyality_onl == 1}
                            {if $blockblogloyality_user_my_show_statusl == "loyality_user_my_show"}

                                {if $blockblogloyality_user_my_show_timesul < $blockblogloyality_user_my_show_timesl}
                                    <br/><br/>
                                    <div class="advertise-text-review">

                                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/flag.png"
                                             alt ="{$blockblogloyality_user_my_show_pointsl|escape:'htmlall':'UTF-8'}"
                                                />&nbsp;{$blockblogloyality_user_my_show_descriptionl|replace:'[points]':$blockblogloyality_user_my_show_pointsl nofilter}

                                    </div>

                                {/if}
                            {/if}
                        {/if}
                        {* loyalty program *}
                    </dd>
                </dl>

                <dl>
                    <dt class="check-box">
                        <label class="check-box">{l s='Information' mod='blockblog'}</label>
                    </dt>
                    <dd class="padding-top-5 info-about-customer-dd">
                        <textarea rows="3" class="form-control" id="info" name="info">{$blockbloginfo|escape:'htmlall':'UTF-8'}</textarea>

                    </dd>
                </dl>

                <dl class="b-photo-ed">
                    <dt><label for="avatar-review">{l s='Avatar:' mod='blockblog'}</label></dt>
                    <dd>
                        <div class="b-avatar">
                            <img class="profile-adv-user-img" src="{$blockblogavatar_thumb|escape:'htmlall':'UTF-8'}"/>
                        </div>

                        <div class="b-edit">
                            {if $blockblogexist_avatar == 1}
                                <input type="radio" name="post_images" checked="" style="display: none">
                            {/if}
                            <input type="file" name="avatar-review" id="avatar-review" />
                        </div>
                        <div class="clr"><!-- --></div>
                        <div class="b-guide">
                            {l s='Allow formats' mod='blockblog'} *.jpg; *.jpeg; *.png; *.gif.
                        </div>
                        <div class="clr"></div>
                        <div class="errorTxtAdd" id="error_avatar-review"></div>
                        {* loyalty program *}
                        {if $blockblogloyality_onl == 1}
                            {if $blockblogloyality_user_my_avatar_statusl == "loyality_user_my_avatar"}

                                {if $blockblogloyality_user_my_avatar_timesul < $blockblogloyality_user_my_avatar_timesl}
                                    <div class="advertise-text-review">

                                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/flag.png"
                                             alt ="{$blockblogloyality_user_my_avatar_pointsl|escape:'htmlall':'UTF-8'}"
                                                />&nbsp;{$blockblogloyality_user_my_avatar_descriptionl|replace:'[points]':$blockblogloyality_user_my_avatar_pointsl nofilter}

                                    </div>

                                {/if}
                            {/if}
                        {/if}
                        {* loyalty program *}


                        {if $blockblogis_demo == 1}
                            <div class="bootstrap">
                                <div class="alert alert-warning">
                                    <button type="button" data-dismiss="alert" class="close">×</button>
                                    <strong>Warning</strong><br>
                                    Feature disabled on the demo mode
                                    &zwnj;</div>
                            </div>
                        {/if}
                    </dd>
                </dl>

                <div class="b-buttons-save">
                    <button class="btn btn-success" value="Add review" type="submit">{l s='Save Changes' mod='blockblog'}</button>
                </div>

            </div>

        </div>

    </form>


{literal}
<script type="text/javascript">

    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function (e) {
            $("#user_avatar_img").on('submit',(function(e) {



                $('#user_avatar_img').css('opacity',0.5);

                e.preventDefault();
                $.ajax({
                    url: '{/literal}{$blockblogajax_url nofilter}{literal}',
                    type: "POST",
                    data:  new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    dataType: 'json',
                    success: function(data)
                    {

                        $('#user_avatar_img').css('opacity',1);

                        if (data.status == 'success') {

                            window.location.reload();


                        } else {

                            var error_type = data.params.error_type;

                            if(error_type == 8){
                                field_state_change_account('avatar-review','failed', '{/literal}{$blockblogava_msg8|escape:'htmlall':'UTF-8'}{literal}');
                                return false;
                            } else if(error_type == 9){
                                field_state_change_account('avatar-review','failed', '{/literal}{$blockblogava_msg9|escape:'htmlall':'UTF-8'}{literal}');
                                return false;
                            }

                        }

                    }
                });





            }));

        });

    });

</script>

{/literal}

{/if}

    <br/>
    <ul class="footer_links clearfix">
        <li class="float-left margin-right-10">
            <a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-blockblog">
                <span><i class="icon-chevron-left"></i> {l s='Home' mod='blockblog'}</span>
            </a>
        </li>
        <li class="float-left">
            <a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-blockblog">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='blockblog'}
			</span>
            </a>
        </li>

    </ul>






