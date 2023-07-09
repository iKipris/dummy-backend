<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CasesController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function indexCases(Request $request): JsonResponse
    {
        $cases = $request->user()->cases;
        $responseData = [];
        foreach ($cases as $case) {
            $responseData[] = [
                'id' => $case->id,
                'caseProperties' => json_decode(
                    $case->case_properties,
                    JSON_THROW_ON_ERROR,
                    512,
                    JSON_THROW_ON_ERROR
                )
            ];
        }
        return response()->json($responseData);
    }

    /**
     * @throws \JsonException
     */
    public function indexCase(Request $request): JsonResponse
    {
        $cases = $request->user()->cases->where('id', $request->get('id'));

        foreach ($cases as $case) {
            $responseData = [
                'caseProperties' => json_decode(
                    $case->case_properties,
                    JSON_THROW_ON_ERROR,
                    512,
                    JSON_THROW_ON_ERROR
                )
            ];
        }
        return response()->json($responseData);
    }

    /**
     * @throws \JsonException
     */
    public function createCase(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $requestData = $request->all();
        $newCase = Cases::create(
            [
                'user_id' => $user_id,
                'case_properties' => json_encode($requestData['caseProperties'], JSON_THROW_ON_ERROR)
            ]
        );

        if ($newCase) {
            $responseData = [];
            $cases = $request->user()->cases;
            foreach ($cases as $case) {
                $responseData[] = [
                    'id' => $case->id,
                    'caseProperties' => json_decode(
                        $case->case_properties,
                        JSON_THROW_ON_ERROR,
                        512,
                        JSON_THROW_ON_ERROR
                    )
                ];
            }
            return response()->json($responseData);
        }

        return response()->json(['error' => 'New case could not be created'], 400);
    }
}
