<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\UrlClickRepositoryInterface;
use App\Contracts\UrlRepositoryInterface;
use App\DTOs\RecordClickDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecordUrlClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of attempts for this job.
     */
    public int $tries = 3;

    /**
     * Retry backoff in seconds.
     *
     * @var array<int>
     */
    public array $backoff = [5, 15, 30];

    /**
     * Timeout for the job in seconds.
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly RecordClickDTO $clickDTO
    ) {
        $this->onQueue('analytics');
    }

    /**
     * Execute the job.
     */
    public function handle(
        UrlClickRepositoryInterface $clickRepository,
        UrlRepositoryInterface $urlRepository
    ): void {
        // 1. Record the detailed click event
        $clickRepository->record($this->clickDTO);

        // 2. Atomically increment the total click counter on the URL record
        $urlRepository->incrementClickCount($this->clickDTO->urlId, 1);
    }

    /**
     * Handle job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Failed to process RecordUrlClickJob: ' . $exception->getMessage(), [
            'url_id' => $this->clickDTO->urlId,
            'ip' => $this->clickDTO->ipAddress,
            'exception' => $exception->getTraceAsString(),
        ]);
    }
}
