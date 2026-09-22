<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //
   public function login(
        Request $request
    ): JsonResponse {

        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        if (
            !Auth::attempt(
                $credentials,
                $request->boolean('remember')
            )
        ) {
            return response()->json([
                'message' =>
                    'Correo o contraseña incorrectos.',
            ], 422);
        }

        $request
            ->session()
            ->regenerate();

        return response()->json([
            'message' =>
                'Sesión iniciada correctamente.',

            'user' =>
                $request->user(),
        ]);
    }

    public function logout(
        Request $request
    ): JsonResponse {

        Auth::guard('web')->logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return response()->json([
            'message' =>
                'Sesión cerrada correctamente.',
        ]);
    }
}
