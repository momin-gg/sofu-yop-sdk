<?php
/**
 * Sofu PHP SDK 测试
 */

require __DIR__ . '/../vendor/autoload.php';

use Sofu\Pay\SofuPay;

$sdk = new SofuPay();

// H5 支付示例
$response = $sdk->unifiedOrder(
    'SF' . date('YmdHis'),      // 订单号
    0.01,                        // 金额
    '测试商品',                   // 商品名
    'H5_PAY',                    // 支付方式
    'WECHAT',                    // 渠道
    'https://example.com/notify', // 回调地址
    '127.0.0.1'                  // 用户IP（H5必填）
);

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
