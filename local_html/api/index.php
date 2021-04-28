<?php
require('../config.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$requestType = $_SERVER['REQUEST_METHOD'];

$db= new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE1);

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
		
		$intersection = [];
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
				$intersection = array_intersect($intersection, $keyArray);
			}
		}

		$jsonResponse = "[";
		foreach($intersection as $jobSearch){
			$jsonResponse .= "{";
			$jobInfo = mysqli_fetch_assoc($db->query("SELECT * FROM jobs WHERE id = $jobSearch"));
			$jsonResponse .= '"title": "'.$jobInfo['jobTitle'].'","company": "'.$jobInfo['company'].'", "url": "'.$jobInfo['url'].'", "lat": "'.$jobInfo['lat'].'", "lng": "'.$jobInfo['lng'].'"},';

		}
		$jsonResponse = rtrim($jsonResponse, ", ");
		$jsonResponse .= "]";
		echo $jsonResponse;


	}
}