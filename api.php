<?php
header('Content-Type: application/json');
$apiKey = "HIDDEN_API_KEY";
$type = $_GET['type'] ?? 'prediction';

if ($type === 'incidents') {
    $url = "https://api.wmata.com/Incidents.svc/json/Incidents";
} elseif ($type === 'positions') {
    $url = "https://api.wmata.com/TrainPositions/TrainPositions?contentType=json";
} else {
    $stationCode = $_GET['station'] ?? 'A04';
    $url = "https://api.wmata.com/StationPrediction.svc/json/GetPrediction/{$stationCode}";
}

$opts = ["http" => ["method" => "GET", "header" => "api_key: " . $apiKey]];
echo file_get_contents($url, false, stream_context_create($opts));