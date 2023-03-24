<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\CampaignInstall;
use App\Models\EventLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CampaignAutoStatusProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CampaignAutoStatusProcess {action}';

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
        $action = $this->argument('action');
        $this->$action();

        return 0;
    }

    /**
     * @example php artisan CampaignAutoStatusProcess processAutoOffByTotalInstall
     * @return void
     */
    public function processAutoOffByTotalInstall()
    {
        $this->info("Process campaign AutoOffByTotalInstall");
        $campaigns = Campaign::query()
            ->where('status', '=', 1)
            ->get();

        /**
         * @var Campaign $campaign
         */
        $today = date('Y-m-d');
        foreach ($campaigns as $campaign) {

            try {
                $tag = '[AutoOffByTotalInstall] [' . $campaign->id . ']';
                $allInstalled = CampaignInstall::query()
                    ->where('campaign_id', $campaign->id)
                    ->count();

                $todayInstalled = CampaignInstall::query()
                    ->where('date_install', $today)
                    ->where('campaign_id', $campaign->id)
                    ->count();

                $campaignDailyInstall = (int)$campaign->daily_install;
                $campaignTotalInstall = (int) $campaign->total_install;

                $p1 = get_percent($allInstalled, $campaignTotalInstall);
                $p2 = get_percent($todayInstalled, $campaignDailyInstall);
                $this->info("$tag Total $allInstalled/$campaignTotalInstall ($p1%),Daily: $todayInstalled/$campaignDailyInstall ($p2%)");

                if ($allInstalled >= $campaignTotalInstall) {
                    $campaign->status = 0;
                    $campaign->open_next_day = 0;
                    $campaign->save();
                    $reason = 'Đã đủ số cài đặt tổng: ' . $campaign->total_install;
                    $log = $this->addAutoOffEventLog($campaign, $reason);
                    $this->warn( $tag . $log->title);
                } else  if ($todayInstalled >= $campaignDailyInstall) {
                    $campaign->status = 0;
                    $campaign->save();
                    $reason = 'Đã đủ số cài đặt hàng ngày: ' . $campaign->daily_install;
                    $log = $this->addAutoOffEventLog($campaign, $reason);
                    $this->warn( $tag . $log->title);
                    continue;
                }
            } catch (\Throwable $ex) {
                $this->error($ex);
            }

        }
    }

    private function addAutoOffEventLog(Campaign $campaign, string $reason): EventLog
    {
        $log = new EventLog();

        $log->campaign_id = $campaign->id;
        $log->time = date('Y-m-d H:i:s');
        $log->title = "Tự động tắt campaign: " . $campaign->id . "::" .
            $campaign->name . ' lúc '. $log->time . '. Lý do: ' . $reason;
        $log->content = $log->title;
        $log->save();

        return $log;
    }

    public function processAutoOff()
    {
        $this->info("Process campaign auto Off");
        $campaigns = Campaign::query()
            ->where('status', '=', 1)
            ->where('auto_off_status', '=', 1)
            ->get();

        if ($campaigns->count() === 0) {
            $this->warn("No campaign to auto off");
        }

        /**
         * @var Campaign $campaign
         */
        foreach ($campaigns as $campaign) {

            try {
                $tag = '[AUTO OFF][' . $campaign->id . ']';
                $this->info("$tag Processing campaign {$campaign->id} {$campaign->name}");
                if (!$campaign->auto_off_at) {
                    $this->warn("$tag Warn: auto_off_at is null. Ignored {$campaign->id}");
                    continue;
                }

                $autoOffAtTime = strtotime($campaign->auto_off_at);
                if ($autoOffAtTime <= time()) {

                    $campaign->status = 0;
                    $campaign->auto_off_status = 0;
                    $campaign->save();
                    $log = new EventLog();
                    $log->time = date('Y-m-d H:i:s');
                    $log->title = "Tự động tắt campaign: " . $campaign->id . "::" . $campaign->name . ' lúc ' . $log->time;
                    $log->content = $log->title;
                    $log->campaign_id = $campaign->id;

                    $this->warn( $tag . $log->title);
                    $log->save();
                } else {
                    $this->warn($tag . "Not enough time");
                }
            } catch (\Throwable $ex) {
                $this->error($ex);
            }

        }
    }

    public function processAutoOn()
    {
        $this->info("Process campaign auto On");
        $campaigns = Campaign::query()
            ->where('status', '=', 0)
            ->where('auto_on_status', '=', 1)
            ->get();

        if ($campaigns->count() === 0) {
            $this->warn("No campaign to auto on");
        }

        /**
         * @var Campaign $campaign
         */
        foreach ($campaigns as $campaign) {


            try {
                $tag = '[AUTO ON] [' . $campaign->id . ']';
                $this->info("$tag Processing campaign {$campaign->id} {$campaign->name}");
                if (!$campaign->auto_on_at) {
                    $this->warn("$tag Warn: auto_on_at is null. Ignored {$campaign->id}");
                    continue;
                }

                $autoOnAtTime = strtotime($campaign->auto_on_at);
                if ($autoOnAtTime <= time()) {

                    $campaign->status = 1;
                    $campaign->auto_on_status = 0;
                    $campaign->save();
                    $log = new EventLog();


                    $log->campaign_id = $campaign->id;
                    $log->time = date('Y-m-d H:i:s');
                    $log->title = "Tự động bật campaign: " . $campaign->id . "::" . $campaign->name . ' lúc '. $log->time;
                    $log->content = $log->title;
                    $this->warn( $tag . $log->title);
                    $log->save();

                } else {
                    $this->warn($tag . "Not enough time");
                }
            } catch (\Throwable $ex) {
                $this->error($ex);

            }

        }
    }

    public function processAutoCheck() {
        $this->info("Process campaign processAutoCheck");
        $campaigns = Campaign::query()
            ->where('status', '=', 1)
            ->get();

        /**
         * @var Campaign $campaign
         */
        $today = date('Y-m-d');
        foreach ($campaigns as $campaign) {

            try {
                $tag = '[AutoCheckInstall] [' . $campaign->id . ']';
                $totalInstalled = CampaignInstall::query()
                    ->where('campaign_id', $campaign->id)
                    ->count();


                $todayInstalled = CampaignInstall::query()
                    ->where('date_install', $today)
                    ->where('campaign_id', $campaign->id)
                    ->count();

                $this->info("$tag TotalInstalled: $totalInstalled/{$campaign->total_install}, Today: $todayInstalled/$campaign->daily_install");

            } catch (\Throwable $ex) {
                $this->error($ex);
            }

        }
    }
}
