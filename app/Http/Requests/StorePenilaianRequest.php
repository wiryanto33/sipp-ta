<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_penilaians_id' => 'required|exists:category_penilaians,id',
            'jadwal_sidang_id' => 'required|exists:jadwal_sidangs,id',
            'penguji_sidang_id' => 'required|exists:penguji_sidangs,id',
            'aspek_penilaian' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0|max:100',
            'bobot' => 'required|numeric|min:0|max:100',
            'komentar' => 'nullable|string|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'category_penilaians_id.required' => 'Kategori penilaian harus dipilih',
            'category_penilaians_id.exists' => 'Kategori penilaian tidak valid',
            'jadwal_sidang_id.required' => 'Jadwal sidang harus dipilih',
            'jadwal_sidang_id.exists' => 'Jadwal sidang tidak valid',
            'penguji_sidang_id.required' => 'Penguji sidang harus dipilih',
            'penguji_sidang_id.exists' => 'Penguji sidang tidak valid',
            'aspek_penilaian.required' => 'Aspek penilaian harus diisi',
            'aspek_penilaian.max' => 'Aspek penilaian maksimal 255 karakter',
            'nilai.required' => 'Nilai harus diisi',
            'nilai.numeric' => 'Nilai harus berupa angka',
            'nilai.min' => 'Nilai minimal 0',
            'nilai.max' => 'Nilai maksimal 100',
            'bobot.required' => 'Bobot harus diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 100',
            'komentar.max' => 'Komentar maksimal 1000 karakter'
        ];
    }
}

class StoreBatchPenilaianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jadwal_sidang_id' => 'required|exists:jadwal_sidangs,id',
            'penguji_sidang_id' => 'required|exists:penguji_sidangs,id',
            'penilaians' => 'required|array|min:1',
            'penilaians.*.category_penilaians_id' => 'required|exists:category_penilaians,id',
            'penilaians.*.aspek_penilaian' => 'required|string|max:255',
            'penilaians.*.nilai' => 'required|numeric|min:0|max:100',
            'penilaians.*.bobot' => 'required|numeric|min:0|max:100',
            'penilaians.*.komentar' => 'nullable|string|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'jadwal_sidang_id.required' => 'Jadwal sidang harus dipilih',
            'jadwal_sidang_id.exists' => 'Jadwal sidang tidak valid',
            'penguji_sidang_id.required' => 'Penguji sidang harus dipilih',
            'penguji_sidang_id.exists' => 'Penguji sidang tidak valid',
            'penilaians.required' => 'Data penilaian harus diisi',
            'penilaians.array' => 'Format data penilaian tidak valid',
            'penilaians.min' => 'Minimal harus ada 1 penilaian',
            'penilaians.*.category_penilaians_id.required' => 'Kategori penilaian harus dipilih',
            'penilaians.*.category_penilaians_id.exists' => 'Kategori penilaian tidak valid',
            'penilaians.*.aspek_penilaian.required' => 'Aspek penilaian harus diisi',
            'penilaians.*.aspek_penilaian.max' => 'Aspek penilaian maksimal 255 karakter',
            'penilaians.*.nilai.required' => 'Nilai harus diisi',
            'penilaians.*.nilai.numeric' => 'Nilai harus berupa angka',
            'penilaians.*.nilai.min' => 'Nilai minimal 0',
            'penilaians.*.nilai.max' => 'Nilai maksimal 100',
            'penilaians.*.bobot.required' => 'Bobot harus diisi',
            'penilaians.*.bobot.numeric' => 'Bobot harus berupa angka',
            'penilaians.*.bobot.min' => 'Bobot minimal 0',
            'penilaians.*.bobot.max' => 'Bobot maksimal 100',
            'penilaians.*.komentar.max' => 'Komentar maksimal 1000 karakter'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $penilaians = $this->input('penilaians', []);
            $totalBobot = array_sum(array_column($penilaians, 'bobot'));

            if ($totalBobot > 100) {
                $validator->errors()->add('total_bobot', 'Total bobot tidak boleh lebih dari 100%');
            }
        });
    }
}

class UpdatePenilaianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_penilaians_id' => 'required|exists:category_penilaians,id',
            'aspek_penilaian' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0|max:100',
            'bobot' => 'required|numeric|min:0|max:100',
            'komentar' => 'nullable|string|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'category_penilaians_id.required' => 'Kategori penilaian harus dipilih',
            'category_penilaians_id.exists' => 'Kategori penilaian tidak valid',
            'aspek_penilaian.required' => 'Aspek penilaian harus diisi',
            'aspek_penilaian.max' => 'Aspek penilaian maksimal 255 karakter',
            'nilai.required' => 'Nilai harus diisi',
            'nilai.numeric' => 'Nilai harus berupa angka',
            'nilai.min' => 'Nilai minimal 0',
            'nilai.max' => 'Nilai maksimal 100',
            'bobot.required' => 'Bobot harus diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 100',
            'komentar.max' => 'Komentar maksimal 1000 karakter'
        ];
    }
}
