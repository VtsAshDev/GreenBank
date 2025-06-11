<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreUserRequest;
use App\Jobs\TestRabbit;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function store(StoreUserRequest $request): JsonResponse
    {
        TestRabbit::dispatch();

        $user = User::create($request->validated());

        Wallet::create([
            'user_id' => $user->id,
        ]);

        Mail::to($user->email)->queue(new WelcomeEmail());

        return response()->json([
            'message' => 'Usuário criado com sucesso',
        ], Response::HTTP_CREATED);
    }
}
