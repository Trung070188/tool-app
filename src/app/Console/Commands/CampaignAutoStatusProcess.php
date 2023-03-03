<?php

namespace App\Console\Commands;

use App\Models\Campaign;
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
    protected $signature = 'CampaignAutoStatusProcess';

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
        $this->processAutoOn();
        $this->processAutoOff();

        return 0;
    }

    public function processAutoOff()
    {
        $this->info("Process campaign auto Off");
        $campaigns = Campaign::query()
            ->where('status', '=', 1)
            ->where('auto_off_status', '=', 1)
            ->get();

        if ($campaigns->count() === 0) {
            $this->warn("No campaign to process");
        }

        /**
         * @var Campaign $campaign
         */
        foreach ($campaigns as $campaign) {
            DB::beginTransaction();

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
                    $log->title = "Tự động tắt campaign: " . $campaign->id . "::" . $campaign->name;
                    $log->content = $log->title;
                    $log->campaign_id = $campaign->id;
                    $log->time = date('Y-m-d H:i:s');
                    $this->warn( $tag . $log->title);
                    $log->save();
                    DB::commit();
                } else {
                    $this->warn($tag . "Not enough time");
                }
            } catch (\Throwable $ex) {
                $this->error($ex);
                DB::rollBack();
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
            $this->warn("No campaign to process");
        }

        /**
         * @var Campaign $campaign
         */
        foreach ($campaigns as $campaign) {
            DB::beginTransaction();

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
                    $log->title = "Tự động bật campaign: " . $campaign->id . "::" . $campaign->name;
                    $log->content = $log->title;
                    $log->campaign_id = $campaign->id;
                    $log->time = date('Y-m-d H:i:s');
                    $this->warn( $tag . $log->title);
                    $log->save();
                    DB::commit();
                } else {
                    $this->warn($tag . "Not enough time");
                }
            } catch (\Throwable $ex) {
                $this->error($ex);
                DB::rollBack();
            }

        }
    }
}
