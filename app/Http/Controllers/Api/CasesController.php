<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                ),
                'id' => $case->id,
                'caseNotes' => $case->case_notes
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

    /**
     * @throws \JsonException
     */
    public function editCase(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $case = Cases::where('id', $request->get('caseId'))->where('user_id', $user_id)->first();
        $caseProperties = $request->get('caseProperties');
        if ($case) {
            $case->case_properties = $caseProperties;
            $case->save();
            return response()->json($caseProperties);
        }
        return response()->json(['error' => 'Case properties could not be edited'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function storeCaseNotes(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $case = Cases::where('id', $request->get('caseId'))->where('user_id', $user_id)->first();
        $caseNotes = $request->get('caseNotes');

        if ($case) {
            $case->case_notes = $caseNotes;
            $case->save();
            return response()->json($caseNotes);
        }
        return response()->json(['error' => 'Case properties could not be edited'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function deleteCase(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $case = Cases::where('id', $request->get('caseId'))->where('user_id', $user_id)->first();
        if ($case) {
            $case->delete();
            return response()->json();
        }
        return response()->json(['error' => 'Case could not be deleted'], 400);
    }
}
