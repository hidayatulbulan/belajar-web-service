<?php
header("Content-Type: application/json");
require "data-dosen.php";

$method = $_SERVER['REQUEST_METHOD'];

function validasiInputDosen($input) {
    $wajib = ['nidn', 'nama', 'fakultas'];
    $kosong = [];
    foreach ($wajib as $field) {
        if (empty($input[$field])) {
            $kosong[] = $field;
        }
    }
    return $kosong;
}

if ($method === "GET") {
    http_response_code(200);
    echo json_encode($dosen);
    exit;
}

if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true) ?? [];
    $fieldKosong = validasiInputDosen($input);

    if (!empty($fieldKosong)) {
        http_response_code(400);
        echo json_encode([
            "error" => "Field berikut wajib diisi: " . implode(", ", $fieldKosong)
        ]);
        exit;
    }

    $idBaru = count($dosen) + 1;
    $dosenBaru = [
        "id" => $idBaru,
        "nidn" => $input['nidn'],
        "nama" => $input['nama'],
        "fakultas" => $input['fakultas'],
    ];

    http_response_code(201);
    header("Location: /dosen.php?id=$idBaru");
    echo json_encode($dosenBaru);
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method $method tidak didukung di endpoint ini"]);