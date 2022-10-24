<?php

namespace Icodestuff\Mailytics\Traits;

use Icodestuff\Mailytics\Models\Mailytics;

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
        if (isset($this->viewData['mailytics_pixel'], $this->viewData['mailytics_url'])) {
            Mailytics::updateOrCreate(['pixel' => $this->viewData['mailytics_pixel']], [
                'mailable_class' => self::class,
                'recipients' => $this->to,
                'ccs' => $this->cc,
                'bccs' => $this->bcc,
                'sent_at' => now(),
                'subject' => $this->subject,
            ]);
        }

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

        $pixel = $mailytics->generateImagePixelFile();
        $data['mailytics_pixel'] = $pixel;
        $data['mailytics_url'] = route('mailytics.viewed', ['pixel' => $pixel]);

        $compiledView = $mailytics->compile($view, $pixel);

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

        $pixel = $mailytics->generateImagePixelFile();
        $data['mailytics_pixel'] = $pixel;
        $data['mailytics_url'] = route('mailytics.viewed', ['pixel' => $pixel]);

        $compiledView = $mailytics->compile($view, $pixel);

        return parent::markdown($compiledView, $data);
    }
}
