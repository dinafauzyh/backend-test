<?php

namespace App\Http\Helpers;

use Storage;

class FileHelper 
{
    /**
     * return filename
     *
     */
    public function moveFile($file, $path)
    {
        $ext = $file->getClientOriginalName();
        $name = pathinfo($ext, PATHINFO_FILENAME);
        $name = str_replace(' ', '-', $name);
        $extension = $file->getClientOriginalExtension();
        $finalName = $name . '-' . time() . '.' . $extension;
        $save = $file->move(public_path($path), $finalName);
        if ($save) {
            return $path . $finalName;
        }
        return false;
    }
}