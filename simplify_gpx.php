<?php

$file = $argv[1] ?? 'data/lelfile.gpx';

$everyNSeconds = 10;

$dom=new DOMDocument();
$dom->load($file, LIBXML_PARSEHUGE) or die("Failed to load");
$xmldata = $dom->documentElement;

if (sizeof($xmldata->getElementsByTagName('trk')) === 0) {
    die("No tracks in this file");
}

$nodesToDelete=array();

$allEmpty = true;
$tracks = $xmldata->getElementsByTagName('trk');

$pointsToDelete = [];
// echo sizeof($array) . "\n";

foreach($tracks as $trk) {
    $trkSegments = $trk->getElementsByTagName('trkseg');
    $allEmpty &= sizeof($trkSegments) === 0;
    if ($allEmpty) {
        continue;
    }
    foreach($trkSegments as $j => $segment) {
        $points = $segment->getElementsByTagName('trkpt');
        $allEmpty &= sizeof($points) === 0;
        if ($allEmpty) {
            continue;
        }

        echo sizeof($points) . " points in track\n";

        $i = 0;
        foreach ($points as $trkpt) {
           if ($i++ % 10 === 0) {
            continue;
           }
           $pointsToDelete[] = $trkpt;
           echo "\r" . sizeof($pointsToDelete) . " points to delete";
        }
        echo PHP_EOL;
    }
}

echo "\n";

if ($allEmpty) {
    die("No track segments / points in a single track");
}
