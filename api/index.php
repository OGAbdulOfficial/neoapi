<?php
// Suppress all errors from appearing in output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API Expiry Check - Valid until April 6, 2026
$expiryDate = strtotime('2026-04-06');
$currentDate = time();

if ($currentDate > $expiryDate) {
    echo json_encode([
        "success" => false,
        "message" => "API Expired! Contact @AbdulDevStoreBot for renewal",
        "credit" => "@Rytce",
        "channel" => "https://t.me/NEOBLADE701"
    ]);
    exit;
}

// Calculate remaining days
$remainingDays = floor(($expiryDate - $currentDate) / (60 * 60 * 24));

// User API - Fetches from Owner's Vercel API
// Replace OWNER_API_URL with your Vercel deployment URL

define('OWNER_API_URL', 'https://tg2num-owner-api.vercel.app'); // Example: https://tg2num-owner-api.vercel.app

$userid = isset($_GET['userid']) ? $_GET['userid'] : null;

if (!$userid) {
    echo json_encode([
        "success" => false,
        "message" => "Please provide a Telegram User ID",
        "credit" => "@Rytce",
        "channel" => "https://t.me/NEOBLADE701",
        "api_valid_until" => "April 6, 2026",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

// Clean the user ID
$userid = preg_replace('/[^0-9]/', '', $userid);

// Fetch from Owner's API
$ownerApi = OWNER_API_URL . "?userid=" . $userid;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ownerApi);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch data",
        "credit" => "@Rytce",
        "channel" => "https://t.me/NEOBLADE701",
        "api_valid_until" => "April 6, 2026",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid response",
        "credit" => "@Rytce",
        "channel" => "https://t.me/NEOBLADE701",
        "api_valid_until" => "April 6, 2026",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

// Return clean version - ONLY with our credits
$output = [
    "success" => false,
    "version" => "1.1", // Deployment verification
    "credit" => "@Rytce",
    "channel" => "https://t.me/NEOBLADE701",
    "api_valid_until" => "April 6, 2026",
    "days_remaining" => $remainingDays
];

// Determine success from various possible keys
if (isset($data['status']) && (strcasecmp($data['status'], 'success') === 0 || strcasecmp($data['status'], 'ok') === 0)) {
    $output['success'] = true;
} elseif (isset($data['success']) && ($data['success'] === true || $data['success'] === 1 || $data['success'] === 'true' || strcasecmp($data['success'], 'success') === 0)) {
    $output['success'] = true;
}

// Add result data if exists - look for 'data' or 'result'
if (isset($data['data']) && !empty($data['data'])) {
    $output['result'] = $data['data'];
} elseif (isset($data['result']) && !empty($data['result'])) {
    $output['result'] = $data['result'];
}

// Final check: if we have a result but success is still false, set it to true
if (isset($output['result']) && $output['success'] === false) {
    $output['success'] = true;
}

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
