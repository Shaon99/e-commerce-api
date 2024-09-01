<?php

namespace App\Jobs;

use App\Mail\BrandCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Events\BrandEmailSent;

class SendBrandCreatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $brand;
    
    // Set the maximum number of attempts
    public $tries = 5;

    // Set the maximum execution time in seconds
    public $timeout = 120;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($brand)
    {
        $this->brand = $brand;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to('admin@example.com')->send(new BrandCreated($this->brand));
            event(new BrandEmailSent($this->brand));
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            throw $e;
        }
    }
}
