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
       $user=Auth::user();
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
            })->where('campaigns.customer_id', $user->id);
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
            $campaignInstall->where('campaigns.name', 'LIKE', '%' . $req->keyword. '%')
                ->orWhere('partners.name', 'LIKE', '%' . $req->keyword. '%');
        }
        if($req->campaign)
        {
            $campaignInstall->where('campaigns.name',  $req->campaign );
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
        $limit = 25;
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
}
