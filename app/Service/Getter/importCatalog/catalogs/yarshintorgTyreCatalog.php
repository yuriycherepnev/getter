<?php namespace App\Service\Getter\importCatalog\catalogs;

use common\components\helpers\ArrayHelper;
use console\models\getter\GetterServiceModel;
use console\models\getter\importCatalog\ImportCatalog;
use yii\console\Exception;

class yarshintorgTyreCatalog extends ImportCatalog
{
    /** @var array */
    static public $count = [0, 0];

    const ALLOWED_BRAND = [
        'joyroad',
        'nexen',
        'centara',
        'bars',
        'contyre'
    ];

    const BANNED_TYPES = [
        'Грузовые',
        'Сельскохозяйственные',
        'Шины для вилочных погрузчиков',
    ];

    /** @var string */
    static $extFile = 'txt';

    public function initParseRules()
    {
        $this->params = [
            'code' => ['name' => 'id', 'regExp' => $this->regExp['id'], 'require' => true],
            'brand' => ['name' => 'brand', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'model' => ['name' => 'model', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'name' => ['name' => 'name', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'width' => ['name' => 'w', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'height' => ['name' => 'h', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'diametr' => ['name' => 'd', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'speed_index' => ['name' => 'index_speed', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'load_index' => ['name' => 'index_load', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'thorn' => ['name' => 'spike', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'runflat' => ['name' => 'runFlat', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'season' => ['name' => 'season', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'price' => ['name' => 'price_prime_cost', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'restyar' => ['name' => 'rest', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'restspb' => ['name' => 'restspb', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'type' => ['name' => 'type', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'article' => ['name' => 'manufacturer_article', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'picture' => ['name' => 'image_proposal', 'regExp' => $this->regExp['url'], 'require' => false],
        ];
    }

    /**
     * @param array $good
     * @return array|false
     * @throws Exception
     */
    protected function validateGood($good)
    {
        $newGood = $this->validateProductArray((array)$good);
        $restspb = ArrayHelper::getValue($newGood, 'restspb', 0);
        $rest = ArrayHelper::getValue($newGood, 'rest', 0);
        if ($restspb) {
            $qnt = $restspb;
            $custom = $this->customs['spb'];
        } else {
            $qnt = $rest;
            $custom = $this->customs['msc'];
        }
        $this->addCustomCount($custom);

        if (preg_match('/[(]20[0-9]{2}[)]/', $good['name'])) {
            return false;
        }
        if (!$newGood) {
            ImportCatalog::$errors['notSetGood'] =
                (isset(ImportCatalog::$errors['notSetGood'])) ?
                    ++ImportCatalog::$errors['notSetGood'] : 1;
            return false;
        }
        $multiseason = 0;
        $season = ArrayHelper::getValue($newGood, 'season', 0);
        if ($season === 'summer') {
            $season = 0;
        } elseif ($season === 'winter') {
            $season = 1;
        } elseif ($season === 'multiseason' || $season === 'allseason') {
            $season = 1;
            $multiseason = 1;
        }
        self::$count[$season] ++;
        if (preg_match('/_[\d]{4}$/', $good['article'])) {
            return false;
        }
        $type = ArrayHelper::getValue($newGood, 'type', null);
        $id = ArrayHelper::getValue($newGood, 'id', 0);
        $name = ArrayHelper::getValue($newGood, 'name', '');
        $brand = ArrayHelper::getValue($newGood, 'brand', '');
        $model = ArrayHelper::getValue($newGood, 'model', '');
        $w = ArrayHelper::getValue($newGood, 'w', 0);
        $h = ArrayHelper::getValue($newGood, 'h', 0);
        $d = ArrayHelper::getValue($newGood, 'd', 0);
        $speedIndex = ArrayHelper::getValue($newGood, 'index_speed', '');
        $loadIndex = ArrayHelper::getValue($newGood, 'index_load', '');

        if (!in_array(strtolower($brand), self::ALLOWED_BRAND)) {
            ImportCatalog::$errors['notAllowedBrand'][] = $id;
            return false;
        }

        $forSpike = 0;

        if (in_array($type, self::BANNED_TYPES)) {
            throw new Exception("Type $type is banned");
        } elseif ($type === 'Неошипованные легковые') {
            $forSpike = 1;
        }

        preg_match($this->regExp['c'], $d, $matches);
        $d = isset($matches[1]) ? $matches[1] : "";
        $c = isset($matches[2]) ? 1 : 0;

        if ($qnt <= 0) {
            ImportCatalog::$errors['zeroQnt'][] = $id;
            return false;
        }

        if ($w == "10.5" && $h == "31") {
            $w = "31";
            $h = "10.5";
        }

        if (!$speedIndex && !$loadIndex) {
            preg_match('/[\s,\.]([0-9]{2,3})([A-Z,А-Я])/', $name, $m1);
            if (isset($m1[1]) && isset($m1[2])) {
                $speedIndex = $m1[1];
                $loadIndex = $m1[2];
            }
        }

        $extraLoad = (stristr($name, 'xl')) ? 1 : 0;

        $brand = ucfirst(strtolower(trim($brand)));
        $model = ucfirst(strtolower(trim($model)));

        $idWithCustom =  $custom . "z" . $id;

        ArrayHelper::setValue($newGood, 'for_spike', $forSpike);
        ArrayHelper::setValue($newGood, 'qnt', $qnt);
        ArrayHelper::setValue($newGood, 'extra_load', $extraLoad);
        ArrayHelper::setValue($newGood, 'index_speed', $speedIndex);
        ArrayHelper::setValue($newGood, 'index_load', $loadIndex);
        ArrayHelper::setValue($newGood, 'w', $w);
        ArrayHelper::setValue($newGood, 'h', $h);
        ArrayHelper::setValue($newGood, 'd', $d);
        ArrayHelper::setValue($newGood, 'c', $c);
        ArrayHelper::setValue($newGood, 'brand', $brand);
        ArrayHelper::setValue($newGood, 'model', $model);
        ArrayHelper::setValue($newGood, 'custom', $custom);
        ArrayHelper::setValue($newGood, 'season', $season);
        ArrayHelper::setValue($newGood, 'multiseason', $multiseason);
        ArrayHelper::setValue($newGood, ImportCatalog::PROVIDER_ARTICLE, $id);
        ArrayHelper::setValue($newGood, 'id', $idWithCustom);

        return $newGood;
    }
}
