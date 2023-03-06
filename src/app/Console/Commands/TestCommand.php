<?php

namespace App\Console\Commands;

use App\Components\ReportExcel\ExcelModel\BlockIntegratedBank;
use App\Components\ReportExcel\ExcelModel\BlockTransactionDetail;
use App\Components\ReportExcel\ExcelReportBuilder;
use App\Helpers\ExcelBuilder;
use App\Helpers\PhpDoc;
use App\Models\TransactionTypeCode;
use App\Services\AppStoreService;
use App\Services\XlsxService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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

    private AppStoreService $appStoreService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AppStoreService $appStoreService)
    {
        parent::__construct();
        $this->appStoreService = $appStoreService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dd(config('domain'));
    }
}
