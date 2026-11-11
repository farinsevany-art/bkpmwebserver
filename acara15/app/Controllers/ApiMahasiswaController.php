<?php
namespace App\Controllers;

use App\Models\Mahasiswa;

class ApiMahasiswaController {
    private $mahasiswaModel;

    public function __construct() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    private function getMahasiswaModel() {
        if ($this->mahasiswaModel === null) {
            $this->mahasiswaModel = new Mahasiswa();
        }

        return $this->mahasiswaModel;
    }

    public function handleRequest() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->index();
                return;
            case 'POST':
                $this->store();
                return;
            case 'OPTIONS':
                $this->preflight();
                return;
            default:
                http_response_code(405);
                header('Allow: GET, POST, OPTIONS');
                echo json_encode([
                    'success' => false,
                    'message' => 'Method tidak diizinkan'
                ], JSON_PRETTY_PRINT);
        }
    }

    public function index() {
        if (isset($_GET['id']) || isset($_GET['nim'])) {
            $data = isset($_GET['id'])
                ? $this->getMahasiswaModel()->getById($_GET['id'])
                : $this->getMahasiswaModel()->getByNim($_GET['nim']);

            if ($data) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Detail data mahasiswa berhasil diambil',
                    'data' => $data
                ], JSON_PRETTY_PRINT);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan',
                    'data' => null
                ], JSON_PRETTY_PRINT);
            }
        } else {
            $data = $this->getMahasiswaModel()->getAll();
            echo json_encode([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $data
            ], JSON_PRETTY_PRINT);
        }
    }

    public function store() {
        $inputData = json_decode(file_get_contents("php://input"), true);

        if (!is_array($inputData)) {
            $inputData = $_POST;
        }

        if (
            isset($inputData['nim'], $inputData['nama'], $inputData['email'])
            && trim((string) $inputData['nim']) !== ''
            && trim((string) $inputData['nama']) !== ''
            && trim((string) $inputData['email']) !== ''
        ) {
            $result = $this->getMahasiswaModel()->create($inputData);

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Mahasiswa berhasil ditambahkan',
                    'data' => [
                        'nim' => $inputData['nim'],
                        'nama' => $inputData['nama'],
                        'email' => $inputData['email']
                    ]
                ], JSON_PRETTY_PRINT);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menambahkan data mahasiswa'
                ], JSON_PRETTY_PRINT);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Data tidak lengkap. Harap isi nim, nama, dan email.'
            ], JSON_PRETTY_PRINT);
        }
    }

    public function preflight() {
        http_response_code(204);
    }
}
