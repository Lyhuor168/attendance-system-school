<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Navbar --}}
<nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
    <div class="flex items-center gap-3">
        <div class="bg-blue-100 p-2 rounded-lg">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7
                       a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
        </div>
        <span class="font-bold text-gray-800">Attendance System</span>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500">👤 {{ Auth::user()->name }}</span>
        <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full">Admin</span>
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
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 font-medium text-sm">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.users') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm">
                👥 គ្រប់គ្រង Users
            </a>
            <a href="{{ route('admin.classes') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm">
                🏫 គ្រប់គ្រង Classes
            </a>
            <a href="{{ route('admin.report') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm">
                📈 Report វត្តមាន
            </a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-1">Admin Dashboard</h1>
        <p class="text-sm text-gray-500 mb-6">{{ now()->format('d/m/Y') }}</p>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
                <p class="text-2xl font-bold text-blue-600">{{ $totalStudents }}</p>
                <p class="text-sm text-gray-500 mt-1">សិស្សទាំងអស់</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
                <p class="text-2xl font-bold text-green-600">{{ $totalTeachers }}</p>
                <p class="text-sm text-gray-500 mt-1">គ្រូបង្រៀន</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
                <p class="text-2xl font-bold text-yellow-600">{{ $todayAttendance }}</p>
                <p class="text-sm text-gray-500 mt-1">វត្តមានថ្ងៃនេះ</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
                <p class="text-2xl font-bold text-red-600">{{ $absentToday }}</p>
                <p class="text-sm text-gray-500 mt-1">អវត្តមានថ្ងៃនេះ</p>
            </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="font-semibold text-gray-700 mb-4">វត្តមានថ្មីៗ</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b">
                        <th class="pb-2">សិស្ស</th>
                        <th class="pb-2">ថ្នាក់</th>
                        <th class="pb-2">ឆ្នាំ-ខែ-ថ្ងៃ</th>
                        <th class="pb-2">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                @forelse(App\Models\Attendance::with(['student.user','class'])
                    ->latest()->take(5)->get() as $att)
                <tr class="border-b last:border-0 hover:bg-gray-50">
                    <td class="py-2">{{ $att->student->user->name ?? '-' }}</td>
                    <td class="py-2">{{ $att->class->name ?? '-' }}</td>
                    <td class="py-2">{{ $att->date }}</td>
                    <td class="py-2">
                        @if($att->status == 'present')
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Present</span>
                        @elseif($att->status == 'absent')
                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Absent</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">Late</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400">មិនទាន់មានទិន្នន័យ</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
