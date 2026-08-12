<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard', [
                'counts' => [
                    'teachers' => Teacher::count(),
                    'subjects' => Subject::count(),
                    'classes' => SchoolClass::count(),
                    'students' => Student::count(),
                ],
            ]);
        }

        return view('dashboard', [
            'homeroomClasses' => $user->teacher?->homeroomClasses ?? collect(),
        ]);
    }
}
