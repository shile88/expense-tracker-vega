<?php

namespace App\Jobs;

use App\Services\JobService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ScheduledTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(JobService $jobService): void
    {
        $jobService->createTransaction();
        Log::info('Create transaction job finished successfully');
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ScheduleTransaction job failed', ['exception' => $exception]);
    }
}
