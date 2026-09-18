<?php

namespace App\View\Helpers;

class DocumentHelper
{
    public static function readableFileSize($size): string
    {
        if($size < 1000){
            return floor($size) . ' B';
        } else if($size < 1000000){
            return floor($size/1000) . ' KB';
        } else {
            return floor($size/1000000) . ' MB';
        }
    }
}
