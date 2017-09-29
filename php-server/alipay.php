<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__.'/libs/alipay/wappay/service/AlipayTradeService.php';
require_once __DIR__.'/libs/alipay/wappay/buildermodel/AlipayTradeQueryContentBuilder.php';
require_once __DIR__.'/libs/alipay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
require_once __DIR__.'/libs/alipay/config.php';


if($_GET['getQuery'] && $_GET['out_trade_no']){
	
	$out_trade_no = $_GET['out_trade_no'];
	
	$RequestBuilder = new AlipayTradeQueryContentBuilder();
    $RequestBuilder->setOutTradeNo($out_trade_no);

    $Response = new AlipayTradeService($config);
    $result = $Response->Query($RequestBuilder);
	
	header('Content-Type: application/json');
	echo json_encode($result->alipay_trade_query_response);
	
}else{
	if($_GET['price']){
		
		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
		$out_trade_no = "APP-".$d->format("YmdHisv");
		
		$price = round($_GET['price'] * 7, 2); // You will need to convert currecny to CNY. I am using fixed 1 USD = 7 RMB/CNY

		$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
		$payRequestBuilder->setBody($_GET['name']);
		$payRequestBuilder->setSubject($_GET['name']);
		$payRequestBuilder->setOutTradeNo($out_trade_no);
		$payRequestBuilder->setTotalAmount($price);
		$payRequestBuilder->setTimeExpress("5m");

		$payResponse = new AlipayTradeService($config);
		$result = $payResponse->wapPay($payRequestBuilder, $config['return_url'], $config['notify_url']);

		if($result){
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://openapi.alipay.com/gateway.do?".$result);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_REFERER, $_SERVER['SERVER_NAME']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
			$result = curl_exec($ch);
				
			//preg_match('/h5_route_token=(.*)\"/', $result, $match);
			preg_match('/h5_route_token=(.*)\'/', $result, $match);
			preg_match('/startApp\?appId=(.*)\&/', $result, $appid);
			
			$url = "";

			if($match){
				$token = rtrim($match[1], "'");
				$url = "alipays://platformapi/startApp?appId=".trim($appid[1])."&orderSuffix=h5_route_token%3D%22".$token."%22%26is_h5_route%3D%22true%22#Intent;scheme=alipays;package=com.eg.android.AlipayGphone;end";
			}
			
			header('Content-Type: application/json');
			echo json_encode(array(
				"url" => $url,
				"out_trade_no" => $out_trade_no
			));
		}
	}
	
}


?>
