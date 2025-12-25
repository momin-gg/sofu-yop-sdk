<?php

namespace Sofu\Pay;

use Sofu\Pay\Lib\HttpClient;
use Sofu\Pay\Lib\Utils;

/**
 * 嗖付 SDK
 */
class SofuPay
{
    private $client;
    private $decryptKey;

    /**
     * 构造函数 - 自动加载 .env 配置
     */
    public function __construct()
    {
        $envPath = $this->findEnvFile();
        if ($envPath) {
            Utils::loadEnv($envPath);
        }

        $this->decryptKey = Utils::env('SOFU_DECRYPT_KEY', '');
        $this->client = new HttpClient([
            'merchant_no' => Utils::env('SOFU_MERCHANT_NO'),
            'app_key'     => Utils::env('SOFU_APP_KEY'),
            'private_key' => Utils::env('SOFU_PRIVATE_KEY'),
            'endpoint'    => Utils::env('SOFU_ENDPOINT', 'https://developer.sofubao.com'),
            'timeout'     => 30,
        ]);
    }

    private function findEnvFile()
    {
        $paths = [
            dirname(__DIR__) . '/.env',
            getcwd() . '/.env',
            dirname(getcwd()) . '/.env',
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }
        return null;
    }

    // ==================== API 方法 ====================

    /**
     * 聚合支付统一下单
     */
    public function unifiedOrder($orderId, $orderAmount, $goodsName, $payWay, $channel, $notifyUrl, $options = [])
    {
        return $this->client->post('/zro/pay/unify-pay', array_merge([
            'orderId'     => $orderId,
            'orderAmount' => $orderAmount,
            'goodsName'   => $goodsName,
            'payWay'      => $payWay,
            'channel'     => $channel,
            'notifyUrl'   => $notifyUrl,
        ], $options));
    }

    /**
     * 订单查询
     */
    public function queryOrder($orderNo)
    {
        return $this->client->post('/zro/trade/order-query', ['orderNo' => $orderNo]);
    }

    /**
     * 申请退款
     */
    public function refund($orderNo, $refundMoney, $description = null, $notifyUrl = null)
    {
        $params = ['orderNo' => $orderNo, 'refundMoney' => $refundMoney];
        if ($description) $params['description'] = $description;
        if ($notifyUrl) $params['notifyUrl'] = $notifyUrl;
        return $this->client->post('/zro/trade/refund', $params);
    }

    /**
     * 退款查询
     */
    public function queryRefund()
    {
        return $this->client->post('/zro/trade/refund-query');
    }

    /**
     * 账户余额查询
     */
    public function queryBalance()
    {
        return $this->client->post('/zro/account/balance-query');
    }

    /**
     * 待结算查询
     */
    public function queryPendingSettlement()
    {
        return $this->client->post('/zro/account/settlable-query');
    }

    /**
     * 解密回调数据
     */
    public function decryptCallback($data, $key = null)
    {
        return Utils::decrypt($data, $key ?: $this->decryptKey);
    }
}
