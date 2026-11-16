{*
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 *}
<div id="columns" class="zentria_onboarding columns-container d-flex flex-column flex-shrink-0 flex-grow-1">
    <div class="container-xxl">
        <div class="row my-5">
            <div class="col-8 mx-auto my-4">
                <div id="onboarding_steps" class="d-flex flex-column">
                    <div class="top-container">
                        <h1 class="h1 mt-2">{l s="Welcome to Zentria" mod='zentria'}</h1>
                        <h5 class="h5 mt-2">
                            {l s="You're almost there!" mod='zentria'}<br>
                            {l s="To take advantage of Zentria, please complete the following steps" mod='zentria'}
                        </h5>
                    </div>
                    <div id="onboarding_item" class="mt-5">
                        <div class="step-item d-flex gap-3 align-items-center">
                            <div class="step-item-icon">
                                {if !$is_module_enabled}
                                    <i class="icon icon-onboarding-step-2"></i>
                                {else}
                                    <div><i class="icon icon-onboarding-step-succeed"></i></div>
                                {/if}
                            </div>
                            <div class="step-item-content">
                                <p class="fw-medium">{l s="Module download and installation" mod='zentria'}</p>
                                {if !$is_module_enabled}
                                    <div class="step-content">
                                        <p class="text-grey">{l s="You must activate the module for it to work" mod='zentria'}</p>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="step-item d-flex gap-3">
                            <div class="step-item-icon">
                                <i class="icon icon-onboarding-step-2"></i>
                            </div>
                            <div class="step-item-content">
                                <p class="fw-medium">{l s="Subscribe to Zentria" mod='zentria'}</p>
                                <div class="step-content">
                                    <p class="text-grey mb-2">{l s="Link your module to your Zentria account to access the interface and benefit from all the information and advice you need to optimize your store" mod='zentria'}</p>
                                    <div class="step-list-btn">
                                        <a class="btn btn-primary-onboarding skip_step" href="https://zentria.profileo.com/{$iso_lang|escape:'html':'UTF-8'}/index.php?controller=detail&module=eo_sub&fc=module&addwebsite=1&id_product=35935" target="_blank">
                                            {l s="Start the 14-day free trial" mod='zentria'}
                                        </a>
                                        <a class="btn btn-outline-primary-onboarding" href="https://www.profileo.com/{$iso_lang|escape:'html':'UTF-8'}/zentria" target="_blank">
                                            {l s="Read more" mod='zentria'}
                                        </a>
                                        <button class="btn btn-outline-primary-onboarding skip_step">
                                            {l s="I already have a subscription" mod='zentria'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="step-item d-flex gap-3 align-items-center">
                            <div class="step-item-icon">
                                <i class="icon icon-onboarding-step-3"></i>
                            </div>
                            <div class="step-item-content">
                                <p class="fw-medium">{l s="Launch a first scan from the Zentria app" mod='zentria'}</p>
                                <div class="step-content inactive_step">
                                    <p class="text-grey mb-2">{l s="Once your subscription has been created, you can run your first scan to check the status of your store" mod='zentria'}</p>
                                    <div class="step-list-btn">
                                        <a class="btn btn-primary-onboarding skip_step" id="validate_onboarding" data-url-validate="{$zentria_configuration|escape:'html':'UTF-8'}" href="https://zentria.profileo.com/{$iso_lang|escape:'html':'UTF-8'}/index.php?controller=zentriadashboard&setDomain={$domain_name|escape:'html':'UTF-8'}&launch_scan=1" target="_blank">
                                            {l s="Launch scan" mod='zentria'}
                                        </a>
                                        <button class="btn btn-outline-primary-onboarding skip_step" id="validate_onboarding_ignore" data-url-validate="{$zentria_configuration|escape:'html':'UTF-8'}">
                                            {l s="Ignore" mod='zentria'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="step-item last-step d-flex gap-3 align-items-center">
                            <div class="step-item-icon">
                                <i class="icon icon-onboarding-step-4"></i>
                            </div>
                            <div class="step-item-content">
                                <p class="fw-medium">{l s="Take full advantage of Zentria" mod='zentria'}</p>
                                <div class="step-content inactive_step">
                                    <p class="text-grey mb-2">{l s="Thank you for your confidence. Please do not hesitate to contact us if you have any concerns" mod='zentria'}</p>
                                    <div class="step-list-btn">
                                        <a class="btn btn-primary-onboarding" href="https://zentria.profileo.com/{$iso_lang|escape:'html':'UTF-8'}/index.php?controller=contact" target="_blank">{l s="Contact us" mod='zentria'}</a>
                                        <a class="btn btn-outline-primary-onboarding" href="{$zentria_configuration|escape:'html':'UTF-8'}">
                                            {l s="Configure" mod='zentria'}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>