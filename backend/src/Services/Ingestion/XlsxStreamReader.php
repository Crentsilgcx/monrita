<?php

namespace App\Services\Ingestion;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class XlsxStreamReader
{
    public function rows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open XLSX archive');
        }

        $sheetXml = $this->getWorksheetXml($zip);
        $sharedStrings = $this->getSharedStrings($zip);
        $zip->close();

        if ($sheetXml === null) {
            return [];
        }

        $xml = new SimpleXMLElement($sheetXml);
        $rows = [];
        if (!isset($xml->sheetData)) {
            return $rows;
        }
        foreach ($xml->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $column = preg_replace('/\d+/', '', $reference);
                $index = $this->columnIndex($column);
                $value = (string) $cell->v;
                if ((string) $cell['t'] === 's') {
                    $lookupIndex = (int) $value;
                    $value = $sharedStrings[$lookupIndex] ?? '';
                }
                $cells[$index] = $value;
            }
            if (!empty($cells)) {
                ksort($cells);
                $rows[] = array_values($cells);
            }
        }
        return $rows;
    }

    private function getWorksheetXml(ZipArchive $zip): ?string
    {
        $primary = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($primary !== false) {
            return $primary;
        }
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (!empty($stat['name']) && str_starts_with($stat['name'], 'xl/worksheets/sheet')) {
                $xml = $zip->getFromIndex($i);
                if ($xml !== false) {
                    return $xml;
                }
            }
        }
        return null;
    }

    private function getSharedStrings(ZipArchive $zip): array
    {
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml === false) {
            return [];
        }
        $xml = new SimpleXMLElement($sharedXml);
        $strings = [];
        foreach ($xml->si as $si) {
            $text = '';
            if (isset($si->t)) {
                $text = (string) $si->t;
            } elseif (isset($si->r)) {
                foreach ($si->r as $run) {
                    $text .= (string) $run->t;
                }
            }
            $strings[] = $text;
        }
        return $strings;
    }

    private function columnIndex(string $column): int
    {
        $column = strtoupper($column);
        $length = strlen($column);
        $index = 0;
        for ($i = 0; $i < $length; $i++) {
            $index *= 26;
            $index += ord($column[$i]) - 64;
        }
        return max(0, $index - 1);
    }
}
