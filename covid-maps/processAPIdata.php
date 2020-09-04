<?php

// It's not possible to query the Covid Tracking Project API over a date range
// and the total US state daily dataset has become too large,
// so let's do some preprocessing via cron and PHP.

// some boilerplate CURLing
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.covidtracking.com/v1/states/daily.json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

$dailyData = json_decode($output);

// cut off data before 35 days ago - 4 weeks plus 1 more for safety
$cutoffDate = mktime(0, 0, 0, date("m")  , date("d")-35, date("Y"));
$cutoffDate = date("Ymd", $cutoffDate);

// build a new data object
$newDataset = array();
foreach ($dailyData as $thisData) {
	if (strval($thisData->date) == $cutoffDate) break;
	$newDataset[] = $thisData;
}

// write new dataset to this folder - edit destination as necessary
$newDataset = json_encode($newDataset);
file_put_contents("/home/mbarklage/mikebarklage.com/codesamples/covid-maps/daily.json", $newDataset);

