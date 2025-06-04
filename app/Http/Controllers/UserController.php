<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create($validated);

        Wallet::create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
        ], Response::HTTP_CREATED);
    }
}
