<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function indexMarketing(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $user = $request->user();
        $responseClickData = [];
        $responseRequestData = [];
        if ($user && $user->analytics) {
            $analytics = $user->analytics->where('user_id', $user->id)->whereBetween('data_date', [$requestData['startDate'], $requestData['endDate'] ?? $requestData['startDate']])->get()->toArray();
            if ($analytics) {
                foreach ($analytics as $item) {
                    $responseClickData[] = [
                        'x' => $item['data_date'] . ' GMT',
                        'y' => $item['clicks'],
                    ];
                    $responseRequestData[] = [
                        'x' => $item['data_date'] . ' GMT',
                        'y' => $item['requests'],
                    ];
                }
            }
            return response()->json(['clicks' => $responseClickData, 'requests' => $responseRequestData]);
        }
        return response()->json(['clicks' => $responseClickData, 'requests' => $responseRequestData]);
    }

    /**
     * @throws \Exception
     */
    public function indexGeneral(Request $request): JsonResponse
    {
        $openCases = random_int(1, 150);
        $closedCases = random_int(1, 150);
        $pendingCases = random_int(1, 150);
        $totalCases = $openCases + $closedCases + $pendingCases;
        $customers = random_int(1, 150);
        $events = random_int(1, 150);
        $impressions = 0;
        $user = $request->user();

        if ($user && $user->analytics) {
            $analytics = $user->analytics()->where('user_id', $user->id)->get()->toArray();

            if ($analytics) {
                foreach ($analytics as $item) {
                    $impressions += $item['clicks'];
                }
            }
        }

        //Resources? no time
        $settingsData = [
            "openCases"    => $openCases,
            "closedCases"  => $closedCases,
            "totalCases"   => $pendingCases,
            "pendingCases" => $totalCases,
            "customers"    => $customers,
            "events"       => $events,
            "impressions"  => $impressions,
        ];


        return response()->json($settingsData);
    }
}
