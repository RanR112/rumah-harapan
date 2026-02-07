<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the dashboard overview.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = $this->dashboardService->getDashboardStats();
        $recentActivities = $this->dashboardService->getRecentActivities();

        return view('pages.home', array_merge($stats, [
            'recentActivities' => $recentActivities
        ]));
    }
}