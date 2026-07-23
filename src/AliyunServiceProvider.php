<?php
/**
 * This is NOT a freeware, use is subject to license terms
 */

declare(strict_types=1);

namespace Larva\Aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use Illuminate\Support\ServiceProvider;

/**
 * 阿里云服务提供者
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AliyunServiceProvider extends ServiceProvider
{
    /**
     * 注册阿里云客户端
     * @throws ClientException
     */
    public function register(): void
    {
        $this->setupConfig();

        $accounts = config('aliyun');
        foreach ($accounts as $account => $config) {
            if ($account != 'default') {
                $config = array_merge($accounts['default'], $config);
            }
            try {
                if ($config['type'] == 'access_key') {
                    $client = AlibabaCloud::accessKeyClient($config['access_id'], $config['access_key']);
                } elseif ($config['type'] == 'ecs_ram_role') {
                    $client = AlibabaCloud::ecsRamRoleClient($config['role_name']);
                } elseif ($config['type'] == 'ram_role_arn') {
                    $client = AlibabaCloud::ramRoleArnClient($config['access_id'], $config['access_key'], $config['role_arn'], $config['role_session_name']);
                } elseif ($config['type'] == 'rsa_key_pair') {
                    $client = AlibabaCloud::rsaKeyPairClient($config['public_key_id'], $config['private_key_file']);
                } else {
                    throw new ClientException('不支持此类型客户端!', 500);
                }
                if ($account == 'default') {
                    $client->asDefaultClient();
                } else {
                    $client->name($account);
                }
                $client->regionId($config['region'])
                    ->connectTimeout($config['connect_timeout'] ?? 5)
                    ->timeout($config['timeout'] ?? 10)
                    ->proxy($config['proxy']);
            } catch (ClientException $exception) {
                throw new ClientException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
            }
        }
    }

    /**
     * Set up the config.
     */
    protected function setupConfig(): void
    {
        $source = realpath($raw = __DIR__ . '/../config/aliyun.php') ?: $raw;

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $source => config_path('aliyun.php'),
            ], 'aliyun-config');
        }

        $this->mergeConfigFrom($source, 'aliyun');
    }
}