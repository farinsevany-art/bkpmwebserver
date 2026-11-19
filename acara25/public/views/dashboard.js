const API_BASE_URL = new URL('../', window.location.href);
const token = localStorage.getItem('token');
const role = localStorage.getItem('role');

function kembaliKeLogin() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    localStorage.removeItem('username');
    window.location.replace('login.html');
}

if (!token || !['admin', 'user'].includes(role)) {
    kembaliKeLogin();
} else {
    const status = document.querySelector('#status');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    const dialog = document.querySelector('#dialog-mahasiswa');
    const form = document.querySelector('#form-mahasiswa');
    const isAdmin = role === 'admin';

    document.querySelector('#welcome').textContent =
        `Login sebagai ${localStorage.getItem('username') || role} (${role})`;
    document.querySelector('#btn-tambah').hidden = !isAdmin;
    document.querySelector('#kolom-aksi').hidden = !isAdmin;

    function setStatus(message, isError = false) {
        status.textContent = message;
        status.dataset.error = String(isError);
    }

    async function readResponse(response) {
        let result;
        try {
            result = await response.json();
        } catch (error) {
            if (error instanceof SyntaxError) {
                throw new Error(
                    `Server mengirim response bukan JSON (HTTP ${response.status}). Periksa log PHP/server.`
                );
            }
            throw error;
        }
        if (response.status === 401) {
            throw new Error(
                `API menolak token (401): ${result.message || 'silakan login kembali.'}`
            );
        }
        if (!response.ok || !result.success) {
            throw new Error(result.message || `Request gagal (HTTP ${response.status})`);
        }
        return result;
    }

    function addCell(row, value) {
        const cell = document.createElement('td');
        cell.textContent = value == null || value === '' ? '-' : String(value);
        row.appendChild(cell);
    }

    function openForm(mahasiswa = null) {
        form.reset();
        document.querySelector('#mahasiswa-nim-lama').value = mahasiswa?.nim ?? '';
        document.querySelector('#judul-form').textContent =
            mahasiswa ? 'Ubah Mahasiswa' : 'Tambah Mahasiswa';
        if (mahasiswa) {
            form.elements.nim.value = mahasiswa.nim ?? '';
            form.elements.nama.value = mahasiswa.nama ?? '';
            form.elements.jenis_kelamin.value = mahasiswa.jenis_kelamin ?? 'L';
            form.elements.alamat.value = mahasiswa.alamat ?? '';
            form.elements.email.value = mahasiswa.email ?? '';
            form.elements.prodi.value = mahasiswa.prodi ?? mahasiswa.id_prodi ?? '';
            form.elements.angkatan.value = mahasiswa.angkatan ?? '';
            form.elements.status.value = mahasiswa.status ?? 'aktif';
            form.elements.foto.value = mahasiswa.foto ?? '';
        }
        dialog.showModal();
    }

    async function hapusMahasiswa(nim) {
        if (!nim) {
            setStatus('NIM mahasiswa tidak tersedia.', true);
            return;
        }
        if (!window.confirm('Yakin ingin menghapus data mahasiswa ini?')) return;

        try {
            const response = await fetch(new URL(`mahasiswa/${encodeURIComponent(nim)}`, API_BASE_URL), {
                method: 'DELETE',
                headers: { Authorization: `Bearer ${token}` }
            });
            await readResponse(response);
            await muatDataMahasiswa();
            setStatus('Data mahasiswa berhasil dihapus.');
        } catch (error) {
            setStatus(error.message, true);
        }
    }

    async function muatDataMahasiswa() {
        setStatus('Memuat data...');
        tbody.replaceChildren();

        try {
            const response = await fetch(new URL('mahasiswa', API_BASE_URL), {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`
                }
            });
            const result = await readResponse(response);

            if (!Array.isArray(result.data)) {
                throw new Error('Format data dari API tidak valid.');
            }

            document.querySelector('#total-mahasiswa').textContent = String(result.data.length);
            if (result.data.length === 0) {
                setStatus('Belum ada data');
                return;
            }

            for (const mahasiswa of result.data) {
                const row = document.createElement('tr');
                addCell(row, mahasiswa.nim);
                addCell(row, mahasiswa.nama);
                addCell(row, mahasiswa.prodi ?? mahasiswa.nama_prodi ?? mahasiswa.id_prodi);

                if (isAdmin) {
                    const actions = document.createElement('td');
                    const editButton = document.createElement('button');
                    editButton.className = 'btn-edit';
                    editButton.type = 'button';
                    editButton.textContent = 'Ubah';
                    editButton.addEventListener('click', () => openForm(mahasiswa));

                    const deleteButton = document.createElement('button');
                    deleteButton.className = 'btn-hapus';
                    deleteButton.type = 'button';
                    deleteButton.textContent = 'Hapus';
                    deleteButton.addEventListener('click', () => hapusMahasiswa(mahasiswa.nim));

                    actions.append(editButton, deleteButton);
                    row.appendChild(actions);
                }

                tbody.appendChild(row);
            }

            setStatus(`${result.data.length} data mahasiswa berhasil dimuat.`);
        } catch (error) {
            setStatus(error.message || 'Gagal memuat data mahasiswa.', true);
        }
    }

    document.querySelector('#btn-logout').addEventListener('click', kembaliKeLogin);
    document.querySelector('#btn-tambah').addEventListener('click', () => openForm());
    document.querySelector('#btn-batal').addEventListener('click', () => dialog.close());

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const nimLama = form.elements.nim_lama.value;
        const url = nimLama
            ? new URL(`mahasiswa/${encodeURIComponent(nimLama)}`, API_BASE_URL)
            : new URL('mahasiswa', API_BASE_URL);

        try {
            const response = await fetch(url, {
                method: nimLama ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`
                },
                body: JSON.stringify({
                    nim: form.elements.nim.value.trim(),
                    nama: form.elements.nama.value.trim(),
                    jenis_kelamin: form.elements.jenis_kelamin.value,
                    alamat: form.elements.alamat.value.trim() || null,
                    email: form.elements.email.value.trim() || null,
                    prodi: form.elements.prodi.value.trim(),
                    angkatan: form.elements.angkatan.value.trim(),
                    status: form.elements.status.value,
                    foto: form.elements.foto.value.trim() || null
                })
            });
            await readResponse(response);
            dialog.close();
            await muatDataMahasiswa();
            setStatus(nimLama ? 'Data mahasiswa berhasil diperbarui.' : 'Data mahasiswa berhasil ditambahkan.');
        } catch (error) {
            setStatus(error.message, true);
        }
    });

    muatDataMahasiswa();
}
