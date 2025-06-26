<?php

use common\models\catalog\goods\GoodType;

return [
    'importCatalogPath' => [
        'yarshintorg' => [
            GoodType::TYPE_DISK_SINGULAR => 'http://terminal.yst.ru/api/xml/disk/d28e0369-0c63-4a97-93b6-edd38e815353',
            GoodType::TYPE_TYRE_SINGULAR_CORRECT => 'http://terminal.yst.ru/api/xml/tyre/d28e0369-0c63-4a97-93b6-edd38e815353',
        ],
        'moscowPidors' => [
            GoodType::TYPE_DISK_SINGULAR => 'https://xn--80aegpbanvh8af7exb.xn--p1ai/1c_data/ostkolrad.xml',
        ],
        'diskoptim' => [
            GoodType::TYPE_DISK_SINGULAR => 'ftp://00-00000210:jqByBr3r@diskoptim.ru/Остатки Дископтим Эксклюзив.xml',
        ],
        'autoComponent' => [
            GoodType::TYPE_ACCUM => 'ftp://exclusive:Lg5231@91.233.117.68/Price.xml',
        ],
        'kooper' => [
            GoodType::TYPE_TYRE_SINGULAR_CORRECT => [
                'url_html' => 'https://cooper.ru/price/cooper_price.html',
                'url_file_prefix' => 'https://cooper.ru/price/cooper_price_files',
            ]
        ],
        'severAuto' => [
            GoodType::TYPE_TYRE_SINGULAR_CORRECT => [
                'catalog' => 'https://webmim.svrauto.ru/api/v1/catalog/unload?access-token=k8gAXGXcp8v2qDe8f2r1C3dgLuPJcy8l&format=xlsx&gzip=0&types=1%3B2&forced=1&stores=1&stocks=1&modifOnly=1&priceOnly=0&use-date=1&sterritoryid=103261489424',
                'prices' => 'https://webmim.svrauto.ru/api/v1/catalog/unload?access-token=k8gAXGXcp8v2qDe8f2r1C3dgLuPJcy8l&format=xlsx&gzip=0&types=1%3B2&forced=1&stores=1&stocks=1&modifOnly=0&priceOnly=1&use-date=1&sterritoryid=103261489424',
            ],
            GoodType::TYPE_DISK_SINGULAR => [
                'catalog' => 'https://webmim.svrauto.ru/api/v1/catalog/unload?access-token=k8gAXGXcp8v2qDe8f2r1C3dgLuPJcy8l&format=xlsx&gzip=0&types=1%3B2&forced=1&stores=1&stocks=1&modifOnly=1&priceOnly=0&use-date=1&sterritoryid=103261489424',
                'prices' => 'https://webmim.svrauto.ru/api/v1/catalog/unload?access-token=k8gAXGXcp8v2qDe8f2r1C3dgLuPJcy8l&format=xlsx&gzip=0&types=1%3B2&forced=1&stores=1&stocks=1&modifOnly=0&priceOnly=1&use-date=1&sterritoryid=103261489424',
            ],
        ],
        'shinService' => [
            GoodType::TYPE_TYRE_SINGULAR_CORRECT => 'https://duplo-api.shinservice.ru/api/v1/data-export/download/b1c83c3ac63a62e497bcc217912eb84a.xml',
            GoodType::TYPE_DISK_SINGULAR => 'https://duplo-api.shinservice.ru/api/v1/data-export/download/b1c83c3ac63a62e497bcc217912eb84a.xml'
        ],
        'continental' => [
            GoodType::TYPE_TYRE_SINGULAR_CORRECT => [
                'user' => 'change',
                'pass' => 'Zg5NIuv86Q',
                'host' => '172.27.178.44',
                'rest_name' => 'CONTINENTAL_STOCK-REPORT.csv',
            ]
        ],
        'fortochki' => [
            GoodType::TYPE_TYRE_SINGULAR_CORRECT => [
                'catalog' => 'https://b2b.4tochki.ru/export_data/M14829.xml',
                'rests' => 'https://b2b.4tochki.ru/export_data/M33260.xml',
            ],
            GoodType::TYPE_DISK_SINGULAR => [
                'catalog' => 'https://b2b.4tochki.ru/export_data/M14829.xml',
                'rests' => 'https://b2b.4tochki.ru/export_data/M33260.xml',
            ],
        ],
        'carWheels' => [
            GoodType::TYPE_DISK_SINGULAR => [
                'catalog' => 'http://carwheels.pro/upload/spb_export_disk_nom.xml',
                'rests' => 'http://carwheels.pro/upload/spb_export_disk_price.xml',
            ]
        ],
        'redWheel' => [
            GoodType::TYPE_DISK_SINGULAR => 'ftp://red-wheel_client7:3GOLAeQm@ftp.red-wheel.nichost.ru/Base.xml',
        ],
        'stTuning' => [
            GoodType::TYPE_DISK_SINGULAR => [
                'user' => 'StFtpUser',
                'pass' => 'Fgthr*#)241&',
                'host' => '81.23.96.254',
                'port' => '2121',
                'file' => 'RIMS.xml',
            ]
        ],
        'yultek' => [
            GoodType::TYPE_DISK_SINGULAR => 'https://b2b.euro-diski.ru/feed/ymarket_feed_opt.xml',
            GoodType::TYPE_TYRE_SINGULAR_CORRECT => 'https://b2b.euro-diski.ru/feed/ymarket_feed_opt.xml',
        ],
    ]
];
