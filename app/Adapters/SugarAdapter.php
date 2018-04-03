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
    public function dashboard()
    {
        $result = [];
        //1 get points
        $sugar = new SugarAdapter();
        $sugar->init();

//        $opportunities = $sugar->getSimpleOpportunities();
        $opportunities = $sugar->getOpportunitiesRobin();
        foreach ($opportunities->records as $item) {
            //i know that it is not effective, but for me this is the only way to get account fields - is to make separate request for every id
            $relatedAccount = $item->accounts->records[0];

            /**
             * prepare array of the data for the displaying on the map
             */
            $result[$relatedAccount->id] = [
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

        return view('home', ['accountsGeoNames' => json_encode($result)]);
    }

}
