<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGetterTables extends Migration
{
    const COMPANIES = [
        1 => "exclusive",
        2 => "motorSPB",
        3 => "autodisk",
        4 => "moscowPidors",
        5 => "diskoptim",
        6 => "yarshintorg",
        7 => "newWheels",
        8 => "bikeTires",
        9 => "autoComponent",
        10 => "tubor",
        12 => "kooper",
        13 => "kik",
        14 => "severAuto",
        15 => "shinService",
        16 => "continental",
        17 => "techLine",
        18 => "euroTuning",
        20 => "Laserta",
        21 => "Autonota",
        22 => "Karaks",
        24 => "Taurus",
        25 => "Henshenn",
        26 => "AutoUnion",
        27 => "SmartTeh",
        28 => "Autoden",
        29 => "Optoviy gorod",
        30 => "Dm group",
        31 => "viragSpb",
        32 => "iFree",
        33 => "fortochki",
        34 => "novline",
        35 => "voshod",
        36 => "carWheels",
        37 => "redWheel",
        38 => "koleso78",
        39 => "stTuning",
        40 => "yultek"
    ];

    const CUSTOMS = [
        ['euroTuning', 'Евротюнинг', 18, false, false, false],
        ['novline', 'novline', 34, false, false, false],
        ['rest_spb2', 'Форточки СПб 2', 33, false, true, false],
        ['SmartTeh', 'СмартТех', 27, true, false, false],
        ['Autoden', 'Автоден', 28, true, false, false],
        ['Optoviy gorod', 'Оптовый город', 29, true, false, false],
        ['Dm group', 'Dm group', 30, true, false, false],
        ['viragSpb', 'Вираж СПб', 31, false, false, false],
        ['iFree', 'КиК', 32, false, false, false],
        ['rest_mkrs', 'Форточки Москва', 33, false, false, false],
        ['rest_sk2', 'Форточки Склад 2', 33, false, true, false],
        ['rest_sk3', 'Форточки Склад 3', 33, false, true, false],
        ['rest_Spb_pish', 'Форточки СПб', 33, false, true, false],
        ['voshod', 'voshod', 35, false, false, false],
        ['Henshenn', 'Хеншенн', 25, true, false, false],
        ['carWheels1', 'Кар Вилс Склад1', 36, false, false, false],
        ['carWheels2', 'Кар Вилс Склад2', 36, false, false, false],
        ['yarshintorg_zakaz', 'Яршинторг Диски на з', 6, false, true, false],
        ['rest_spb_pish2', 'Форточки СПб 3', 33, false, true, false],
        ['rest_yamka', 'Форточки Ямка', 33, false, true, false],
        ['redWheelReg', 'Рэд Вилс Региональны', 37, false, true, false],
        ['redWheelMsc', 'Рэд Вилс Склад МСК', 37, false, true, false],
        ['koleso78', 'Колесо 78', 38, false, false, false],
        ['rest_spb', 'Форточки СПб', 33, false, true, false],
        ['stTuning', 'Ст Тюнинг', 39, false, true, false],
        ['yultek', 'Юлтэк', 40, false, true, false],
        ['yultek_2', 'Юлтэк Москва', 40, false, true, true],
        ['kooper', 'Купер', 12, false, false, false],
        ['motorSPB', 'Мотор', 2, false, false, false],
        ['autodisk', 'Алкар', 3, false, false, false],
        ['moscowPidors', 'Колесный ряд', 4, false, true, false],
        ['diskoptimSpb', 'Дископтим Питер', 5, false, true, false],
        ['yarshintorgSpb', 'Яршинторг Питер', 6, false, true, false],
        ['yarshintorgMsc', 'Яршинторг Ярославль', 6, false, true, false],
        ['first', 'Новые колеса дальний', 7, false, false, false],
        ['second', 'Новые колёса ближний', 7, false, false, false],
        ['autoComponent', 'Авто-компонент', 9, false, false, false],
        ['tubor', 'Тубор', 10, false, false, false],
        ['restspb', 'Яршинторг Питер шины', 6, false, true, false],
        ['rest', 'Яршинторг Яр шины', 6, false, true, false],
        ['AutoUnion', 'АвтоЮнион', 26, true, false, false],
        ['kik', 'Кик (Мир колёс)', 13, false, false, false],
        ['severAuto', 'СеверАвто', 14, false, true, false],
        ['shinServiceSpb', 'Шинсервис', 15, false, true, false],
        ['continental', 'Continental', 16, false, false, false],
        ['diskoptimMsc', 'Дископтим Москва', 5, false, true, false],
        ['techLine', 'techLine', 17, false, false, false],
        ['shinServiceMsc', 'Шинсервис. Москва', 15, false, true, false],
        ['Laserta', '', 20, true, false, false],
        ['Autonota', 'Автонота', 21, true, false, false],
        ['Karaks', 'Каракс', 22, true, false, false],
        ['Taurus', 'Таурус', 24, true, false, false],
        ['exclusive', 'Эксклюзив', 1, false, false, false]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('custom', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ru')->default('');
            $table->unsignedBigInteger('id_company');
            $table->boolean('visible_for_manager')->default(false);
            $table->boolean('parse_on')->default(false);
            $table->boolean('paused')->default(false);

            $table->foreign('id_company')
                ->references('id')
                ->on('company');
        });

        foreach (self::COMPANIES as $id => $name) {
            DB::table('company')->insert([
                'id' => $id,
                'name' => $name,
            ]);
        }

        foreach (self::CUSTOMS as $custom) {
            DB::table('custom')->insert([
                'name' => $custom[0],
                'name_ru' => $custom[1],
                'id_company' => $custom[2],
                'visible_for_manager' => $custom[3],
                'parse_on' => $custom[4],
                'paused' => $custom[5],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom');
        Schema::dropIfExists('company');
    }
}
