<?php

namespace App\Helpers;


if (!function_exists('checkUploadedFileProperties')) {
     function checkUploadedFileProperties($extension, $fileSize)
        {
            $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
            $maxFileSize = 5242888; // Uploaded file size limit is 5mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize <= $maxFileSize) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
}



