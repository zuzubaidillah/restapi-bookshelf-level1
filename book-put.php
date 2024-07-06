<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "method tidak sesuai"]);
    exit();
}

require_once __DIR__ . "/koneksi.php";

$id = htmlspecialchars($_POST['id'], true);
$sql = "SELECT * FROM book WHERE id='$id' limit 1";
$query = mysqli_query($conn, $sql);
$cekId = mysqli_fetch_all($query, MYSQLI_ASSOC);

// jika data id tidak ditemukan
if (!sizeof($cekId)) {
    http_response_code(400);
    echo json_encode([
    "error" => "Data [ $id ] Tidak ditemukan."
    ]);
    exit();
}

// validasi title tidak boleh sama
$title = htmlspecialchars($_POST['title'], true);
$sql = "SELECT * FROM book WHERE title = '$title' AND id != '$id'";
$query = mysqli_query($conn, $sql);
$cekTitle = mysqli_fetch_all($query, MYSQLI_ASSOC);

if (sizeof($cekTitle)) {
    http_response_code(400);
    echo json_encode([
        "error" => "Data $title sudah ada."
    ]);
    exit();
}

// proses update data
$author = $_POST['author'];
$year = $_POST['year'];
$isComplete = $_POST['isComplete'];
$sql = "UPDATE book SET title='$title', year='$year', 
author='$author', isComplete='$isComplete' WHERE id='$id'";
$query = mysqli_query($conn, $sql);

if ($query) {
    // Mengambil data yang baru saja di-create
    $sql = "SELECT * FROM book WHERE id = '$id'";
    $query = mysqli_query($conn, $sql);
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    $response = ["data" => []];

    foreach ($result as $row) {
        $response = [
            "data" => $row
        ];
    }

    http_response_code(200);
    echo json_encode($response);
    exit();
}

http_response_code(400);
echo json_encode([
    "error" => "Gagal memasukkan data ke dalam database"
]);
exit();