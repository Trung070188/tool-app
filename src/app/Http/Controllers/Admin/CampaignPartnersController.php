<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Models\Partner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\CampaignPartner;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class CampaignPartnersController extends AdminBaseController
{

    /**
    * Index page
    * @uri  /xadmin/campaign_partners/index
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function index()
    {
        $title = 'CampaignPartner';
        $component = 'PartnerCampaignIndex';
        return vue(compact('title', 'component'));
    }

    /**
    * Create new entry
    * @uri  /xadmin/campaign_partners/create
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function create (Request $req)
    {
        $component = 'PartnerCampaignForm';
        $title = 'Create campaignPartners';
        return vue(compact('title', 'component'));
    }

    /**
    * @uri  /xadmin/campaign_partners/edit?id=$id
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function edit (Request $req)
    {
        $id = $req->id;
        $entry = CampaignPartner::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
        * @var  CampaignPartner $entry
        */
        $jsonData = compact('entry');
        $title = 'Edit';
        $component = 'PartnerCampaignDetail';

        return vue(compact('title', 'component'), $jsonData);
    }

    /**
    * @uri  /xadmin/campaign_partners/remove
    * @return  array
    */
    public function remove(Request $req)
    {
        $id = $req->id;
        $entry = CampaignPartner::find($id);

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
    * @uri  /xadmin/campaign_partners/save
    * @return  array
    */
    public function save(Request $req)
    {
        if (!$req->isMethod('POST')) {
            return ['code' => 405, 'message' => 'Method not allow'];
        }
        $data = $req->get('entry');
        $rules = [
//    ' partner_campaign_id' => 'required|numeric',
//    'campaign_id' => 'numeric',
//    'partner_id' => 'numeric',
];

        $v = Validator::make($data, $rules);

        if ($v->fails()) {
            return [
                'code' => 2,
                'errors' => $v->errors()
            ];
        }

        /**
        * @var  CampaignPartner $entry
        */
        if (isset($data['id'])) {
            $entry = CampaignPartner::find($data['id']);
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
            $entry = new CampaignPartner();
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
        $entry = CampaignPartner::find($id);

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
    * @uri  /xadmin/campaign_partners/data
    * @return  array
    */
    public function data(Request $req)
    {
        $query = CampaignPartner::query()->orderBy('id', 'desc');

        if ($req->keyword) {
            //$query->where('title', 'LIKE', '%' . $req->keyword. '%');
        }

        $query->createdIn($req->created);

        $entries = $query->paginate();
        $data=[];
        foreach ($entries as $entry)
        {
            $campaign=Campaign::query()->where('id',$entry->campaign_id)->first();

            $partner=Partner::query()->where('id',$entry->partner_id)->first();
            $data[]=[
              'id'=>$entry->id,
              'name'=>$entry->name,
              'campaign_name'=>$campaign->name,
              'partner_name'=>$partner->name,
            ];

        }

        return [
            'code' => 0,
            'data' => $data,
            'paginate' => [
                'currentPage' => $entries->currentPage(),
                'lastPage' => $entries->lastPage(),
            ]
        ];
    }

    public function dataEdit(Request $req)
    {
        $campaigns=Campaign::query()->orderBy('id','desc')->get();

        $partners=Partner::query()->orderBy('id','desc')->get();
        return [
            'campaigns'=>$campaigns,
            'partners'=>$partners
        ];
    }

    public function export()
    {
                $keys = [
                            ' partner_campaign_id' => ['A', ' partner_campaign_id'],
                            'campaign_id' => ['B', 'campaign_id'],
                            'partner_id' => ['C', 'partner_id'],
                            ];

        $query = CampaignPartner::query()->orderBy('id', 'desc');

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
