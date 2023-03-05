<?php

namespace App\Http\Controllers;

class HomeController
{
    public function index()
    {
        $SERVER_NAME = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['SERVER_NAME'];
        $domainCustomer = config('domain.customer');
        $domainManager = config('domain.manager');

        if ($domainManager === 'localhost' ) {
            return redirect()->to('/xadmin/login');
        }

        if ($domainCustomer === $domainManager) {
            abort(404);
        }

        if ($SERVER_NAME === $domainCustomer) {
            return redirect()->to('/customer/login');
        }

        if ($SERVER_NAME === $domainManager) {
            return redirect()->to('/xadmin/login');
        }


        abort(404);
    }
}
