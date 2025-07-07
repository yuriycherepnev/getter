<?php namespace App\Service\Getter\ImportCatalog\Catalog;

use App\Service\Getter\ImportCatalog\ImportCatalog;
use Illuminate\Support\Arr;

class shinServiceTyreCatalog extends ImportCatalog
{
    /** @var array */
    static public $count = [0, 0];

    const SEASONS = [
        's' => 0,
        'S' => 0,
        'w' => 1,
        'W' => 1
    ];

    const ALLOWED_REST = [33, 647, 662, 666, 665, 669, 1, 672, 682, 683, 684, 720, 721];

    public function initParseRules()
    {
        $this->params = [
            'id' => ['name' => 'id', 'regExp' => $this->regExp['id'], 'require' => true],
            'brand' => ['name' => 'brand', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'model' => ['name' => 'model', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'season' => ['name' => 'season', 'regExp' => $this->regExp['season'], 'require' => true],
            'width' => ['name' => 'w', 'regExp' => $this->regExp['int'], 'require' => true],
            'profile' => ['name' => 'h', 'regExp' => $this->regExp['int'], 'require' => false],
            'diam' => ['name' => 'd', 'regExp' => $this->regExp['diam'], 'require' => true],
            'load' => ['name' => 'load', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'speed' => ['name' => 'index_speed', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'price' => ['name' => 'price_prime_cost', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'retail_price' => ['name' => 'recommendedPrice', 'regExp' => $this->regExp['notZero'], 'require' => true],
            'runflat' => ['name' => 'runFlat', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'stock' => ['name' => 'stock', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'local_stock' => ['name' => 'local_stock', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'sku' => ['name' => 'manufacturer_article', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'sale' => ['name' => 'markdown', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'prod_year' => ['name' => 'prod_year', 'regExp' => $this->regExp['notZero'], 'require' => false],
            'photo' => ['name' => 'image_proposal', 'regExp' => $this->regExp['url'], 'require' => false],
        ];
    }

    /**
     * @param $goods
     * @return array
     */
    public function validateGoods($goods): array
    {
        $newArr = [];
        if ($goods && isset($goods[1]['tire'])) {
            foreach ($goods[1]['tire'] as $good) {
                $amount = 0;
                if (isset($good->shops) && current($good->shops)) {
                    foreach (current($good->shops) as $shop) {
                        $currentShop = current($shop);
                        if ($currentShop && isset($currentShop['amount'])) {
                            $amount += intval(current($shop['amount']));
                        }
                    }
                }
                if ($amount > 0) {
                    $validateGood = $this->validateGood(current($good), $this->customs['spb'], $amount);
                } else if (intval(current($good['local_stock']))) {
                    $validateGood = $this->validateGood(current($good), $this->customs['spb'], current($good['local_stock']));
                } else {
                    $validateGood = $this->validateGood(current($good), $this->customs['msc'], current($good['stock']));
                }
                if ($validateGood) {
                    $newArr[] = $validateGood;
                }
            }
        }

        return $newArr;
    }

    /**
     * @param array $good
     * @param string $custom
     * @param $stock
     * @return array|false
     */
    protected function validateGood($good, $custom, $stock)
    {
        try {
            $newGood = [];
            $good['runflat'] = (isset($good['runflat']) && $good['runflat'] == 'Y') ? 1 : 0;
            $newGood = $this->validateProductArray((array)$good);
            if (!$newGood) {
                ImportCatalog::$errors['notSetGood'] =
                    (isset(ImportCatalog::$errors['notSetGood'])) ?
                        ++ImportCatalog::$errors['notSetGood'] : 1;
                return false;
            }

            $w = Arr::get($newGood, 'w', '');
            $h = Arr::get($newGood, 'h', '');
            $season = Arr::get($newGood, 'season', '');
            $load = Arr::pull($newGood, 'load', '');
            $id = Arr::get($newGood, 'id', '');
            $d = Arr::get($newGood, 'd', '');

            if (array_key_exists($season, self::SEASONS)) {
                $season = self::SEASONS[$season];
            } else {
                $season = 0;
            }
            self::$count[$season]++;
//            if ($custom == 17) {
//                $qnt = ArrayHelper::remove($newGood, 'local_stock', '');
//            } else {
//                $qnt = ArrayHelper::remove($newGood, 'stock', '');
//            }
//            $qnt = 0;
//            foreach ($stock as $k => $v) {
//                if (in_array($v['shop_id'], self::ALLOWED_REST)) {
//                    $qnt += $v['stock'];
//                }
//            }
            $qnt = intval($stock);
            if ($qnt <= 0) {
                ImportCatalog::$errors['zeroQnt'][] = $id;
                return false;
            }

            preg_match($this->regExp['c'], $d, $matches);
            $d = $matches[1] ? (int)$matches[1] : "";
            $c = isset($matches[2]) ? 1 : 0;

            preg_match($this->regExp['indexLoad'], $load, $indexLoad);
            $indexLoadNew = isset($indexLoad[0]) ? $indexLoad[0] : "";
            $extraLoad = 0;

            $markdown = 0;
            $markdownYear = '';
            if (isset($good['prod_year']) && $good['prod_year'] && $good['prod_year'] <= (date('Y') - 2)) {
                $markdown = 1;
                $markdownYear = 'Год выпуска ' . $good['prod_year'];
            }

            Arr::set($newGood, ImportCatalog::PROVIDER_ARTICLE, $id);
            Arr::set($newGood, 'id', $custom . "z" . $id);
            Arr::set($newGood, 'w', strval(intval($w)));
            Arr::set($newGood, 'h', strval(floatval($h)));
            Arr::set($newGood, 'd', $d);
            Arr::set($newGood, 'qnt', $qnt);
            Arr::set($newGood, 'season', $season);
            Arr::set($newGood, 'custom', $custom);
            Arr::set($newGood, 'index_load', $indexLoadNew);
            Arr::set($newGood, 'extra_load', $extraLoad);
            Arr::set($newGood, 'c', $c);
            Arr::set($newGood, 'markdown', $markdown);
            Arr::set($newGood, 'markdown_reason', $markdownYear);

            return $newGood;
        } catch (\Exception $e) {
            self::$errors[__FUNCTION__][] = [
                'good' => $good,
                'validateGood' => $newGood,
                'errorMessage' => $e->getMessage(),
            ];

            return false;
        }
    }
}
