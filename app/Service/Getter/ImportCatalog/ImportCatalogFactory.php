<?php namespace App\Service\Getter\ImportCatalog;

use common\models\catalog\Customs;
use common\models\Company;
use console\models\getter\importCatalog\ImportCatalogInterface;
use yii\console\Exception;

class ImportCatalogFactory
{
    const NAMESPACE_FOR_CATALOG = 'console\models\getter\importCatalog\catalogs\\';

    /**
     * @param Company $company
     * @param string $type
     * @param array $path
     * @return ImportCatalogInterface
     * @throws Exception
     */
    public static function getImportCatalog($company, $type, $path)
    {
        $customs = Customs::getCustomsForGetter($company->id, $type);
        $class = static::getClassname($company->name . ucfirst($type));

        return new $class($path, $customs);
    }

    /**
     * @param string $name
     * @return string|string[]
     * @throws Exception
     */
    public static function getClassname(string $name): string
    {
        $className = str_replace(" ", "", self::NAMESPACE_FOR_CATALOG . $name . "Catalog");
        if (class_exists($className)) {
            return $className;
        } else {
            throw new Exception('Class ' . $className . " not found.");
        }
    }
}
