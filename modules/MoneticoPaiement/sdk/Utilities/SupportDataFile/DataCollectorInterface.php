<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\SupportDataFile;

interface DataCollectorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function collect(): array;
}