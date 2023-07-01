<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadFileToS3Controller extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        if ($request->has('file')) {
            $base64File = $request->get('file');

            $fileData = explode(',', $base64File);

            if (count($fileData) !== 2) {
                return response()->json(['error' => 'Invalid base64 file data.'], 400);
            }

            $fileData = base64_decode($fileData[1]);
            $fileType = $this->detectFileType($fileData);
            $fileName = 'file_' . time() . '.' . $fileType;

            Storage::disk('s3')->put($fileName, $fileData);

            $url = Storage::disk('s3')->url($fileName);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'No file data provided.'], 400);
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
            'application/pdf' => 'pdf',
            // Add more MIME types and their corresponding extensions as needed
        ];

        return $extensions[$fileType] ?? 'file';
    }
}
