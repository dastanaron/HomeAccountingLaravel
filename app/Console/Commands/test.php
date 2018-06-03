<?php

namespace App\Console\Commands;

use App\Bills;
use App\Funds;
use Illuminate\Console\Command;

use App\RabbitMQ\Analytics\MessagePush;

class test extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*
        $rabbitMQ = MessagePush::init();

        $messageBody = [
            'method' => 'название метода',
            'param1' => 'Первый параметр',
            'param2' => 'Второй параметр',
        ];

        //$messageBody = 'quit';

        $rabbitMQ->push($messageBody);
        */

        $chartData = \App\Components\DataCharts\ExpensesByCategory::init(1, '2018-04-01 00:00:00','2018-04-31 23:59:59');

        dump($chartData->getJsonByChart());
    }
}
