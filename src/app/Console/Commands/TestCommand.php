<?php

namespace App\Console\Commands;

use App\Services\FakeInstallService;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private FakeInstallService $fakeInstallService;
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
        $total = 1200;
        $installed = 0;
        for ($hour = 0; $hour <= 23; $hour++) {
            $remain = $total - $installed;
            $service = new FakeInstallService($remain);
            $currentHourInstall = $service->getCount($hour);
            $installPerMinute = $currentHourInstall/60;

            for ($i = 0; $i < 60; $i++) {

            }
            $installed += $currentHourInstall;
            echo "Hour $hour, Install = " . $currentHourInstall . ". Remain=$remain, TotalInstalled=$installed\n";
        }


    }


}
