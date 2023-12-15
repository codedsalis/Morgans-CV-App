<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    protected $statusCode = Response::HTTP_OK;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function respond($data, string $status = 'success', array $headers = []): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'data'   => $data,
        ], $this->getStatusCode(), $headers);
    }
}
