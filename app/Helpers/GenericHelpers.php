<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenericHelpers
{
    /**
     * Gets an image on base64, creates a file, renames it and save it to public folder
     *
     *
     * @param  string  $image
     * @return string $link Generated link to image for src consumption
     */
    public static function ImageTolink(string $image) : string
    {
        // Maybe link app storage with public and not save it publicly?? ? ? ? ? ? ? ?
        // base64 image on request? why not? why yes?
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
        $imageName = Str::random(20);
        $imagePath = "images/{$imageName}.png";
        Storage::disk('public')->put($imagePath, $data);

        return url(Storage::url($imagePath));
    }
}
