<?php namespace App\Service\Getter\ImportCatalog\Catalog;

use App\Service\Getter\ImportCatalog\ImportCatalog;

class shinServiceDiskCatalog extends ImportCatalog
{
    /** @var array */
    private $allowedBrands = [
        'ALCAR STAHLRAD (KFZ)',
        'Dotz',
        'DOTZ 4X4 STAHLRADER',
        'Enzo',
        'AEZ',
        'Dezent',
    ];

    const ALLOWED_REST = [33, 647, 662, 666, 665, 669, 1, 672, 682, 683, 684, 720, 721];

    public function initParseRules()
    {
        $this->params = [
            'id' => ['name' => 'id', 'regExp' => $this->regExp['id'], 'require' => true],
            'brand' => ['name' => 'brand', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'model' => ['name' => 'model', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'size' => ['name' => 'size', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'bp' => ['name' => 'lz', 'regExp' => $this->regExp['int'], 'require' => true],
            'pcd' => ['name' => 'pcd', 'regExp' => $this->regExp['float'], 'require' => true],
            'pcd2' => ['name' => 'pcd2', 'regExp' => $this->regExp['float'], 'require' => true],
            'et' => ['name' => 'et', 'regExp' => $this->regExp['float'], 'require' => true],
            'centerbore' => ['name' => 'dia', 'regExp' => $this->regExp['float'], 'require' => true],
            'type' => ['name' => 'type', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'color' => ['name' => 'color', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'price' => ['name' => 'price_prime_cost', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'retail_price' => ['name' => 'recommendedPrice', 'regExp' => $this->regExp['notZero'], 'require' => true],
            'stock' => ['name' => 'stock', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'local_stock' => ['name' => 'local_stock', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'sku' => ['name' => 'manufacturer_article', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'photo' => ['name' => 'image_proposal', 'regExp' => $this->regExp['url'], 'require' => false],
        ];
    }

    /**
     * @param array $goods
     * @return array
     */
    public function validateGoods($goods)
    {
        $newArr = [];
        if ($goods && isset($goods[2]['wheel'])) {
            foreach ($goods[2]['wheel'] as $good) {
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
        $this->addCustomCount($custom);
        try {
            $newGood = [];
            $newGood = $this->validateProductArray((array)$good);
            if (!$newGood) {
                ImportCatalog::$errors['notSetGood'] =
                    (isset(ImportCatalog::$errors['notSetGood'])) ?
                        ++ImportCatalog::$errors['notSetGood'] : 1;
                return false;
            }

            $brand = ArrayHelper::getValue($newGood, 'brand', '');
            $id = ArrayHelper::getValue($newGood, 'id', '');
            $size = ArrayHelper::remove($newGood, 'size', '');

            if (!in_array($brand, $this->allowedBrands)) {
                ImportCatalog::$errors['notAllowedBrand'][] = $id;
                return false;
            }

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
            $sizeExploded = explode('/', $size);

            $dNew = isset($sizeExploded[0]) ? trim($sizeExploded[0]) : '';
            $wNew = isset($sizeExploded[0]) ? str_replace('J', '', trim($sizeExploded[1])) : '';

            ArrayHelper::setValue($newGood, ImportCatalog::PROVIDER_ARTICLE, $id);
            ArrayHelper::setValue($newGood, 'id', $custom . "z" . $id);
            ArrayHelper::setValue($newGood, 'custom', $custom);
            ArrayHelper::setValue($newGood, 'brand', $brand);
            ArrayHelper::setValue($newGood, 'qnt', $qnt);
            ArrayHelper::setValue($newGood, 'd', $dNew);
            ArrayHelper::setValue($newGood, 'w', $wNew);

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
