<?php

namespace App\Traits\Api;

use Illuminate\Support\Facades\Http;

trait ApiHelper
{
    /**
     * successResponse
     *
     * @param  mixed $response
     * @param  mixed $responseCode
     * @return void
     */
    public function successResponse($response, $responseCode)
    {
        return response()->json([
            'status' => 'success',
            'data' => $response,
        ], $responseCode)
            ->header('Content-Type', 'application/json');
    }
    /**
     * invalidResponse
     *
     * @param  mixed $message
     * @param  mixed $responseCode
     * @return void
     */
    public function invalidResponse($message, $responseCode)
    {
        return response()->json([
            'status' => 'invalid',
            'message' => $message,
        ], $responseCode)
            ->header('Content-Type', 'application/json');
    }
    /**
     * errorResponse
     *
     * @param  mixed $message
     * @param  mixed $responseCode
     * @return void
     */
    public function errorResponse($message, $responseCode)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $responseCode)
            ->header('Content-Type', 'application/json');
    }
    /**
     * noDataResponse
     *
     * @param  mixed $responseCode
     * @return void
     */
    public function noDataResponse($responseCode)
    {
        return response()->json([
            'status' => 'no-data',
            'message' => 'No data found for the specified criteria.',
            'data' => [],
        ], $responseCode)
            ->header('Content-Type', 'application/json');
    }
}
