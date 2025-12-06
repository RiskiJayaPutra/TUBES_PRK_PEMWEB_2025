<?php
// ============================================
// UPDATE STATUS - update_status.php
// ============================================
// File ini menangani pembaruan status servis dari dropdown di dashboard

// Headers untuk JSON response
header('Content-Type: application/json');

// Pastikan ini POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Ambil data dari request
$serviceId = isset($_POST['serviceId']) ? intval($_POST['serviceId']) : null;
$status = isset($_POST['status']) ? intval($_POST['status']) : null;

// Validasi input
if ($serviceId === null || $status === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
    exit;
}

// Validasi status hanya nilai 1-4
if ($status < 1 || $status > 4) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

// Status mapping
$statusMap = [
    1 => 'Diterima admin',
    2 => 'Dikerjakan oleh teknisi',
    3 => 'Selesai dikerjakan',
    4 => 'Barang sudah dapat diambil'
];

// TODO: Koneksi ke database dan simpan status
// Contoh koneksi database (uncomment dan sesuaikan dengan setup database Anda):
/*
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fixtrack', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('UPDATE services SET status = :status, updated_at = NOW() WHERE id = :serviceId');
    $stmt->execute([
        ':status' => $status,
        ':serviceId' => $serviceId
    ]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'serviceId' => $serviceId,
            'status' => $status,
            'statusName' => $statusMap[$status]
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Service tidak ditemukan']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
*/

// Untuk saat ini, kembalikan response sukses (placeholder)
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Status berhasil diperbarui',
    'serviceId' => $serviceId,
    'status' => $status,
    'statusName' => $statusMap[$status]
]);
?>
