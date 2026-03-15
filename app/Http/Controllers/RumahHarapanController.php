<?php

namespace App\Http\Controllers;

use App\Http\Requests\RumahHarapan\StoreRumahHarapanRequest;
use App\Http\Requests\RumahHarapan\UpdateRumahHarapanRequest;
use App\Services\RumahHarapanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RumahHarapanController extends Controller
{
    protected RumahHarapanService $service;

    public function __construct(RumahHarapanService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of asrama with AJAX search/pagination.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $filters = [
                'search'    => $request->input('search'),
                'is_active' => $request->has('is_active')
                    ? $request->boolean('is_active')
                    : null,
            ];

            $perPage = $request->input('per_page', 7);
            $rumahHarapanList = $this->service->getPaginated($filters, $perPage, false);

            $rawData = [];
            foreach ($rumahHarapanList->items() as $rumahHarapan) {
                $rawData[] = [
                    'id'          => $rumahHarapan->id,
                    'kode'        => $rumahHarapan->kode,
                    'nama'        => $rumahHarapan->nama,
                    'alamat'      => $rumahHarapan->alamat,
                    'kota'        => $rumahHarapan->kota,
                    'provinsi'    => $rumahHarapan->provinsi,
                    'telepon'     => $rumahHarapan->telepon,
                    'email'       => $rumahHarapan->email,
                    'koordinator' => $rumahHarapan->koordinator,
                    'is_active'   => $rumahHarapan->is_active,
                    'created_by'  => $rumahHarapan->createdBy?->name ?? 'N/A',
                ];
            }

            return response()->json([
                'data'         => $rawData,
                'current_page' => $rumahHarapanList->currentPage(),
                'last_page'    => $rumahHarapanList->lastPage(),
                'total'        => $rumahHarapanList->total(),
                'per_page'     => $rumahHarapanList->perPage(),
                'first_item'   => $rumahHarapanList->firstItem(),
            ]);
        }

        $filters = [
            'search'    => $request->input('search'),
            'is_active' => $request->has('is_active')
                ? $request->boolean('is_active')
                : null,
        ];

        return view('pages.rumah-harapan.index', compact('filters'));
    }

    /**
     * Show detail asrama (read-only) — accessible by admin & petugas.
     */
    public function show(int $id)
    {
        $rumahHarapan = $this->service->findById($id);

        return view('pages.rumah-harapan.show', compact('rumahHarapan'));
    }

    /**
     * Show form create.
     */
    public function create()
    {
        return view('pages.rumah-harapan.create');
    }

    /**
     * Store new asrama.
     */
    public function store(StoreRumahHarapanRequest $request)
    {
        try {
            $this->service->create(
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('rumah-harapan.index', ['success' => 'Asrama berhasil ditambahkan.']);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit(int $id)
    {
        $rumahHarapan = $this->service->findById($id);

        return view('pages.rumah-harapan.edit', compact('rumahHarapan'));
    }

    /**
     * Update asrama.
     */
    public function update(UpdateRumahHarapanRequest $request, int $id)
    {
        try {
            $rumahHarapan = $this->service->findById($id);

            $this->service->update(
                $rumahHarapan,
                $request->validated(),
                $request->user()
            );

            // Kembali ke page asal — dikirim dari hidden input di form edit
            $currentPage = (int) $request->input('current_page', 1);

            return redirect()->route('rumah-harapan.index', array_filter([
                'page'    => $currentPage > 1 ? $currentPage : null,
                'success' => 'Asrama berhasil diperbarui.',
            ]));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput();
        }
    }

    /**
     * Hard delete (permanent delete) asrama.
     */
    public function destroy(int $id)
    {
        try {
            $currentPage = (int) request()->input('current_page', 1);

            $this->service->hardDelete($id);

            // Hitung sisa data setelah delete untuk menentukan redirect page
            $perPage       = 7;
            $remainingData = $this->service->getPaginated([], $perPage, false);
            $lastPage      = $remainingData->lastPage();

            // Jika page saat ini melebihi last page, turun ke page sebelumnya
            $redirectPage = min($currentPage, max(1, $lastPage));

            return redirect()->route('rumah-harapan.index', array_filter([
                'page'    => $redirectPage > 1 ? $redirectPage : null,
                'success' => 'Data Asrama berhasil dihapus.',
            ]));
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }
}
