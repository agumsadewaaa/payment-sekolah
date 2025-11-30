<?php

namespace App\Repositories;

use App\Models\Kelas;
use App\Repositories\BaseRepository;

class KelasRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'kode',
        'kelas',
        'jurusan'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Kelas::class;
    }

    public function getJurusanByKelas($kelas)
    {
        return $this->model
            ->where('kelas', $kelas)
            ->pluck('jurusan', 'id'); // hasil: [id => jurusan]
    }

    public function getAllOrderedByKode()
    {
        return $this->model->orderBy('kode', 'asc')->paginate(10);
    }

}
