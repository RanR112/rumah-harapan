<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\RumahHarapan;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;

/**
 * ExportAnakAsuhService handles exporting foster child data to Excel or CSV.
 */
class ExportAnakAsuhService
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function export(array $filters, string $format = 'xlsx', string $filename = 'Data Anak Asuh Rumah Harapan'): string
    {
        $query = AnakAsuh::with('rumahHarapan');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_anak', 'like', "%{$filters['search']}%")
                    ->orWhere('nik', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
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

        $data = $query->get()->map(function ($anak) {
            return [
                'RH'                             => $anak->rumahHarapan->kode ?? '',
                'NAMA ANAK'                      => $anak->nama_anak,
                'NIK'                            => $anak->nik,
                'NO KARTU KELUARGA'              => $anak->no_kartu_keluarga,
                'ALAMAT LENGKAP'                 => $anak->alamat_lengkap,
                'JENIS KEL'                      => $anak->jenis_kel,
                'TEMPAT LAHIR'                   => $anak->tempat_lahir,
                'TANGGAL LAHIR'                  => $anak->tanggal_lahir
                    ? $anak->tanggal_lahir->format('Y-m-d')
                    : '',
                'UMUR'                           => $anak->umur,
                'STATUS'                         => $anak->status,
                'STATUS KEAKTIFAN'               => $anak->is_active ? 'Aktif' : 'Tidak Aktif',
                'GRADE'                          => $anak->grade,
                'PENDIDIKAN KELAS'               => $anak->pendidikan_kelas,
                'NAMA ORANG TUA'                 => $anak->nama_orang_tua,
                'NO HANDPHONE'                   => $anak->no_handphone,
                'TANGGAL MASUK RH'               => $anak->tanggal_masuk_rh
                    ? $anak->tanggal_masuk_rh->format('Y-m-d')
                    : '',
                'YANG MENGASUH SEBELUM DIASRAMA' => $anak->yang_mengasuh_sebelum_diasrama,
                'REKOMENDASI'                    => $anak->rekomendasi,
            ];
        });

        // Format nama file: "Data Anak Asuh Rumah Harapan_15-01-2025.xlsx"
        // Tanpa waktu, tanggal format d-m-Y agar mudah dibaca
        $fullPath = storage_path(
            "app/exports/{$filename}_" . now()->format('d-m-Y') . ".{$format}"
        );

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        (new FastExcel($data))->export($fullPath);

        if (Auth::check()) {
            $this->auditLogService->logCustom(
                'Expor Data Anak Asuh',
                'export',
                [
                    'format'     => $format === 'xlsx' ? 'Excel' : 'CSV',
                    'nama file'  => basename($fullPath),
                    'total data' => $data->count(),
                ]
            );
        }

        return $fullPath;
    }
}
