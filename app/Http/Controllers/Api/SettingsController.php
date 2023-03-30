<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GenericHelpers;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use function Psy\debug;

class SettingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Maybe this should be in a Request class specificly for this request?
        $settings = $request->user()->settings;
        $settingsData = [];
        //Resources? no time
        if ($settings) {
            $settingsData = [
                "firstName" => $settings->first_name,
                "lastName" => $settings->last_name,
                "phone" => $settings->phone_number,
                "avatar" => $settings->avatar_link,
                "zipCode" => $settings->zip_code,
                "address" => $settings->address,
                "city" => $settings->city,
            ];
        }
        return response()->json($settingsData);
    }

    public function store(Request $request)
    {
        $user_id = $request->user()->id;
        $requestData = $request->all();
        $settings = new Settings();
        if ($requestData['avatar']) {
            $link = GenericHelpers::ImageTolink($requestData['avatar']);
        } else {
            $link = '';
        }

        $settings = Settings::updateOrCreate(
            ['user_id' => $user_id],
            [
                'first_name' => $requestData['firstName'],
                'last_name' => $requestData['lastName'],
                'phone_number' => $requestData['phone'],
                'avatar_link' => $link,
                'zip_code' => $requestData['zipCode'],
                'address' => $requestData['address'],
                'city' => $requestData['city'],

            ]
        );

        //Resources? no time
        $settingsData = [
            "firstName" => $settings->first_name,
            "lastName" => $settings->last_name,
            "phone" => $settings->phone_number,
            "avatarLink" => $settings->avatar_link,
            "zipCode" => $settings->zip_code,
            "address" => $settings->address,
            "city" => $settings->city,
        ];

        return response()->json($settingsData);
    }
}
