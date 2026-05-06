<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Classes;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalStudents   = Student::count();
        $totalTeachers   = User::where('role', 'teacher')->count();
        $todayAttendance = Attendance::whereDate('date', today())->count();
        $absentToday     = Attendance::whereDate('date', today())
                            ->where('status', 'absent')->count();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'todayAttendance',
            'absentToday'
        ));
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function classes()
    {
        $classes = Classes::withCount('students')->get();
        return view('admin.classes', compact('classes'));
    }

    public function storeClass(Request $request)
    {
        $request->validate(['name' => 'required']);
        Classes::create([
            'name'    => $request->name,
            'subject' => $request->subject,
            'teacher' => $request->teacher,
        ]);
        return redirect()->route('admin.classes')
            ->with('success', 'ថ្នាក់ត្រូវបានបន្ថែម!');
    }

    public function updateClass(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        Classes::findOrFail($id)->update([
            'name'    => $request->name,
            'subject' => $request->subject,
            'teacher' => $request->teacher,
        ]);
        return redirect()->route('admin.classes')
            ->with('success', 'ថ្នាក់ត្រូវបានកែ!');
    }

    public function destroyClass($id)
    {
        Classes::findOrFail($id)->delete();
        return redirect()->route('admin.classes')
            ->with('success', 'ថ្នាក់ត្រូវបានលុប!');
    }

    public function report()
    {
        $records = Attendance::with(['student.user', 'class'])
            ->orderBy('date', 'desc')
            ->get();

        $classes = Classes::all();
        $total   = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $late    = $records->where('status', 'late')->count();

        return view('admin.report', compact(
            'records', 'classes',
            'total', 'present', 'absent', 'late'
        ));
    }

    public function students()
    {
        $students = Student::with(['user', 'class'])->get();
        $classes  = Classes::all();
        return view('admin.students', compact('students', 'classes'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name'         => 'required',
            'email'        => 'required|email|unique:users',
            'student_code' => 'required|unique:students',
            'class_id'     => 'required|exists:classes,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password ?? 'student1234'),
            'role'     => 'student',
        ]);

        Student::create([
            'user_id'      => $user->id,
            'student_code' => $request->student_code,
            'class_id'     => $request->class_id,
        ]);

        return redirect()->route('admin.students')
            ->with('success', 'សិស្សត្រូវបានបន្ថែម!');
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update(['class_id' => $request->class_id]);
        $student->user->update(['name' => $request->name]);

        return redirect()->route('admin.students')
            ->with('success', 'សិស្សត្រូវបានកែ!');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->user->delete();
        $student->delete();

        return redirect()->route('admin.students')
            ->with('success', 'សិស្សត្រូវបានលុប!');
    }
}