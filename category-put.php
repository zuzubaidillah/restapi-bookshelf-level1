<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "method tidak sesuai"]);
    exit();
}

require_once __DIR__ . "/koneksi.php";

$category_id = htmlspecialchars($_POST['category_id'], true);
$sql = "SELECT * FROM category WHERE category_id='$category_id' limit 1";
$query = mysqli_query($conn, $sql);
$cekcategory_id = mysqli_fetch_all($query, MYSQLI_ASSOC);

// jika data id tidak ditemukan
if (!sizeof($cekcategory_id)) {
    http_response_code(400);
    echo json_encode([
    "error" => "Data [ $category_id ] Tidak ditemukan."
    ]);
    exit();
}

// validasi title tidak boleh sama
$category_name = htmlspecialchars($_POST['category_name'], true);
$sql = "SELECT * FROM category WHERE category_name = '$category_name' AND category_id != '$category_id'";
$query = mysqli_query($conn, $sql);
$cekcategory_name = mysqli_fetch_all($query, MYSQLI_ASSOC);

if (sizeof($cekcategory_name)) {
    http_response_code(400);
    echo json_encode([
        "error" => "Data $category_name sudah ada."
    ]);
    exit();
}

// proses update data
$sql = "UPDATE category SET category_name='$category_name' WHERE category_id='$category_id'";
$query = mysqli_query($conn, $sql);

if ($query) {
    // Mengambil data yang baru saja di-create
    $sql = "SELECT * FROM category WHERE category_id = '$category_id'";
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