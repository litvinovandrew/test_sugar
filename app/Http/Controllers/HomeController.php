<?php

namespace App\Http\Controllers;

use App\Adapters\SugarAdapter;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $result = [];
        //1 get points
        $sugar = new SugarAdapter();
        $sugar->init();
        $accountIDS = [];
//        $accounts = $sugar->getAccounts();
//        dd($accounts);

        $opportunities = $sugar->getSimpleOpportunities();
        foreach ($opportunities->records as $item) {
            $accountIDS[] = $item->accounts->id;
            $relatedAccount = $sugar->getAccountByID($item->accounts->id)->records[0];

            $result[$item->accounts->id] = [
                'opportunity_name' => $item->name,
                'opportunity_sales_stage' => $item->sales_stage,
                'opportunity_amount' => $item->amount,
                'opportunity_date_modified' => $item->date_modified,
                'account_name' => $relatedAccount->name,
                'account_address' =>  implode(',', [
                    $relatedAccount->billing_address_country,
                    $relatedAccount->billing_address_postalcode,
                    $relatedAccount->billing_address_city,
                    $relatedAccount->billing_address_street,
                ]),
            ];

        }

//dd($result);

//        $accounts = $sugar->getAccountsByIds($accountIDS);
//        foreach ($accounts as $account) {
//            $result[$account->id] = [
//                'account_name' => $account->name,
//                'account_address' => implode(',', [
//                    $account->billing_address_country,
//                    $account->billing_address_postalcode,
//                    $account->billing_address_city,
//                    $account->billing_address_street,
//                ]),
//
//
//            ];
//        }


        return view('home', ['accountsGeoNames' => json_encode($result)]);
    }


}
