<?php

$config = json_decode(file_get_contents('config.json'), true);

$result = apiCall('/lol/summoner/v3/summoners/by-name/Cichori');


var_dump($config);
echo '<hr>';

var_dump($result);
echo '<hr>';

// wow
echo 'ye';




function apiCall($endpoint)
{
    global $config; 

	$curl = curl_init();

	curl_setopt_array($curl, array
	(
        CURLOPT_URL => "https://".$config['region'].".api.riotgames.com".$endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
	    "accept-charset: application/x-www-form-urlencoded; charset=UTF-8",
	    "accept-language: nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4",
	    "cache-control: no-cache",
	    "origin: https://developer.riotgames.com",	    
	    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
	    "x-riot-token: ".$config['apikey']
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);    
    if ($err) 
    {
        return "cURL Error #:" . $err;
    } 
    else 
    {
        return json_decode($response);
    }
}