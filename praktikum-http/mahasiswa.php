<?php 
header("Content-Type: application/json"); 
require "data.php"; 
  
$method = $_SERVER['REQUEST_METHOD']; 
$input = json_decode(file_get_contents("php://input"), true) ?? []; 
  
if ($method === "GET") { 
    http_response_code(200); 
    echo json_encode($students); 
    exit; 
} 
  
if ($method === "POST") { 
    if (!isset($input['nim']) || !isset($input['name']) || !isset($input['major'])) { 
        http_response_code(400); 
        echo json_encode(["error" => "Field nim, name, dan major wajib diisi"]); 
        exit; 
    } 
  
    $newId = count($students) + 1; 
    $newStudent = [ 
        "id" => $newId, 
        "nim" => $input['nim'], 
        "name" => $input['name'], 
        "major" => $input['major'], 
    ]; 
  
    http_response_code(201); 
    header("Location: /mahasiswa.php?id=$newId"); 
    echo json_encode($newStudent); 
    exit; 
} 

if ($method === "PUT") {
    $id = $_GET['id'] ?? $input['id'] ?? null;

    if ($id === null || !isset($input['nim']) || !isset($input['name']) || !isset($input['major'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID, nim, name, dan major wajib diisi"]);
        exit;
    }

    $updatedStudent = null;
    foreach ($students as $index => $student) {
        if ((int)$student['id'] === (int)$id) {
            $students[$index] = [
                "id" => (int)$id,
                "nim" => $input['nim'],
                "name" => $input['name'],
                "major" => $input['major'],
            ];
            $updatedStudent = $students[$index];
            break;
        }
    }

    if ($updatedStudent === null) {
        http_response_code(404);
        echo json_encode(["error" => "Data mahasiswa tidak ditemukan"]);
        exit;
    }

    http_response_code(200);
    echo json_encode($updatedStudent);
    exit;
}

if ($method === "DELETE") {
    $id = $_GET['id'] ?? $input['id'] ?? null;

    if ($id === null) {
        http_response_code(400);
        echo json_encode(["error" => "ID mahasiswa wajib diisi"]);
        exit;
    }

    $deletedStudent = null;
    foreach ($students as $index => $student) {
        if ((int)$student['id'] === (int)$id) {
            $deletedStudent = $student;
            unset($students[$index]);
            $students = array_values($students);
            break;
        }
    }

    if ($deletedStudent === null) {
        http_response_code(404);
        echo json_encode(["error" => "Data mahasiswa tidak ditemukan"]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        "message" => "Data mahasiswa berhasil dihapus",
        "deleted" => $deletedStudent,
    ]);
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method tidak didukung"]); 