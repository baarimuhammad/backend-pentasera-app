<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response.
     */
    protected function success($data = null, string $message = 'Berhasil', int $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function error(string $message, int $code = 400, $data = null)
    {
        $response = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response.
     */
    protected function validationError($errors, string $message = 'Validasi gagal')
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}
