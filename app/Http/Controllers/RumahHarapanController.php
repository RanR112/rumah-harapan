<?php

namespace App\Http\Controllers;

use App\Http\Requests\RumahHarapan\StoreRumahHarapanRequest;
use App\Http\Requests\RumahHarapan\UpdateRumahHarapanRequest;
use App\Services\RumahHarapanService;
use Illuminate\Http\Request;

class RumahHarapanController extends Controller
{
    protected RumahHarapanService $service;

    public function __construct(RumahHarapanService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $filters = [
            'search'    => $request->input('search'),
            'is_active' => $request->has('is_active')
                ? $request->boolean('is_active')
                : null,
        ];

        $perPage = 15;
        $trashed = $request->boolean('trashed', false);

        $branches = $this->service
            ->getPaginated($filters, $perPage, $trashed)
            ->appends($request->query());

        return view('rumah-harapan.index', [
            'branches' => $branches,
            'trashed'  => $trashed,
        ]);
    }

    /**
     * Show form create.
     */
    public function create()
    {
        return view('rumah-harapan.create');
    }

    /**
     * Store new branch.
     */
    public function store(StoreRumahHarapanRequest $request)
    {
        $this->service->create(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('admin.rumah-harapan.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    /**
     * Show edit form.
     */
    public function edit(int $id)
    {
        $rumahHarapan = $this->service->findById($id);

        return view('rumah-harapan.edit', compact('rumahHarapan'));
    }

    /**
     * Update branch.
     */
    public function update(UpdateRumahHarapanRequest $request, int $id)
    {
        $branch = $this->service->findById($id);

        $this->service->update(
            $branch,
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('admin.rumah-harapan.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    /**
     * Soft delete branch.
     */
    public function destroy(int $id)
    {
        $branch = $this->service->findById($id);
        $this->service->delete($branch);

        return redirect()
            ->route('admin.rumah-harapan.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }

    /**
     * Restore branch.
     */
    public function restore(int $id)
    {
        $branch = $this->service->restore($id);

        if (!$branch) {
            return redirect()
                ->route('admin.rumah-harapan.index')
                ->with('error', 'Cabang tidak ditemukan.');
        }

        return redirect()
            ->route('admin.rumah-harapan.index', ['trashed' => true])
            ->with('success', 'Cabang berhasil dipulihkan.');
    }

    /**
     * Hard delete branch.
     */
    public function hardDelete(int $id)
    {
        $success = $this->service->hardDelete($id);

        if (!$success) {
            return redirect()
                ->route('admin.rumah-harapan.index', ['trashed' => true])
                ->with('error', 'Cabang tidak ditemukan atau belum dihapus.');
        }

        return redirect()
            ->route('admin.rumah-harapan.index', ['trashed' => true])
            ->with('success', 'Cabang berhasil dihapus permanen.');
    }
}
