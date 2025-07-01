<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSynonymTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_model_synonym', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_model');
            $table->string('brand_synonym');
            $table->string('model_synonym');

            $table->foreign('id_model')
                ->references('id')
                ->on('model');
        });

        $automaticBrandModels = DB::connection('tyres')
            ->table('automatic_brands_models')
            ->select('brand', 'model', 'synonym1', 'synonym2')
            ->where('synonym2', '!=', '0')
            ->where('synonym2', '!=', '')
            ->where('synonym1', '!=', '0')
            ->where('synonym1', '!=', '')
            ->groupBy('synonym2')
            ->orderBy('model', 'asc')
            ->get();

        foreach ($automaticBrandModels as $automaticItem) {
            $automaticModel = trim($automaticItem->model);
            $modelId = DB::table('model')
                ->where('name', $automaticModel)
                ->value('id');

            if ($modelId) {
                DB::table('brand_model_synonym')->insert([
                    'id_model' => $modelId,
                    'brand_synonym' => trim($automaticItem->synonym1),
                    'model_synonym' => trim($automaticItem->synonym2),
                ]);
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
        Schema::dropIfExists('brand_model_synonym');
    }
}
