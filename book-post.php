<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Izinkan permintaan dari semua sumber lintas asal
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Izinkan metode GET, POST, PUT, DELETE
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Izinkan header Content-Type dan Authorization

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "method tidak sesuai"]);
    exit();
}

// START PROSES VALIDASI REQUEST
if (!isset($_POST['title']) || empty($_POST['title'])) {
    http_response_code(400);
    echo json_encode(["error" => "request title wajib dikirim"]);
    exit();
}
if (!isset($_POST['author']) || empty($_POST['author'])) {
    http_response_code(400);
    echo json_encode(["error" => "request author wajib dikirim"]);
    exit();
}
if (!isset($_POST['year']) || empty($_POST['year'])) {
    http_response_code(400);
    echo json_encode(["error" => "request year wajib dikirim"]);
    exit();
}
if (!isset($_POST['isComplete'])) {
http_response_code(400);
    echo json_encode(["error" => "request isComplete wajib dikirim, 
    dan harus ada isinya 1 atau 0"]);
    exit();
} else {
// cek isComplete value harus 1 or 0
if (($_POST['isComplete'] != 1 && $_POST['isComplete'] != 0)) {
    http_response_code(400);
    echo json_encode(["error" => "isComplete haru bernilai 1 
    atau 0"]);
    exit();
}
}


require_once __DIR__ . "/koneksi.php";

// MEMPEROLEH DATA INPUTAN
$title = htmlspecialchars($_POST['title'], true);
// MEMASTIKAN TITLE TIDAK BOLEH SAMA
$sql = "SELECT * FROM book WHERE title = '$title' LIMIT 1";
$query = mysqli_query($conn, $sql);
$cekTitle = mysqli_fetch_assoc($query);

if ($cekTitle) {
    http_response_code(400);
    echo json_encode([
    "error" => "Data $title sudah ada."
    ]);
    exit();
}

// MEMPEROLEH DATA INPUTAN LAINNYA
$author = $_POST['author'];
$year = $_POST['year'];
$isComplete = $_POST['isComplete'];
// MEMASUKKAN DATA KE DALAM DATABASE
$sql = "INSERT INTO book (id, title, year, author, isComplete)
VALUES(NULL, '$title', '$year', '$author', '$isComplete')";
$query = mysqli_query($conn, $sql);

if ($query) {
    // INSERT DATA BERHASIL
    // MENDAPATKAN ID YANG BARU SAJA DI-INSERT
    $insertedId = mysqli_insert_id($conn);
    // MENGAMBIL DATA YANG BARU SAJA DI-CREATE
    $sql = "SELECT * FROM book WHERE id = $insertedId";
    $query = mysqli_query($conn, $sql);
    $result = mysqli_fetch_assoc($query);
    // MENGIRIMKAN RESPONSE BERHASIL
    http_response_code(200);
    echo json_encode([
        "data" => $result
        ]);
    exit();
}

// MENGIRIMKAN RESPONSE GAGAL
http_response_code(400);
echo json_encode([
"error" => "Gagal memasukkan data ke dalam database."
]);
exit();

?>