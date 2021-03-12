<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Events\TimelinePaused;
use App\Events\TimelinePlayed;
use App\Events\TimelineSeeked;
use App\Http\Requests\StoreTimelineRequest;
use App\Models\Timeline;
use Carbon\CarbonInterval;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TimelineController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Timeline::where('created_at', '>', now()->subDay())->latest()->first());
    }

    public function store(StoreTimelineRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $avatar = null;

        if (isset($payload['avatar'])) {
            $avatar = explode('storage/', $payload['avatar'])[1];

            if (! Storage::exists($avatar) || str_contains($avatar, 'user.png')) {
                $avatar = null;
            }
        }

        $timeline = Timeline::create($payload);

        $messagePayload = [
            'name' => $payload['name'],
            'avatar' => $avatar,
        ];

        if ($payload['seeked']) {
            $interval = CarbonInterval::seconds($timeline->seconds)->cascade()->format('%H:%i:%s');

            $messagePayload['message'] = 'התחיל/ה את הסרט בדקה ' . $interval;

            TimelineSeeked::dispatch($timeline);
        } else if ($timeline->is_paused) {
            $messagePayload['message'] = 'עצר/ה את הסרט';

            TimelinePaused::dispatch($timeline);
        } else if ($payload['is_played']) {
            $messagePayload['message'] = 'התחיל/ה לנגן את הסרט';

            TimelinePlayed::dispatch($timeline);
        }

        if (isset($messagePayload['message'])) {
            MessageCreated::dispatch($timeline->message()->create($messagePayload));
        }

        return response()->json($timeline);
    }
}
