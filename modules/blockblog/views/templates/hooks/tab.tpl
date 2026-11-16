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

{if $blockblogbtabs_type == 2 || $blockblogbtabs_type == 3}
{if $blockblogtab_blog_pr == 1}

{if count($blockblogrelated_posts) > 0}
<li {if $blockblogbtabs_type == 3}class="nav-item"{/if}>
	<a href="#idTab2018" data-toggle="tab" {if $blockblogbtabs_type == 3}class="nav-link"{/if}>{l s='Related Blog Posts' mod='blockblog'}</a>
</li>
{/if}

{/if}
{/if}