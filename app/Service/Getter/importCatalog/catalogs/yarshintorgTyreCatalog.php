<?php namespace App\Service\Getter\importCatalog\catalogs;

use App\Service\Getter\importCatalog\ImportCatalog;

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
        $restspb = data_get($newGood, 'restspb', 0);
        $rest = data_get($newGood, 'rest', 0);
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
        $season = data_get($newGood, 'season', 0);
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
        $type = data_get($newGood, 'type', null);
        $id = data_get($newGood, 'id', 0);
        $name = data_get($newGood, 'name', '');
        $brand = data_get($newGood, 'brand', '');
        $model = data_get($newGood, 'model', '');
        $w = data_get($newGood, 'w', 0);
        $h = data_get($newGood, 'h', 0);
        $d = data_get($newGood, 'd', 0);
        $speedIndex = data_get($newGood, 'index_speed', '');
        $loadIndex = data_get($newGood, 'index_load', '');

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

        data_set($newGood, 'for_spike', $forSpike);
        data_set($newGood, 'qnt', $qnt);
        data_set($newGood, 'extra_load', $extraLoad);
        data_set($newGood, 'index_speed', $speedIndex);
        data_set($newGood, 'index_load', $loadIndex);
        data_set($newGood, 'w', $w);
        data_set($newGood, 'h', $h);
        data_set($newGood, 'd', $d);
        data_set($newGood, 'c', $c);
        data_set($newGood, 'brand', $brand);
        data_set($newGood, 'model', $model);
        data_set($newGood, 'custom', $custom);
        data_set($newGood, 'season', $season);
        data_set($newGood, 'multiseason', $multiseason);
        data_set($newGood, ImportCatalog::PROVIDER_ARTICLE, $id);
        data_set($newGood, 'id', $idWithCustom);

        return $newGood;
    }
}
