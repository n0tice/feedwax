<?php

if (!empty($_GET['spreadsheet_url'])) {
	$spreadsheet_url = urldecode($_GET['spreadsheet_url']);
	$query = parse_url($spreadsheet_url, PHP_URL_QUERY);
	parse_str($query, $output);
	$spreadsheet_key = $output['key'];
} elseif (!empty($_GET['spreadsheet_key'])) {
	$spreadsheet_key = $_GET['spreadsheet_key'];
}

// Set your CSV feed
$feed = 'https://docs.google.com/spreadsheet/pub?key='.$spreadsheet_key.'&oe=utf-8&single=true&output=csv';

// Arrays we'll use later
$keys = array();
$newArray = array();
 
function csvToArray($file, $delimiter) { 
  if (($handle = fopen($file, 'r')) !== FALSE) { 
    $i = 0; 
    while (($lineArray = fgetcsv($handle, 6000, $delimiter, '"')) !== FALSE) { 
      for ($j = 0; $j < count($lineArray); $j++) { 
        $arr[$i][$j] = $lineArray[$j]; 
      } 
      $i++; 
    } 
    fclose($handle); 
  } 
  return $arr; 
} 

$data = csvToArray($feed, ',');
$count = count($data) - 1;
$labeldata = array_shift($data);  
$labels =  array_map('strtolower',$labeldata);
 
foreach ($labels as $label) {
  $keys[] = $label;
}
 
$keys[] = 'id';
 
for ($i = 0; $i < $count; $i++) {
  $data[$i][] = $i;
}
 
for ($j = 0; $j < $count; $j++) {
  $d = array_combine($keys, $data[$j]);
  $newArray[$j] = $d;
}

?>