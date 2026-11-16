<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

namespace DwfProductExtraFields\Component\HttpFoundation\File;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\HttpFoundation\File\UploadedFile;

class OhUploadedFile extends UploadedFile
{
    public $webPath;

    public function setWebPath($webPath) {
        $this->webPath = $webPath;
    }
}
