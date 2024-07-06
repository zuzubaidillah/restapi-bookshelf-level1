<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(["error" => "Method tidak sesuai"]);
    exit();
}

require_once __DIR__ . "/koneksi.php";

$sql = "SELECT * FROM category ORDER BY id DESC";
$query = mysqli_query($conn, $sql);

if ($query) {
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    $response = array(
    "data" => $result,
    );
    http_response_code(200);
    echo json_encode($response);
    exit();
}

// ketika query gagal
http_response_code(500);
echo json_encode(array("error" => "Gagal mengambil data dari database"));
exit();

?>