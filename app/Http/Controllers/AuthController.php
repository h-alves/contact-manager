<?php

namespace App\Http\Controllers;


use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $fields = $request->validated();

        $user = User::create($fields);

        $token = $user->createToken($request->name);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], status: 201);
    }

    public function login(LoginRequest $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'email' => ['As informações estão incorretas.'],
                ]
            ], 422);
        }

        $token = $user->createToken($user->name);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], status: 200);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Você está deslogado com sucesso!',
        ];
    }

    public function deleteAccount(DeleteAccountRequest $request) {
        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['Senha incorreta.']
                ]
            ], 422);
        }

        $user->delete();
        return response()->json([
            'message' => 'Sua conta foi deletada com sucesso!'
        ], 200);
    }
}
