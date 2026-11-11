<?php
class BukuController {
    private Buku $model;

    public function __construct() {
        $this->model = new Buku();
    }

    public function index(): void {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $data = $this->model->find((int) $id);
            if (!$data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Data buku tidak ditemukan']);
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
        
        if (empty($data['judul']) || empty($data['pengarang']) || empty($data['penerbit']) || empty($data['tahun_terbit'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field judul, pengarang, penerbit, dan tahun_terbit wajib diisi']);
            exit;
        }
        
        $id = $this->model->create($data);
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Data buku berhasil ditambahkan',
            'data' => ['id' => $id]
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
            echo json_encode(['success' => false, 'message' => 'Data buku tidak ditemukan untuk diperbarui']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $payload = [
            'judul' => $data['judul'] ?? $existing['judul'],
            'pengarang' => $data['pengarang'] ?? $existing['pengarang'],
            'penerbit' => $data['penerbit'] ?? $existing['penerbit'],
            'tahun_terbit' => $data['tahun_terbit'] ?? $existing['tahun_terbit']
        ];

        $this->model->update((int) $id, $payload);
        echo json_encode(['success' => true, 'message' => 'Data buku berhasil diperbarui']);
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
            echo json_encode(['success' => false, 'message' => 'Data buku tidak ditemukan untuk dihapus']);
            exit;
        }

        $this->model->delete((int) $id);
        echo json_encode(['success' => true, 'message' => 'Data buku berhasil dihapus']);
    }
}
?>