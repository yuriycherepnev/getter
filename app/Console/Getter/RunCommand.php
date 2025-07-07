<?php namespace App\Console\Getter;

use App\Service\Getter\GetterServiceModel;
use Illuminate\Console\Command;

class RunCommand extends Command
{
    /**
     * Название и сигнатура команды
     */
    protected $signature = 'getter:run
                            {goodType : Обязательное имя}
                            {--debug : Режим отладки}';

    /**
     * Описание команды
     */
    protected $description = 'Выгрузка остатков поставщиков';

    /**
     * Логика выполнения команды
     */
    public function handle()
    {
        $goodType = $this->argument('goodType');

        $service = new GetterServiceModel();
        $service->parseGoods();


    }
}
