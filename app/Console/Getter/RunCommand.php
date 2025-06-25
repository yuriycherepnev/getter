<?php namespace App\Console\Getter;

use Illuminate\Console\Command;

class RunCommand extends Command
{
    /**
     * Название и сигнатура команды
     */
    protected $signature = 'getter:run
                            {name : Обязательное имя}
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
        $name = $this->argument('name');

        echo '<pre>';
        var_dump($name);
        die;
    }
}
