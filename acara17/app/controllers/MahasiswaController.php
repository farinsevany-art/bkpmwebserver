<?php
class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index(): void {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $data = $this->model->find((int) $id);
            if (!$data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
                exit;
            }
            echo json_encode(['success' => true, 'data' => $data]);
            exit;
        }
        
        echo json_encode(['success' => true, 'data' => $this->model->getAll()]);
    }

    public function store(): void {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['nim']) || empty($data['nama']) || empty($data['prodi'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field nim, nama, dan prodi wajib diisi']);
            exit;
        }

        if ($this->model->isNimExists($data['nim'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'NIM sudah terdaftar dalam sistem (tidak boleh duplikat)']);
            exit;
        }
        
        $id = $this->model->create($data);
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Data mahasiswa berhasil ditambahkan',
            'data' => ['id' => $id, 'nim' => $data['nim'], 'nama' => $data['nama'], 'prodi' => $data['prodi']]
        ]);
    }

    public function update($id): void {
        header('Content-Type: application/json');
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parameter ID wajib disertakan']);
            exit;
        }

        $existing = $this->model->find((int) $id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan untuk diperbarui']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $nim = $data['nim'] ?? $existing['nim'];
        $nama = $data['nama'] ?? $existing['nama'];
        $prodi = $data['prodi'] ?? $existing['prodi'];

        if ($this->model->isNimExists($nim, (int) $id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'NIM sudah digunakan oleh mahasiswa lain']);
            exit;
        }

        $this->model->update((int) $id, ['nim' => $nim, 'nama' => $nama, 'prodi' => $prodi]);
        echo json_encode(['success' => true, 'message' => 'Data mahasiswa berhasil diperbarui']);
    }

    public function destroy($id): void {
        header('Content-Type: application/json');
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parameter ID wajib disertakan']);
            exit;
        }

        $existing = $this->model->find((int) $id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan untuk dihapus']);
            exit;
        }

        $this->model->delete((int) $id);
        echo json_encode(['success' => true, 'message' => 'Data mahasiswa berhasil dihapus']);
    }
}
?>