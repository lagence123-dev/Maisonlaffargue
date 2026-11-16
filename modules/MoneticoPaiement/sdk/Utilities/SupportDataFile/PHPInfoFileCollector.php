<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\SupportDataFile;

use ZipArchive;

final class PHPInfoFileCollector implements FileCollectorInterface
{
    public function collect(ZipArchive $archive): void
    {
        ob_start();
        phpinfo();
        $phpinfoContent = ob_get_clean();

        $archive->addFromString('phpinfo.html', $phpinfoContent);
    }
}