<?php
$api_key = 'ZNx40cKnYC1BHjY81RbIUrrOYHp81jDqwhxac4WlJSWvTTPt4mvJaDz';
$target_number = '6282287255183';
$message = "⚠️ Peringatan: Tingkat kebisingan melebihi ambang batas yang ditentukan! Harap segera cek sistem Anda.";

$url = 'https://console.wablas.com/api/send-message';
$data = [
    "phone" => $target_number,
    "message" => $message,
    "secret" => false
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: $api_key"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(["success" => false, "message" => "cURL Error: $err"]);
} else {
    echo $response;
}
