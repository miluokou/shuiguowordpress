﻿# Wenprise Alipay Gateway For WooCommerce #
Contributors: iwillhappy1314
Donate link: https://www.wpzhiku.com/
Tags: Alipay, WooCommerce, woocommerce, payment, payment gateway, gateway, 支付宝, 支付宝支付, Alipay payment gateway, Wechat gateway, credit card, pay, online payment, shop, e-commerce, ecommerce
Requires PHP: 5.6.0
Requires at least: 3.9
Tested up to: 5.1
Stable tag: 1.0.4
License: GPL-2.0+

Alipay payment gateway for WooCommerce, WooCommerce 支付宝免费全功能支付网关。

## Description ##
**功能更全面的 WooCommerce 免费支付宝支付网关**，企业版，需要支付宝企业认证才可以使用。支持功能如下：

* 支持所有 WooCommerce 产品类型
* PC 端扫码或登录账户支付
* 移动端调起支付宝 APP 或者登录 wap 版支付宝支付
* 支持支付宝同步回调和异步回调
* 支持主动查询支付宝订单完成状态的功能
* 支持在 WooCommerce 订单中直接通过支付宝支付退款，退款原路返回
* 由于支付宝在微信中被屏蔽掉了，支付宝支付在微信中自动隐藏
* 货币不是人民币时，可以设置一个固定汇率

插件设置方法及使用教程请参考：
[Wenprise Alipay Gateway For WooCommerce 插件设置教程](https://www.wpzhiku.com/wenprise-alipay-gateway-for-woocommerce-document/)

微信支付网关：
[Wenprise WeChatPay Payment Gateway For WooCommerce](https://wordpress.org/plugins/wenprise-wechatpay-checkout-for-woocommerce/)

### Support 技术支持 ###

Email: amos@wpcio.com

## Installation ##

1. 上传插件到`/wp-content/plugins/` 目录，或在 WordPress 安装插件界面搜索 "Wenprise Alipay Gateway For WooCommerce"，点击安装。
2. 在插件管理菜单激活插件

## Upgrade Notice ##

更新之前，请先备份数据库。


## Frequently Asked Questions ##

### 支持支付宝海外版吗？ ###
这个插件只支持支付宝国内版，海外版需要另外的插件支持，后续会发布另外一个插件。

### 支持在微信中使用吗？###
因为微信无耻的屏蔽掉了支付宝支付，所以本插件不支持在微信中使用，在微信中支付时，插件会自动屏蔽掉自己，以免不能支付带来不好的用户体验。

## Screenshots ##
* Setting
* payment

## Changelog ##

### 1.0.4 ###
* 修复某些情况下未支付时，显示支付成功的 Bug

### 1.0.3 ###
* 修改支付宝跳转方式为站内页面代理，以便在在新窗口中打开、同时弹出支付确认窗口
* 增加主动查询功能、以便在其他验证方式不可用时，验证订单是否支付

### 1.0.2 ###
* Bugfix

### 1.0 ###
* 初次发布