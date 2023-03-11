<?php

namespace App\Http\Controllers\Customer;

use App\Models\Campaign;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerDashboardController extends CustomerBaseController
{
    public function index()
    {
        $title = 'Thống kê';
        $component = 'CustomerDashboardIndex';

        return customerVue(compact('component', 'title'));
    }

    public function data(Request $req)
    {
        $user = Auth::user();
        $query = Campaign::query()->where('customer_id', $user->id)
            ->orderBy('status','desc')
            ->orderBy('open_next_day','desc')
            ->orderBy('id', 'desc');

        if ($req->keyword) {
            $query->where('name', 'LIKE', '%' . $req->keyword . '%');
        }
        if ($req->os) {
            $query->where('name', $req->os);
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
}
