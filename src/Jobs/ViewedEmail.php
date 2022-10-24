<?php

namespace Icodestuff\Mailytics\Jobs;

use Icodestuff\Mailytics\Models\Mailytics;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ViewedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $pixel)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailytics = Mailytics::where('pixel', '=', $this->pixel)->firstOrFail();

        // Update Mailytics
        $mailytics->update(['seen_at' => now()]);
    }
}
