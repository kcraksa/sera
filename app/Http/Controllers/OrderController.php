<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Firebase\FirebaseLib;

class OrderController extends Controller
{
    protected $url = "https://testing-5562a-default-rtdb.asia-southeast1.firebasedatabase.app/";
    protected $token = "qYE1whzGFnxYUEu4AzNijd1p7LAtoQ0WIzjUWsoH";
    protected $path = "/orders";

    /**
     * 
     * Fetch order data (using Firebase Realtime Database)
     * 
     * @OA\GET(
     *      path="/api/order",
     *      description="Fetch all data order.",
     *      tags={"Get All Order"},
     *      operationId="fetchorder",
     *      security={{"apiAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Fetch data success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_order_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Data fetched successfully.",
     *                      "data": "Order data"
     *                  },
     *                  summary="Response while fetch data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to fetch data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_order_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Failed to fetch data",
     *                      "data": ""
     *                  },
     *                  summary="Response while fetch data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */

    public function index()
    {
        $firebase = new FirebaseLib($this->url, $this->token);

        try {
            $data = $firebase->get($this->path);
            return $this->response("success", "Data fetched successfully.", json_decode($data), 200); 
        } catch (\Exception $e) {
            return $this->response("error", "Failed to fetch data", "", 400);
        }
    }

    /**
     * 
     * Store order data (using Firebase Realtime Database)
     * 
     * @OA\POST(
     *      path="/api/order/store",
     *      description="Store order data.",
     *      tags={"Store Order"},
     *      operationId="storeorder",
     *      security={{"apiAuth": {}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="customer",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="product",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="qty",
     *                      type="integer"
     *                  ),
     *                  example={"customer": "Amir Khan", "product": "Bardi Smart Lamp", "qty": 1}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Order created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_store_data_Order_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Order created successfully.",
     *                      "data": "Order data inserted"
     *                  },
     *                  summary="Response while store data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to store data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_store_data_order_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Order created failed.",
     *                      "data": ""
     *                  },
     *                  summary="Response while store data failed"
     *              ),
     *              @OA\Examples(
     *                  example="response_while_store_data_order_failed_because_mandatory_field_empty",
     *                  value={
     *                      "status": "error",
     *                      "message": "Mandatory field cannot be empty",
     *                      "data": "Validation Information"
     *                  },
     *                  summary="Response while store data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */
    public function store(Request $request)
    {
        $firebase = new FirebaseLib($this->url, $this->token);

        try {
            $validation = $this->validate($request, [
                'customer' => 'required',
                'product' => 'required',
                'qty' => 'required'
            ]);

            $data = [
                'customer' => $request->customer,
                'product' => $request->product,
                'qty' => $request->qty
            ];

            $article = $firebase->set($this->path. "/". date('YmdHisu'), $data);

            return $this->response("success", "Order created successfully", $data, 200);
        } catch (\Exception $e) {

            if ($e->getResponse()->original) {
                return $this->response("error", "Mandatory field cannot be empty", $e->getResponse()->original, 400);
            } else {
                return $this->response("error", "Order created failed.", "", 400);
            }
        }
            
    }

    /**
     * 
     * Fetch order data by ID (using Firebase Realtime Database)
     * 
     * @OA\GET(
     *      path="/api/order/{id}/show",
     *      description="Fetch order data by ID.",
     *      tags={"Get Order By ID"},
     *      operationId="fetchOrderbyid",
     *      security={{"apiAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Order ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Fetch data success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_order_byid_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Data fetched successfully.",
     *                      "data": "Order data"
     *                  },
     *                  summary="Response while fetch data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to fetch data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_order_byid_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Failed to fetch data",
     *                      "data": ""
     *                  },
     *                  summary="Response while fetch data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Failed to fetch data because ID not given",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_order_byid_failed_because_no_id",
     *                  value={
     *                      "status": "error",
     *                      "message": "Page Not Found",
     *                      "data": ""
     *                  },
     *                  summary="Response while fetch data failed because ID not given"
     *              )
     *          )
     *      ),
     * )
     * 
     * */
    public function show($id)
    {
        $firebase = new FirebaseLib($this->url, $this->token);

        try {

            if (empty($id)) {
                return $this->response("error", "Failed to fetch data", "", 400);
            }

            $data = $firebase->get($this->path. "/". $id);
            return $this->response("success", "Data fetched successfully.", json_decode($data), 200); 
        } catch (\Exception $e) {
            return $this->response("error", "Failed to fetch data", "", 400);
        }
    }

    /**
     * 
     * Update order data (using Firebase Realtime Database)
     * 
     * @OA\POST(
     *      path="/api/order/{ID}/update",
     *      description="Update order data.",
     *      tags={"Update Order"},
     *      operationId="updateorder",
     *      security={{"apiAuth": {}}},
     *      @OA\Parameter(
     *          name="ID",
     *          description="Order ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="customer",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="product",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="qty",
     *                      type="integer"
     *                  ),
     *                  example={"customer": "Amir Khan", "product": "Bardi Smart Lamp", "qty": 1}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Order updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_update_data_Order_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Order updated successfully.",
     *                      "data": "Order data inserted"
     *                  },
     *                  summary="Response while update data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to update data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_update_data_Order_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Order updated failed.",
     *                      "data": ""
     *                  },
     *                  summary="Response while update data failed"
     *              ),
     *              @OA\Examples(
     *                  example="response_while_update_data_Order_failed_because_mandatory_field_empty",
     *                  value={
     *                      "status": "error",
     *                      "message": "Mandatory field cannot be empty",
     *                      "data": "Validation Information"
     *                  },
     *                  summary="Response while update data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */
    public function update(Request $request, $id)
    {
        $firebase = new FirebaseLib($this->url, $this->token);

        try {
            $validation = $this->validate($request, [
                'customer' => 'required',
                'product' => 'required',
                'qty' => 'required'
            ]);

            $data = [
                'customer' => $request->customer,
                'product' => $request->product,
                'qty' => $request->qty
            ];

            $article = $firebase->update($this->path. "/". $id, $data);

            return $this->response("success", "Order updated successfully", $data, 200);
        } catch (\Exception $e) {

            if ($e->getResponse()->original) {
                return $this->response("error", "Mandatory field cannot be empty", $e->getResponse()->original, 400);
            } else {
                return $this->response("error", "Order updated failed.", "", 400);
            }
        }
    }

    /**
     * 
     * Delete order data (using Firebase Realtime Database)
     * 
     * @OA\POST(
     *      path="/api/order/{ID}/delete",
     *      description="Delete order data.",
     *      tags={"Delete Order"},
     *      operationId="deleteOrder",
     *      security={{"apiAuth": {}}},
     *      @OA\Parameter(
     *          name="ID",
     *          description="Order ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Order deleted successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_delete_data_Order_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Order deleted successfully.",
     *                      "data": ""
     *                  },
     *                  summary="Response while delete data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to delete data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_delete_data_order_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Delete order failed.",
     *                      "data": ""
     *                  },
     *                  summary="Response while delete data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */
    public function delete($id)
    {
        $firebase = new FirebaseLib($this->url, $this->token);

        try {
            $is_exists = $firebase->get($this->path. "/" .$id);
            if ($is_exists === null) {
                return $this->response("error", "Delete Article failed.", "", 400);
            }

            $firebase->delete($this->path. "/" .$id);
            return $this->response("success", "Article deleted successfully", "", 200);
        } catch (\Exception $e) {
            return $this->response("error", "Delete Article failed.", "", 400);
        }
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
