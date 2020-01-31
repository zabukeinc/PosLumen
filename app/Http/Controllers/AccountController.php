<?php

namespace App\Http\Controllers;

use App\Models\Account;

class AccountController extends Controller {


    public function index(){
        $account = Account::OrderBy('id_account');

        $result = [
            'message' => 'account',
            'data' => $account
        ];

        return response()->json($result, 200);
    }

}