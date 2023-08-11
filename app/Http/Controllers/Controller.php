<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    }

    /**
     * @OA\OpenApi(
     *     @OA\Info(
     *         version="1.0.0",
     *         title="API Docs APP ",
     *         description="Open API Documentation",
     *         termsOfService="http://swagger.io/terms/",
     *     ),
     *  )
     */

    /**
     * @OA\Post(
     *     path="/customer/v1/inquiryCustomer",
     *     tags={"API Reporting"},
     *     operationId="/customer/v1/inquiryCustomer",
     *     description="Get inquiryCustomer",
     *     @OA\RequestBody(
     *         description="Get inquiryCustomer",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string")
     *              ),
     *              @OA\Examples(example="request", value={ "name": "test"}, summary="Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Examples(example="result", value={"responseCode": "00","responseDesc": "Success","responseData": {{"id":1,"name":"ramayana","address":"test"}}}, summary="Response")
     *         )
     *     )
     *  )
     */

    /**
     * @OA\Post(
     *     path="/customer/v1/createCustomer",
     *     tags={"API Reporting"},
     *     operationId="/customer/v1/createCustomer",
     *     description="createCustomer",
     *     @OA\RequestBody(
     *         description="createCustomer",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="address", type="string")
     *              ),
     *              @OA\Examples(example="request", value={ "name": "test", "address": "test" }, summary="Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Examples(example="result", value={"responseCode": "00","responseDesc": "Success Insert Data","responseMessage": "Success"}, summary="Response")
     *         )
     *     )
     *  )
     */

     /**
     * @OA\Post(
     *     path="/customer/v1/updateCustomer",
     *     tags={"API Reporting"},
     *     operationId="/customer/v1/updateCustomer",
     *     description="updateCustomer",
     *     @OA\RequestBody(
     *         description="updateCustomer",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="id", type="number"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="address", type="string")
     *              ),
     *              @OA\Examples(example="request", value={ "id": 1, "name": "coba", "address": "jakarta" }, summary="Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Examples(example="result", value={"responseCode": "00","responseDesc": "Success Update Data","responseMessage": "Success"}, summary="Response")
     *         )
     *     )
     *  )
     */

    /**
     * @OA\Post(
     *     path="/customer/v1/deleteCustomer",
     *     tags={"API Reporting"},
     *     operationId="/customer/v1/deleteCustomer",
     *     description="deleteCustomer",
     *     @OA\RequestBody(
     *         description="deleteCustomer",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="id", type="number")
     *              ),
     *              @OA\Examples(example="request", value={ "id": 4}, summary="Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Examples(example="result", value={"responseCode": "00","responseDesc": "Success Delete Data","responseMessage": "Success"}, summary="Response")
     *         )
     *     )
     *  )
     */

}
