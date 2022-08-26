<?php
$startTime = '07 aug 2022 06:30:00';
$minStopTime = 60 * 10;

$lines = file('data/lelride.csv');
$headers = [];
$sleepIntervals = [];
$intervalCounter = 0;
$previousValues = [];
foreach ($lines as $ln => $line) {
    $parsed = str_getcsv($line);
    if ($ln === 0) {
        $headers = $parsed;
        continue;
    }

    $values = array_combine($headers, $parsed);
    
    if (!empty($previousValues)) { 
        if (($values['secs'] - 1) != $previousValues['secs']) {
            $sleepIntervals[$intervalCounter++] = [
                'length' => $values['secs'] - $previousValues['secs'],
                'startTime' => date('d/m/Y H:i:s', strtotime($startTime . '+' . $previousValues['secs'] . ' seconds')) ,
                'finishTime' => date('d/m/Y H:i:s', strtotime($startTime . '+' . $values['secs'] . ' seconds')) ,
                'lat' => $values['lat'],
                'lon' => $values['lon'],
            ];
        }
    }

    $previousValues = $values;
}

$filteredIntervals = array_filter($sleepIntervals, fn($val) => $val['length'] >= $minStopTime);

function toHMS($seconds) {
    $h = floor($seconds / 60 / 60);
    $m = floor(($seconds - $h*60*60) / 60);
    $m = $m < 10 ? '0' . $m : $m;
    $s = $seconds - $m*60 - $h*60*60;
    $s = $s < 10 ? '0' . $s : $s;
    $result = $m . 'm' . $s . 's';
    $result = $h > 0 ? $h . "h" . $result : $result;
    return $result;
}


$sep = '  |  ';
$dateLength = strlen($startTime) - 1;
$header = " " . join($sep, [str_pad('arrived', $dateLength), str_pad('left', $dateLength), str_pad('spend', 8), str_pad('latitude', 11), str_pad('longitude', 11)]) . "\n";
$header .= str_repeat('-', strlen($header)) . "\n";
echo $header;
foreach ($filteredIntervals as $interval) {
    echo " " . join($sep, [$interval['startTime'], $interval['finishTime'], str_pad(toHMS($interval['length']), 8, " ", STR_PAD_LEFT), $interval['lat'], $interval['lon']]) . "\n";
}
