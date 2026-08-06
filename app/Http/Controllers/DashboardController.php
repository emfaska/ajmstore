<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the analytics dashboard.
     * Access restricted to Owner and Admin via route middleware.
     */
    public function index()
    {
        $data = $this->dashboardService->getDashboardData();

        return view('dashboard', $data);
    }
}
