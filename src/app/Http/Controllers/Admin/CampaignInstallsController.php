<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Models\Partner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\CampaignInstall;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class CampaignInstallsController extends AdminBaseController
{

    /**
    * Index page
    * @uri  /xadmin/campaign_installs/index
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function index()
    {
        $title = 'CampaignInstall';
        $component = 'CampaignInstallIndex';
        return vue(compact('title', 'component'));
    }

    /**
    * Create new entry
    * @uri  /xadmin/campaign_installs/create
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function create (Request $req)
    {
        $component = 'Campaign_installForm';
        $title = 'Create campaign_installs';
        return vue(compact('title', 'component'));
    }

    /**
    * @uri  /xadmin/campaign_installs/edit?id=$id
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function edit (Request $req)
    {
        $id = $req->id;
        $entry = CampaignInstall::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
        * @var  CampaignInstall $entry
        */
        $jsonData = compact('entry');
        $title = 'Edit';
        $component = 'Campaign_installForm';

        return vue(compact('title', 'component'), $jsonData);
    }

    /**
    * @uri  /xadmin/campaign_installs/remove
    * @return  array
    */
    public function remove(Request $req)
    {
        $id = $req->id;
        $entry = CampaignInstall::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        $entry->delete();

        return [
            'code' => 0,
            'message' => 'Đã xóa'
        ];
    }

    /**
    * @uri  /xadmin/campaign_installs/save
    * @return  array
    */
    public function save(Request $req)
    {
        if (!$req->isMethod('POST')) {
            return ['code' => 405, 'message' => 'Method not allow'];
        }

        $data = $req->get('entry');

        $rules = [
    'campaign_id' => 'numeric',
    'partner_campaign_id' => 'numeric',
    'partner_id' => 'numeric',
    'installed_at' => 'date_format:Y-m-d H:i:s',
    'ip' => 'max:50',
];

        $v = Validator::make($data, $rules);

        if ($v->fails()) {
            return [
                'code' => 2,
                'errors' => $v->errors()
            ];
        }

        /**
        * @var  CampaignInstall $entry
        */
        if (isset($data['id'])) {
            $entry = CampaignInstall::find($data['id']);
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
            $entry = new CampaignInstall();
            $entry->fill($data);
            $entry->save();

            return [
                'code' => 0,
                'message' => 'Đã thêm',
                'id' => $entry->id
            ];
        }
    }

    /**
    * @param  Request $req
    */
    public function toggleStatus(Request $req)
    {
        $id = $req->get('id');
        $entry = CampaignInstall::find($id);

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

    /**
    * Ajax data for index page
    * @uri  /xadmin/campaign_installs/data
    * @return  array
    */
    public function data(Request $req)
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $campaignInstall=DB::table('campaign_installs')->join('partner_campaigns',function ($join)
        {
            $join->on('partner_campaigns.id','=','campaign_installs.partner_campaign_id');
        })
            ->leftJoin('campaigns',function ($join)
            {
                $join->on('campaigns.id','=','partner_campaigns.campaign_id');
            })
            ->leftJoin('partners',function ($join)
            {
                $join->on('partners.id','=','partner_campaigns.partner_id');
            });
        $campaignInstall = $campaignInstall->select([
            'campaigns.name as campaign',
            'campaigns.created_at as created_at',
            'campaigns.updated_at as updated_at',
            'campaign_installs.id as id',
            'campaign_installs.campaign_id as campaignId',
            'campaign_installs.device_id as device_id',
            'campaign_installs.ip as campaign_installs',
            'campaign_installs.os as os',
            'campaign_installs.ip as ip',
            'campaign_installs.partner_id as partner_id',
            'partners.name as partner_name',

        ]);

        if ($req->keyword) {
//            $query->where('title', 'LIKE', '%' . $req->keyword. '%');
        }
        if($req->campaign)
        {
           $campaignInstall->where('campaigns.name', $req->campaign);
        }
        if($req->partner_name)
        {
            $campaignInstall->where('partners.name',  $req->partner_name);
        }
       if($req->created)
       {
           $dates = $req->created;
           $date_range = explode('_', $dates);
           $start_date = $date_range[0];
           $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
           $end_date = $date_range[1];
           $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
           $campaignInstall->whereBetween('campaigns.created_at',[$start_date,$end_date]);
       }
        $limit = 50;
        if ($req->limit) {
            $limit = $req->limit;
        }
        $campaigns=Campaign::query()->orderBy('name','desc')->get();
        $partners=Partner::query()->orderBy('name','desc')->get();

        $entries = $campaignInstall->paginate($limit);

        return [
            'code' => 0,
            'data' => $entries->items(),
            'campaigns'=>$campaigns,
            'partners'=>$partners,
            'paginate' => [
                'currentPage' => $entries->currentPage(),
                'lastPage' => $entries->lastPage(),
            ]
        ];
    }

    public function export()
    {
                $keys = [
                            'campaign_id' => ['A', 'campaign_id'],
                            'partner_campaign_id' => ['B', 'partner_campaign_id'],
                            'partner_id' => ['C', 'partner_id'],
                            'installed_at' => ['D', 'installed_at'],
                            'device_id' => ['E', 'device_id'],
                            'ip' => ['F', 'ip'],
                            'os' => ['G', 'os'],
                            ];

        $query = CampaignInstall::query()->orderBy('id', 'desc');

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
