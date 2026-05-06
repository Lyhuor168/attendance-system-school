<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classes;

class TeacherController extends Controller
{
    // Dashboard — កត់វត្តមានថ្ងៃនេះ
    public function index()
    {
        $classes = Classes::all();
        return view('teacher.dashboard', compact('classes'));
    }

    // Store — រក្សាទុកវត្តមាន
    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'date'       => 'required|date',
            'attendance' => 'required|array',
        ]);

        foreach ($request->attendance as $student_id => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student_id,
                    'class_id'   => $request->class_id,
                    'date'       => $request->date,
                ],
                [
                    'status'     => $status,
                    'teacher_id' => Auth::id(),
                ]
            );
        }

        return redirect()->back()->with('success', 'វត្តមានត្រូវបានរក្សាទុក!');
    }

    // Edit — កែវត្តមានចាស់
    public function edit()
    {
        $classes = Classes::all();
        $records = Attendance::with(['student.user', 'class'])
            ->where('teacher_id', Auth::id())
            ->orderBy('date', 'desc')
            ->take(20)
            ->get();

        return view('teacher.edit', compact('classes', 'records'));
    }

    // Update — Save ការកែ
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'វត្តមានត្រូវបានកែ!');
    }

    // Report — Print
    public function report()
    {
        $classes = Classes::all();
        $records = Attendance::with(['student.user', 'class'])
            ->where('teacher_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return view('teacher.report', compact('classes', 'records'));
    }

    // Get Students by Class (AJAX)
    public function getStudents($class_id)
    {
        $students = Student::with('user')
            ->where('class_id', $class_id)
            ->get()
            ->map(function ($s) {
                return [
                    'id'   => $s->id,
                    'name' => $s->user->name ?? '-',
                ];
            });

        return response()->json($students);
    }
}