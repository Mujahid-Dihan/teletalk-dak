<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FileMovement; // আপনার অডিট লগ মডেল
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'daily'); // Default হচ্ছে daily
        $query = FileMovement::with(['dakFile', 'user', 'fromDepartment', 'toDepartment'])->latest();

        // ফিল্টারিং লজিক
        if ($type === 'daily') {
            $query->whereDate('created_at', Carbon::today());
            $title = "Daily Audit Report (" . Carbon::today()->format('d M, Y') . ")";
        } elseif ($type === 'weekly') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $title = "Weekly Audit Report (" . Carbon::now()->startOfWeek()->format('d M') . " - " . Carbon::now()->endOfWeek()->format('d M, Y') . ")";
        } elseif ($type === 'monthly') {
            $query->whereMonth('created_at', Carbon::now()->month);
            $title = "Monthly Audit Report (" . Carbon::now()->format('F, Y') . ")";
        }

        $logs = $query->get();

        // যদি ইউজার Download PDF এ ক্লিক করে
        if ($request->has('download') && $request->download == 'pdf') {
            $pdf = Pdf::loadView('admin.reports.pdf', compact('logs', 'title', 'type'));
            return $pdf->download('Teletalk_Audit_Report_'.$type.'.pdf');
        }

        // ওয়েবসাইটে দেখানোর জন্য
        return view('admin.reports.index', compact('logs', 'title', 'type'));
    }
}
