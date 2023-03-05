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
    public function dataStatistical(Request $req)
    {
        $user=Auth::user();
        $customers = Customer::query()->orderBy('id', 'desc')->get();

        $fakes= DB::table('campaign_installs')
            ->select('campaign_id', DB::raw('COUNT(faked_at) as total_fake'))
            ->whereNotNull('faked_at')
            ->groupBy('campaign_id');
        $totalInstall=DB::table('campaign_installs')
            ->select('campaign_id', DB::raw('COUNT(id) as total_install'))
            ->groupBy('campaign_id');

        $query = Campaign::query()->with(['customer','campaignPartner'])
            ->leftJoinSub($fakes, 'fakes', function ($join) {
                $join->on('campaigns.id', '=', 'fakes.campaign_id');
            })
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
                DB::raw('COALESCE(fakes.total_fake, 0) as total_fake'),
                DB::raw('COALESCE(total_install.total_install, 0) as total_install')
            ])
            ->where('campaigns.customer_id',$user->id)
            ->groupBy('campaigns.id')
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
            'customers' => $customers,
            'data' => $entries->items(),
            'paginate' => [
                'currentPage' => $entries->currentPage(),
                'lastPage' => $entries->lastPage(),
            ]
        ];
    }


}
