<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
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
    <aside class="w-56 bg-white min-h-screen shadow-sm py-6 px-4">
        <p class="text-xs text-gray-400 uppercase font-semibold mb-3">Menu</p>
        <nav class="flex flex-col gap-1">
            <a href="{{ route('teacher.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 text-green-700 font-medium text-sm">
                📋 កត់វត្តមានថ្ងៃនេះ
            </a>
            <a href="{{ route('teacher.edit') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm">
                ✏️ កែវត្តមានចាស់
            </a>
            <a href="{{ route('teacher.report') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 text-sm">
                🖨️ Print Report
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-1">កត់វត្តមានថ្ងៃនេះ</h1>
        <p class="text-sm text-gray-500 mb-6">{{ now()->format('d/m/Y') }}</p>

        @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
            ✅ {{ session('success') }}
        </div>
        @endif

        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('teacher.attendance.store') }}">
                @csrf

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">ថ្នាក់ / Class</label>
                        <select name="class_id" id="classSelect"
                            onchange="loadStudents(this.value)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-400">
                            <option value="">-- ជ្រើស ថ្នាក់ --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">ថ្ងៃ / Date</label>
                        <input type="date" name="date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-400">
                        @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div id="studentTable" class="hidden">
                    <table class="w-full text-sm mb-6">
                        <thead>
                            <tr class="text-left text-gray-400 border-b">
                                <th class="pb-2">ឈ្មោះសិស្ស</th>
                                <th class="pb-2 text-center">Present</th>
                                <th class="pb-2 text-center">Absent</th>
                                <th class="pb-2 text-center">Late</th>
                            </tr>
                        </thead>
                        <tbody id="studentRows"></tbody>
                    </table>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-2.5
                                   rounded-xl text-sm font-medium">
                            រក្សាទុក វត្តមាន
                        </button>
                    </div>
                </div>

                {{-- Empty state --}}
                <div id="emptyState" class="text-center py-8 text-gray-400 text-sm">
                    សូមជ្រើស ថ្នាក់ ដើម្បីបង្ហាញ សិស្ស
                </div>

            </form>
        </div>
    </main>
</div>

<script>
function loadStudents(classId) {
    if (!classId) {
        document.getElementById('studentTable').classList.add('hidden');
        document.getElementById('emptyState').classList.remove('hidden');
        return;
    }

    fetch(`/teacher/students/${classId}`)
        .then(res => res.json())
        .then(students => {
            const tbody = document.getElementById('studentRows');
            tbody.innerHTML = '';

            if (students.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-400">
                            មិនមានសិស្សក្នុងថ្នាក់នេះទេ
                        </td>
                    </tr>`;
            } else {
                students.forEach((s, i) => {
                    tbody.innerHTML += `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-green-100 text-green-700
                                                flex items-center justify-center text-xs font-medium">
                                        ${s.name.charAt(0)}
                                    </div>
                                    ${s.name}
                                </div>
                            </td>
                            <td class="py-3 text-center">
                                <input type="radio" name="attendance[${s.id}]"
                                    value="present" checked class="accent-green-600">
                            </td>
                            <td class="py-3 text-center">
                                <input type="radio" name="attendance[${s.id}]"
                                    value="absent" class="accent-red-600">
                            </td>
                            <td class="py-3 text-center">
                                <input type="radio" name="attendance[${s.id}]"
                                    value="late" class="accent-yellow-600">
                            </td>
                        </tr>`;
                });
            }

            document.getElementById('studentTable').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
        })
        .catch(err => console.error(err));
}
</script>

</body>
</html>