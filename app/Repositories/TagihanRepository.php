<?php

namespace App\Repositories;

use App\Models\Tagihan;
use App\Repositories\BaseRepository;

class TagihanRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'kelas',
        'tagihan',
        'nominal'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Tagihan::class;
    }
}
