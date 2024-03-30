<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Direction;
use App\Services\Dto\TableRow;
use App\Services\Decimal\Decimal;
use App\Services\Dto\TableRows;
use DOMDocument;
use RuntimeException;
use DOMElement;

class HtmlParser
{
    public function convertToCollection(Direction $direction, string $html): TableRows
    {
        $htmlTable = substr($html, 0, strpos($html, "[delimiter]"));
        $htmlTable = mb_convert_encoding($htmlTable, 'HTML-ENTITIES', "UTF-8");

        $dom = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($htmlTable);
        libxml_use_internal_errors($internalErrors);
        $table = $dom->getElementById('content_table');
        if (null === $table) {
            throw new RuntimeException("html does not contain table with rates data");
        }

        $rows = $table->getElementsByTagName('tr');
        $tableRows = new TableRows($direction);

        /** @var DOMElement $row */
        foreach ($rows as $position => $row) {
            if (0 === $position) {
                continue;
            }

            $cells = $row->getElementsByTagName('td');

            $row = new TableRow(
                strtolower(explode(' ', $cells[1]->nodeValue)[0]),
                new Decimal(explode(' ', $cells[2]->nodeValue)[0]),
                new Decimal(explode(' ', $cells[3]->nodeValue)[0]),
                $position,
            );

            $tableRows->addRow($row);
        }

        return $tableRows;
    }
}
