<?php
/**
 * Created by PhpStorm.
 * User: lee
 * Date: 4/1/18
 * Time: 10:52 PM
 */

namespace App\Adapters;


use Illuminate\Support\Facades\Cache;

class SugarAdapter
{
    const AUTH_CACHE_KEY = 'oauth_key';

    public $instance_url = null;
    public $username = null;
    public $password = null;

    public $oauth_token = null;

    public function __construct()
    {
        $this->instance_url = "https://e7920-93.mycrmspace.de/rest/v10";
        $this->username = "andrii";
        $this->password = "te)%=NP,qA-97XaDg8";
    }


    public function init()
    {

        if (Cache::has(self::AUTH_CACHE_KEY)) {
            $this->oauth_token = Cache::get(self::AUTH_CACHE_KEY);
        } else {
            //Login - POST /oauth2/token
            $auth_url = $this->instance_url . "/oauth2/token";

            $oauth2_token_arguments = array(
                "grant_type" => "password",
                //client id - default is sugar.
                //It is recommended to create your own in Admin > OAuth Keys
                "client_id" => "sugar",
                "client_secret" => "",
                "username" => $this->username,
                "password" => $this->password,
                //platform type - default is base.
                //It is recommend to change the platform to a custom name such as "custom_api" to avoid authentication conflicts.
                "platform" => "custom_api"
            );

            $auth_request = curl_init($auth_url);
            curl_setopt($auth_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($auth_request, CURLOPT_HEADER, false);
            curl_setopt($auth_request, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($auth_request, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($auth_request, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($auth_request, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));


            //convert arguments to json
            $json_arguments = json_encode($oauth2_token_arguments);
            curl_setopt($auth_request, CURLOPT_POSTFIELDS, $json_arguments);

            //execute request
            $oauth2_token_response = curl_exec($auth_request);

            //decode oauth2 response to get token
            $oauth2_token_response_obj = json_decode($oauth2_token_response);
            $this->oauth_token = $oauth2_token_response_obj->access_token;
            Cache::put(self::AUTH_CACHE_KEY, $this->oauth_token, 10);
        }

    }

    /**
     * @param int $minAmount
     * @return mixed
     */
    public function getSimpleOpportunities($minAmount = 1000)
    {
        $fetch_url = $this->instance_url . "/Opportunities/filter";

        $filter_arguments = [
            'filter' => [
                ['amount' => ['$gte' => $minAmount]],
//                [
//                    'or' => [
//                        ['sales_stage' => ['$not_equals	' => 'Closed Won']],
//                        ['sales_stage' => ['$not_equals	' => 'Closed Lost']],
//                    ]
//                ],
            ],
//            'fields' => ['name', 'amount','related_account_id',
//                'accounts',
//                'accounts.billing_address_country',
//                [
//                    'name' => 'account',
//                    'fields' => [
//                        'billing_address_country',
//                        'billing_address_postalcode',
//                        'billing_address_city',
//                        'billing_address_street',
//                    ]
//                ]
//            ]
        ];

        $fetch_request = $this->setCurl($fetch_url, $this->oauth_token);
        //convert arguments to json
        $json_arguments = json_encode($filter_arguments);
        curl_setopt($fetch_request, CURLOPT_POST, 1);
        curl_setopt($fetch_request, CURLOPT_POSTFIELDS, $json_arguments);

        //execute request
        $filter_response = curl_exec($fetch_request);

        //decode json
        $filter_response_obj = json_decode($filter_response);
        return $filter_response_obj;

    }

    /**
     * @param int $minAmount
     * @return mixed
     */
    public function getAccounts($minAmount = 1000)
    {
        $fetch_url = $this->instance_url . "/Accounts/filter";

        $filter_arguments = [
            'filter' => [
                ['opportunities.amount' => ['$gte' => $minAmount]]
            ],
        ];

        $fetch_request = $this->setCurl($fetch_url, $this->oauth_token);
        //convert arguments to json
        $json_arguments = json_encode($filter_arguments);
        curl_setopt($fetch_request, CURLOPT_POST, 1);
        curl_setopt($fetch_request, CURLOPT_POSTFIELDS, $json_arguments);

        //execute request
        $filter_response = curl_exec($fetch_request);

        //decode json
        $filter_response_obj = json_decode($filter_response);
        return $filter_response_obj;

    }

    /**
     * @return null|string
     */
    public function getAccountByID($id)
    {
        $fetch_url = $this->instance_url . "/Accounts/filter";

        $filter_arguments = [
            'filter' => [
                ['id' => ['$equals' => $id]],
            ]
        ];

        $fetch_request = $this->setCurl($fetch_url, $this->oauth_token);
        //convert arguments to json
        $json_arguments = json_encode($filter_arguments);
        curl_setopt($fetch_request, CURLOPT_POST, 1);
        curl_setopt($fetch_request, CURLOPT_POSTFIELDS, $json_arguments);

        //execute request
        $filter_response = curl_exec($fetch_request);

        //decode json
        $filter_response_obj = json_decode($filter_response);
        return $filter_response_obj;
    }

    /**
     * @param int $minAmount
     * @return mixed
     */
    public function getAccountsByIds(array $ids)
    {
        $fetch_url = $this->instance_url . "/Accounts/filter";

        $filter_arguments = [
            'filter' => [
                ['id' => ['$in' => $ids]]
            ],
        ];

        $fetch_request = $this->setCurl($fetch_url, $this->oauth_token);
        //convert arguments to json
        $json_arguments = json_encode($filter_arguments);
        curl_setopt($fetch_request, CURLOPT_POST, 1);
        curl_setopt($fetch_request, CURLOPT_POSTFIELDS, $json_arguments);

        //execute request
        $filter_response = curl_exec($fetch_request);

        //decode json
        $filter_response_obj = json_decode($filter_response);
        return $filter_response_obj->records;

    }

    private function setCurl($fetch_url, $oauth_token)
    {

        $fetch_request = curl_init($fetch_url);
        curl_setopt($fetch_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($fetch_request, CURLOPT_HEADER, false);
        curl_setopt($fetch_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($fetch_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($fetch_request, CURLOPT_FOLLOWLOCATION, 0);

        curl_setopt($fetch_request, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "oauth-token: {$oauth_token}"
        ));

        return $fetch_request;
    }


    /**
     * @param array $accounts
     * @return array|string
     */
    public function getAccountsGeoNames(array $accounts)
    {
        $geoNames = [];

        foreach ($accounts as $account) {
            $geoNames[] = implode(',', [
                $account->billing_address_country,
                $account->billing_address_postalcode,
                $account->billing_address_city,
                $account->billing_address_street,
            ]);


        }
        return array_unique($geoNames);
    }

}