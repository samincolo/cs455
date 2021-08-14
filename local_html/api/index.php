<?php
require('../config.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$requestType = $_SERVER['REQUEST_METHOD'];

$db= new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);
$ip = getUserIP();
error_log(json_encode([$ip, $_SERVER['QUERY_STRING']]).PHP_EOL, 3, "queries.log");

if($requestType == "GET"){
	if($_GET['url'] == 'getJobs'){
		$city = $_GET['city'];
		$stmt = $db->prepare("SELECT id FROM cities WHERE cityName = ?");
		$stmt->bind_param("s", $city);
		$stmt->execute();
		$cityId = $stmt->get_result()->fetch_assoc()['id'];
		$stmt->close();

		$keywords = explode(",", $_GET['keywords']);
		$ids = [];
		$stmt = $db->prepare("SELECT id FROM keywords WHERE name = ?");
		$stmt->bind_param("s", $key);
		foreach($keywords as $key){
			$stmt->execute();
			$result = $stmt->get_result()->fetch_assoc();
			array_push($ids,$result['id']);
		}
		$stmt->close();
		$jsonRes = [];

		findCoorelations($ids, $cityId, $db, $jsonRes);

		$intersection = [];
		$strict = false;
		if($_GET['strict'] == "true"){
			$strict = true;
		}
		foreach($ids as $kword){
			$keyArray = [];
			$jobIds = $db->query("SELECT jobId FROM job_keyword WHERE cityId = $cityId AND keywordId = $kword");
			while($getKey = $jobIds->fetch_assoc()){
				array_push($keyArray, $getKey['jobId']);
			}
			if(count($intersection) == 0){
				$intersection = $keyArray;
			}
			else{
				if($strict){
					$intersection = array_intersect($intersection, $keyArray);
				}
				else{
					$intersection = array_unique(array_merge($intersection, $keyArray));
				}
			}
		}

		$jsonResponse = "[[";
		$jsonResponse2 = array();
		$jobs = array();
		foreach($intersection as $jobSearch){
		    $jsonResponse .= "{";
			$jobInfo = mysqli_fetch_assoc($db->query("SELECT * FROM jobs WHERE id = $jobSearch"));
			$jsonResponse .= '"title": "'.$jobInfo['jobTitle'].'","company": "'.$jobInfo['company'].'", "url": "'.$jobInfo['url'].'", "lat": "'.$jobInfo['lat'].'", "lng": "'.$jobInfo['lng'].'", "id": "'.$jobInfo['id'].'"},';
            $job = array("title" => $jobInfo['jobTitle'], "company" => $jobInfo['company'], "url" => $jobInfo['url'], "lat" => $jobInfo['lat'], "lng" => $jobInfo['lng'], "id" => $jobInfo['id']);
            array_push($jobs, $job);
		}
		$jsonResponse = rtrim($jsonResponse, ", ");
		$jsonResponse .= "],".json_encode($jsonRes)."]";
		array_push($jsonResponse2, $jobs, $jsonRes);
		echo json_encode($jsonResponse2);
		echo $jsonResponse;


	}

	if($_GET['url'] == 'getJobDesc'){
		$stmt = $db->prepare("SELECT description FROM jobs_descriptions WHERE id = ?");
		$stmt->bind_param("i", $_GET['id']);
		$stmt->execute();
		$jsonResponse = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		echo json_encode($jsonResponse);
	}

}

function findCoorelations($ids, $cityid, $db, &$return){
	$keywords = array();
	idsAndNames($ids, $keywords);

	$counter = 0;
	foreach($ids as $id){
		$result = returnCorrelated($db, $id, $cityid);
		$avgArray = [];
		$return[$keywords[$counter]] = array();
		while($find = $result->fetch_assoc()){
			$kname = mysqli_fetch_assoc($db->query("SELECT name FROM keywords WHERE id = $find[coorelatedId]"))['name'];
			if(isset($avgArray[$kname])){
				array_push($avgArray[$kname], $find['coorelationValue']);
			}
			else{
				$avgArray[$kname] = [$find['coorelationValue']];
			}
			
		}
		foreach ($avgArray as $key => $value) {
			$temp = [$key, (array_sum($avgArray[$key]) / count($avgArray[$key]) ) ];
			array_push($return[$keywords[$counter]], $temp);
		}
		$counter += 1;
	}
	


}

function returnCorrelated($db, $id, $cityid){
	$result = $db->query("SELECT coorelatedId, coorelationValue FROM correlations WHERE keywordsSum = $id and cityId = $cityid AND coorelationValue < 1 ORDER BY coorelationValue DESC LIMIT 3");
	if(mysqli_num_rows($result) == 0){
		$result = $db->query("SELECT coorelatedId, coorelationValue FROM correlations WHERE keywordsSum = $id  AND coorelationValue < 1 ORDER BY coorelationValue DESC LIMIT 3");
	}
	if(mysqli_num_rows($result) == 0){
		$result = $db->query("SELECT coorelatedId, coorelationValue FROM correlations WHERE keywordsSum = $id  AND coorelationValue ORDER BY coorelationValue DESC LIMIT 3");
	}
	return $result;
}

function permute($arr, $temp_string, &$permutedIds) {
    if ($temp_string != "") 
        $permutedIds []= $temp_string;

    for ($i=0, $iMax = sizeof($arr); $i < $iMax; $i++) {
        $arrcopy = $arr;
        $elem = array_splice($arrcopy, $i, 1);
        if (sizeof($arrcopy) > 0) {
            permute($arrcopy, (int)$temp_string + $elem[0], $permutedIds);
        } else {
            $permutedIds []= $temp_string + $elem[0];
        }   
    }   
}

function idsAndNames(&$keyIds, &$keyNames){

    $keywordNameToIds = json_decode('{"expressjs":"4398046511104","tensorflow":"2199023255552","kotlin":"1099511627776","bash":"549755813888","docker":"274877906944","spark":"137438953472","hadoop":"68719476736","azure":"34359738368","aws":"17179869184","perl":"8589934592","laravel":"4294967296","vba":"2147483648","linux":"1073741824","django":"536870912","swift":"268435456","ruby":"134217728","asp.net":"67108864","node.js":"33554432","r":"16777216","ios":"8388608","mysql":"4194304","css":"2097152","jquery":"1048576","html":"524288","android":"262144","lisp":"131072","mongo":"65536","nosql":"32768","clojure":"16384","haskell":"8192","scala":"4096","rust":"2048","c#":"1024","c++":"512","c":"256","golang":"128","python":"64","php":"32","java":"16","vue":"8","reactjs":"4","angular":"2","javascript":"1"}', true);


    if(count($keyIds) > 1 && count($keyIds) < 5){

        $permutedIds = array();
        permute($keyIds, "", $permutedIds);

        $keyIds = array_unique($permutedIds);
    }
    else if(count($keyIds) >= 5){
        array_push($keyIds, array_sum($keyIds));
    }

    foreach($keyIds as $entry){
        $entrycopy = $entry;
        $keywords = "";
        foreach ($keywordNameToIds as $key => $value) {
            if($entrycopy - $value >= 0){
                $entrycopy -= $value;
                $keywords .= $key.'&';
            }
        }
        $keywords = rtrim($keywords, "&");
        array_push($keyNames, $keywords);
    }
}



function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

