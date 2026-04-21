<?php

namespace App\Http\Controllers;

use App\Models\DakFile;
use App\Models\Department;
use App\Models\FileMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DakController extends Controller
{
    // 1. Load the Dashboard
    public function index(Request $request)
    {
        $user = auth()->user();
        $departments = Department::all();

        if ($user->role === 'viewer') {
            // সে শুধুমাত্র তার নিজের খোলা ফাইলগুলো দেখতে পাবে
            $myFiles = DakFile::where('user_id', $user->id)->get();
            return view('dashboards.viewer', compact('myFiles'));
        }

        // Super Admin Dashboard
        if ($user->role === 'super_admin') {
            $allActiveFiles = DakFile::where('status', '!=', 'Completed')->count();
            $departmentWorkloads = DakFile::selectRaw('current_department_id, count(*) as total')
                ->where('status', '!=', 'Completed')
                ->groupBy('current_department_id')
                ->get()
                ->keyBy('current_department_id');
                
            $pendingUsersCount = User::where('is_approved', false)->count();
                
            return view('dashboards.super_admin', compact('departments', 'allActiveFiles', 'departmentWorkloads', 'pendingUsersCount'));
        }

        // Admin (Department Head) Dashboard
        if ($user->role === 'admin') {
            // অ্যাডমিন শুধু তার ডিপার্টমেন্টের ফাইলগুলো দেখবে
            $departmentFiles = DakFile::where('current_department_id', $user->department_id)
                ->where('status', '!=', 'Completed')
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('dashboards.admin', compact('departments', 'departmentFiles'));
        }

        // Staff Dashboard
        if ($user->role === 'staff') {
            // স্টাফ শুধু তার নিজের তৈরি করা ফাইলগুলো দেখবে
            $query = DakFile::where('user_id', $user->id); // Note: ফাইলের মডেলে user_id (initiator) ট্র্যাকিং থাকতে হবে
            
            // Search by Tracking ID
            if ($request->filled('tracking_id')) {
                $query->where('tracking_id', 'like', '%' . $request->tracking_id . '%');
            }
            
            // Filter by exact date
            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }
            
            // Filter by time range
            if ($request->filled('start_time') && $request->filled('end_time')) {
                $query->whereTime('created_at', '>=', $request->start_time)
                      ->whereTime('created_at', '<=', $request->end_time);
            } elseif ($request->filled('start_time')) {
                $query->whereTime('created_at', '>=', $request->start_time);
            } elseif ($request->filled('end_time')) {
                $query->whereTime('created_at', '<=', $request->end_time);
            }

            $myEntries = $query->orderBy('created_at', 'desc')->get();
                
            return view('dashboards.staff', compact('departments', 'myEntries'));
        }

        // Fallback
        abort(403, 'Unauthorized Role.');
    }

    // 2. Bind and Store a New File
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:Normal,High,Urgent',
            'current_department_id' => 'required|exists:departments,id',
        ]);

        // স্বয়ংক্রিয় Tracking ID তৈরি (যেমন: TTBL-20260408-A1B2)
        $trackingId = 'TTBL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        $file = DakFile::create([
            'tracking_id' => $trackingId,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'Pending',
            'origin_department_id' => auth()->user()->department_id,
            'current_department_id' => $request->current_department_id,
            'user_id' => auth()->id(),
        ]);

        // ফাইল মুভমেন্ট ট্র্যাকিং
        FileMovement::create([
            'dak_file_id' => $file->id,
            'user_id' => auth()->id(),
            'from_department_id' => auth()->user()->department_id,
            'to_department_id' => $request->current_department_id,
            'action' => 'Initiated'
        ]);

        // QR কোডের ডাটা সেশনে পাঠিয়ে রিডাইরেক্ট করা
        return back()->with('new_qr_id', $trackingId);
    }

    // 3. Omni-Search for the Barcode Scanner (Returns JSON)
    public function search(Request $request)
    {
        $file = DakFile::with(['originDepartment', 'currentDepartment', 'movements.user', 'movements.toDepartment'])
            ->where('tracking_id', $request->tracking_id)
            ->first();

        if ($file) {
            return response()->json(['success' => true, 'file' => $file]);
        }

        return response()->json(['success' => false, 'message' => 'File not found in the system.']);
    }

    // 4. One-Click Archival
    public function archive(Request $request, $id)
    {
        $request->validate([
            'physical_location' => 'required|string|max:255',
        ]);

        $file = DakFile::findOrFail($id);
        
        // SECURITY CHECK: Abort with a 403 error if the file isn't in their department
        abort_if($file->current_department_id !== auth()->user()->department_id, 403, 'Unauthorized. File is not in your department.');
        abort_if(auth()->user()->role === 'staff', 403, 'Unauthorized. Staff cannot forward files.');
        
        $file->update([
            'status' => 'Completed',
            'physical_location' => $request->physical_location
        ]);

        FileMovement::create([
            'dak_file_id' => $file->id,
            'user_id' => auth()->id(),
            'from_department_id' => $file->current_department_id,
            'to_department_id' => null,
            'action' => 'Completed',
            'remarks' => 'Archived at: ' . $request->physical_location
        ]);

        return back()->with('success', 'File archived and locked successfully.');
    }

    // Forward a file to another department
    public function forward(Request $request, $id)
    {
        $request->validate([
            'target_department_id' => 'required|exists:departments,id',
            'remarks' => 'nullable|string'
        ]);

        $file = DakFile::findOrFail($id);
        
        // SECURITY CHECK: Abort with a 403 error if the file isn't in their department
        abort_if($file->current_department_id !== auth()->user()->department_id, 403, 'Unauthorized. File is not in your department.');
        
        $old_dept = $file->current_department_id;

        // Update the file's current location
        $file->update([
            'current_department_id' => $request->target_department_id,
            'status' => 'In-Transit'
        ]);

        // Log the movement
        FileMovement::create([
            'dak_file_id' => $file->id,
            'user_id' => auth()->id(),
            'from_department_id' => $old_dept,
            'to_department_id' => $request->target_department_id,
            'action' => 'Forwarded',
            'remarks' => $request->remarks
        ]);

        return back()->with('success', 'File forwarded successfully.');
    }

    // Upload a scanned PDF for the file
    public function uploadPdf(Request $request, $id)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        $file = DakFile::findOrFail($id);

        // Security check: The user must be the admin of the current department to attach files, or at least in the department.
        abort_if($file->current_department_id !== auth()->user()->department_id, 403, 'Unauthorized. File is not in your department.');

        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('scanned_pdfs', 'public');
            
            $file->update([
                'scanned_pdf_path' => $path
            ]);

            FileMovement::create([
                'dak_file_id' => $file->id,
                'user_id' => auth()->id(),
                'from_department_id' => $file->current_department_id,
                'to_department_id' => $file->current_department_id,
                'action' => 'Document Scanned',
                'remarks' => 'A PDF scan was attached to this record.'
            ]);

            return response()->json(['success' => true, 'message' => 'PDF scanned and uploaded successfully.', 'path' => $path]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to upload PDF.'], 400);
    }
}
