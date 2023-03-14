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
            ->where('hourly_fake_install', '>', 0)
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
                $timestamp = date('Y-m-d H:i:s');
                $tag = "[$timestamp][AUTO FAKE][" . $campaign->id . ']';
                $this->info("$tag Processing auto fake campaign {$campaign->id} {$campaign->name}");

                $hourlyFakeInstall  = $campaign->hourly_fake_install;

                $today = date('Y-m-d');
                $fullCurrentHour = date('Y-m-d H:00:00');
                $currentHourFakedCount = CampaignInstall::query()
                    ->whereNotNull('faked_at')
                    ->where('installed_at', '>=', $fullCurrentHour)
                    ->where('campaign_id', $campaign->id)
                    ->where('date_install', $today)
                    ->count();


                $remainFakedInstall = $hourlyFakeInstall - $currentHourFakedCount;

                $this->info("$tag HourlyFakeInstall=$hourlyFakeInstall
                RemainFakedInstall=$remainFakedInstall
                CurrentHourFakedCount=$currentHourFakedCount
                ");
                $installCount = (int) ceil($remainFakedInstall/60);



                for ($i = 1; $i <= $installCount; $i++) {
                    $install = new CampaignInstall();
                    $install->device_id = substr(sha1(uniqid()), 0, 16);
                    $install->faked_at = date('Y-m-d H:i:s');
                    $install->installed_at = date('Y-m-d H:i:s');
                    $install->os = $campaign->os;
                    $install->price = $campaign->price;
                    $install->ip = randomVNIp();
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
