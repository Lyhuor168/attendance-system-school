<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Attendance System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Navbar --}}
<nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
    <div class="flex items-center gap-3">
        <div class="bg-blue-100 p-2 rounded-lg">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
            </svg>
        </div>
        <span class="font-bold text-gray-800">Attendance System</span>
        @php
            $pendingCount = 0;
            if(Auth::check() && Auth::user()->role == 'teacher') {
                $pendingCount = App\Models\PermissionRequest::where('status','pending')->count();
            }
            if(Auth::check() && Auth::user()->role == 'student') {
                $student = Auth::user()->student;
                $pendingCount = $student ? App\Models\PermissionRequest::where('student_id', $student->id)->where('status','pending')->count() : 0;
            }
        @endphp
    </div>
    <div class="flex items-center gap-4">
        {{-- Notification Bell --}}
        @if(Auth::check() && Auth::user()->role == 'teacher' && $pendingCount > 0)
        <a href="{{ route('teacher.permissions') }}"
           class="relative flex items-center text-gray-500 hover:text-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                {{ $pendingCount }}
            </span>
        </a>
        @endif
        <span class="text-sm text-gray-500">👤 {{ Auth::user()->name }}</span>
        @php $role = Auth::user()->role; @endphp
        <span class="text-xs px-3 py-1 rounded-full
            {{ $role == 'admin' ? 'bg-blue-100 text-blue-700' :
              ($role == 'teacher' ? 'bg-green-100 text-green-700' :
               'bg-yellow-100 text-yellow-700') }}">
            {{ ucfirst($role) }}
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-red-500 hover:text-red-700">Logout</button>
        </form>
    </div>
</nav>

<div class="flex">
    {{-- Sidebar --}}
    <aside class="w-56 bg-white min-h-screen shadow-sm py-6 px-4">
        <p class="text-xs text-gray-400 uppercase font-semibold mb-3">Menu</p>
        <nav class="flex flex-col gap-1">

            @if(Auth::user()->role == 'admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.users') }}"
                   class="nav-item {{ request()->is('admin/users') ? 'active' : '' }}">
                    👥 គ្រប់គ្រង Users
                </a>
                <a href="{{ route('admin.students') }}"
                   class="nav-item {{ request()->is('admin/students') ? 'active' : '' }}">
                    👨‍🎓 គ្រប់គ្រង Students
                </a>
                <a href="{{ route('admin.classes') }}"
                   class="nav-item {{ request()->is('admin/classes') ? 'active' : '' }}">
                    🏫 គ្រប់គ្រង Classes
                </a>
                <a href="{{ route('admin.report') }}"
                   class="nav-item {{ request()->is('admin/report') ? 'active' : '' }}">
                    📈 Report វត្តមាន
                </a>

            @elseif(Auth::user()->role == 'teacher')
                <a href="{{ route('teacher.dashboard') }}"
                   class="nav-item {{ request()->is('teacher/dashboard') ? 'active' : '' }}">
                    📋 កត់វត្តមានថ្ងៃនេះ
                </a>
                <a href="{{ route('teacher.edit') }}"
                   class="nav-item {{ request()->is('teacher/edit') ? 'active' : '' }}">
                    ✏️ កែវត្តមានចាស់
                </a>
                <a href="{{ route('teacher.report') }}"
                   class="nav-item {{ request()->is('teacher/report') ? 'active' : '' }}">
                    🖨️ Print Report
                </a>
                <a href="{{ route('teacher.permissions') }}"
                   class="nav-item {{ request()->is('teacher/permissions') ? 'active' : '' }}">
                    📋 Permission Requests
                    @if($pendingCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                        {{ $pendingCount }}
                    </span>
                    @endif
                </a>

            @elseif(Auth::user()->role == 'student')
                <a href="{{ route('student.dashboard') }}"
                   class="nav-item {{ request()->is('student/dashboard') ? 'active' : '' }}">
                    📊 វត្តមានខ្លួនឯង
                </a>
                <a href="{{ route('student.profile') }}"
                   class="nav-item {{ request()->is('student/profile') ? 'active' : '' }}">
                    👤 Profile ខ្លួនឯង
                </a>
                <a href="{{ route('student.permissions') }}"
                   class="nav-item {{ request()->is('student/permissions') ? 'active' : '' }}">
                    📋 ស្នើសុំច្បាប់
                    @if($pendingCount > 0)
                    <span class="ml-auto bg-yellow-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                        {{ $pendingCount }}
                    </span>
                    @endif
                </a>
            @endif

        </nav>
    </aside>

    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

<style>
.nav-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    border-radius: 8px;
    font-size: 13px;
    color: #6B7280;
    text-decoration: none;
}
.nav-item:hover { background: #F3F4F6; color: #111827; }
.nav-item.active { background: #EFF6FF; color: #1D4ED8; font-weight: 500; }
</style>

    @stack('scripts')
</body>
</html>

