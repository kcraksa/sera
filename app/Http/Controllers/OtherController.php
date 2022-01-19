<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtherController extends Controller
{
    /**
     * 
     * @OA\GET(
     *  path="/api/soal_nomor_6_a",
     *  tags={"Soal Nomor 6A"},
     *  description="Integrasi API dengan handling selain response success",
     *  @OA\Response(
     *      response=400,
     *      description="Error"
     *  )
     * )
     * 
     * */
    public function soal_nomor_6_a()
    {
        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->request('POST', 'https://reqres.in/api/register', [
                'form_params' => [
                    'email' => 'eve.holt@regres.in',
                    'password' => 'pistol'
                ]
            ]);
            return $this->response("success", "Data fetched successfully", $res->getBody(), $res->getStatusCode());
        } catch (\RuntimeException $e) {
            return $this->response("error", $e->getMessage(), "", $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @OA\GET(
     *  path="/api/soal_nomor_6b",
     *  tags={"Soal Nomor 6B"},
     *  description="Integrasi API dengan handling selain response success",
     *  @OA\Response(
     *      response=400,
     *      description="Error"
     *  )
     * )
     * 
     * */

    public function soal_nomor_6_b()
    {
        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->request('POST', 'https://reqres.in/api/login', [
                'form_params' => [
                    'email' => 'eve.holt@regres.in',
                    'password' => 'pistol'
                ]
            ]);
            return $this->response("success", "Data fetched successfully", $res->getBody(), $res->getStatusCode());
        } catch (\RuntimeException $e) {
            return $this->response("error", $e->getMessage(), "", $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @OA\GET(
     *  path="/api/soal_nomor_7",
     *  tags={"Soal Nomor 7"},
     *  description="Filter Object",
     *  @OA\Response(
     *      response=200,
     *      description="Success"
     *  )
     * )
     * 
     * */

    public function soal_nomor_7()
    {
        $filejson = "https://gist.githubusercontent.com/Loetfi/fe38a350deeebeb6a92526f6762bd719/raw/9899cf13cc58adac0a65de91642f87c63979960d/filter-data.json";
        $json = file_get_contents($filejson);
        $arr = json_decode($json);
            
        $max_denom = 100000;      
        $arr_denom = array();      

        foreach ($arr->data->response->billdetails as $bill) {

            $get_denom = explode(":", $bill->body[0]);
            $denom = (int)$get_denom[1];

            if ($denom >= $max_denom) {
                array_push($arr_denom, $denom);
            }

        }

        print_r($arr_denom);
    }

    protected function response($status, $message, $data, $responseCode) 
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $responseCode);
    }
}
