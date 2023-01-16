<?php

namespace App\Http\Controllers\Customer;

class CustomerDashboardController extends CustomerBaseController
{
    public function index()
    {
        $title = 'Thống kê';
        $component = 'CustomerDashboardIndex';

        return customerVue(compact('component', 'title'));
    }
}
