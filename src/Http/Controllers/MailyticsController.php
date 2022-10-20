<?php

namespace Icodestuff\Mailytics\Http\Controllers;

use Icodestuff\Mailytics\Jobs\ViewedEmail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class MailyticsController
{
    public function showImageSignature($imageSignature)
    {
        if (! request()->hasValidSignature()) {
            abort(404);
        }

        $path = storage_path('app/public/mailytics/'.$imageSignature);
        if (! File::exists($path)) {
            abort(404);
        }

        ViewedEmail::dispatch($imageSignature);

        // Mark as Seen
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file);
        $response->header('Content-Type', $type);

        return $response;
    }
}
