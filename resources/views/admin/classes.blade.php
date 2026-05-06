@extends('layouts.app')

@section('title', 'គ្រប់គ្រង Classes')

@section('content')


@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
    ✅ {{ session('success') }}
</div>
@endif

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold text-gray-800">គ្រប់គ្រង Classes</h1>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')"
        class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded-xl text-sm font-medium">
        + បន្ថែម ថ្នាក់
    </button>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm p-5">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b">
                <th class="pb-3">ថ្នាក់</th>
                <th class="pb-3">មុខវិជ្ជា</th>
                <th class="pb-3">គ្រូ</th>
                <th class="pb-3">សិស្ស</th>
                <th class="pb-3">Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($classes as $class)
        <tr class="border-b last:border-0 hover:bg-gray-50">
            <td class="py-3 font-medium">{{ $class->name }}</td>
            <td class="py-3">{{ $class->subject ?? '-' }}</td>
            <td class="py-3">{{ $class->teacher ?? '-' }}</td>
            <td class="py-3">{{ $class->students_count ?? 0 }} នាក់</td>
            <td class="py-3">
                <div class="flex items-center gap-3">
                    <button onclick="openEdit({{ $class->id }}, '{{ $class->name }}', '{{ $class->subject }}', '{{ $class->teacher }}')"
                        class="text-blue-500 hover:text-blue-700 text-xs font-medium">
                        កែ
                    </button>
                    <form method="POST"
                        action="{{ route('admin.classes.destroy', $class->id) }}"
                        onsubmit="return confirm('លុប ថ្នាក់នេះ?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 text-xs">លុប</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="py-8 text-center text-gray-400">មិនទាន់មាន Class ទេ</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Add Modal --}}
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <h2 class="text-lg font-bold text-gray-800 mb-4">+ បន្ថែម ថ្នាក់ថ្មី</h2>
        <form method="POST" action="{{ route('admin.classes.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">ឈ្មោះ ថ្នាក់</label>
                <input type="text" name="name" placeholder="ឧ: A1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">មុខវិជ្ជា</label>
                <input type="text" name="subject" placeholder="ឧ: Computer Science"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-600 mb-1">គ្រូបង្រៀន</label>
                <input type="text" name="teacher" placeholder="ឧ: ចាន់ សុភា"
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
        <h2 class="text-lg font-bold text-gray-800 mb-4">✏️ កែ ថ្នាក់</h2>
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">ឈ្មោះ ថ្នាក់</label>
                <input type="text" name="name" id="editName"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">មុខវិជ្ជា</label>
                <input type="text" name="subject" id="editSubject"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-600 mb-1">គ្រូបង្រៀន</label>
                <input type="text" name="teacher" id="editTeacher"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
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
function openEdit(id, name, subject, teacher) {
    document.getElementById('editName').value = name;
    document.getElementById('editSubject').value = subject || '';
    document.getElementById('editTeacher').value = teacher || '';
    document.getElementById('editForm').action = `/admin/classes/${id}`;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endpush
