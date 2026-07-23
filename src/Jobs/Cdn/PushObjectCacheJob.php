<?php
/**
 * This is NOT a freeware, use is subject to license terms
 */

declare(strict_types=1);

namespace Larva\Aliyun\Jobs\Cdn;

use AlibabaCloud\Cdn\Cdn;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * 将源站的内容主动预热到L2缓存节点上。
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PushObjectCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var array
     */
    protected array $urls;

    /**
     * @var string 内容区域
     */
    public string $area;

    /**
     * Create a new job instance.
     *
     * @param array|string $urls
     * @param string $area
     */
    public function __construct(array|string $urls, string $area = 'domestic')
    {
        if (is_string($urls)) {
            $this->urls = [$urls];
        } else {
            $this->urls = $urls;
        }
        $this->area = $area;
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
        $response = Cdn::v20180510()
            ->pushObjectCache()
            ->withObjectPath(implode("\n", $this->urls))
            ->request();
        if (!$response->isSuccess()) {
            $this->fail();
        }
    }
}