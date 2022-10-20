<?php

namespace Icodestuff\Mailytics\Traits;

use Icodestuff\Mailytics\Models\Mailytics;
use Illuminate\Mail\Mailable;
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
        /** @var \Icodestuff\Mailytics\Mailytics $mailytics */
        $mailytics = app(\Icodestuff\Mailytics\Mailytics::class);
        $imageSignature = $mailytics->generateImageSignatureFile();
        $url = URL::signedRoute('mailytics.signature', ['imageSignature' => $imageSignature]);
        $this->viewData['mailytics_url'] = $url;

        Mailytics::create([
            'mailable_class' => self::class,
            'image_signature' => $imageSignature,
            'recipients' => $this->to,
            'ccs' => $this->cc,
            'bccs' => $this->bcc,
            'sent_at' => now(),
            'subject' => $this->subject,
        ]);

        return parent::buildViewData();
    }

    /**
     * Set the view and view data for the message.
     *
     * @param  string  $view
     * @param  array  $data
     * @return $this
     */
    public function view($view, array $data = [])
    {
        /** @var \Icodestuff\Mailytics\Mailytics $mailytics */
        $mailytics = app(\Icodestuff\Mailytics\Mailytics::class);

        $compiledView = $mailytics->compile($view);

        return parent::view($compiledView, $data);
    }

    /**
     * Set the view and view data for the message.
     *
     * @param  string  $view
     * @param  array  $data
     * @return $this
     */
    public function markdown($view, array $data = [])
    {
        /** @var \Icodestuff\Mailytics\Mailytics $mailytics */
        $mailytics = app(\Icodestuff\Mailytics\Mailytics::class);

        $compiledView = $mailytics->compile($view);

        return parent::markdown($compiledView, $data);
    }
}
