<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\SupportDataFile;

use ZipArchive;

final class ZipFileBuilder
{
    private string $fileName;
    private string $filePath;
    private ZipArchive $file;

    /**
     * @var array<FileCollectorInterface>
     */
    private array $collectors;

    public function __construct()
    {
        $this->fileName = date('YmdHis') . '_monetico_support_data.zip';
        $this->filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->fileName;

        $this->file = new ZipArchive();
        $errorCode = $this->file->open($this->filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($errorCode !== true) {
            $errorCode = (int)$errorCode; // (int) so that false becomes 0
            throw new SupportDataFileException(
                "Couldn't open zip archive with error code: $errorCode", // this will get picked-up and displayed to the user on the admin form
            );
        }

        $this->collectors = [
            new PHPInfoFileCollector(),
            new LogFileCollector(ini_get('error_log'), 'php_error.log'),
        ];
    }

    public function addFileCollector(FileCollectorInterface $collector): void
    {
        $this->collectors[] = $collector;
    }

    public function build(): void
    {
        foreach ($this->collectors as $collector) {
            $collector->collect($this->file);
        }
    }

    /**
     * Outputs the file as binary, with the proper headers and then exits,
     * forcing the file to be downloaded by the client.
     *
     * @return never
     */
    public function sendToClient(): void
    {
        $this->file->close();

        header('Content-Type: application/zip');
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
        header('Content-Length: ' . filesize($this->filePath));

        readfile($this->filePath); // outputs the file

        unlink($this->filePath);

        exit;
    }
}