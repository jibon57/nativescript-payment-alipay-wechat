<?php
require_once __DIR__.'/libs/wechat.inc.php';

//https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=3_1

$mer_id = ""; // business number 微信支付分配的商户号
$app_id = ""; //APPID 
$key = ""; //API密钥
	
if($_GET['getQuery'] && $_GET['out_trade_no']){
	
	$out_trade_no = $_GET['out_trade_no'];
	
	$parms = array(
		"out_trade_no" => $out_trade_no,
	);
	
	$newClass = new WeChatPrepare($mer_id, $app_id, $key, $parms);
	$result = $newClass->getOrderquery();
	
	header('Content-Type: application/json');
	echo json_encode($result);
	
}else{
	if($_GET['price']){
		
		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
		$out_trade_no = "APP-".$d->format("YmdHisv");
		
		$parms = array(
			"body" => $_GET['name'],
			"notify_url" => "https://example.com/notify.php", // your notify url
			"out_trade_no" => $out_trade_no,
			"trade_type" => "MWEB",
			"scene_info" => '{"h5_info": {"type":"Wap","wap_url": "https://pay.qq.com","wap_name": "腾讯充值"}}',
			"fee_type" => "USD",
			"spbill_create_ip" => $_SERVER['SERVER_ADDR'], //this is important because your server will collect the URL not client ;)
			"total_fee" => $_GET['price'] * 100,
		);
		$newClass = new WeChatPrepare($mer_id, $app_id, $key, $parms);
		$result = $newClass->getUnifiedorder();

		if($result["mweb_url"]){
			$out = $newClass->getWechatUrl($result["mweb_url"], $out_trade_no);
			header('Content-Type: application/json');
			echo json_encode($out);
		}
	}
}
?>
