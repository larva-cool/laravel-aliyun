# Laravel Aliyun

This is a Aliyun expansion for the Laravel.

[![License](https://poser.pugx.org/larva/laravel-aliyun/license.svg)](https://packagist.org/packages/larva/laravel-aliyun)
[![Latest Stable Version](https://poser.pugx.org/larva/laravel-aliyun/v/stable.png)](https://packagist.org/packages/larva/laravel-aliyun)
[![Total Downloads](https://poser.pugx.org/larva/laravel-aliyun/downloads.png)](https://packagist.org/packages/larva/laravel-aliyun)

## 环境需求

- PHP >= 8.2
- Laravel >= 12.0

## 安装

```bash
composer require larva/laravel-aliyun -vv
```

## 配置

### 1. 发布配置文件

```bash
php artisan vendor:publish --tag="aliyun-config"
```

发布后配置文件位于 `config/aliyun.php`。

### 2. 环境变量

在 `.env` 文件中配置阿里云访问凭证：

```env
ALIBABA_CLOUD_ACCESS_KEY_ID=your-access-key-id
ALIBABA_CLOUD_ACCESS_KEY_SECRET=your-access-key-secret
```

### 3. 多账号配置

`config/aliyun.php` 支持配置多个阿里云账号，`default` 为默认账号，其余账号会与默认配置合并：

```php
return [
    'default' => [
        'type' => 'access_key',
        'region' => 'cn-beijing',
        'access_id' => env('ALIBABA_CLOUD_ACCESS_KEY_ID'),
        'access_key' => env('ALIBABA_CLOUD_ACCESS_KEY_SECRET'),
        'connect_timeout' => 5,
        'timeout' => 10,
        'proxy' => null,
    ],

    // 额外账号（配置会与 default 合并）
    'secondary' => [
        'type' => 'access_key',
        'access_id' => env('ALIYUN_SECONDARY_ACCESS_KEY_ID'),
        'access_key' => env('ALIYUN_SECONDARY_ACCESS_KEY_SECRET'),
        'region' => 'cn-shanghai',
    ],
];
```

### 4. 认证方式

支持四种阿里云认证方式，通过 `type` 字段切换：

| type | 说明 | 必需参数 |
|------|------|----------|
| `access_key` | AccessKey 认证（默认） | `access_id`, `access_key` |
| `ecs_ram_role` | ECS RAM 角色认证 | `role_name` |
| `ram_role_arn` | RAM Role ARN 认证 | `access_id`, `access_key`, `role_arn`, `role_session_name` |
| `rsa_key_pair` | RSA 密钥对认证 | `public_key_id`, `private_key_file` |

## 功能

### 短信服务（SMS）

```php
use Larva\Aliyun\AliyunHelper;

// 发送短信
AliyunHelper::sendSms(
    phoneNumbers: '13800138000',           // string|array 手机号
    templateCode: 'SMS_123456789',         // 模板Code
    templateParam: ['code' => '1234'],     // array|string 模板参数
    signName: '阿里云',                    // 签名
    smsUpExtendCode: null,                 // 选填，上行短信扩展码
    outId: null,                           // 选填，外部流水号
);
```

### CDN 服务

```php
use Larva\Aliyun\Jobs\Cdn\PushObjectCacheJob;
use Larva\Aliyun\Jobs\Cdn\RefreshObjectCachesJob;

// 预热缓存（将源站内容预热到 L2 缓存节点）
PushObjectCacheJob::dispatch(['https://example.com/page1', 'https://example.com/page2'], 'domestic');
// $area: domestic（国内）| overseas（海外）

// 刷新缓存
RefreshObjectCachesJob::dispatch('https://example.com/page1', 'File');
// $objectType: File（文件）| Directory（目录）
```

### DNS 服务（Alidns）

```php
use Larva\Aliyun\Jobs\Dns\DeleteDomainRecord;
use Larva\Aliyun\Jobs\Dns\DeleteSubDomainRecordsJob;
use Larva\Aliyun\Jobs\Dns\UpdateDomainRecordJob;

// 删除解析记录
DeleteDomainRecord::dispatch('recordId');

// 删除主机记录的所有解析
DeleteSubDomainRecordsJob::dispatch('example.com', 'www');

// 修改解析记录
UpdateDomainRecordJob::dispatch(
    recordId: 'recordId',
    rr: 'www',
    type: 'A',
    value: '192.168.1.1',
    ttl: 600,         // 选填，默认 600
    line: 'default',  // 选填，默认 default
);
```

### 直播服务（Live）

```php
use Larva\Aliyun\LiveHelper;
use Larva\Aliyun\Jobs\Live\StreamsNotifySetJob;
use Larva\Aliyun\Jobs\Live\StreamNotifyDeleteJob;

// 设置直播推流回调（同步）
LiveHelper::setLiveStreamsNotifyUrlConfig('push.example.com', 'https://example.com/callback');

// 删除流回调配置（同步）
LiveHelper::deleteLiveStreamsNotifyUrlConfig('push.example.com');

// 禁止推流（同步）
LiveHelper::forbidLiveStream('app', 'push.example.com', 'streamName');

// 恢复推流（同步）
LiveHelper::resumeLiveStream('app', 'push.example.com', 'streamName');

// 设置直播推流回调（异步队列）
StreamsNotifySetJob::dispatch('push.example.com', 'https://example.com/callback');

// 删除流回调配置（异步队列）
StreamNotifyDeleteJob::dispatch('push.example.com');
```

### 宽带提速服务

```php
use Larva\Aliyun\AliyunHelper;

// 宽带提速预检查
AliyunHelper::SnsuBandPreCheck('192.168.1.1', 80);
```

## 队列任务

所有 Job 默认实现 `ShouldQueue`，通过 `dispatch()` 方法分发到队列异步执行。可通过 Job 类的 `$tries` 属性控制重试次数：

| Job | 默认重试次数 |
|-----|-------------|
| `PushObjectCacheJob` | 默认 |
| `RefreshObjectCachesJob` | 3 |
| `DeleteDomainRecord` | 默认 |
| `DeleteSubDomainRecordsJob` | 默认 |
| `UpdateDomainRecordJob` | 2 |
| `SendSmsJob` | 2 |
| `StreamsNotifySetJob` | 默认 |
| `StreamNotifyDeleteJob` | 默认 |

## License

[MIT](LICENSE)
