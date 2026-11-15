const API_URL = new URL('../mahasiswa', window.location.href);

async function muatDataMahasiswa() {
    const status = document.querySelector('#status');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    status.textContent = 'Memuat data...';
    tbody.replaceChildren();

    try {
        const response = await fetch(API_URL, {
            headers: { Accept: 'application/json' }
        });
        const hasil = await response.json();

        if (!response.ok || !hasil.success) {
            throw new Error(hasil.message || `Request gagal (HTTP ${response.status})`);
        }

        if (!Array.isArray(hasil.data)) {
            throw new Error('Format data dari API tidak valid.');
        }

        if (hasil.data.length === 0) {
            status.textContent = 'Belum ada data';
            return;
        }

        for (const mahasiswa of hasil.data) {
            const row = document.createElement('tr');
            const programStudi = mahasiswa.prodi
                ?? mahasiswa.nama_prodi
                ?? mahasiswa.id_prodi
                ?? '-';

            for (const value of [mahasiswa.nim, mahasiswa.nama, programStudi]) {
                const cell = document.createElement('td');
                cell.textContent = value == null || value === '' ? '-' : String(value);
                row.appendChild(cell);
            }

            tbody.appendChild(row);
        }

        status.textContent = `${hasil.data.length} data mahasiswa berhasil dimuat.`;
    } catch (error) {
        console.error('Gagal memuat data mahasiswa:', error);
        status.textContent = `Gagal memuat data: ${error.message}`;
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
