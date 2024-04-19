<?php

namespace App\Jobs;

use App\Services\JobService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateAccountBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $incomeOrExpenseTransactionIds, protected $account)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(JobService $jobService): void
    {
        $jobService->updateAccountBalance($this->incomeOrExpenseTransactionIds);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Account update job failed', ['exception' => $exception]);
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->account->id)];
    }
}
