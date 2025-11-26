<?php
header("Content-Type: application/json");

// Bericht uitlezen
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["message"])) {
    echo json_encode(["response" => "Geen bericht ontvangen."]);
    exit;
}

// Simpele antwoord generator (werkt altijd)
$userMessage = $data["message"];
$botResponse = "Je zei: " . $userMessage;

echo json_encode(["response" => $botResponse]);
