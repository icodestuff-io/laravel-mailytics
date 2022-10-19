<?php

namespace Icodestuff\Mailytics\Jobs;

use App\Models\Mailytics;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TrackEmailView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $imageSignature)
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
        $mailytics = Mailytics::where('image_signature', '=', $this->imageSignature)->firstOrFail();

        // Update Mailytics
        $mailytics->update(['seen_at' => now()]);

        // Delete image signature
        File::delete(storage_path("app/public/mailytics/$this->imageSignature"));
    }
}
