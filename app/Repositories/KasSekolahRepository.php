<?php

namespace App\Repositories;

use App\Models\KasSekolah;
use App\Repositories\BaseRepository;

class KasSekolahRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'tanggal',
        'catatan',
        'tipe',
        'metode_pembayaran',
        'nominal'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return KasSekolah::class;
    }
}
