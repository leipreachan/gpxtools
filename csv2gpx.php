<?php

$csvFileName = $argv[1] ?? "data/lelride.csv";
$gpxFileName= "data/lelfile.gpx";
$startTime = '07 aug 2022 05:30:00'; // UTC
$minStopTime = 60 * 10;

$lines = file($csvFileName);
$name = 'LEL 2022';

$dateFormat = 'Y-m-d\TH:i:s\Z';


// Creating sample link object for metadata

$start = date($dateFormat, strtotime($startTime));
$header = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<gpx creator="SoloRider.cc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas
/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd" version="1.1" xmlns=
"http://www.topografix.com/GPX/1/1" xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1" xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3">
EOF;
$metaData = <<<EOF
<metadata>
<link href="http://solorider.cc/lel-2022">
  <text>$name</text>
</link>
<time>$start</time>
</metadata>

EOF;

$track = <<<EOF
<trk>
<name>$name</name>
<type>cycling</type>
<trkseg>

EOF;

file_put_contents($gpxFileName, $header . $metaData . $track);

foreach ($lines as $ln => $line) {
    $parsed = str_getcsv($line);
    if ($ln === 0) {
        $headers = $parsed;
        continue;
    }

    if ($ln % 5 == 0) {
        continue;
    }

    $values = array_combine($headers, $parsed);

    $date = date('Y-m-d\TH:i:s\Z', strtotime($startTime . '+' . $values['secs'] . ' seconds'));
    $point = <<<EOF
    <trkpt lat="${values['lat']}" lon="${values['lon']}">
    <ele>${values['alt']}</ele>
    <time>$date</time>
   </trkpt>
EOF;
    file_put_contents($gpxFileName, $point,  FILE_APPEND);
}

$data = <<<EOF
</trkseg>
</trk>
</gpx>
EOF;

file_put_contents($gpxFileName, $data,  FILE_APPEND);

echo "done";
