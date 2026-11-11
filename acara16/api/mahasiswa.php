<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

function respond($status, array $body)
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT);
    exit;
}

function readJsonBody()
{
    $rawBody = file_get_contents('php://input');
    $data = json_decode($rawBody);

    if (json_last_error() !== JSON_ERROR_NONE || !is_object($data)) {
        respond(400, [
            'success' => false,
            'message' => 'Body harus berupa JSON object yang valid.'
        ]);
    }

    return get_object_vars($data);
}

function validateStudentData(array $data)
{
    foreach (['nim', 'nama', 'email'] as $field) {
        if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '') {
            return 'Data tidak lengkap. NIM, nama, dan email wajib diisi.';
        }
    }

    if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
        return 'Format email tidak valid.';
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
    header('Allow: GET, POST, PUT, DELETE, OPTIONS');
    respond(405, [
        'success' => false,
        'message' => 'Method HTTP tidak diizinkan.'
    ]);
}

$pathInfo = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/') : '';
$pathId = $pathInfo !== '' ? $pathInfo : null;
$requestId = $pathId !== null ? $pathId : (isset($_GET['id']) ? $_GET['id'] : null);

if ($pathId !== null && !preg_match('/^[1-9][0-9]*$/', $pathId)) {
    respond(400, [
        'success' => false,
        'message' => 'ID mahasiswa harus berupa bilangan bulat positif.'
    ]);
}

if (in_array($method, ['PUT', 'DELETE'], true) && $requestId === null) {
    respond(400, [
        'success' => false,
        'message' => 'ID mahasiswa wajib disertakan pada URL, contoh: /api/mahasiswa/1.'
    ]);
}

if (
    $requestId !== null
    && (!is_scalar($requestId) || !preg_match('/^[1-9][0-9]*$/', (string) $requestId))
) {
    respond(400, [
        'success' => false,
        'message' => 'ID mahasiswa harus berupa bilangan bulat positif.'
    ]);
}

$data = null;
if (in_array($method, ['POST', 'PUT'], true)) {
    $data = readJsonBody();
    $validationError = validateStudentData($data);

    if ($validationError !== null) {
        respond(400, [
            'success' => false,
            'message' => $validationError
        ]);
    }

    $data['nim'] = trim($data['nim']);
    $data['nama'] = trim($data['nama']);
    $data['email'] = trim($data['email']);
}

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    switch ($method) {
        case 'GET':
            if ($requestId !== null) {
                $stmt = $db->prepare('SELECT * FROM mahasiswa WHERE id = :id');
                $stmt->execute([':id' => $requestId]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$student) {
                    respond(404, [
                        'success' => false,
                        'message' => 'Data mahasiswa tidak ditemukan.',
                        'data' => null
                    ]);
                }

                respond(200, [
                    'success' => true,
                    'message' => 'Detail data mahasiswa berhasil diambil.',
                    'data' => $student
                ]);
            }

            if (isset($_GET['nim']) && trim((string) $_GET['nim']) !== '') {
                $stmt = $db->prepare('SELECT * FROM mahasiswa WHERE nim = :nim');
                $stmt->execute([':nim' => trim((string) $_GET['nim'])]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$student) {
                    respond(404, [
                        'success' => false,
                        'message' => 'Data mahasiswa tidak ditemukan.',
                        'data' => null
                    ]);
                }

                respond(200, [
                    'success' => true,
                    'message' => 'Detail data mahasiswa berhasil diambil.',
                    'data' => $student
                ]);
            }

            $stmt = $db->query('SELECT * FROM mahasiswa ORDER BY nim ASC');
            respond(200, [
                'success' => true,
                'message' => 'Data berhasil diambil.',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);

        case 'POST':
            $stmt = $db->prepare(
                'INSERT INTO mahasiswa
                    (nim, nama, jenis_kelamin, alamat, email, id_prodi, angkatan, status)
                 VALUES
                    (:nim, :nama, :jenis_kelamin, :alamat, :email, :id_prodi, :angkatan, :status)'
            );
            $stmt->execute([
                ':nim' => $data['nim'],
                ':nama' => $data['nama'],
                ':jenis_kelamin' => $data['jenis_kelamin'] ?? 'P',
                ':alamat' => $data['alamat'] ?? 'Nganjuk',
                ':email' => $data['email'],
                ':id_prodi' => $data['id_prodi'] ?? 1,
                ':angkatan' => $data['angkatan'] ?? date('Y'),
                ':status' => $data['status'] ?? 'aktif'
            ]);

            $data['id'] = (int) $db->lastInsertId();
            respond(201, [
                'success' => true,
                'message' => 'Data mahasiswa berhasil ditambahkan.',
                'data' => [
                    'id' => $data['id'],
                    'nim' => $data['nim'],
                    'nama' => $data['nama'],
                    'email' => $data['email']
                ]
            ]);

        case 'PUT':
            $findStmt = $db->prepare('SELECT * FROM mahasiswa WHERE id = :id');
            $findStmt->execute([':id' => $requestId]);
            $existing = $findStmt->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                respond(404, [
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan.',
                    'data' => null
                ]);
            }

            $stmt = $db->prepare(
                'UPDATE mahasiswa
                 SET nim = :nim, nama = :nama, email = :email,
                     jenis_kelamin = :jenis_kelamin, alamat = :alamat,
                     id_prodi = :id_prodi, angkatan = :angkatan, status = :status
                 WHERE id = :id'
            );
            $stmt->execute([
                ':nim' => $data['nim'],
                ':nama' => $data['nama'],
                ':email' => $data['email'],
                ':jenis_kelamin' => $data['jenis_kelamin'] ?? $existing['jenis_kelamin'],
                ':alamat' => $data['alamat'] ?? $existing['alamat'],
                ':id_prodi' => $data['id_prodi'] ?? $existing['id_prodi'],
                ':angkatan' => $data['angkatan'] ?? $existing['angkatan'],
                ':status' => $data['status'] ?? $existing['status'],
                ':id' => $requestId
            ]);

            respond(200, [
                'success' => true,
                'message' => 'Data mahasiswa berhasil diubah.',
                'data' => [
                    'id' => (int) $requestId,
                    'nim' => $data['nim'],
                    'nama' => $data['nama'],
                    'email' => $data['email']
                ]
            ]);

        case 'DELETE':
            $findStmt = $db->prepare('SELECT id FROM mahasiswa WHERE id = :id');
            $findStmt->execute([':id' => $requestId]);

            if (!$findStmt->fetch(PDO::FETCH_ASSOC)) {
                respond(404, [
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan.',
                    'data' => null
                ]);
            }

            $stmt = $db->prepare('DELETE FROM mahasiswa WHERE id = :id');
            $stmt->execute([':id' => $requestId]);

            respond(200, [
                'success' => true,
                'message' => 'Data mahasiswa berhasil dihapus.'
            ]);
    }
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        respond(400, [
            'success' => false,
            'message' => 'NIM atau email sudah terdaftar.'
        ]);
    }

    respond(500, [
        'success' => false,
        'message' => 'Terjadi kesalahan pada server saat memproses data.'
    ]);
}
