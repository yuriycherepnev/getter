<?php namespace App\Service\Getter\ImportCatalog;

interface ImportCatalogInterface
{
    /**
     * @return string
     */
    public function getExtensionFile(): string;

    /**
     * @return array|string
     */
    public function getFilePath();

    /**
     * @param array $config
     * @return array
     */
    public function getParamsForParse(array $config = []): array;

    /**
     * @param array $goods
     * @return array
     */
    public function validateGoods($goods): array;

    public function initParseRules();
}
