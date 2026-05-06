@extends('layouts.app')
@section('title', 'គ្រប់គ្រង Students')
@section('content')

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
    ✅ {{ session('success') }}
</div>
@endif

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold text-gray-800">👨‍🎓 គ្រប់គ្រង Students</h1>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')"
        class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded-xl text-sm font-medium">
        + បន្ថែម សិស្ស
    </button>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm p-5">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b">
                <th class="pb-3">ឈ្មោះ</th>
                <th class="pb-3">លេខសម្គាល់</th>
                <th class="pb-3">អ៊ីមែល</th>
                <th class="pb-3">ថ្នាក់</th>
                <th class="pb-3">Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($students as $student)
        <tr class="border-b last:border-0 hover:bg-gray-50">
            <td class="py-3 font-medium">{{ $student->user->name ?? '-' }}</td>
            <td class="py-3 text-gray-500">{{ $student->student_code }}</td>
            <td class="py-3 text-gray-500">{{ $student->user->email ?? '-' }}</td>
            <td class="py-3">
                @if($student->class)
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">
                        {{ $student->class->name }}
                    </span>
                @else
                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-xs">
                        មិនទាន់មានថ្នាក់
                    </span>
                @endif
            </td>
            <td class="py-3">
                <div class="flex gap-3">
                    <button onclick="openEdit({{ $student->id }}, '{{ $student->user->name }}', {{ $student->class_id ?? 'null' }})"
                        class="text-blue-500 hover:text-blue-700 text-xs font-medium">
                        កែ
                    </button>
                    <form method="POST"
                        action="{{ route('admin.students.destroy', $student->id) }}"
                        onsubmit="return confirm('លុប សិស្សនេះ?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 text-xs">លុប</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="py-8 text-center text-gray-400">មិនទាន់មានសិស្សទេ</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Add Modal --}}
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <h2 class="text-lg font-bold text-gray-800 mb-4">+ បន្ថែម សិស្សថ្មី</h2>
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-sm text-gray-600 mb-1">ឈ្មោះ</label>
                <input type="text" name="name" placeholder="ឧ: សុខ ដាវីត"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm text-gray-600 mb-1">អ៊ីមែល</label>
                <input type="email" name="email" placeholder="ឧ: student@university.edu"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm text-gray-600 mb-1">លេខសម្គាល់</label>
                <input type="text" name="student_code" placeholder="ឧ: STU006"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm text-gray-600 mb-1">ថ្នាក់</label>
                <select name="class_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="">-- ជ្រើស ថ្នាក់ --</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-600 mb-1">
                    Password <span class="text-gray-400">(default: student1234)</span>
                </label>
                <input type="password" name="password" placeholder="student1234"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button"
                    onclick="document.getElementById('addModal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 rounded-xl text-sm text-gray-600">
                    បោះបង់
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-gray-800 text-white rounded-xl text-sm font-medium">
                    រក្សាទុក
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <h2 class="text-lg font-bold text-gray-800 mb-4">✏️ កែ សិស្ស</h2>
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">ឈ្មោះ</label>
                <input type="text" name="name" id="editName"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-600 mb-1">ថ្នាក់</label>
                <select name="class_id" id="editClass"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button"
                    onclick="document.getElementById('editModal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 rounded-xl text-sm text-gray-600">
                    បោះបង់
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium">
                    រក្សាទុក
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEdit(id, name, classId) {
    document.getElementById('editName').value = name;
    if (classId) document.getElementById('editClass').value = classId;
    document.getElementById('editForm').action = `/admin/students/${id}`;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endpush