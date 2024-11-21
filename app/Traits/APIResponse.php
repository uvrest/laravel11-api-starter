<?php

namespace App\Traits;

trait APIResponse
{
    public function success($data = null, $message = null, $code = 200): \Illuminate\Http\JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message ?? 'Operação realizada com sucesso',
            'data' => $data,
        ];

        return response()->json($response, $code);
    }

    public function error($errors = null, $message = null, $code = 400): \Illuminate\Http\JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message ?? 'Ocorreu um erro durante a operação',
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }
}
