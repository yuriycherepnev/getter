<?php namespace App\Service\Getter\ImportCatalog;

class ImportCatalogFactory
{
    const NAMESPACE_FOR_CATALOG = 'console\models\getter\importCatalog\catalogs\\';

    public static function getImportCatalog($company, $type, $path)
    {
        $customs = Custom::getCustomsForGetter($company->id, $type);
        $class = static::getClassname($company->name . ucfirst($type));

        return new $class($path, $customs);
    }

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
