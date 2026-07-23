<?php
/**
 * This is NOT a freeware, use is subject to license terms
 */

declare(strict_types=1);

namespace Larva\Aliyun\Jobs\Sms;

use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Larva\Aliyun\AliyunHelper;

/**
 * 发送手机短信
 * @author Tongle Xu <xutongle@gmail.com>
 */
class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public int $tries = 2;

    /**
     * @var array|string
     */
    protected array|string $phoneNumbers;

    /**
     * @var string
     */
    protected string $templateCode;

    /**
     * @var array|string
     */
    protected array|string $templateParam;

    /**
     * @var string
     */
    protected string $signName;

    /**
     * @var string|null
     */
    protected ?string $smsUpExtendCode;

    /**
     * @var string|null
     */
    protected ?string $outId;

    /**
     * @param array|string $phoneNumbers
     * @param string $templateCode
     * @param string|array $templateParam JSON格式的参数
     * @param string $signName
     * @param string|null $smsUpExtendCode
     * @param string|null $outId
     */
    public function __construct(array|string $phoneNumbers, string $templateCode, array|string $templateParam, string $signName, ?string $smsUpExtendCode = null, ?string $outId = null)
    {
        $this->phoneNumbers = $phoneNumbers;
        $this->templateCode = $templateCode;
        $this->templateParam = $templateParam;
        $this->signName = $signName;
        $this->smsUpExtendCode = $smsUpExtendCode;
        $this->outId = $outId;
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
        AliyunHelper::sendSms($this->phoneNumbers, $this->templateCode, $this->templateParam, $this->signName, $this->smsUpExtendCode, $this->outId);
    }
}