<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalysisFilterRequest;
use App\Services\AnalysisService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    protected $analysisService;

    /**
     * Create a new AnalysisController instance.
     *
     * @param AnalysisService $analysisService
     */
    public function __construct(AnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Display a listing of the resource (Analysis Dashboard).
     *
     * @param AnalysisFilterRequest $request
     * @return View
     */
    public function index(AnalysisFilterRequest $request): View
    {
        $data = $this->analysisService->getDashboardData($request->validated());

        return view('analysis.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(404);
    }
}
