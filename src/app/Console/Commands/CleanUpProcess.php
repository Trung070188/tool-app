<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUpProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CleanUpProcess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('app.env') === 'production') {
            $prev = date('Y-m-d H:i:s', strtotime('-90 days'));
        } else {
            $prev = date('Y-m-d H:i:s', strtotime('-3 days'));
        }

        $deleted1 = DB::table('xlogger')->where('time', '<=', $prev)->delete();
        $deleted2 = DB::table('api_logs')->where('time', '<=', $prev)->delete();
        $this->info("DELETED $deleted1 FROM xlogger");
        $this->info("DELETED $deleted2 FROM api_logs");
        return 0;
    }
}
