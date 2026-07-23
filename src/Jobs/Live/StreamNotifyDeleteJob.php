<?php

declare(strict_types=1);

namespace Larva\Aliyun\Jobs\Live;

use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Live\Live;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Larva\Aliyun\AliyunException;

class StreamNotifyDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var string
     */
    protected string $pushDomain;

    /**
     * Create a new job instance.
     */
    public function __construct(string $pushDomain)
    {
        $this->pushDomain = $pushDomain;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws AliyunException
     */
    public function handle(): void
    {
        try {
            Live::v20161101()->deleteLiveStreamsNotifyUrlConfig()
                ->withDomainName($this->pushDomain)
                ->format('JSON')
                ->request();
        } catch (ClientException|ServerException $e) {
            throw new AliyunException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
}