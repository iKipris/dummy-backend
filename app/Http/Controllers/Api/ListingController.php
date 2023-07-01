<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListingController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function index(Request $request): JsonResponse
    {
        $listing = $request->user()->listing;

        if ($listing) {
            Log::debug(var_export($listing->listing_data, true));
            return response()->json($listing->listing_data);
        }

        return response()->json(['error' => 'Listing data could not be fetched'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function store(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $requestData = $request->all();
        $preferences = $requestData['preferences'] ?? [];
        $listings = Listing::updateOrCreate(
            ['user_id' => $user_id],
            [
                'listing_data' => json_encode($requestData, JSON_THROW_ON_ERROR),
            ]
        );
        if ($listings) {
            return response()->json($requestData);
        }

        return response()->json(['error' => 'Listing data could not be store'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function indexPreferences(Request $request): JsonResponse
    {
        $listing = $request->user()->listing;

        if ($listing) {

            return response()->json($listing->preferences);
        }

        return response()->json(['error' => 'Listing preferences could not be fetched'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function storePreferences(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $requestData = $request->all();

        $listing = Listing::where('user_id', $user_id)->first();
        if ($listing) {
            $listing->preferences = $requestData;
            $listing->save();
            Log::debug(var_export($requestData, true));
            return response()->json($requestData);
        }

        return response()->json(['error' => 'Listing preferences could not be store'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function publish(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;

        $listing = Listing::where('user_id', $user_id)->first();
        if ($listing) {
            $listing->published = 1;
            $listing->save();
            return response()->json(['message' => 'Your listing has been published'], 200);
        }

        return response()->json(['error' => 'Listing preferences could not be store'], 400);
    }
}
