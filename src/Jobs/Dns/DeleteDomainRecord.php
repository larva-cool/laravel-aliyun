<?php
/**
 * This is NOT a freeware, use is subject to license terms
 */

declare(strict_types=1);

namespace Larva\Aliyun\Jobs\Dns;

use AlibabaCloud\Alidns\Alidns;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * 删除解析记录
 * @author Tongle Xu <xutongle@gmail.com>
 */
class DeleteDomainRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var string
     */
    protected string $recordId;

    /**
     * Create a new job instance.
     *
     * @param string $recordId
     */
    public function __construct(string $recordId)
    {
        $this->recordId = $recordId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ClientException
     * @throws ServerException
     */
    public function handle(): void
    {
        $response = Alidns::v20150109()
            ->deleteDomainRecord()
            ->withRecordId($this->recordId)
            ->request();
        if (!$response->isSuccess()) {
            $this->fail();
        }
    }
}