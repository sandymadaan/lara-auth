<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\User\UserInterface;
use App\Http\Requests\UserRegister;
use App\Http\Requests\UserLogin;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponser;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function register(UserRegister $request): JsonResponse
    {
        $user = $this->user->create($request->validated());
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }

    public function login(UserLogin $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return $this->error('Credentials not match', 401);
        }

        return $this->success([
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }
}
