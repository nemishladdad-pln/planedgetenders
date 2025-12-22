<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


if (!function_exists('save_image')) {

    function save_image($image, string $model, string $uniqueId = null):string
    {
        // First of all we need to check if the image file is valid.
        $mime = explode('/', $image->getMimeType());
        // if ($mime[0] !== 'image' || !in_array($mime[1], ['jpg', 'jpeg', 'gif', 'png'])) {
        //     throw new \Exception('did not match data URI with image data');
        // }
        $fileName = time().'_'.$image->getClientOriginalName();
        $filePath = $image->storeAs($model . '/' . $uniqueId, $fileName, 'public');

        return 'storage/'.$filePath;
    }
}

if (!function_exists('save_documents')) {

    function save_documents($image, string $model, string $uniqueId = null):string
    {
        // First of all we need to check if the image file is valid.
        // $mime = explode('/', $image->getMimeType());
        // if ($mime[0] !== 'image' || !in_array($mime[1], ['jpg', 'jpeg', 'gif', 'png'])) {
        //     throw new \Exception('did not match data URI with image data');
        // }
        $fileName = time().'_'.$image->getClientOriginalName();
        $filePath = $image->storeAs($model . '/' . $uniqueId .'/documents', $fileName, 'public');

        return 'storage/'.$filePath;
    }
}
