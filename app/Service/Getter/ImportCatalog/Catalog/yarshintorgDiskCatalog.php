<?php namespace App\Service\Getter\ImportCatalog\Catalog;

use App\Service\Getter\ImportCatalog\ImportCatalog;
use Illuminate\Support\Arr;

class yarshintorgDiskCatalog extends ImportCatalog
{
    const DISK_CUSTOMS = [5, 6];

    public const DISABLED_BRANDS = [
        'arrivo',
        'harp',
        'sdt',
        'vissol'
    ];

    public const ENABLE_BRANDS = [
        'alcasta',
        'cross street',
        'nz',
        'реплика',
        'реплика legeartis',
        'ifree',
        'steger',
        'кик',
        'скад',
        'megami',
        'x-race'
    ];

    /** @var array */
    private $carsArray = [
        'AC' => 'Acura',
        'A' => 'Audi',
        'B' => 'BMW',
        'CL' => 'Cadillac',
        'CHR' => 'Chery',
        'GM' => 'Chevrolet',
        'GN' => 'Chevrolet',
        'CR' => 'Chrysler',
        'Ci' => 'Citroen',
        'DW' => 'Daewoo',
        'FA' => 'FAW',
        'FT' => 'Fiat',
        'FD' => 'Ford',
        'GL' => 'Geely',
        'GW' => 'Great Wall',
        'H' => 'Honda',
        'HND' => 'Hyundai',
        'INF' => 'Infiniti',
        'JG' => 'Jaguar',
        'JP' => 'Jeep',
        'Ki' => 'Kia',
        'LR' => 'Land Rover',
        'LX' => 'Lexus',
        'LF' => 'Lifan',
        'MZ' => 'Mazda',
        'MB' => 'Mercedes',
        'MR' => 'Mercedes',
        'MN' => 'Mini',
        'Mi' => 'Mitsubishi',
        'NS' => 'Nissan',
        'OPL' => 'Opel',
        'PG' => 'Peugeot',
        'PR' => 'Porsche',
        'RN' => 'Renault',
        'ST' => 'Seat',
        'SK' => 'Skoda',
        'SNG' => 'SsangYong',
        'SB' => 'Subaru',
        'SZ' => 'Suzuki',
        'TY' => 'Toyota',
        'VW' => 'Volkswagen',
        'V' => 'Volvo',
        'TG' => 'ТаГАЗ',
    ];

    /** @var string */
    static $extFile = 'txt';

    public function initParseRules()
    {
        $this->params = [
            'code' => ['name' => 'id', 'regExp' => $this->regExp['id'], 'require' => true],
            'brand' => ['name' => 'brand', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'model' => ['name' => 'model', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'name' => ['name' => 'name', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'width' => ['name' => 'w', 'regExp' => $this->regExp['float'], 'require' => true],
            'diametr' => ['name' => 'd', 'regExp' => $this->regExp['int'], 'require' => true],
            'bolts_count' => ['name' => 'lz', 'regExp' => $this->regExp['int'], 'require' => true],
            'bolts_spacing' => ['name' => 'pcd', 'regExp' => $this->regExp['float'], 'require' => true],
            'et' => ['name' => 'et', 'regExp' => $this->regExp['float'], 'require' => true],
            'dia' => ['name' => 'dia', 'regExp' => $this->regExp['float'], 'require' => true],
            'color' => ['name' => 'color', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'price' => ['name' => 'price_prime_cost', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'price_recomend_rozn' => ['name' => 'recommendedPrice', 'regExp' => $this->regExp['notNull'], 'require' => true],
            'restyar' => ['name' => 'rest', 'regExp' => $this->regExp['notZeroRest'], 'require' => false],
            'restspb' => ['name' => 'restspb', 'regExp' => $this->regExp['notZeroRest'], 'require' => false],
            'article' => ['name' => 'manufacturer_article', 'regExp' => $this->regExp['notNull'], 'require' => false],
            'picture' => ['name' => 'image_proposal', 'regExp' => $this->regExp['url'], 'require' => false],
        ];
    }

    /**
     * @param array $good
     * @return array|false
     */
    protected function validateGood($good)
    {
        $newGood = $this->validateProductArray((array)$good);
        $restspb = Arr::pull($newGood, 'restspb', 0);
        $rest = Arr::pull($newGood, 'rest', 0);
        if ($restspb) {
            $qnt = $restspb;
            $custom = $this->customs['spb'];
        } else {
            $qnt = $rest;
            $custom = $this->customs['msc'];
        }
        if (!$newGood) {
            ImportCatalog::$errors['notSetGood'] =
                (isset(ImportCatalog::$errors['notSetGood'])) ?
                    ++ImportCatalog::$errors['notSetGood'] : 1;
            return false;
        }

        if (preg_match('/_[\d]{4}$/', $good['article'])) {
            return false;
        }
        $id = Arr::get($newGood, 'id', 0);
        $brand = Arr::get($newGood, 'brand', 0);
        $model = Arr::get($newGood, 'model', 0);
        $name = Arr::get($newGood, 'name', 0);
        $priceCustom = Arr::get($newGood, 'price_prime_cost', 0);
        $color = mb_strtoupper(Arr::get($newGood, 'color', ''));

        if ($id == '9139485') { //Палет
            return false;
        }

        if ($brand == 'РаспродажаУценка') { //Мусор
            return  false;
        }

        if ($priceCustom <= 0) {
            ImportCatalog::$errors['zeroPrice'][] = $id;
            return false;
        }
        $markdownReason = '';
        $markdown = 0;
        $model = str_replace('_', '', $model);

        if (preg_match("/(" . implode("|", ['Распродажа', 'Уценка']) . ")/iu", $brand)) {
            return false;
            /*$markdown = 1;
            $regBrand = "/(^.+)\s" . preg_quote($model) . "/iu";

            if (preg_match($regBrand, $name, $matches)) {
                if (isset($matches[1])) {
                    $brand = $matches[1];
                }
            }

            $regReason = "/\((.+)\)$/iu";

            if (preg_match($regReason, $name, $matches)) {
                if (isset($matches[1])) {
                    $markdownReason = $matches[1];
                }
            }*/
        }

        if (!in_array($brand, ['NZ', 'YST'])) {
            $brand = ucfirst(strtolower($brand));
        }

        $car = '';

        if ($brand == 'Replica') {
            if (strpos($model, 'Concept-') !== false) {
                $model = str_replace('Concept-', '', $model);
            }

            $model = '(LA) ' . $model;

            foreach ($this->carsArray as $key => $carVal) {
                if (preg_match('/ ' . $key . '[ \d]/', $model)) {
                    $car = $carVal;
                    break;
                }
            }
        }

        if ($qnt <= 1) {
            ImportCatalog::$errors['zeroQnt'][] = $id;
            return false;
        }
        if ($brand == "VISSOL" && preg_match('/^F/', $model)) {
            $qnt = "4";
            $custom = "46";
        }
        if ($qnt == "*") {
            $qnt = 12;
        }
        $priceCustom = floatval(preg_replace('/[^\d\.]/', '', $priceCustom));

        $idWithCustom = $custom . "z" . trim(preg_replace("/[\(\)\-]+/", '', $id));

        Arr::set($newGood, 'markdown', $markdown);
        Arr::set($newGood, 'color', $color);
        Arr::set($newGood, 'markdown_reason', $markdownReason);
        Arr::set($newGood, 'custom', $custom);
        Arr::set($newGood, 'brand', $brand);
        Arr::set($newGood, 'model', $model);
        Arr::set($newGood, 'qnt', $qnt);
        Arr::set($newGood, 'car_brand', $car);
        Arr::set($newGood, 'price_prime_cost', $priceCustom);
        Arr::set($newGood, ImportCatalog::PROVIDER_ARTICLE, strval($id));
        Arr::set($newGood, 'id', $idWithCustom);

        return $newGood;
    }
}
