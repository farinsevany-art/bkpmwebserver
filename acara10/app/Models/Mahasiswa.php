<?php

/**
 * Entity Mahasiswa.
 * Atribut dibuat private dan diakses melalui getter/setter.
 */
class Mahasiswa
{
    private string $nim = '';
    private string $nama = '';

    public function __construct(string $nim = '', string $nama = '')
    {
        if ($nim !== '') {
            $this->setNim($nim);
        }
        if ($nama !== '') {
            $this->setNama($nama);
        }
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function setNim(string $nim): void
    {
        $nim = trim($nim);

        if ($nim !== '' && !ctype_digit($nim)) {
            throw new InvalidArgumentException('NIM harus berupa angka.');
        }

        $this->nim = $nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function setNama(string $nama): void
    {
        $nama = trim($nama);

        if ($nama === '') {
            throw new InvalidArgumentException('Nama mahasiswa tidak boleh kosong.');
        }

        if (mb_strlen($nama) > 100) {
            throw new InvalidArgumentException('Nama maksimal 100 karakter.');
        }

        $this->nama = $nama;
    }
}
