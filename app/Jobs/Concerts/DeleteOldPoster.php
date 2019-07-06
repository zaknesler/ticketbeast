<?php

namespace App\Jobs\Concerts;

use App\Models\Concert;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteOldPoster implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The concert to which the poster belongs.
     *
     * @var \App\Models\Concert
     */
    public $concert;

    /**
     * The path of the image that must be deleted.
     *
     * @var string
     */
    public $oldImagePath;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Concert  $concert
     * @param  string  $oldImagePath
     * @return void
     */
    public function __construct(Concert $concert, $oldImagePath)
    {
        $this->concert = $concert;
        $this->oldImagePath = $oldImagePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Storage::disk('public')->delete($this->oldImagePath);
    }
}
