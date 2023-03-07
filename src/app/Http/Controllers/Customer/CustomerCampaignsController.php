<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\CampaignInstall;
use App\Models\Customer;
use App\Models\EventLog;
use App\Services\AppStoreService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Campaign;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class CustomerCampaignsController extends CustomerBaseController
{
    private AppStoreService $appStoreService;

    public function __construct(AppStoreService $appStoreService)
    {
        $this->appStoreService = $appStoreService;
    }

    /**
     * Index page
     * @uri  /xadmin/campaigns/index
     * @throw  NotFoundHttpException
     * @return  View
     */
    public function statistical()
    {
        $title = 'Campaign';
        $component = 'CampaignStatistical';
        return customerVue(compact('title', 'component'));
    }

    public function detail(Request $req)
    {
        $id = $req->id;
        $entry = Campaign::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
         * @var  Customer $entry
         */
        $jsonData = compact('entry');
        $title = 'Thống kê';
        $component = 'CampaignDetail';

        return customerVue(compact('title', 'component'), $jsonData);
    }

    public function dataStatistical(Request $req)
    {
        $user = Auth::user();

        $totalInstall = DB::table('campaign_installs')
            ->select('campaign_id', DB::raw('COUNT(id) as total_install'))
            ->groupBy('campaign_id');

        $query = Campaign::query()
            ->leftJoinSub($totalInstall, 'total_install', function ($join) {
                $join->on('campaigns.id', '=', 'total_install.campaign_id');
            })
            ->select([
                'campaigns.id',
                'campaigns.name',
                'campaigns.package_id',
                'campaigns.icon',
                'campaigns.price',
                'campaigns.os',
                'campaigns.customer_id',
                'campaigns.type',
                'campaigns.created_at',
                DB::raw('COALESCE(total_install.total_install, 0) as total_install')
            ])
            ->where('campaigns.customer_id', $user->id)
            ->orderBy('campaigns.id', 'desc');
        if ($req->keyword) {
            $query->where('name', 'LIKE', '%' . $req->keyword . '%')
                ->orWhere('package_id', 'LIKE', '%' . $req->keyword . '%')
                ->orWhereHas('customer', function ($join) use ($req) {
                    $join->where('name', 'LIKE', '%' . $req->keyword . '%')->orwhere('id', 'LIKE', '%' . $req->keyword . '%');
                });
        }
        if ($req->customer_id) {
            $query->where('customer_id', $req->customer_id);
        }
        if ($req->os) {
            $query->where('os', $req->os);
        }
        if ($req->type) {
            $query->where('type', $req->type);
        }
        if ($req->created) {
            $dates = $req->created;
            $date_range = explode('_', $dates);
            $start_date = $date_range[0];
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
            $end_date = $date_range[1];
            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }

//        $query->createdIn($req->created);
        $limit = 25;
        if ($req->limit) {
            $limit = $req->limit;
        }

        $entries = $query->paginate($limit);

        return [
            'code' => 0,
            'data' => $entries->items(),
            'paginate' => [
                'currentPage' => $entries->currentPage(),
                'lastPage' => $entries->lastPage(),
            ]
        ];
    }

    public function dataDetail(Request $req)
    {
        $dates = $req->created;
        $date_range = explode('_', $dates);
        $start_date = $date_range[0];
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = $date_range[1];
        $end_date = date('Y-m-d', strtotime($end_date));
        $startHour = '00:00:00';
        $endHour = '23:59:59';

        if ($end_date != $start_date) {
            $results = DB::table('campaign_installs')->where('campaign_id', $req->id)
                ->selectRaw('DATE(installed_at) AS date, COUNT(campaign_id) AS campaign_count')
                ->whereBetween('installed_at', [$start_date.'_'.$startHour, $end_date.'_'.$endHour])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            $install_counts = [];
            foreach ($results as $result) {
                $install_counts[$result->date] = $result->campaign_count;
            }
            $start_datetime = new \DateTime($start_date);
            $end_datetime = new \DateTime($end_date);
            $diff = $start_datetime->diff($end_datetime);
            $num_days = $diff->days + 1;
            // Thêm 1 vì kể cả ngày cuối cùng
            $data = [];
            for ($i = 0; $i < $num_days; $i++) {
                $date = date('Y-m-d', strtotime($start_date . ' +' . $i . ' day'));
                $count = isset($install_counts[$date]) ? $install_counts[$date] : 0;
                $data[] = [
                    'count' => $count,
                    'date' => $date
                ];
            }
        }

        if ($start_date == $end_date && $req->timeLine==0) {
            $results = DB::table('campaign_installs')->where('campaign_id', $req->id)
                ->selectRaw('DATE_FORMAT(installed_at, "%H") AS hour, COUNT(campaign_id) AS campaign_count')
                ->whereBetween('installed_at', [$start_date . '_' . $startHour, $end_date . '_' . $endHour])
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            $hours = range(0, 23);

            $resultArray = array_fill_keys($hours, 0);
            foreach ($results as $result) {
                $resultArray[(int) $result->hour] = $result->campaign_count;
            }

            $data = [];
            foreach ($resultArray as $hour => $campaignCount) {
                $data[] = [
                    'date' => $hour,
                    'count' => $campaignCount
                ];
            }


        }
        if ($start_date == $end_date && $req->timeline != 00) {
            $end_date = date('Y-m-d', strtotime("+1 day", strtotime($start_date)));
            $startTimeLine = $req->timeline . ':00:00';
            $endTimeLine = ($req->timeline - 1) . ':59:59';
            $resultMap = DB::table('campaign_installs')->where('campaign_id', $req->id)
                ->selectRaw('DATE_FORMAT(installed_at, "%H") AS hour, COUNT(campaign_id) AS campaign_count')
                ->whereBetween('installed_at', [$start_date . ' ' . $startTimeLine, $end_date . ' ' . $endTimeLine])
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('campaign_count', 'hour');

            $startTime = strtotime($start_date . ' ' . $startTimeLine);
            $endTime = strtotime($end_date . ' ' . $endTimeLine);
            $formattedTime = [];
            for ($i = $startTime; $i <= $endTime; $i += 3600) {
                $date = date('Y-m-d H', $i);
                $formattedTime[] = $date;
            }
            $data = [];

            foreach ($formattedTime as $time) {
                $hour = substr($time, -2);
                $data[] = [
                    'date' => $time,
                    'count' => $resultMap[$hour] ?? 0
                ];
            }


        }

        return [
            'code' => 200,
            'data' => $data
        ];

    }


}
