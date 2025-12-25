<?php

namespace Sofu\Pay;

use Sofu\Pay\Lib\HttpClient;
use Sofu\Pay\Lib\Api;
use Sofu\Pay\Lib\Utils;

/**
 * 嗖付 SDK 入口
 */
class SofuPay
{
    private $api;
    private $decryptKey;

    private static $apiPaths = [
        'unified_order'    => '/zro/pay/unify-pay',
        'query_order'      => '/zro/trade/order-query',
        'refund'           => '/zro/trade/refund',
        'query_refund'     => '/zro/trade/refund-query',
        'query_balance'    => '/zro/account/balance-query',
        'query_settlement' => '/zro/account/settlable-query',
    ];

    /**
     * 构造函数
     * 
     * 自动从项目根目录的 .env 文件加载配置
     */
    public function __construct()
    {
        // 自动查找 .env 文件
        $envPath = $this->findEnvFile();
        if ($envPath) {
            Utils::loadEnv($envPath);
        }

        $merchantNo  = Utils::env('SOFU_MERCHANT_NO');
        $appKey      = Utils::env('SOFU_APP_KEY');
        $privateKey  = Utils::env('SOFU_PRIVATE_KEY');
        $endpoint    = Utils::env('SOFU_ENDPOINT', 'https://developer.sofubao.com');
        $this->decryptKey = Utils::env('SOFU_DECRYPT_KEY', '');

        $client = new HttpClient([
            'merchant_no' => $merchantNo,
            'app_key'     => $appKey,
            'private_key' => $privateKey,
            'endpoint'    => $endpoint,
            'timeout'     => 30,
        ]);

        $this->api = new Api($client, self::$apiPaths);
    }

    /**
     * 查找 .env 文件
     */
    private function findEnvFile()
    {
        $paths = [
            dirname(__DIR__) . '/.env',           // SDK 目录
            getcwd() . '/.env',                   // 当前工作目录
            dirname(getcwd()) . '/.env',          // 上级目录
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        return null;
    }

    /**
     * 获取 API 实例
     */
    public function api()
    {
        return $this->api;
    }

    /**
     * 解密回调数据
     */
    public function decryptCallback($data, $key = null)
    {
        return Utils::decrypt($data, $key ?: $this->decryptKey);
    }

    // ==================== API 快捷方法 ====================

    public function unifiedOrder($orderId, $orderAmount, $goodsName, $payWay, $channel, $notifyUrl, $options = [])
    {
        return $this->api->unifiedOrder($orderId, $orderAmount, $goodsName, $payWay, $channel, $notifyUrl, $options);
    }

    public function queryOrder($orderNo)
    {
        return $this->api->queryOrder($orderNo);
    }

    public function refund($orderNo, $refundMoney, $description = null, $notifyUrl = null)
    {
        return $this->api->refund($orderNo, $refundMoney, $description, $notifyUrl);
    }

    public function queryRefund()
    {
        return $this->api->queryRefund();
    }

    public function queryBalance()
    {
        return $this->api->queryBalance();
    }

    public function queryPendingSettlement()
    {
        return $this->api->queryPendingSettlement();
    }
}
