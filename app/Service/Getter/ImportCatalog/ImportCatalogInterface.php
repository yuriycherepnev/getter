<?php namespace App\Service\Getter\ImportCatalog;

interface ImportCatalogInterface
{
    /**
     * @return string
     */
    public function getExtensionFile();

    /**
     * @return array|string
     */
    public function getFilePath();

    /**
     * @return array
     */
    public function getParamsForParse(array $config = []);

    /**
     * @return array
     */
    public function getCustomCount();

    /**
     * @return void
     */
    public function addCustomCount($customId);

    /**
     * @param array $goods
     * @return array
     */
    public function validateGoods($goods);

    public function initParseRules();
}
