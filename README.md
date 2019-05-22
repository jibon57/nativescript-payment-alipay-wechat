# nativescript-payment-alipay-wechat
An example of wechat &amp; alipay payment using H5 feature for NativeScript

## Note: This isn't a plugin!! This is an example, how I have added WeChat & Alipay payment method without using any Native Library. 

Here I have used H5 feature of Wechat & Alipay. Detail from here:

WeChat: https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=15_1

AliPay: https://docs.open.alipay.com/320

So, you should have a business account with H5 feature enable. Here you will need a PHP server for processing all request. You can check `php-server` directory. You will need to add necessary configuration information.

WeChat: `wechat.php` & `libs/wechat.inc.php`

Alipay: `alipay.php` & `libs/alipay/config.php`

I have used NativeScript Angular for this example. Files are located in `demo` directory. Open `home.component.ts` & `common.services.ts` to see the example :) 

This example basically for Android. I will be happy if anyone contribute for iOS :)

**If you would like to use Native Library for wechat login/payment you can have a look [nativescript-wechat-login-sample](https://github.com/jibon57/nativescript-wechat-login-sample) example which is for both iOS and Android.**
