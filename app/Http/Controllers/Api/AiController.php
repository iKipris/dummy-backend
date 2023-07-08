<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $aiData = $request->user()->ai;
        $user_id = $request->user()->id;
        $settings = $request->user()->settings;

        if ($aiData) {
            $ai = [
                "profileUser" => [
                    "id" => $aiData->user_id,
                    "avatar" => $aiData->avatar,
                ],
                "chats" => json_decode($aiData->chats)
            ];

            return response()->json($ai);
        }

        $ai = [
            "profileUser" => [
                "id" => $user_id,
                "avatar" => $settings->avatar_link ?? '',
            ],
            "chats" => []
        ];

        return response()->json($ai);
    }

    public function storeChat(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $avatar_link = $request->user()->settings->avatar_link ?? '';
        $chats = json_encode($request->all(), JSON_THROW_ON_ERROR); // Convert the chats array to a JSON string

        $ai = Ai::updateOrCreate(
            ['user_id' => $user_id],
            [
                'user_id' => $user_id,
                'avatar_link' => $avatar_link,
                'chats' => $chats,
            ]
        );

        return response()->json($ai);
    }

    public function addMessage(Request $request): JsonResponse
    {
        // get user_id from request and sender_id which is always the same with chat_id return message
        $user_id = $request->user()->id;
        $requestData = $request->all();
    }
}
