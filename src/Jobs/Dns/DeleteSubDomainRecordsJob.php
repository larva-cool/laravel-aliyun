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
 * 删除主机记录的解析记录
 * @author Tongle Xu <xutongle@gmail.com>
 */
class DeleteSubDomainRecordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var string 操作的域名
     */
    protected string $domain;

    /**
     * @var string 主机记录。
     */
    protected string $rr;

    /**
     * Create a new job instance.
     *
     * @param string $domain
     * @param string $rr
     */
    public function __construct(string $domain, string $rr)
    {
        $this->domain = $domain;
        $this->rr = $rr;
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
        $response = Alidns::v20150109()->deleteSubDomainRecords()->withDomainName($this->domain)->withRR($this->rr)->request();
        if (!$response->isSuccess()) {
            $this->fail();
        }
    }
}