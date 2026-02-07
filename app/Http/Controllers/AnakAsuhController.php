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

class AnakAsuhController extends Controller
{
    protected AnakAsuhService $anakAsuhService;
    protected ExportAnakAsuhService $exportService;

    public function __construct(
        AnakAsuhService $anakAsuhService,
        ExportAnakAsuhService $exportService
    ) {
        $this->anakAsuhService = $anakAsuhService;
        $this->exportService = $exportService;
    }

    /**
     * Display a listing of foster children with AJAX search/pagination.
     */
    public function index(Request $request)
    {
        // Handle AJAX request for search/pagination
        if ($request->ajax() || $request->wantsJson()) {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'grade'  => $request->input('grade'),
                'rh'     => $request->input('rh'),
            ];

            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);

            // Hanya tampilkan data aktif (tidak ada soft delete)
            $anakAsuh = $this->anakAsuhService->getPaginated($filters, $perPage, false);

            $rawData = [];
            foreach ($anakAsuh->items() as $anak) {
                $rawData[] = [
                    'id' => $anak->id,
                    'nama_anak' => $anak->nama_anak,
                    'nik' => $anak->nik,
                    'no_kartu_keluarga' => $anak->no_kartu_keluarga,
                    'jenis_kelamin' => $anak->jenis_kel,
                    'tanggal_lahir' => $anak->tanggal_lahir,
                    'status' => $anak->status,
                    'grade' => $anak->grade,
                    'nama_orang_tua' => $anak->nama_orang_tua,
                    'tanggal_masuk_rh' => $anak->tanggal_masuk_rh,
                    'rh_kode' => $anak->rumahHarapan->kode ?? 'N/A',
                    'created_by' => $anak->createdBy?->name ?? 'N/A',
                ];
            }

            return response()->json([
                'data' => $rawData,
                'current_page' => $anakAsuh->currentPage(),
                'last_page' => $anakAsuh->lastPage(),
                'total' => $anakAsuh->total(),
                'per_page' => $anakAsuh->perPage(),
                'first_item' => $anakAsuh->firstItem(),
            ]);
        }

        // Traditional request for initial page load
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'grade'  => $request->input('grade'),
            'rh'     => $request->input('rh'),
        ];

        $cabangs = RumahHarapan::all();

        return view('pages.anak-asuh.index', compact('cabangs'));
    }

    /**
     * Show form create.
     */
    public function create()
    {
        $cabangs = RumahHarapan::all();
        
        return view('pages.anak-asuh.create', compact('cabangs'));
    }

    /**
     * Store new foster child with query parameter alert.
     */
    public function store(StoreAnakAsuhRequest $request)
    {
        try {
            $this->anakAsuhService->create(
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('anak-asuh.index')
                ->withQuery(['success' => 'Data anak asuh berhasil ditambahkan.']);
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withQuery(['error' => 'Gagal menambahkan data anak asuh.'])
                ->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit(int $id)
    {
        $anakAsuh = $this->anakAsuhService->findById($id);
        $cabangs  = RumahHarapan::all();

        return view('pages.anak-asuh.edit', compact('anakAsuh', 'cabangs'));
    }

    /**
     * Update foster child with query parameter alert.
     */
    public function update(UpdateAnakAsuhRequest $request, int $id)
    {
        try {
            $anakAsuh = $this->anakAsuhService->findById($id);

            $this->anakAsuhService->update(
                $anakAsuh,
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('anak-asuh.index')
                ->withQuery(['success' => 'Data anak asuh berhasil diperbarui.']);
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withQuery(['error' => 'Gagal memperbarui data anak asuh.'])
                ->withInput();
        }
    }

    /**
     * Hard delete (permanent delete) with query parameter alert.
     */
    public function destroy(int $id)
    {
        try {
            $this->anakAsuhService->hardDelete($id);

            return redirect()
                ->route('anak-asuh.index')
                ->withQuery(['success' => 'Data anak asuh berhasil dihapus permanen.']);
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withQuery(['error' => 'Gagal menghapus data anak asuh.']);
        }
    }

    /**
     * Export data.
     */
    public function export(Request $request)
    {
        $filters = [
            'search'  => $request->input('search'),
            'status'  => $request->input('status'),
            'grade'   => $request->input('grade'),
            'rh'      => $request->input('rh'),
            'trashed' => false, // Tidak ada soft delete
        ];

        $format = in_array($request->input('format'), ['xlsx', 'csv'])
            ? $request->input('format')
            : 'xlsx';

        $filePath = $this->exportService->export($filters, $format, 'anak_asuh');

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * Import data.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
        ]);

        $path = $request->file('file')->store('imports', 'local');

        try {
            $service = new ImportDataAnakAsuh($request->user());
            $result  = $service->execute(storage_path('app/' . $path));

            Storage::disk('local')->delete($path);

            if (!empty($result['errors'])) {
                return redirect()->back()
                    ->withQuery(['error' => 'Import selesai dengan data anak yang sudah ada.']);
            }

            return redirect()
                ->route('anak-asuh.index')
                ->withQuery(['success' => "Import berhasil ({$result['success_count']} data)."]);
        } catch (\Exception $e) {
            Storage::disk('local')->delete($path);

            return redirect()->back()
                ->withQuery(['error' => 'Gagal mengimpor: ' . $e->getMessage()]);
        }
    }

    /**
     * Show detail page.
     */
    public function show(int $id)
    {
        $anakAsuh = $this->anakAsuhService->findById($id);
        $berkas   = BerkasAnak::where('anak_asuh_id', $id)->get();

        return view('pages.anak-asuh.show', compact('anakAsuh', 'berkas'));
    }

    /**
     * Upload file.
     */
    public function uploadBerkas(Request $request, int $id)
    {
        $anakAsuh = $this->anakAsuhService->findById($id);

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        (new BerkasAnakService())->upload(
            $request->file('file'),
            $anakAsuh,
            $request->user()
        );

        return redirect()
            ->route('anak-asuh.show', $id)
            ->withQuery(['success' => 'Berkas berhasil diunggah.']);
    }

    /**
     * Delete file.
     */
    public function deleteBerkas(int $anakAsuhId, int $berkasId)
    {
        $berkas = BerkasAnak::where('anak_asuh_id', $anakAsuhId)
            ->findOrFail($berkasId);

        (new BerkasAnakService())->delete($berkas);

        return redirect()
            ->route('anak-asuh.show', $anakAsuhId)
            ->withQuery(['success' => 'Berkas berhasil dihapus.']);
    }
}