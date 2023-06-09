<?php

namespace App\Http\Controllers\Admin;

use App\Models\CampaignInstall;
use App\Models\Customer;
use App\Models\EventLog;
use App\Services\AppStoreService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Campaign;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class CampaignsController extends AdminBaseController
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
    public function index()
    {
        $title = 'Campaign';
        $component = 'CampaignIndex';
        return vue(compact('title', 'component'));
    }

    public function deleteData()
    {
        $title = 'Campaign';
        $component = 'DeleteCampaign';
        return vue(compact('title', 'component'));
    }

    public function statistical()
    {
        $title = 'Campaign';
        $component = 'CampaignStatistical';
        return vue(compact('title', 'component'));
    }

    /**
     * Create new entry
     * @uri  /xadmin/campaigns/create
     * @throw  NotFoundHttpException
     * @return  View
     */
    public function create(Request $req)
    {
        $component = 'CampaignForm';
        $title = 'Create campaigns';
        $entry = new Campaign();
        $entry->total_install = 0;
        $entry->daily_fake_install = 0;
        $entry->type = 'cpi';
        $entry->customer_id = '';
        $entry->auto_off_status = 0;
        $entry->auto_on_status = 0;
        $entry->total_daily_install = 0;

        $eventLogs = [];
        $customer = Customer::query()->orderBy('id', 'desc')->get();
        $jsonData = compact('customer', 'entry', 'eventLogs');
        return vue(compact('title', 'component'), $jsonData);
    }

    /**
     * @uri  /xadmin/campaigns/edit?id=$id
     * @throw  NotFoundHttpException
     * @return  View
     */
    public function edit(Request $req)
    {
        $id = $req->id;
        $entry = Campaign::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
         * @var  Campaign $entry
         */

        $eventLogs = EventLog::query()->where('campaign_id', $entry->id)
            ->orderBy('id', 'desc')
            ->limit(100)->get();


        $customer = Customer::query()->orderBy('id', 'desc')->get();
        $jsonData = compact('entry', 'customer', 'eventLogs');
        $title = 'Sửa campaign: ' . $entry->name;
        $component = 'CampaignForm';

        return vue(compact('title', 'component'), $jsonData);
    }

    public function detail(Request $req)
    {
        $id = $req->id;
        $time=$req->time;
        $entry = Campaign::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
         * @var  Customer $entry
         */
        $jsonData = compact('entry','time');
        $title = 'Thống kê';
        $component = 'CampaignStatisticalDetail';

        return Vue(compact('title', 'component'), $jsonData);
    }

    /**
     * @uri  /xadmin/campaigns/remove
     * @return  array
     */
    public function remove(Request $req)
    {
        $id = $req->id;
        $entry = Campaign::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        $entry->delete();

        return [
            'code' => 0,
            'message' => 'Đã xóa'
        ];
    }

    public function removeCampaign(Request $req)
    {
        Campaign::query()->whereIn('id', $req->campaignIds)->update(['deleted_at' => Carbon::now()]);
        return [
            'code' => 0,
            'message' => 'Đã xóa'
        ];
    }

    /**
     * @uri  /xadmin/campaigns/save
     * @return  array
     */
    public function save(Request $req)
    {
        if (!$req->isMethod('POST')) {
            return ['code' => 405, 'message' => 'Method not allow'];
        }

        $data = $req->get('entry');

        $rules = [
            'name' => 'max:300|required',
            'package_id' => 'max:200|required',
            'icon' => 'max:300|required',
            'customer_id' => 'numeric|required',
            'status' => 'numeric',
            'price' => 'required',
            'os' => 'required',
            'store_url' => 'required',
            'type' => 'required',
            'total_install' => 'required|numeric|min:0',
            'daily_install' => 'numeric|min:0'
        ];

        $v = Validator::make($data, $rules);

        if ($v->fails()) {
            return [
                'code' => 2,
                'errors' => $v->errors()
            ];
        }

        /**
         * @var  Campaign $entry
         */
        if (isset($data['id'])) {
            $entry = Campaign::find($data['id']);

            if (!$entry) {
                return [
                    'code' => 3,
                    'message' => 'Không tìm thấy',
                ];
            }

            $entry->fill($data);

            $entry->save();

            return [
                'code' => 0,
                'message' => 'Đã cập nhật',
                'id' => $entry->id
            ];
        } else {
            $entry = new Campaign();
            $entry->fill($data);
            $entry->save();

            return [
                'code' => 0,
                'message' => 'Đã thêm',
                'id' => $entry->id
            ];
        }
    }
    public function clone(Request $req)
    {
        if (!$req->isMethod('POST')) {
            return ['code' => 405, 'message' => 'Method not allow'];
        }

        $data = $req->get('entry');

        /**
         * @var  Campaign $entry
         */
        $entry = new Campaign();
        $entry->name = $data['name'];
        $entry->package_id = $data['package_id'];
        $entry->icon = $data['icon'];
        $entry->price = $data['price'];
        $entry->type = $data['type'];
        $entry->store_url = $data['store_url'];
        $entry->customer_id = $data['customer_id'];
        $entry->os = $data['os'];
        $entry->daily_install = $data['daily_install'];
        $entry->hourly_fake_install = $data['hourly_fake_install'];
        $entry->total_install = $data['total_install'];
        $entry->save();

            return [
                'code' => 0,
                'message' => 'Đã thêm',
                'id' => $entry->id
            ];
    }

    /**
     * @param Request $req
     */
    public function toggleStatus(Request $req)
    {
        $id = $req->get('id');
        $entry = Campaign::find($id);

        if (!$id) {
            return [
                'code' => 404,
                'message' => 'Not Found'
            ];
        }

        $entry->status = $req->status ? 1 : 0;
        $entry->save();

        return [
            'code' => 200,
            'message' => 'Đã lưu'
        ];
    }

    public function openNextDay(Request $req)
    {
        $id = $req->entry['id'];
        Campaign::where('id', $id)->update(['open_next_day' => $req->entry['open_next_day']]);
        return [
            'code' => 200,
            'message' => 'Đã lưu'
        ];

    }

    public function switchStatus(Request $req)
    {
        $id = $req->entry['id'];
        Campaign::where('id', $id)->update(['status' => $req->entry['status']]);
        return [
            'code' => 200,
            'message' => 'Đã lưu'
        ];

    }

    /**
     * Ajax data for index page
     * @uri  /xadmin/campaigns/data
     * @return  array
     */
    public function data(Request $req)
    {

        $query = Campaign::query()->with(['campaignPartner', 'customer'])
            ->orderBy('status','desc')
            ->orderBy('open_next_day','desc')
            ->orderBy('id', 'desc');
        $customers = Customer::query()
            ->orderBy('id', 'desc')->get();
        if ($req->keyword) {
            $query->where('name', 'LIKE', '%' . $req->keyword . '%')
                ->orWhere('id', $req->keyword)
                ->orWhere('package_id', 'LIKE', '%' . $req->keyword . '%')
                ->orWhereHas('customer', function ($join) use ($req) {
                    $join->where('name', 'LIKE', '%' . $req->keyword . '%');
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

        $entries = $query->paginate();

        return [
            'code' => 0,
            'customers' => $customers,
            'data' => $entries->items(),
            'paginate' => [
                'currentPage' => $entries->currentPage(),
                'lastPage' => $entries->lastPage(),
            ]
        ];
    }

    public function dataStatistical(Request $req)
    {
        $customers = Customer::query()->orderBy('id', 'desc')->get();
        if ($req->created) {
            $dates = $req->created;
            $date_range = explode('_', $dates);
            $start_date = $date_range[0];
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
            $end_date = $date_range[1];
            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
            $fakes = DB::table('campaign_installs')
                ->whereBetween('faked_at', [$start_date, $end_date])
                ->select('campaign_id', DB::raw('COUNT(faked_at) as total_fake'))
                ->whereNotNull('faked_at')
                ->groupBy('campaign_id');
            $totalInstall = DB::table('campaign_installs')
                ->whereBetween('installed_at', [$start_date, $end_date])
                ->select('campaign_id', DB::raw('COUNT(id) as total_install'))
                ->groupBy('campaign_id');

        } else {
            $fakes = DB::table('campaign_installs')
                ->select('campaign_id', DB::raw('COUNT(faked_at) as total_fake'))
                ->whereNotNull('faked_at')
                ->groupBy('campaign_id');
            $totalInstall = DB::table('campaign_installs')
                ->select('campaign_id', DB::raw('COUNT(id) as total_install'))
                ->groupBy('campaign_id');

        }

        $query = Campaign::query()->with(['customer', 'campaignPartner'])
            ->leftJoinSub($fakes, 'fakes', function ($join) {
                $join->on('campaigns.id', '=', 'fakes.campaign_id');
            })
            ->leftJoinSub($totalInstall, 'total_install', function ($join) {
                $join->on('campaigns.id', '=', 'total_install.campaign_id');
            })
            ->where(function ($q) {
                $q->where('campaigns.status', '=', 0)
                    ->where('total_install.total_install', '<>', 0)
                    ->orWhere('campaigns.status', '=', 1);
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
                DB::raw('COALESCE(fakes.total_fake, 0) as total_fake'),
                DB::raw('COALESCE(total_install.total_install, 0) as total_install')
            ])
            ->groupBy('campaigns.id');
        if ($req->keyword) {
            $query->where('name', 'LIKE', '%' . $req->keyword . '%')
                ->orWhere('id', $req->keyword)
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
//        if ($req->created) {
//            $dates = $req->created;
//            $date_range = explode('_', $dates);
//            $start_date = $date_range[0];
//            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
//            $end_date = $date_range[1];
//            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
//            $query->whereBetween('created_at', [$start_date, $end_date]);
//        }

//        $query->createdIn($req->created);
//        $entries = $query->paginate();
        return [
            'code' => 0,
            'customers' => $customers,
            'data' => $query->get(),
//            'paginate' => [
//                'currentPage' => $entries->currentPage(),
//                'lastPage' => $entries->lastPage(),
//            ]
        ];
    }

    public function dataDetail(Request $req)
    {
        if($req->created)
        {
            $dates=$req->created;
        }
        else{
            $dates=$req->time;
        }
        $date_range = explode('_', $dates);
        $start_date = $date_range[0];
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = $date_range[1];
        $end_date = date('Y-m-d', strtotime($end_date));
        $startHour = '00:00:00';
        $endHour = '23:59:59';

        $results = DB::table('campaign_installs')->where('campaign_id', $req->id)
            ->selectRaw('DATE(installed_at) AS date, COUNT(campaign_id) AS campaign_count')
            ->whereBetween('installed_at', [$start_date . '_' . $startHour, $end_date . '_' . $endHour])
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();
        $results = $results->reverse();
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

//        if ($start_date == $end_date && $req->timeLine == 0) {
//            $results = DB::table('campaign_installs')->where('campaign_id', $req->id)
//                ->selectRaw('DATE_FORMAT(installed_at, "%H") AS hour, COUNT(campaign_id) AS campaign_count')
//                ->whereBetween('installed_at', [$start_date . '_' . $startHour, $end_date . '_' . $endHour])
//                ->groupBy('hour')
//                ->orderBy('hour')
//                ->get();
//
//            $hours = range(0, 23);
//
//            $resultArray = array_fill_keys($hours, 0);
//            foreach ($results as $result) {
//                $resultArray[(int)$result->hour] = $result->campaign_count;
//            }
//
//            $data = [];
//            foreach ($resultArray as $hour => $campaignCount) {
//                $data[] = [
//                    'date' => $hour,
//                    'count' => $campaignCount
//                ];
//            }
//
//
//        }
//        if ($start_date == $end_date && $req->timeline != 00) {
//            $end_date = date('Y-m-d', strtotime("+1 day", strtotime($start_date)));
//            $startTimeLine = $req->timeline . ':00:00';
//            $endTimeLine = ($req->timeline - 1) . ':59:59';
//            $resultMap = DB::table('campaign_installs')->where('campaign_id', $req->id)
//                ->selectRaw('DATE_FORMAT(installed_at, "%H") AS hour, COUNT(campaign_id) AS campaign_count')
//                ->whereBetween('installed_at', [$start_date . ' ' . $startTimeLine, $end_date . ' ' . $endTimeLine])
//                ->groupBy('hour')
//                ->orderBy('hour')
//                ->pluck('campaign_count', 'hour');
//
//            $startTime = strtotime($start_date . ' ' . $startTimeLine);
//            $endTime = strtotime($end_date . ' ' . $endTimeLine);
//            $formattedTime = [];
//            for ($i = $startTime; $i <= $endTime; $i += 3600) {
//                $date = date('Y-m-d H', $i);
//                $formattedTime[] = $date;
//            }
//            $data = [];
//
//            foreach ($formattedTime as $time) {
//                $hour = substr($time, -2);
//                $data[] = [
//                    'date' => $time,
//                    'count' => $resultMap[$hour] ?? 0
//                ];
//            }
//
//
//        }

        return [
            'code' => 200,
            'data' => $data
        ];

    }


    public function getAppIcon(Request $request)
    {
        try {
            $url = $request->get('url');

            $info = parse_url($url);
            $query = $info['query'];
            parse_str($query, $queryOutput);
            $id = $queryOutput['id'];


            return [
                'code' => 200,
                'data' => $this->appStoreService->getAppInfo($id),
            ];
        } catch (\Throwable $ex) {
            return [
                'code' => 503,
                'message' => $ex->getMessage(),
                'trace' => explode("\n", $ex->getTraceAsString())
            ];
        }

    }

    public function export()
    {
        $keys = [
            'name' => ['A', 'name'],
            'package_id' => ['B', 'package_id'],
            'icon' => ['C', 'icon'],
            'price' => ['D', 'price'],
            'os' => ['E', 'os'],
            'customer_id' => ['F', 'customer_id'],
            'type' => ['G', 'type'],
            'status' => ['H', 'status'],
        ];

        $query = Campaign::query()->orderBy('id', 'desc');

        $entries = $query->paginate();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($keys as $key => $v) {
            if (is_string($v)) {
                $sheet->setCellValue($v . "1", $key);
            } elseif (is_array($v)) {
                list($c, $n) = $v;
                $sheet->setCellValue($c . "1", $n);
            }
        }

        foreach ($entries as $index => $entry) {
            $idx = $index + 2;
            foreach ($keys as $key => $v) {
                if (is_string($v)) {
                    $sheet->setCellValue("$v$idx", data_get($entry->toArray(), $key));
                } elseif (is_array($v)) {
                    list($c, $n) = $v;
                    $sheet->setCellValue("$c$idx", data_get($entry->toArray(), $key));
                }
            }
        }
        $writer = new Xlsx($spreadsheet);
        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');
        $filename = uniqid() . '-' . date('Y_m_d H_i') . ".xlsx";

        // It will be called file.xls
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Write file to the browser
        $writer->save('php://output');
        die;
    }
}
