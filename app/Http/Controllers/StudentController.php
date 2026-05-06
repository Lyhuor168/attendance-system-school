<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Classes;
use Illuminate\Http\Request;


class StudentController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return view('student.dashboard', [
                'attendances'  => collect(),
                'total'        => 0,
                'present'      => 0,
                'absent'       => 0,
                'late'         => 0,
                'percent'      => 0,
                'bySubject'    => collect(),
            ]);
        }

        $attendances = Attendance::with('class')
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();

        $total   = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent  = $attendances->where('status', 'absent')->count();
        $late    = $attendances->where('status', 'late')->count();
        $percent = $total > 0 ? round(($present / $total) * 100) : 0;

        // % តាមមុខវិជ្ជា
        $bySubject = $attendances->groupBy('class_id')->map(function ($items) {
            $t = $items->count();
            $p = $items->where('status', 'present')->count();
            return [
                'subject'  => $items->first()->class->subject ?? $items->first()->class->name ?? '-',
                'class'    => $items->first()->class->name ?? '-',
                'total'    => $t,
                'present'  => $p,
                'absent'   => $items->where('status', 'absent')->count(),
                'late'     => $items->where('status', 'late')->count(),
                'percent'  => $t > 0 ? round(($p / $t) * 100) : 0,
            ];
        });

        return view('student.dashboard', compact(
            'attendances',
            'total',
            'present',
            'absent',
            'late',
            'percent',
            'bySubject'
        ));
    }
    public function profile()
{
    $user    = Auth::user();
    $student = $user->student;

    return view('student.profile', compact('user', 'student'));
}

public function updateProfile(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name'  => 'required|min:2',
        'email' => 'required|email|unique:users,email,' . $user->id,
    ]);

    $user->update(['updated_at' => now()] + [
        'name'  => $request->name,
        'email' => $request->email,
    ]);

    if ($request->filled('password')) {
        $request->validate([
            'password' => 'min:8|confirmed',
        ]);
        $user->update(['password' => bcrypt($request->password)]);
    }

    return redirect()->back()->with('success', 'Profile ត្រូវបានកែ!');
}
}