<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Message::where('created_at', '>', now()->subHours(3))->get());
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $payload = $request->validated();

        if (isset($payload['avatar'])) {
            $avatar = explode('storage/', $payload['avatar'])[1];

            $payload['avatar'] = $avatar;

            if (! Storage::exists($avatar) || str_contains($payload['avatar'], 'user.png')) {
                unset($payload['avatar']);
            }
        }

        $message = Message::create($payload);

        MessageCreated::dispatch($message);

        return response()->json($message);
    }
}
