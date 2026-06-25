<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the reports and the report generation form.
     */
    public function index(Request $request): Response
    {
        $reports = $this->reportService->getForUser($request->user());

        return Inertia::render('Reports/Index', [
            'reports' => $reports
        ]);
    }

    /**
     * Generate and store a newly created report in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->reportService->generate(
            $request->user(),
            $request->input('start_date'),
            $request->input('end_date')
        );

        return redirect()->back()->with('success', 'Report generated successfully.');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report, Request $request): RedirectResponse
    {
        // Ensure the user owns the report
        if ($report->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->reportService->delete($report);

        return redirect()->back()->with('success', 'Report deleted successfully.');
    }
}
