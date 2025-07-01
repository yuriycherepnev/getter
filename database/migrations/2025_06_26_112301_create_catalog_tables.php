<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCatalogTables extends Migration
{
    const GOOD_TYPE = [
        'disk' => 'Диски',
        'tyre' => 'Шины'
    ];

    const TYRE_TYPE = [
        'summertires',
        'wintertires',
    ];

    const DISK_TYPE = [
        'disks',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ru');
        });

        foreach (self::GOOD_TYPE as $name => $nameRu) {
            DB::table('good_type')->insert([
                'name' => $name,
                'name_ru' => $nameRu,
            ]);
        }

        Schema::create('brand', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('id_good_type');
            $table->foreign('id_good_type')
                ->references('id')
                ->on('good_type');
        });

        Schema::create('model', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('id_brand');
            $table->foreign('id_brand')
                ->references('id')
                ->on('brand');
        });

        $moduleCatalogInfo = DB::connection('tyres')
            ->table('module_catalog_info')
            ->select('brand', 'model', 'goods_type')
            ->whereNotNull('model')
            ->where('model', '!=', '')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->groupBy('model')
            ->orderBy('model', 'asc')
            ->get();

        foreach ($moduleCatalogInfo as $infoItem) {
            $idGoodType = null;
            if (in_array($infoItem->goods_type, self::TYRE_TYPE)) {
                $idGoodType = 2;
            }
            if (in_array($infoItem->goods_type, self::DISK_TYPE)) {
                $idGoodType = 1;
            }

            if ($idGoodType) {
                $brandName = trim($infoItem->brand);
                $brandId = DB::table('brand')
                    ->where('name', $brandName)
                    ->where('id_good_type', $idGoodType)
                    ->value('id');

                if (!$brandId) {
                    $brandId = DB::table('brand')->insertGetId([
                        'name' => $brandName,
                        'id_good_type' => $idGoodType,
                    ]);
                }
                $modelName = trim($infoItem->model);

                $modelId = DB::table('model')
                    ->where('name', $modelName)
                    ->where('id_brand', $brandId)
                    ->value('id');

                if (!$modelId) {
                    DB::table('model')->insert([
                        'name' => $modelName,
                        'id_brand' => $brandId,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model');
        Schema::dropIfExists('brand');
        Schema::dropIfExists('good_type');
    }
}
