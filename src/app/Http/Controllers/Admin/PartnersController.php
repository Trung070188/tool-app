<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Partner;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class PartnersController extends AdminBaseController
{

    /**
    * Index page
    * @uri  /xadmin/partners/index
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function index()
    {
        $title = 'Partner';
        $component = 'PartnerIndex';
        return vue(compact('title', 'component'));
    }

    /**
    * Create new entry
    * @uri  /xadmin/partners/create
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function create (Request $req)
    {
        $component = 'PartnerForm';
        $title = 'Create partners';
        return vue(compact('title', 'component'));
    }

    /**
    * @uri  /xadmin/partners/edit?id=$id
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function edit (Request $req)
    {
        $id = $req->id;
        $entry = Partner::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
        * @var  Partner $entry
        */
        $jsonData = compact('entry');
        $title = 'Edit';
        $component = 'PartnerForm';

        return vue(compact('title', 'component'), $jsonData);
    }

    /**
    * @uri  /xadmin/partners/remove
    * @return  array
    */
    public function remove(Request $req)
    {
        $id = $req->id;
        $entry = Partner::find($id);

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
    * @uri  /xadmin/partners/save
    * @return  array
    */
    public function save(Request $req)
    {
        if (!$req->isMethod('POST')) {
            return ['code' => 405, 'message' => 'Method not allow'];
        }

        $data = $req->get('entry');

        $rules = [
            'name' => 'required|max:200',
            'ip' => 'required|max:50',
        ];

        $v = Validator::make($data, $rules);

        if ($v->fails()) {
            return [
                'code' => 2,
                'errors' => $v->errors()
            ];
        }

        /**
        * @var  Partner $entry
        */
        if (isset($data['id'])) {
            $entry = Partner::find($data['id']);
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
            $entry = new Partner();
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
        $entry = Partner::find($id);

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
    * @uri  /xadmin/partners/data
    * @return  array
    */
    public function data(Request $req)
    {
        $query = Partner::query()->orderBy('id', 'desc');

        if ($req->keyword) {
            $query->where('name', 'LIKE', '%' . $req->keyword. '%');
        }

        if($req->created)
        {
            $dates = $req->created;
            $date_range = explode('_', $dates);
            $start_date = $date_range[0];
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
            $end_date = $date_range[1];
            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
            $query->whereBetween('created_at',[$start_date,$end_date]);
        }

        $entries = $query->paginate();

        return [
            'code' => 0,
            'data' => $entries->items(),
            'paginate' => [
                'currentPage' => $entries->currentPage(),
                'lastPage' => $entries->lastPage(),
            ]
        ];
    }

    public function export()
    {
                $keys = [
                            'name' => ['A', 'name'],
                            'ip' => ['B', 'ip'],
                            'note' => ['C', 'note'],
                            ];

        $query = Partner::query()->orderBy('id', 'desc');

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

    public function checkCopy(Request $req)
    {
        $id = $req->id;
        Partner::query()->where('id', $id)->update(['check_copy' => 1]);
        return [
            'code' => 0,
            'message' => 'Đã copy'
        ];
    }
    public function createToken(Request $req)
    {
        $id = $req->id;
        $token = $req->token;
        Partner::query()->where('id', $id)->update(['secret'=> $token , 'check_copy' => 0]);
        return [
          'code' => 0,
          'message' => 'Đã tạo mới token'
        ];
    }
}
