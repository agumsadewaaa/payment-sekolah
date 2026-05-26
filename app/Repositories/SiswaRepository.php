<?php

namespace App\Repositories;

use App\Models\Siswa;
use App\Repositories\BaseRepository;

class SiswaRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'nama',
        'nis',
        'kontak_ortu',
        'kelas',
        'jurusan',
        'tahun_masuk',
        'tahun_lulus',
        'status_siswa'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Siswa::class;
    }
}
