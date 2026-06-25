<?php

namespace App\Exports;

use App\Models\Report;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function view(): View
    {
        $transactions = Transaction::with('category')
            ->where('user_id', $this->report->user_id)
            ->whereBetween('date', [$this->report->start_date, $this->report->end_date])
            ->orderBy('date', 'asc')
            ->get();

        return view('exports.report', [
            'report' => $this->report,
            'transactions' => $transactions
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
            4    => ['font' => ['bold' => true]],
        ];
    }
}
