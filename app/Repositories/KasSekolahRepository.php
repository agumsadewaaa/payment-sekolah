<?php

namespace App\Repositories;

use App\Models\KasSekolah;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * Paginate records in descending order by creation date.
     */
    public function paginate(int $perPage, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->allQuery();

        return $query->orderBy('created_at', 'desc')->paginate($perPage, $columns);
    }
}
