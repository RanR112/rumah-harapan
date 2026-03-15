<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnakAsuh\StoreAnakAsuhRequest;
use App\Http\Requests\AnakAsuh\UpdateAnakAsuhRequest;
use App\Models\BerkasAnak;
use App\Models\RumahHarapan;
use App\Services\AnakAsuhService;
use App\Services\BerkasAnakService;
use App\Services\ExportAnakAsuhService;
use App\Services\ImportDataAnakAsuh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AnakAsuhController extends Controller
{
    protected AnakAsuhService $anakAsuhService;
    protected ExportAnakAsuhService $exportService;
    protected BerkasAnakService $berkasAnakService;
    protected ImportDataAnakAsuh $importDataAnakAsuh;

    public function __construct(
        AnakAsuhService $anakAsuhService,
        ExportAnakAsuhService $exportService,
        BerkasAnakService $berkasAnakService,
        ImportDataAnakAsuh $importDataAnakAsuh
    ) {
        $this->anakAsuhService    = $anakAsuhService;
        $this->exportService      = $exportService;
        $this->berkasAnakService  = $berkasAnakService;
        $this->importDataAnakAsuh = $importDataAnakAsuh;
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $filters = [
                'search'    => $request->input('search'),
                'status'    => $request->input('status'),
                'is_active' => $request->input('is_active'),
                'grade'     => $request->input('grade'),
                'rh'        => $request->input('rh'),
            ];

            $perPage  = $request->input('per_page', 8);
            $anakAsuh = $this->anakAsuhService->getPaginated($filters, $perPage);

            $rawData = [];
            foreach ($anakAsuh->items() as $anak) {
                $rawData[] = [
                    'id'                => $anak->id,
                    'nama_anak'         => $anak->nama_anak,
                    'nik'               => $anak->nik,
                    'no_kartu_keluarga' => $anak->no_kartu_keluarga,
                    'jenis_kelamin'     => $anak->jenis_kel,
                    'tanggal_lahir'     => $anak->tanggal_lahir,
                    'status'            => $anak->status,
                    'status_label'      => $anak->status_label,
                    'is_active'         => $anak->is_active,
                    'grade'             => $anak->grade,
                    'nama_orang_tua'    => $anak->nama_orang_tua,
                    'tanggal_masuk_rh'  => $anak->tanggal_masuk_rh,
                    'rh_kode'           => $anak->rumahHarapan->kode ?? 'N/A',
                    'created_by'        => $anak->createdBy?->name ?? 'N/A',
                    'foto_url'          => $anak->foto_url,
                    'berkas_count'      => $anak->berkas_anak_count ?? 0,
                ];
            }

            return response()->json([
                'data'         => $rawData,
                'current_page' => $anakAsuh->currentPage(),
                'last_page'    => $anakAsuh->lastPage(),
                'total'        => $anakAsuh->total(),
                'per_page'     => $anakAsuh->perPage(),
                'first_item'   => $anakAsuh->firstItem(),
            ]);
        }

        $asramas = RumahHarapan::all();
        return view('pages.anak-asuh.index', compact('asramas'));
    }

    public function create()
    {
        $asramas = RumahHarapan::all();
        return view('pages.anak-asuh.create', compact('asramas'));
    }

    public function store(StoreAnakAsuhRequest $request)
    {
        try {
            $anakAsuh = $this->anakAsuhService->create(
                $request->validated(),
                $request->user()
            );

            $this->handleFotoUpload($request, $anakAsuh);

            return redirect()->route('anak-asuh.show', [
                'id'      => $anakAsuh->id,
                'new'     => 'true',
                'success' => 'Data anak asuh berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('anak-asuh.create', [
                'error' => 'Gagal menyimpan data anak asuh: ' . $e->getMessage()
            ]);
        }
    }

    public function edit(int $id)
    {
        $anakAsuh = $this->anakAsuhService->findById($id);
        $asramas  = RumahHarapan::all();
        return view('pages.anak-asuh.edit', compact('anakAsuh', 'asramas'));
    }

    public function update(UpdateAnakAsuhRequest $request, int $id)
    {
        try {
            $anakAsuh = $this->anakAsuhService->findById($id);

            $oldFotoPath = $anakAsuh->foto_path;
            $this->handleFotoUpload($request, $anakAsuh);
            $newFotoPath = $anakAsuh->foto_path;

            $this->anakAsuhService->update(
                $anakAsuh,
                $request->validated(),
                $request->user(),
                $oldFotoPath,
                $newFotoPath
            );

            // Kembali ke page asal — dikirim dari hidden input di form edit
            $currentPage = (int) $request->input('current_page', 1);

            return redirect()->route('anak-asuh.index', array_filter([
                'page'    => $currentPage > 1 ? $currentPage : null,
                'success' => 'Data anak asuh berhasil diperbarui.',
            ]));
        } catch (\Exception $e) {
            return redirect()->route('anak-asuh.index', [
                'error' => 'Gagal memperbarui data anak asuh: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(int $id)
    {
        try {
            // Baca current_page yang dikirim dari hidden input form delete
            $currentPage = (int) request()->input('current_page', 1);

            // Hitung total data sebelum delete untuk menentukan redirect page
            $filters = [
                'search'    => request()->input('search'),
                'status'    => request()->input('status'),
                'is_active' => request()->input('is_active'),
                'grade'     => request()->input('grade'),
                'rh'        => request()->input('rh'),
            ];

            $this->anakAsuhService->hardDelete($id);

            // Hitung sisa data setelah delete
            $perPage       = 8;
            $remainingData = $this->anakAsuhService->getPaginated($filters, $perPage);
            $lastPage      = $remainingData->lastPage();

            // Jika current page melebihi last page (data di page ini habis),
            // redirect ke page sebelumnya — minimal page 1
            $redirectPage = min($currentPage, max(1, $lastPage));

            return redirect()->route('anak-asuh.index', array_filter([
                'page'    => $redirectPage > 1 ? $redirectPage : null,
                'success' => 'Data anak asuh berhasil dihapus.',
            ]));
        } catch (\Exception $e) {
            return redirect()->route('anak-asuh.index', [
                'error' => 'Gagal menghapus data anak asuh: ' . $e->getMessage()
            ]);
        }
    }

    private function handleFotoUpload(Request $request, $anakAsuh): void
    {
        $shouldDelete = $request->input('delete_foto') === '1';

        if ($shouldDelete && !$request->hasFile('foto')) {
            if ($anakAsuh->foto_path) {
                Storage::disk('public')->delete($anakAsuh->foto_path);
            }
            $anakAsuh->foto_path = null;
            $anakAsuh->save();
            return;
        }

        if ($request->hasFile('foto')) {
            $request->validate([
                'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($anakAsuh->foto_path) {
                Storage::disk('public')->delete($anakAsuh->foto_path);
            }

            $extension = $request->file('foto')->getClientOriginalExtension();
            $filename  = 'foto_anak_asuh_' . $anakAsuh->id . '_' . now()->format('Ymd_His') . '.' . $extension;
            $path      = $request->file('foto')->storeAs('anak_asuh/foto', $filename, 'public');

            $anakAsuh->foto_path = $path;
            $anakAsuh->save();
            return;
        }
    }

    public function uploadBerkas(Request $request, int $id)
    {
        try {
            $request->validate([
                'file'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'original_name' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        try {
            $anakAsuh = $this->anakAsuhService->findById($id);

            $berkas = $this->berkasAnakService->upload(
                $request->file('file'),
                $anakAsuh,
                $request->user(),
                $request->input('original_name')
            );

            return response()->json([
                'success' => true,
                'berkas'  => [
                    'id'            => $berkas->id,
                    'original_name' => $berkas->original_name,
                    'file_url'      => Storage::url($berkas->file_path),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Berkas upload error', [
                'user_id'      => $request->user()->id ?? null,
                'anak_asuh_id' => $id,
                'file_name'    => $request->file('file')->getClientOriginalName() ?? 'unknown',
                'message'      => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
            ]);
            return response()->json(['success' => false, 'error_type' => 'server_error'], 500);
        }
    }

    public function deleteBerkas(int $id, int $berkasId)
    {
        try {
            $berkas = BerkasAnak::where('anak_asuh_id', $id)->findOrFail($berkasId);
            $this->berkasAnakService->delete($berkas);
            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Berkas not found for deletion', [
                'anak_asuh_id' => $id,
                'berkas_id'    => $berkasId,
                'user_id'      => auth()->id() ?? null,
            ]);
            return response()->json(['success' => false, 'error_type' => 'not_found'], 404);
        } catch (\Exception $e) {
            Log::error('Berkas delete error', [
                'anak_asuh_id' => $id,
                'berkas_id'    => $berkasId,
                'user_id'      => auth()->id() ?? null,
                'message'      => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
            ]);
            return response()->json(['success' => false, 'error_type' => 'server_error'], 500);
        }
    }

    public function export(Request $request)
    {
        $filters = [
            'search'    => $request->input('search'),
            'status'    => $request->input('status'),
            'is_active' => $request->input('is_active'),
            'grade'     => $request->input('grade'),
            'rh'        => $request->input('rh'),
        ];

        $format = in_array($request->input('format'), ['xlsx', 'csv'])
            ? $request->input('format')
            : 'xlsx';

        $filePath = $this->exportService->export($filters, $format);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $originalFilename = $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->store('imports', 'local');

        try {
            $result = $this->importDataAnakAsuh->execute(
                storage_path('app/' . $path),
                $request->user(),
                $originalFilename
            );

            Storage::disk('local')->delete($path);

            $successCount = $result['successCount'] ?? 0;
            $errors       = $result['errors'] ?? [];

            return response()->json([
                'success' => empty($errors),
                'data'    => [
                    'success_count' => $successCount,
                    'error_count'   => count($errors),
                    'errors'        => $errors,
                ],
            ]);
        } catch (\Exception $e) {
            Storage::disk('local')->delete($path);
            Log::error('Import error', [
                'user_id'   => $request->user()->id ?? null,
                'file_name' => $originalFilename,
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);
            return response()->json(['success' => false, 'error_type' => 'server_error'], 500);
        }
    }

    public function show(int $id)
    {
        $anakAsuh = $this->anakAsuhService->findById($id);
        $asramas  = RumahHarapan::all();
        $berkas   = BerkasAnak::where('anak_asuh_id', $id)->get();
        return view('pages.anak-asuh.show', compact('anakAsuh', 'asramas', 'berkas'));
    }
}
