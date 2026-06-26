<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize('delete', $report);

        $this->reportService->delete($report);

        return redirect()->back()->with('success', 'Report deleted successfully.');
    }

    /**
     * Export the report in the specified format.
     */
    public function export(Report $report, string $format, Request $request)
    {
        Gate::authorize('view', $report);

        $fileName = 'Report_' . $report->start_date . '_to_' . $report->end_date;

        switch ($format) {
            case 'xlsx':
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportExport($report), $fileName . '.xlsx');
            case 'csv':
                                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportExport($report), $fileName . '.csv', Excel::CSV);

            case 'pdf':
                $transactions = \App\Models\Transaction::with('category')
                    ->where('user_id', $report->user_id)
                    ->whereBetween('date', [$report->start_date, $report->end_date])
                    ->orderBy('date', 'asc')
                    ->get();

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.report_pdf', [
                    'report' => $report,
                    'transactions' => $transactions
                ]);
                return $pdf->download($fileName . '.pdf');
            default:
                abort(404);
        }
    }
}
