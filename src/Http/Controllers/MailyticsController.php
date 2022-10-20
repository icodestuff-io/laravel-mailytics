<?php

namespace Icodestuff\Mailytics\Http\Controllers;

use Icodestuff\Mailytics\Jobs\ViewedEmail;
use Icodestuff\Mailytics\Models\Mailytics;
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

    public function dashboard()
    {
        $sentCount = Mailytics::count();
        $openRate = Mailytics::whereNotNull('seen_at')->count();

        return view('mailytics::dashboard', [
            'sent_count' => $sentCount,
            'open_rate' => number_format($openRate/$sentCount * 100, 2),
            'click_rate' => number_format($openRate/$sentCount * 100, 2),
        ]);
    }
}
