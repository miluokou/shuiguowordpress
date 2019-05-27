# woocommerce-alipay

#### 项目介绍
自定义的woocommerce的支付宝支付插件

***

*网上大多数都是收费插件，于是自己自定义了一个支付宝支付插件，支持电脑网站网页支付*

#### 安装教程

1. 下载压缩包到plugins文件夹
2. 解压缩
3. 去woocommerce结算设置中，开启支付宝支付，并配置参数

#### 赞赏一下

![image](assets/imgs/zs.png)


#### 问题

很多人都不知道，app秘钥是什么，建议你们去看一下支付宝官方的文档，看懂了，再来使用，这里稍微解释下吧

*支付宝提供的工具会生成两个，应用私钥和应用公钥，需要把应用公钥填给支付宝，私钥配置进插件*

至于回调地址，如下例子所示：

http://winton.wang/?wc-api=woocommerce_api_wc_gateway_alipay
