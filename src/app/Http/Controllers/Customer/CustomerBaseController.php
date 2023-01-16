<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;

class CustomerBaseController
{
    public function __invoke(Request $req)
    {
        // TODO: Implement __invoke() method.
        $action = $req->route('action', 'index');

        return $this->{$action}($req);
    }
}
