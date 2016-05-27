<?php

function curl($URLServer,$postdata="", $cookieFile=null, $proxy=true, $proxyRetry=0)
{
		global $proxyCache;
		//sleep(20);
		$agent = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.12) Gecko/20101027 Ubuntu/10.10 (maverick) Firefox/3.6.12";
		$cURL_Session = curl_init();
		curl_setopt($cURL_Session, CURLOPT_URL,$URLServer);
		curl_setopt($cURL_Session, CURLOPT_USERAGENT, $agent);
		if($postdata != "")
		{
		curl_setopt($cURL_Session, CURLOPT_POST, 1);
		curl_setopt($cURL_Session, CURLOPT_POSTFIELDS,$postdata);
		}
		curl_setopt($cURL_Session, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($cURL_Session, CURLOPT_FOLLOWLOCATION, 1);
		if($cookieFile != null){
		curl_setopt($cURL_Session,CURLOPT_COOKIEJAR, $cookieFile);
		curl_setopt($cURL_Session,CURLOPT_COOKIEFILE, $cookieFile);  
		}
		
		if($proxy == true)
		{
			if($proxyCache == "")
			{
			$c = curl("http://www.proxylist.net/", "", null, false);
			preg_match_all("/([0-9]*).([0-9]*).([0-9]*).([0-9]*):([0-9]*)/", $c, $matches);
			$matches = $matches[0];
			$proxyCache = $matches[rand(0, (count($matches)-1))];
			}
			
			echo "proxy:$proxyCache<br>";
			
						
			list($proxy_ip, $proxy_port) = explode(":", $proxyCache);

			curl_setopt($cURL_Session, CURLOPT_PROXYPORT, $proxy_port);
			curl_setopt($cURL_Session, CURLOPT_PROXYTYPE, 'HTTP');
			curl_setopt($cURL_Session, CURLOPT_PROXY, $proxy_ip);		
		}
		
		$result = curl_exec ($cURL_Session);
		
		if($result === false)
		{
    	echo 'Curl error: ' . curl_error($cURL_Session)."<br>";
    	
    	if($proxy == true && $proxyRetry <= 5)
    		curl($URLServer,$postdata="", $cookieFile, $proxy, $proxyRetry++);
		}
		curl_close ($cURL_Session);
		 
		return $result;
}


echo curl("http://www.ricg.com");
