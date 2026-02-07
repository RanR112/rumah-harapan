<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\RumahHarapan;
use Rap2hpoutre\FastExcel\FastExcel;

/**
 * ExportAnakAsuhService handles exporting foster child data to Excel or CSV.
 * Supports filtering and multiple output formats.
 */
class ExportAnakAsuhService
{
    /**
     * Export data to a file in the specified format.
     *
     * @param array $filters Filtering criteria (search, status, grade, rh, trashed)
     * @param string $format Output format: 'xlsx' or 'csv'
     * @param string $filename Base filename without extension
     * @return string Full path to the exported file
     */
    public function export(array $filters, string $format = 'xlsx', string $filename = 'anak_asuh'): string
    {
        $query = AnakAsuh::with('rumahHarapan');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_anak', 'like', "%{$filters['search']}%")
                    ->orWhere('nik', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }

        if (!empty($filters['rh'])) {
            $rh = RumahHarapan::where('kode', $filters['rh'])->first();
            if ($rh) {
                $query->where('rumah_harapan_id', $rh->id);
            }
        }

        if (!empty($filters['trashed']) && $filters['trashed'] === true) {
            $query->withTrashed();
        }

        $data = $query->get()->map(function ($anak) {
            return [
                'RH' => $anak->rumahHarapan->kode ?? '',
                'NAMA_ANAK' => $anak->nama_anak,
                'NIK' => $anak->nik,
                'NO_KARTU_KELUARGA' => $anak->no_kartu_keluarga,
                'ALAMAT_LENGKAP' => $anak->alamat_lengkap,
                'JENIS_KEL' => $anak->jenis_kel,
                'TEMPAT_LAHIR' => $anak->tempat_lahir,
                'TANGGAL_LAHIR' => $anak->tanggal_lahir ? $anak->tanggal_lahir->format('Y-m-d') : '',
                'UMUR' => $anak->umur,
                'STATUS' => $anak->status,
                'GRADE' => $anak->grade,
                'PENDIDIKAN_KELAS' => $anak->pendidikan_kelas,
                'NAMA_ORANG_TUA' => $anak->nama_orang_tua,
                'NO_HANDPHONE' => $anak->no_handphone,
                'TANGGAL_MASUK_RH' => $anak->tanggal_masuk_rh ? $anak->tanggal_masuk_rh->format('Y-m-d') : '',
                'YANG_MENGASUH_SEBELUM_DIASRAMA' => $anak->yang_mengasuh_sebelum_diasrama,
                'REKOMENDASI' => $anak->rekomendasi,
            ];
        });

        $fullPath = storage_path("app/exports/{$filename}_" . now()->format('Ymd_His') . ".{$format}");

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        (new FastExcel($data))->export($fullPath);

        return $fullPath;
    }
}
