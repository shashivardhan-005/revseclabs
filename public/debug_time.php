<?php
// V2 - Standalone
echo '<pre>';
echo 'Default Timezone (php.ini): ' . date_default_timezone_get() . "\n";
echo 'Current time() raw: ' . time() . "\n";
echo 'Current date("Y-m-d H:i:s"): ' . date('Y-m-d H:i:s') . "\n";
echo 'gmdate("Y-m-d H:i:s"): ' . gmdate('Y-m-d H:i:s') . "\n";

$rawDate = date('Y-m-d H:i:s');
$ts = strtotime($rawDate);
echo "strtotime('$rawDate'): $ts\n";
echo "Difference (time() - strtotime): " . (time() - $ts) . " (Should be close to 0)\n";

echo "\n--- Mimicking CI4 (Setting Asia/Kolkata) ---\n";
date_default_timezone_set('Asia/Kolkata');
echo 'New Timezone: ' . date_default_timezone_get() . "\n";
echo 'Current time() raw: ' . time() . "\n";
echo 'Current date("Y-m-d H:i:s"): ' . date('Y-m-d H:i:s') . "\n";

$istString = date('Y-m-d H:i:s');
$istTs = strtotime($istString);
echo "strtotime('$istString'): $istTs\n";
echo "Difference (time() - strtotime): " . (time() - $istTs) . " (Should be close to 0)\n";

echo "\n--- Checking UTC interpretation of IST string ---\n";
// This simulates if we saved IST string but read it back as UTC
date_default_timezone_set('UTC');
echo 'New Timezone: ' . date_default_timezone_get() . "\n";
$utcRead = strtotime($istString); 
echo "Reading IST string '$istString' as UTC: $utcRead\n";
echo "Original IST timestamp: $istTs\n";
echo "Difference: " . ($utcRead - $istTs) . " seconds\n";
