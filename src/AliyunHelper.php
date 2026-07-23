<?php
/**
 * This is NOT a freeware, use is subject to license terms
 */

declare(strict_types=1);

namespace Larva\Aliyun;

use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Client\Result\Result;
use AlibabaCloud\Dysmsapi\Dysmsapi;
use AlibabaCloud\Snsuapi\Snsuapi;

/**
 * 快速操作助手
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AliyunHelper
{
    /**
     * 发送短信
     * @param array|string $phoneNumbers
     * @param string $templateCode
     * @param string|array $templateParam JSON格式的参数
     * @param string $signName
     * @param string|null $smsUpExtendCode
     * @param string|null $outId
     * @return Result
     * @throws ClientException
     * @throws ServerException
     */
    public static function sendSms(array|string $phoneNumbers, string $templateCode, array|string $templateParam, string $signName, ?string $smsUpExtendCode = null, ?string $outId = null): Result
    {
        if (is_array($phoneNumbers)) {
            $phoneNumbers = implode(",", $phoneNumbers);
        }
        if (is_array($templateParam)) {
            $templateParam = json_encode($templateParam);
        }
        $dySmsApi = Dysmsapi::v20170525()
            ->sendSms()
            ->withPhoneNumbers($phoneNumbers)
            ->withTemplateCode($templateCode)
            ->withTemplateParam($templateParam)
            ->withSignName($signName);
        if (!is_null($smsUpExtendCode)) {
            $dySmsApi->withSmsUpExtendCode($smsUpExtendCode);
        }
        if (!is_null($outId)) {
            $dySmsApi->withOutId($outId);
        }
        return $dySmsApi->request();
    }

    /**
     * 宽带提速预检查
     * @param string $ipAddress IP地址
     * @param int $port 端口
     * @return Result
     * @throws ClientException
     * @throws ServerException
     */
    public static function SnsuBandPreCheck(string $ipAddress, int $port): Result
    {
        return Snsuapi::v20180709()->bandPrecheck()->withIpAddress($ipAddress)->withPort($port)
            ->request();
    }
}