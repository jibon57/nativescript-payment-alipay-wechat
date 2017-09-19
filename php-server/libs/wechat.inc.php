<?php
date_default_timezone_set("Asia/Shanghai");

class WeChatPrepare{
	
	protected $appid;
	protected $mch_id;
	protected $key;
	private $parms = array();
	
	public function __construct($appid, $mch_id, $key, $parms){
       $this->appid = $appid;
       $this->mch_id = $mch_id;
	   $this->key = $key;
	   
	   $this->buildParams($parms);
    }
	
	private function getNonceStr($length = 32){
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}
	
	private function buildParams($parms){
		$parms['appid'] = $this->appid;
		$parms['mch_id'] = $this->mch_id;
		$parms['nonce_str'] = $this->getNonceStr();
		
		if(!isset($parms['spbill_create_ip'])){
			$parms['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
		}
		
		ksort($parms);
		$this->parms = $parms;
		
	}
	
	private function getSign($parms){
		$buff = "";
		
		foreach ($parms as $k => $v){
			$buff .= $k . "=" . $v . "&";
		}
		
		$buff = trim($buff, "&");

		$string = $buff. "&key=".$this->key;
		$string = md5($string);
		$sign = strtoupper($string);
		
		return $sign;
	}
	
	private function buildXml(){
		
		$parms = $this->parms;
		
		$sign = $this->getSign($parms);
		
		$xml = "<xml>";
		
		foreach ($parms as $k => $v){
			$xml .= "<". $k . ">" . $v . "</". $k . ">";
		}

		$xml = $xml."<sign>{$sign}</sign></xml>";
		
		return $xml;
		
	}
	
	public function getUnifiedorder(){
		$xml = $this->buildXml();
		
		$ch = curl_init();
		
		curl_setopt($ch,CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/ssl/cacert.pem");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$data = curl_exec($ch);

		if($data){
			libxml_disable_entity_loader(true);
			$result = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
			return $result;
			curl_close($ch);
		}else{
			return curl_errno($ch);
			curl_close($ch);
		}
	}
	
	public function getOrderquery(){
		$xml = $this->buildXml();
		
		$ch = curl_init();
		
		curl_setopt($ch,CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/orderquery");
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/libs/ssl/cacert.pem");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$data = curl_exec($ch);

		if($data){
			libxml_disable_entity_loader(true);
			$result = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
			return $result;
			curl_close($ch);
		}else{
			return curl_errno($ch);
			curl_close($ch);
		}
	}
	
	public function getWechatUrl($mweb, $out_trade_no){
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $mweb);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_REFERER, $_SERVER['SERVER_NAME']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
		
		$result = curl_exec($ch);
		
		preg_match('/weixin:(.*)/', $result, $match);

		$out = array(
			"url" => rtrim($match[0], '"'),
			"out_trade_no" => $out_trade_no
		);
		
		return $out;
	}
}

?>
