<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\DebtSettle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class DebtSettlesController extends AdminBaseController
{

    /**
    * Index page
    * @uri  /xadmin/debt_settle/index
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function index()
    {
        $title = 'DebtSettle';
        $component = 'DebtSettleIndex';
        return vue(compact('title', 'component'));
    }

    /**
    * Create new entry
    * @uri  /xadmin/debt_settle/create
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function create (Request $req)
    {
        $component = 'DebtSettleForm';
        $title = 'Create debt settle';
        return vue(compact('title', 'component'));
    }

    /**
    * @uri  /xadmin/debt_settle/edit?id=$id
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function edit (Request $req)
    {
        $id = $req->id;
        $entry = DebtSettle::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
        * @var  DebtSettle $entry
        */
        $jsonData = compact('entry');
        $title = 'Edit';
        $component = 'DebtSettleDetail';

        return vue(compact('title', 'component'), $jsonData);
    }

    /**
    * @uri  /xadmin/debt_settle/remove
    * @return  array
    */
    public function remove(Request $req)
    {
        $id = $req->id;
        $entry = DebtSettle::find($id);

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
    * @uri  /xadmin/debt_settle/save
    * @return  array
    */
    public function save(Request $req)
    {
        if (!$req->isMethod('POST')) {
            return ['code' => 405, 'message' => 'Method not allow'];
        }

        $data = $req->get('entry');

        $rules = [
    'customer_id' => 'numeric',
];

        $v = Validator::make($data, $rules);

        if ($v->fails()) {
            return [
                'code' => 2,
                'errors' => $v->errors()
            ];
        }

        /**
        * @var  DebtSettle $entry
        */
        if (isset($data['id'])) {
            $entry = DebtSettle::find($data['id']);
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
            $entry = new DebtSettle();
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
        $entry = DebtSettle::find($id);

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
    * @uri  /xadmin/debt_settle/data
    * @return  array
    */
    public function data(Request $req)
    {
        $query=DB::table('debt_settle')->join('customers',function ($join)
        {
            $join->on('debt_settle.customer_id','=','customers.id');
        });
        $query->select([
            'customers.id as customer_id',
            'debt_settle.created_at as created_at',
            'debt_settle.id as id',
           'customers.name as customer_name',
           'debt_settle.pay_booking as pay_booking',
           'debt_settle.pay_debt as pay_debt',
            'debt_settle.note as note'
        ]);

        if ($req->keyword) {
            $query->where('customers.name', 'LIKE', '%' . $req->keyword. '%')
                ->orWhere('customers.id',$req->keyword);
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


//        $query->createdIn($req->created);

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
    public function dataCreate(Request $req)
    {
        $customers=Customer::query()->orderBy('id','desc')->get();
        return [
          'customers'=>$customers
        ];
    }
    public function dataEdit(Request $req)
    {
        $customer=Customer::query()->where('id',$req->customer)->first();
        $listCustomers=Customer::query()->orderBy('id','desc')->get();

        return [
            'customer'=>$customer,
            'listCustomer'=>$listCustomers
        ];

    }

    public function export()
    {
                $keys = [
                            'customer_id' => ['A', 'customer_id'],
                            'pay_booking' => ['B', 'pay_booking'],
                            'pay_debt' => ['C', 'pay_debt'],
                            ];

        $query = DebtSettle::query()->orderBy('id', 'desc');

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
