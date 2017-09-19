<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "",
		
		//异步通知地址
		'notify_url' => "http://example.com/notify.php",
		
		//同步跳转
		'return_url' => "http://example.com/notify.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlQzuwxIWmuIVYH2Gg8gcKzS5ssr/tDX4y1J+USxivlhy616NBE1QCfXHQlJCTD+4RKl1Ml1x5xrh4Ys2HUqzchi3+cSa7I28QXEfgOi6uyph9DHnmb7XMWsiOAJiOtW9E+yByNrUOuENc8VWcxQQu/yZLBgMw58Ov2QGD4mvxpImsP4/N2tSTqrlHU3Nj1ySKLXaRqwrmaIA5XietF+iWSlh/u8EZv5yBjRvZNlkYFh8VLMWiK6cuB/pRVM18Yk9y9rrWvZ11mar3Kz3y3gwIuyG5djFWVfDTdoNIMFDY4d06havGz0Xs0ZUTS4ugdz2l6JPOIiUrI+ve+jSpY9hqwIDAQAB",
		
	
);