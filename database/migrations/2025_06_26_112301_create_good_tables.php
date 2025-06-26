<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGoodTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = DB::connection('tyres')
            ->table('module_catalog_info')
            ->select('*')
            ->whereNotNull('model')
            ->where('model', '!=', '')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->groupBy('model')
            ->orderBy('model', 'ASC')
            ->get();

        echo '<pre>';
        var_dump($results);
        die;

//        Schema::create('good', function (Blueprint $table) {
//            $table->id();
//            $table->string('');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_tables');
    }
}
/*
 TODO запросы на данные из tyres
module_catalog_info - brand and model

SELECT * FROM `module_catalog_info`
WHERE `model` IS NOT NULL
AND `model` != ''
AND `brand` IS NOT NULL
AND `brand` != ''
GROUP by model
ORDER BY `module_catalog_info`.`model` ASC


*/
