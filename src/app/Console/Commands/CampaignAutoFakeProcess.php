<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\CampaignInstall;
use App\Models\EventLog;
use App\Services\FakeInstallService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CampaignAutoFakeProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CampaignAutoFakeProcess';

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
        $this->processAutoFake();
        return 0;
    }

    public function processAutoFake()
    {
        $campaigns = Campaign::query()
            ->where('status', '=', 1)
            ->where('daily_fake_install', '>', 0)
            ->where('is_fake_on', '=', 1)
            ->get();


        if ($campaigns->count() === 0) {
            $this->warn("No campaign to auto fake");
        }

        /**
         * @var Campaign $campaign
         */
        foreach ($campaigns as $campaign) {


            try {
                $tag = '[AUTO FAKE][' . $campaign->id . ']';
                $this->info("$tag Processing auto fake campaign {$campaign->id} {$campaign->name}");
                $dailyFakeInstall  = $campaign->daily_fake_install;
                $today = date('Y-m-d');
                $currentHour = (int)date('H');
                $todayFakedCount = CampaignInstall::query()
                    ->whereNotNull('faked_at')
                    ->where('campaign_id', $campaign->id)
                    ->where('date_install', $today)
                    ->count();
                $remainFakedInstall = $dailyFakeInstall - $todayFakedCount;
                $service = new FakeInstallService($remainFakedInstall);
                $currentHourInstall = $service->getCount($currentHour);

                $installCount = (int) ceil($currentHourInstall/60);

                for ($i = 1; $i <= $installCount; $i++) {
                    $install = new CampaignInstall();
                    $install->device_id = substr(sha1(uniqid()), 0, 16);
                    $install->faked_at = date('Y-m-d H:i:s');
                    $install->installed_at = date('Y-m-d H:i:s');
                    $install->os = $campaign->os;
                    $install->ip = long2ip(rand(0, 4294967295));
                    $install->campaign_id = $campaign->id;
                    $install->date_install = date('Y-m-d');
                    $install->save();
                }


                $this->info("$tag Added $installCount fake install");
            } catch (\Throwable $ex) {
                $this->error($ex);
            }



        }
    }
}
