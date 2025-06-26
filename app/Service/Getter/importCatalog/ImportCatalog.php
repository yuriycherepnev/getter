<?php namespace App\Service\Getter\importCatalog;

use common\components\helpers\ArrayHelper;
use common\models\Company;
use console\models\getter\importCatalog\ImportCatalogInterface;
use Yii;
use yii\console\Exception;

abstract class ImportCatalog implements ImportCatalogInterface
{
    const PROVIDER_ARTICLE = 'provider_article';

    /** @var array */
    static public $errors;

    /** @var string */
    static $extFile = 'xml';

    private static $runFlatSymbols = [
        'rof',
        'SSR',
        'r-f',
        'ZP',
        'ZPS',
        'RFT',
    ];

    private static $runFlatWords = [
        'RunFlat',
        'Run Flat',
        'RunOnFlat',
        'Flat Run',
    ];

    /** @var array */
    protected $regExp = [
        'float' => '/^[-]?[0-9]{1,5}(?:[\.\,][0-9]{1,4})?$/',
        'notNull' => '/^.+$/u',
        'int' => '/^[0-9]{1,3}$/',
        'notZero' => '/^[1-9][0-9]*(?:([\.\,\s]|\xc2\xa0)[0-9]*)?$/',
        'notZeroRest' => '/^(?:[1-9][0-9]*)|(?:больше.+)|(?:Больше.+)$/u',
        'id' => '/^[а-яА-Яa-zA-Z0-9-]+$/u',
        'season' => '/^(s|w)$/i',
        'diam' => '/^r?x?((12|13|14|15|16|17|18|19|20|21|22|23|24|25)([cс]*))$/iu',
        'diamFortochki' => '/^\—?Z?R?x?((12|13|14|15|16|17|18|19|20|21|22|23|24|25)([cCсС]*))$/u',
        'h' => '/^[1-9][0-9][0-9]*/',
        'c' => '/(\d{2})([cCсС])?$/u',
        'indexSpeed' => '/[A-Z]+/',
        'indexLoad' => '/[\d\/]+/',
        'url' => '|(\w-?)+://\S+|',
    ];

    /** @var array */
    protected $pathFile;

    /** @var array */
    protected $params = [];

    /** @var array|int */
    protected $customs;

    /** @var array|int */
    protected $customCount = [];

    /**
     * ImportCatalog constructor.
     * @param array $pathFile
     * @param int|array $customs
     */
    public function __construct($pathFile, $customs)
    {
        $this->initParseRules();
        $this->pathFile = $pathFile;
        $this->customs = $customs;

        if (is_array($customs)) {
            foreach ($customs as $custom) {
                $this->customCount[$custom] = 0;
            }
        } else {
            $this->customCount[$customs] = 0;
        }
    }

    abstract public function initParseRules();

    /**
     * @param string $textInfo
     * @return bool
     */
    public static function searchRunFlat($textInfo)
    {
        foreach (self::$runFlatWords as $runFlatWord) {
            if (strpos(strtolower($textInfo), strtolower($runFlatWord)) !== false) {
                return true;
            }
        }

        foreach (self::$runFlatSymbols as $runFlatSymbol) {
            $regExp = "/\s$runFlatSymbol(\s|$)/i";
            if (preg_match($regExp,  $textInfo)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $company
     * @return array|null
     * @throws \Exception
     */
    public static function getImportCatalogPath($company): ?array
    {
        return ArrayHelper::getValue(Yii::$app->params, 'getter.importCatalogPath.' . $company->name);
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->pathFile;
    }

    /**
     * @param array $goods
     * @return array
     */
    public function validateGoods($goods)
    {
        $newArr = [];
        if ($goods) {
            foreach ($goods as $good) {
                try {
                    $validateGood = [];
                    if ($validateGood = $this->validateGood($good)) {
                        $newArr[] = $validateGood;
                    }
                } catch (\Exception $e) {
                    self::$errors[__FUNCTION__][] = [
                        'good' => $good,
                        'validateGood' => $validateGood,
                        'errorMessage' => $e->getMessage(),
                    ];
                }
            }
        }

        return $newArr;
    }

    /**
     * @return array
     */
    public function getCustomCount(): array
    {
        return $this->customCount;
    }

    /**
     * @return array
     */
    public function getParamsForParse(array $config = [])
    {
        return [];
    }

    /**
     * @return string
     */
    public function getExtensionFile()
    {
        return static::$extFile;
    }

    /**
     * @param array $productArray
     * @param array $params
     * @return array|false
     */
    protected function validateProductArray($productArray, $params = [])
    {
        $params = $params ?: $this->params;
//        $this->dellSpecialChars($productArray);
        $trueArray = [];
        foreach ($params as $k => $v) {
            if ($v['require'] && isset($productArray[$k])) {
                if (preg_match($v['regExp'], $productArray[$k], $matches)) {
                    if (isset($matches[1])) {
                        $trueArray[$v['name']] = $matches[1];
                    } else {
                        $trueArray[$v['name']] = $productArray[$k];
                    }
                } else {
                    $value = $productArray[$k] ?: 'none';
                    ImportCatalog::$errors['errorProductArray'][$k][$value] = $v['regExp'];
                    return false;
                }
            } elseif (!$v['require']) {
                $trueArray[$v['name']] = $productArray[$k] ?? '';
            } else {
                ImportCatalog::$errors['notFoundRequireParam'] =
                    (isset(ImportCatalog::$errors['notFoundRequireParam'])) ?
                        ++ImportCatalog::$errors['notFoundRequireParam'] : 1;
                return false;
            }
        }
        if (isset($trueArray['qnt'])) {
            $trueArray['qnt'] = preg_replace("/(б|Б)ольше /", '', $trueArray['qnt']);
        }
        if (isset($trueArray['manufacturer_article'])) {
            $trueArray['manufacturer_article'] = trim($trueArray['manufacturer_article'], ' ');
        }

        return $trueArray;
    }

    /**
     * @param array $good
     * @return array
     */
    private function dellSpecialChars(&$good)
    {
        $regexToReplace = "/[\'\"><]*/";

        foreach ($good as &$item) {
            $item = preg_replace($regexToReplace, '', $item);
            $item = preg_replace('/[,]/', '.', $item);
        }

        return $good;
    }

    /**
     * @param $customId
     * @return void
     */
    function addCustomCount($customId): void
    {
        if (!array_key_exists($customId, $this->customCount)) {
            $this->customCount[$customId] = 0;
        }
        $this->customCount[$customId]++;
    }
}
