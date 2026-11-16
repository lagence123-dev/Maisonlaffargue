<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\SupportDataFile;

use ZipArchive;

/**
 * Collector for what would be considered a log file.
 *
 * Only keep the lines from the source file that contains the "monetico" string.
 * Output a blank line in between blocks of consecutive kept lines.
 * Only outputs in the target file the last 500 lines (including the blanks).
 */
final class LogFileCollector implements FileCollectorInterface
{
    private string $sourceFileAbsolutePath;
    private string $outputFileName;
    private bool $filterLines;

    /**
     * @param bool $filterLines Tell whether only lines containing "monetico" will be included in the output file
     */
    public function __construct(
        string $sourceFileAbsolutePath,
        string $outputFileName,
        bool $filterLines = true
    )
    {
        $this->sourceFileAbsolutePath = $sourceFileAbsolutePath;
        $this->outputFileName = $outputFileName;
        $this->filterLines = $filterLines;
    }

    public function collect(ZipArchive $archive): void
    {
        if (!file_exists($this->sourceFileAbsolutePath)) {
            return;
        }

        if (!$this->filterLines) {
            // If we don't need to filter the file's content,
            // directly reference the whole source file.
            $archive->addFile(
                $this->sourceFileAbsolutePath,
                $this->outputFileName
            );

            return;
        }

        $file = fopen($this->sourceFileAbsolutePath, 'r');
        if ($file === false) {
            return;
        }

        $keptLines = [];
        $maxKepLines = 500;

        $lastId = -1;
        $addBlankLine = false;
        while (($line = fgets($file)) !== false) {
            if (strpos(strtolower($line), 'monetico') === false) {
                $addBlankLine = true;
                continue;
            }

            if ($addBlankLine && $lastId > -1) {
                $keptLines[] = PHP_EOL;
                $lastId++;
                $addBlankLine = false;
            }

            $keptLines[] = $line;
            $lastId++;

            if ($lastId >= $maxKepLines) {
                unset($keptLines[$lastId - $maxKepLines]);
                // this ensures the array only contains at most 500 lines
                // without removing the first entry with shift() and thus without reorganizing all the keys every time
            }
        }

        fclose($file);

        if (count($keptLines) > 0) {
            $archive->addFromString($this->outputFileName, implode('', $keptLines));
            // no need to join with PHP_EOL since the carriage return is already at the end of every lines
        }
    }
}