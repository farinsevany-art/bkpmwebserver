<?php

class Mahasiswa
{
    private $nim;
    private $nama;
    private $prodi;

    public function __construct($nim, $nama, $prodi)
    {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->prodi = $prodi;
    }

    public function getNim()
    {
        return $this->nim;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getProdi()
    {
        return $this->prodi;
    }

    public function getAngkatan()
    {
        $tahun = substr($this->nim, 0, 2);
        return '20' . $tahun;
    }
}
