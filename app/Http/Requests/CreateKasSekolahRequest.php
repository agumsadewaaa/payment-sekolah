<?php

namespace App\Http\Requests;

use App\Models\KasSekolah;
use Illuminate\Foundation\Http\FormRequest;

class CreateKasSekolahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = KasSekolah::$rules;
        
        // Untuk tipe 2 (Pengeluaran), catatan harus required
        // Untuk tipe 1 (Pendapatan), catatan optional (akan auto-generated)
        if ($this->input('tipe') == 2) {
            $rules['catatan'] = 'required|string|max:500';
        }
        
        return $rules;
    }
}
