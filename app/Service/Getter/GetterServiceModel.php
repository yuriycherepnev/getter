<?php namespace App\Service\Getter;

use App\Models\Supplier\Company;

class GetterServiceModel
{
    public function parseGoods()
    {
        $companies = Company::getForParse();
        echo '<pre>';
        var_dump(config('getter')  );
        die;
    }
}
