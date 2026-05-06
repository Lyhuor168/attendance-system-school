<?php
namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'មិនមាន Student profile! សូមទាក់ទង Admin!');
        }

        $requests = PermissionRequest::where('student_id', $student->id)
            ->with('class')
            ->orderBy('created_at', 'desc')
            ->get();

        $classes = Classes::all();

        return view('student.permissions', compact('requests', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date'     => 'required|date',
            'reason'   => 'required|min:5',
        ]);

        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->back()->with('error', 'មិនមាន Student profile!');
        }

        $exists = PermissionRequest::where('student_id', $student->id)
            ->where('class_id', $request->class_id)
            ->where('date', $request->date)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'អ្នកបានស្នើសុំថ្ងៃនេះហើយ!');
        }

        PermissionRequest::create([
            'student_id' => $student->id,
            'class_id'   => $request->class_id,
            'date'       => $request->date,
            'reason'     => $request->reason,
            'status'     => 'pending',
        ]);

        return redirect()->back()
            ->with('success', 'ស្នើសុំច្បាប់បានជោគជ័យ! រង់ចាំ Teacher Approve!');
    }

    public function teacherIndex()
    {
        $requests = PermissionRequest::with(['student.user', 'class'])
            ->orderBy('status')
            ->orderBy('date', 'desc')
            ->get();

        return view('teacher.permissions', compact('requests'));
    }

    public function update(Request $request, $id)
    {
        $permission = PermissionRequest::findOrFail($id);
        $permission->update([
            'status'       => $request->status,
            'teacher_note' => $request->teacher_note,
        ]);

        if ($request->status === 'approved') {
            \App\Models\Attendance::updateOrCreate(
                [
                    'student_id' => $permission->student_id,
                    'class_id'   => $permission->class_id,
                    'date'       => $permission->date,
                ],
                [
                    'status'     => 'present',
                    'teacher_id' => Auth::id(),
                ]
            );
        }

        return redirect()->back()
            ->with('success', $request->status === 'approved' ? 'Approved!' : 'Rejected!');
    }
}
