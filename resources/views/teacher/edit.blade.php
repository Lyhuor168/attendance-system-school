<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>កែវត្តមានចាស់</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Navbar --}}
<nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
    <div class="flex items-center gap-3">
        <div class="bg-green-100 p-2 rounded-lg">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
            </svg>
        </div>
        <span class="font-bold text-gray-800">Attendance System</span>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500">👤 {{ Auth::user()->name }}</span>
        <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">គ្រូបង្រៀន</span>
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
            <a href="{{ route('teacher.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm">
                📋 កត់វត្តមានថ្ងៃនេះ
            </a>
            <a href="{{ route('teacher.edit') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 text-green-700 font-medium text-sm">
                ✏️ កែវត្តមានចាស់
            </a>
            <a href="{{ route('teacher.report') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm">
                🖨️ Print Report
            </a>
        </nav>
    </aside>

    {{-- Main --}}
    <main class="flex-1 p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-1">កែវត្តមានចាស់</h1>
        <p class="text-sm text-gray-500 mb-6">{{ now()->format('d/m/Y') }}</p>

        {{-- Success --}}
        @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
            ✅ {{ session('success') }}
        </div>
        @endif

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b">
                        <th class="pb-3">ថ្ងៃ</th>
                        <th class="pb-3">សិស្ស</th>
                        <th class="pb-3">ថ្នាក់</th>
                        <th class="pb-3">ស្ថានភាព</th>
                        <th class="pb-3">កែ</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($records as $att)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}
                    </td>
                    <td class="py-3">{{ $att->student->user->name ?? '-' }}</td>
                    <td class="py-3">{{ $att->class->name ?? '-' }}</td>
                    <td class="py-3">
                        @if($att->status == 'present')
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Present</span>
                        @elseif($att->status == 'absent')
                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Absent</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">Late</span>
                        @endif
                    </td>
                    <td class="py-3">
                        {{-- Edit Form --}}
                        <form method="POST"
                            action="{{ route('teacher.attendance.update', $att->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="flex items-center gap-2">
                                <select name="status"
                                    class="border border-gray-300 rounded-lg px-2 py-1 text-xs
                                           focus:outline-none focus:ring-1 focus:ring-green-400">
                                    <option value="present"
                                        {{ $att->status == 'present' ? 'selected' : '' }}>
                                        Present
                                    </option>
                                    <option value="absent"
                                        {{ $att->status == 'absent' ? 'selected' : '' }}>
                                        Absent
                                    </option>
                                    <option value="late"
                                        {{ $att->status == 'late' ? 'selected' : '' }}>
                                        Late
                                    </option>
                                </select>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white
                                           px-3 py-1 rounded-lg text-xs">
                                    រក្សា
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400">
                        មិនទាន់មានទិន្នន័យវត្តមានទេ
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>