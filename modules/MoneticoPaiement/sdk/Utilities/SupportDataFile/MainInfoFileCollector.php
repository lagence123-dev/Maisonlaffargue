<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\SupportDataFile;

use Throwable;
use ZipArchive;

final class MainInfoFileCollector implements FileCollectorInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * @var array<DataCollectorInterface>
     */
    private array $collectors = [];

    public function addDataCollector(DataCollectorInterface $collector): void
    {
        $this->collectors[] = $collector;
    }

    public function collect(ZipArchive $archive): void
    {
        $errors = [];
        foreach ($this->collectors as $collector) {
            // run each collector in a try/catch so that one can fail independently without taking the whole .zip file with them
            try {
                $this->data = array_merge($this->data, $collector->collect());
            } catch (Throwable $e) {
                $collectorFqcn = get_class($collector);
                $exceptionFqcn = get_class($e);
                $exceptionMsg = $e->getMessage();

                $errors[] = "Exception during data collector '$collectorFqcn': '$exceptionFqcn': '$exceptionMsg'";
            }
        }

        if ($errors !== []) {
            $this->data['data_collector_errors'] = $errors;
        }

        $archive->addFromString('info.json', json_encode($this->data, JSON_PRETTY_PRINT));
    }
}