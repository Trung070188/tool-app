<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class CustomersController extends AdminBaseController
{

    /**
    * Index page
    * @uri  /xadmin/customers/index
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function index()
    {
        $title = 'customer';
        $component = 'CustomerIndex';
        return vue(compact('title', 'component'));
    }

    /**
    * Create new entry
    * @uri  /xadmin/customers/create
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function create (Request $req)
    {
        $component = 'CustomerForm';
        $title = 'Create customers';
        return vue(compact('title', 'component'));
    }

    /**
    * @uri  /xadmin/customers/edit?id=$id
    * @throw  NotFoundHttpException
    * @return  View
    */
    public function edit (Request $req)
    {
        $id = $req->id;
        $entry = Customer::find($id);

        if (!$entry) {
            throw new NotFoundHttpException();
        }

        /**
        * @var  Customer $entry
        */
        $jsonData = compact('entry');
        $title = 'Edit';
        $component = 'CustomerDetail';

        return vue(compact('title', 'component'), $jsonData);
    }

    /**
    * @uri  /xadmin/customers/remove
    * @return  array
    */
    public function remove(Request $req)
    {
        $id = $req->id;
        $entry = Customer::find($id);

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
    * @uri  /xadmin/customers/save
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
    'email' => 'required|max:200',
    'phone' => 'max:100',
    'company' => 'max:200',
    'password'=>'required'
];

        $v = Validator::make($data, $rules);
        $v->after(function ($valiadte) use ($data,$req)
        {
           if(!isset($data['id']) && $data['password_conf']!=$data['password'])
           {
              $valiadte->errors()->add('password_conf','The password and confirmation password do not match.');
           }
           if(isset($data['id']) && $req->password_conf!=$req->password)
           {
              $valiadte->errors()->add('password_conf','The password and confirmation password do not match.');
           }
        });

        if ($v->fails()) {
            return [
                'code' => 2,
                'errors' => $v->errors()
            ];
        }

        /**
        * @var  Customer $entry
        */
        if (isset($data['id'])) {
            $entry = Customer::find($data['id']);
            if (!$entry) {
                return [
                    'code' => 3,
                    'message' => 'Không tìm thấy',
                ];
            }

            $entry->fill($data);
            $entry->save();
            $password=Hash::make($req->password);
            Customer::query()->where('id',$entry->id)->update(['password'=>$password]);

            return [
                'code' => 0,
                'message' => 'Đã cập nhật',
                'id' => $entry->id
            ];
        } else {
            $entry = new Customer();
            $entry->fill($data);
            $password=(Hash::make($data['password']));
            $entry->password=$password;
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
        $entry = Customer::find($id);

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
    * @uri  /xadmin/customers/data
    * @return  array
    */
    public function data(Request $req)
    {
        $query = Customer::query()->orderBy('id', 'desc');

        if ($req->keyword) {
            //$query->where('title', 'LIKE', '%' . $req->keyword. '%');
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

    public function export()
    {
                $keys = [
                            'name' => ['A', 'name'],
                            'email' => ['B', 'email'],
                            'phone' => ['C', 'phone'],
                            'company' => ['D', 'company'],
                            'description' => ['E', 'description'],
                            ];

        $query = Customer::query()->orderBy('id', 'desc');

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
