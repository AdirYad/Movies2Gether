<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function index(): JsonResponse
    {
        $avatars = Storage::allFiles('assets/avatars');

        foreach ($avatars as &$avatar) {
            $avatar = "/storage/{$avatar}";
        }

        return response()->json($avatars);
    }
}
