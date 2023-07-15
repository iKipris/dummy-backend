<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                'caseNotes' => $case->case_notes,
                'caseFiles' => $case->case_files
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

    /**
     * @throws \JsonException
     */
    public function addCaseFiles(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $caseFiles = $request->get('caseFiles');
        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();

        if ($case) {
            foreach ($caseFiles as $file) {
                $fileData = [
                    "id"          => Str::random(20),
                    "name"        => $file['name'],
                    "description" => '',
                    "url"         => $file['url'],
                    "type"        => $file['type'],
                    "status"      => 'inactive',
                ];

                $existingCaseFiles = json_decode($case->case_files, true) ?? [];
                $existingCaseFiles[] = $fileData;

                $case->case_files = json_encode($existingCaseFiles);
                $case->save();
            }

            return response()->json();
        }

        return response()->json(['error' => 'Case files could not be added'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function addCaseMember(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $caseMember = $request->get('caseMember');
        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();

        if ($case) {
            $memberData = [
                "id"          => Str::random(20),
                "firstName"   => $caseMember['firstName'],
                "lastName"   => $caseMember['lastName'],
                "description" => $caseMember['description'],
                "email"         => $caseMember['email'],
                "phone"        => $caseMember['phone'],
                "status"      => $caseMember['status'],
            ];

            $existingCaseMembers = json_decode($case->case_members, true) ?? [];
            $existingCaseMembers[] = $memberData;

            $case->case_members = json_encode($existingCaseMembers);
            $case->save();

            return response()->json();
        }

        return response()->json(['error' => 'Member could not be added'], 400);
    }

    /**
     */
    public function uploadMultipleCaseFiles(Request $request): JsonResponse
    {
        $caseFile = $request->get('file');
        $fileName = $request->get('fileName');

        return $this->upload($caseFile,$fileName);
    }

    public function upload($file, $fileName)
    {
        if ($file) {
            $base64File = $file;

            $fileData = explode(',', $base64File);

            if (count($fileData) !== 2) {
                return response()->json(['error' => 'Invalid base64 file data.'], 400);
            }

            $fileData = base64_decode($fileData[1]);
            $fileType = $this->detectFileType($fileData);
            $name = $fileName . '_' . time() . '.' . $fileType;

            Storage::disk('s3')->put($name, $fileData);

            return response()->json(['url' => Storage::disk('s3')->url($name), 'name' => $fileName, 'type' => $fileType]);

        }

        return response()->json(['error' => 'Files could not be uploaded.'], 400);
    }

    private function detectFileType($fileData): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_buffer($finfo, $fileData);
        finfo_close($finfo);

        // Extract the file extension from the MIME type
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/zip' => 'zip',
            'text/plain' => 'txt',
            // Add more MIME types and their corresponding extensions as needed
        ];

        return $extensions[$fileType] ?? 'file';
    }

    /**
     * @throws \JsonException
     */
    public function indexCaseFiles(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();
        $caseFiles = json_decode($case->case_files) ?? [];
        return response()->json($caseFiles);
    }

    /**
     * @throws \JsonException
     */
    public function editFileInfo(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $fileName = $request->get('fileName');
        $fileDescription = $request->get('fileDescription');
        $fileStatus = $request->get('fileStatus');
        $fileId = $request->get('fileId');

        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();

        if ($case) {
            $existingCaseFiles = json_decode($case->case_files, true, 512, JSON_THROW_ON_ERROR) ?? [];
            foreach ($existingCaseFiles as $key=>$file) {
                if ($file["id"] === $fileId) {
                    $existingCaseFiles[$key]["description"] = $fileDescription ?? "";
                    $existingCaseFiles[$key]["status"] = $fileStatus;
                    $existingCaseFiles[$key]["name"] = $fileName;
                }
            }
            $case->case_files = json_encode($existingCaseFiles, JSON_THROW_ON_ERROR);
            $case->save();

            return response()->json();
        }

        return response()->json(['error' => 'Case files could not be added'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function deleteFile(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $fileId = $request->get('fileId');

        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();

        if ($case) {
            $existingCaseFiles = json_decode($case->case_files, true, 512, JSON_THROW_ON_ERROR) ?? [];
            foreach ($existingCaseFiles as $key=>$file) {
                if ($file["id"] === $fileId) {
                    array_splice($existingCaseFiles, $key, 1);
                }
            }
            $case->case_files = json_encode($existingCaseFiles, JSON_THROW_ON_ERROR);
            $case->save();

            return response()->json();
        }

        return response()->json(['error' => 'Case files could not be added'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function deleteMember(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $memberId = $request->get('memberId');

        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();

        if ($case) {
            $existingCaseMembers = json_decode($case->case_members, true, 512, JSON_THROW_ON_ERROR) ?? [];
            foreach ($existingCaseMembers as $key=>$file) {
                if ($file["id"] === $memberId) {
                    array_splice($existingCaseMembers, $key, 1);
                }
            }
            $case->case_members = json_encode($existingCaseMembers, JSON_THROW_ON_ERROR);
            $case->save();

            return response()->json();
        }

        return response()->json(['error' => 'Case member could not be deleted'], 400);
    }

    /**
     * @throws \JsonException
     */
    public function indexCaseMembers(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();
        $caseMembers = json_decode($case->case_members) ?? [];
        return response()->json($caseMembers);
    }

    /**
     * @throws \JsonException
     */
    public function editMemberInfo(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;
        $caseId = $request->get('caseId');
        $memberData = $request->get('memberData');
        $case = Cases::where('id', $caseId)->where('user_id', $user_id)->first();

        if ($case) {
            $existingCaseMembers = json_decode($case->case_members, true, 512, JSON_THROW_ON_ERROR) ?? [];
            foreach ($existingCaseMembers as $key=>$member) {
                if ($member["id"] === $memberData["id"]) {
                    $existingCaseMembers[$key]["description"] = $memberData["description"] ?? "";
                    $existingCaseMembers[$key]["status"] = $memberData["status"];
                    $existingCaseMembers[$key]["firstName"] = $memberData["firstName"];
                    $existingCaseMembers[$key]["lastName"] = $memberData["lastName"];
                    $existingCaseMembers[$key]["phone"] = $memberData["phone"];
                    $existingCaseMembers[$key]["email"] = $memberData["email"];
                }
            }
            $case->case_members = json_encode($existingCaseMembers, JSON_THROW_ON_ERROR);
            $case->save();

            return response()->json();
        }

        return response()->json(['error' => 'Case member could not be edited'], 400);
    }
}
