<?php
/**
 *  @author    Amazzing <modules@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_3_0_2($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    Media::clearCache();
    $module_obj->sliderLibrary('install'); // include load_nav_css
    $custom_css = $module_obj->customCode('get', ['type' => 'css']);
    $to_remove = ['length' => 1402, 'md5' => '78ef8303f57874ded6b838da2b991916'];
    $possible_nav_css = substr($custom_css, 0, $to_remove['length']);
    if (md5($possible_nav_css) == $to_remove['md5']) {
        $custom_css = substr($custom_css, $to_remove['length']); // remove nav styles from custom.css
        $module_obj->customCode('save', ['type' => 'css', 'code' => $custom_css]);
    }
    $module_obj->retro()->updateAllWrappersSettings();

    return true;
}
