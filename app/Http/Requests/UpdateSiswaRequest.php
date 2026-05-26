<?php

namespace App\Http\Requests;

use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
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
        $rules = Siswa::$rules;
        
        // Modify NIS unique rule to exclude current siswa ID
        // route('siswas.update', $id) -> $id is available in route parameter
        $siswaId = $this->route('siswa');  // Get ID from route parameter
        if ($siswaId) {
            $rules['nis'] = 'required|string|max:20|unique:tb_siswa,nis,' . $siswaId;
        }
        
        return $rules;
    }
}
