<?php namespace App\Service\Getter\parsers;

use console\models\getter\parsers\Parser;

class CsvParser extends Parser
{
    /**
     * @return false|string
     */
    protected function getContent(array $config = [])
    {
        $opts = [
            'http' => [
                'method' => "GET",
                'header' => "Content-Type: application/xml\r\n"
            ]
        ];
        $context = stream_context_create($opts);

        return file_get_contents($this->importCatalog->getFilePath(), false, $context);
    }
}
