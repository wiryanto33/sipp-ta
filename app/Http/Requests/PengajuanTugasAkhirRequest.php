<?php

namespace App\Http\Requests;

use App\Models\PengajuanTugasAkhir;
use Illuminate\Foundation\Http\FormRequest;

class PengajuanTugasAkhirRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'judul' => 'required|string|max:255',
            'sinopsis' => 'nullable|string',
            'bidang_penelitian' => 'nullable|string|max:255',
            'file_proposal' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
        ];

        // Jika sedang update dan bukan draft, tambahkan validasi file skripsi
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $tugasAkhir = $this->route('tugas_akhir');
            if ($tugasAkhir && $tugasAkhir->status !== PengajuanTugasAkhir::STATUS_DRAFT) {
                $rules['file_skripsi'] = 'nullable|file|mimes:pdf,doc,docx|max:10240'; // 10MB
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul tugas akhir wajib diisi.',
            'judul.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'file_proposal.mimes' => 'File proposal harus berformat PDF, DOC, atau DOCX.',
            'file_proposal.max' => 'File proposal tidak boleh lebih dari 5MB.',
            'file_skripsi.mimes' => 'File skripsi harus berformat PDF, DOC, atau DOCX.',
            'file_skripsi.max' => 'File skripsi tidak boleh lebih dari 10MB.',
        ];
    }
}
