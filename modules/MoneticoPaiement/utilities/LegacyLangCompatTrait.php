<?php

/**
 * This trait is necessary for Prestashop 9+ to add the translation l() method that was removed from the admin controllers.
 * Before (in 8.x), in was in \ModuleAdminControllerCore, but marked as deprecated.
 */
trait LegacyLangCompatTrait
{
    public function l($string, $class = null, $addslashes = false, $htmlentities = true): string
    {
        if (version_compare(_PS_VERSION_, '9.0.0', '<')) { // PS 1.7.x and 8.x
            return parent::l($string) ?? $string;
        }

        // PS 9.x
        $fileName = (new ReflectionClass($this))->getShortName();
        $translated = $this->module->l($string, $fileName);

        return $translated ?? $string;
    }
}
