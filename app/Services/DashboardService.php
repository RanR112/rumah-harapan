<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Services\UserService;
use App\Services\AnakAsuhService;
use App\Services\RumahHarapanService;

class DashboardService
{
    protected UserService $userService;
    protected AnakAsuhService $anakAsuhService;
    protected RumahHarapanService $rumahHarapanService;

    public function __construct(
        UserService $userService,
        AnakAsuhService $anakAsuhService,
        RumahHarapanService $rumahHarapanService
    ) {
        $this->userService = $userService;
        $this->anakAsuhService = $anakAsuhService;
        $this->rumahHarapanService = $rumahHarapanService;
    }

    /**
     * Get dashboard statistics.
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        return [
            'totalUsers' => $this->userService->getTotalCount(),
            'totalAnakAsuh' => $this->anakAsuhService->getTotalCount(),
            'totalCabang' => $this->rumahHarapanService->getTotalCount(),
            'totalActivity' => $this->getRecentActivityCount(),
        ];
    }

    /**
     * Get recent audit log activities.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities(int $limit = 5)
    {
        return AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get count of activities in the last 24 hours.
     *
     * @return int
     */
    private function getRecentActivityCount(): int
    {
        return AuditLog::where('created_at', '>=', now()->subHours(24))->count();
    }
}