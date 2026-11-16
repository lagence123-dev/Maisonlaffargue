<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\SupportDataFile;

use ZipArchive;

interface FileCollectorInterface
{
    public function collect(ZipArchive $archive): void;
}