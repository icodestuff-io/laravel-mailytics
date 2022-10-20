<?php

namespace Icodestuff\Mailytics\Traits;

use Icodestuff\Mailytics\Models\Mailytics;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

trait TrackEmailAnalytics
{
    /**
     * Build the view data for the message.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function buildViewData()
    {
        $imageSignature = $this->generateMailyticsImageSignature();
        $url = URL::signedRoute('mailytics.signature', ['imageSignature' => $imageSignature]);
        $this->viewData['mailytics_url'] = $url;

        Mailytics::create([
            'image_signature' => $imageSignature,
            'recipients' => $this->to,
            'ccs' => $this->cc,
            'bccs' => $this->bcc,
            'sent_at' => now(),
            'sender' => $this->from,
            'subject' => $this->subject,
        ]);

        return parent::buildViewData();
    }

    private function generateMailyticsImageSignature(): string
    {
        $imageSignature = Str::uuid().'.jpg';
        $created = copy(dirname('', 2).'pixel.png', storage_path("app/public/mailytics/$imageSignature"));

        if (! $created) {
            throw new \Exception('Failed to create image signature');
        }

        return $imageSignature;
    }
}
