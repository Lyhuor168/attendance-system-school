<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\ChatMessage;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Placeholder monthly revenue target, pending a real fee-structure module.
     */
    private const MONTHLY_REVENUE_TARGET = 5000.00;

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard', $this->adminData());
        }

        if ($user->isStudent()) {
            return view('dashboard', $this->studentData($user->student));
        }

        if ($user->isGuardian()) {
            return view('dashboard', $this->guardianData($user->guardian));
        }

        $assignedClasses = $user->teacher
            ? SchoolClass::whereIn('id', $user->teacher->allClassIds())->orderBy('name')->get()
            : collect();

        return view('dashboard', $this->teacherData($assignedClasses));
    }

    /**
     * @return array<string, mixed>
     */
    private function adminData(): array
    {
        $classIds = SchoolClass::pluck('id');

        return [
            'isAdmin' => true,
            'isStudent' => false,
            'isGuardian' => false,
            'kpis' => [
                'totalStudents' => Student::count(),
                'totalStaff' => Teacher::count(),
                'todayAttendancePercentage' => $this->attendancePercentage($classIds, Carbon::today()),
                'monthlyRevenue' => Payment::whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount'),
            ],
            'attendanceTrend' => $this->attendanceTrend($classIds),
            'revenueVsTarget' => $this->revenueVsTarget(),
            'absentToday' => AttendanceRecord::with('student.schoolClass')
                ->whereDate('date', Carbon::today())
                ->where('status', AttendanceStatus::Absent)
                ->get(),
            'recentPayments' => Payment::with('student')->latest('paid_at')->take(5)->get(),
        ];
    }

    /**
     * @param  Collection<int, SchoolClass>  $assignedClasses
     * @return array<string, mixed>
     */
    private function teacherData(Collection $assignedClasses): array
    {
        $classIds = $assignedClasses->pluck('id');

        return [
            'isAdmin' => false,
            'isStudent' => false,
            'isGuardian' => false,
            'assignedClasses' => $assignedClasses,
            'kpis' => [
                'totalStudents' => Student::whereIn('school_class_id', $classIds)->count(),
                'todayAttendancePercentage' => $this->attendancePercentage($classIds, Carbon::today()),
            ],
            'attendanceTrend' => $this->attendanceTrend($classIds),
            'absentToday' => AttendanceRecord::with('student.schoolClass')
                ->whereIn('school_class_id', $classIds)
                ->whereDate('date', Carbon::today())
                ->where('status', AttendanceStatus::Absent)
                ->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function studentData(?Student $student): array
    {
        if ($student === null) {
            return [
                'isAdmin' => false,
                'isStudent' => true,
                'isGuardian' => false,
                'student' => null,
                'kpis' => ['attendancePercentage' => 0, 'totalFeesPaid' => 0],
                'attendanceTrend' => ['labels' => [], 'data' => []],
                'recentAttendance' => collect(),
                'payments' => collect(),
            ];
        }

        return [
            'isAdmin' => false,
            'isStudent' => true,
            'isGuardian' => false,
            'student' => $student,
            'kpis' => [
                'attendancePercentage' => $this->studentAttendancePercentageThisMonth($student),
                'totalFeesPaid' => $student->payments()->sum('amount'),
            ],
            'attendanceTrend' => $this->studentAttendanceTrend($student),
            'recentAttendance' => $student->attendanceRecords()->latest('date')->take(7)->get(),
            'payments' => $student->payments()->latest('paid_at')->take(5)->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function guardianData(?Guardian $guardian): array
    {
        if ($guardian === null) {
            return [
                'isAdmin' => false,
                'isStudent' => false,
                'isGuardian' => true,
                'guardian' => null,
                'children' => collect(),
                'unreadMessages' => 0,
            ];
        }

        return [
            'isAdmin' => false,
            'isStudent' => false,
            'isGuardian' => true,
            'guardian' => $guardian,
            'children' => $guardian->students()->with('schoolClass')->get(),
            'unreadMessages' => ChatMessage::where('receiver_id', $guardian->user_id)
                ->where('is_read', false)
                ->count(),
        ];
    }

    private function studentAttendancePercentageThisMonth(Student $student): int
    {
        $records = $student->attendanceRecords()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        $total = $records->count();

        if ($total === 0) {
            return 0;
        }

        $present = (clone $records)->where('status', AttendanceStatus::Present)->count();

        return (int) round($present / $total * 100);
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function studentAttendanceTrend(Student $student): array
    {
        $labels = [];
        $data = [];

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::today()->subDays($daysAgo);
            $labels[] = $date->format('D');
            $record = $student->attendanceRecords()->whereDate('date', $date)->first();
            $data[] = $record?->status === AttendanceStatus::Present ? 100 : 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @param  Collection<int, int>  $classIds
     */
    private function attendancePercentage(Collection $classIds, Carbon $date): int
    {
        $records = AttendanceRecord::whereIn('school_class_id', $classIds)->whereDate('date', $date);

        $total = $records->count();

        if ($total === 0) {
            return 0;
        }

        $present = (clone $records)->where('status', AttendanceStatus::Present)->count();

        return (int) round($present / $total * 100);
    }

    /**
     * @param  Collection<int, int>  $classIds
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function attendanceTrend(Collection $classIds): array
    {
        $labels = [];
        $data = [];

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::today()->subDays($daysAgo);
            $labels[] = $date->format('D');
            $data[] = $this->attendancePercentage($classIds, $date);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return array{labels: array<int, string>, actual: array<int, float>, target: array<int, float>}
     */
    private function revenueVsTarget(): array
    {
        $labels = [];
        $actual = [];
        $target = [];

        for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
            $month = Carbon::today()->subMonths($monthsAgo);
            $labels[] = $month->format('M');
            $actual[] = (float) Payment::whereMonth('paid_at', $month->month)
                ->whereYear('paid_at', $month->year)
                ->sum('amount');
            $target[] = self::MONTHLY_REVENUE_TARGET;
        }

        return ['labels' => $labels, 'actual' => $actual, 'target' => $target];
    }
}
