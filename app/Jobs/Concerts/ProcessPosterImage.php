<?php

namespace App\Jobs\Concerts;

use App\Models\Concert;
use Illuminate\Bus\Queueable;
use Intervention\Image\Facades\Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPosterImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The concert whose poster image must be processed.
     *
     * @var \App\Models\Concert
     */
    public $concert;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Concert  $concert
     * @return void
     */
    public function __construct(Concert $concert)
    {
        $this->concert = $concert;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imageContents = Storage::disk('public')->get($this->concert->poster_image_path);
        $image = Image::make($imageContents);

        $image->widen(600)->limitColors(255)->encode();

        Storage::disk('public')->put($this->concert->poster_image_path, (string) $image);
    }
}
