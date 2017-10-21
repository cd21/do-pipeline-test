<?php

$targetSummonerName = "Cichori";

$config = json_decode(file_get_contents('config.json'), true);
$summoner = apiCall('/lol/summoner/v3/summoners/by-name/'.$targetSummonerName);
$matchList = apiCall('/lol/match/v3/matchlists/by-account/'.$summoner['accountId'].'/?queue=420&queue=440&endIndex=5');

foreach ($matchList['matches'] as $match)
{    
    echo '<hr>match'.$match['gameId'];

    $matchParticipantData = array();
    $matchData = apiCall('/lol/match/v3/matches/'.$match['gameId']);
    $matchTotals = getMatchTotals($matchData);

    foreach ($matchData['participants'] as $participant)
    {
        $participantData = getPlayerData($matchData, $participant['participantId']);      
        if ($participantData['summonerName'] == $targetSummonerName) /* this stuff filters */
        {  
            $dataParticipant['summonerName'] = $participantData['summonerName'];

            foreach ($config['recordStats'] as $statName)
            {
                $dataParticipant[$statName] = $participant['stats'][$statName];
            }

            $matchParticipantData[$participant['teamId']][] = $dataParticipant;
        }
    }

    echo '<hr><hr>';
    var_dump($matchTotals);    
    var_dump($matchParticipantData);    
}


function getPlayerData($matchData, $participantId)
{
    foreach ($matchData['participantIdentities'] as $participantIdentity)
    {
        if ($participantIdentity['participantId'] == $participantId)
        {
            return $participantIdentity['player'];
        }
    }

    return false;
}

function getMatchTotals($matchData)
{
    global $config;     

    $totals = array();

    foreach ($matchData['participants'] as $participant)
    {
        foreach ($config['recordStats'] as $statName)
        {
            if (empty($totals[$participant['teamId']][$statName]))
            {
                $totals[$participant['teamId']][$statName] = 0;
            }
            
            $totals[$participant['teamId']][$statName] += $participant['stats'][$statName];
        }
    }

    return $totals;
}

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
        return json_decode($response, true);
    }
}
echo "wass";