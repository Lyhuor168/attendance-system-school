@extends('layouts.app')
@section('title', 'Print Report')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold text-gray-800">🖨️ Print Report វត្តមាន</h1>
    <button onclick="window.print()"
        class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded-xl text-sm font-medium">
        🖨️ Print
    </button>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <div class="flex gap-4 flex-wrap">
        <div>
            <label class="block text-sm text-gray-600 mb-1">ថ្នាក់</label>
            <select id="filterClass" onchange="filterTable()"
                class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none">
                <option value="">ទាំងអស់</option>
                @foreach($classes as $class)
                <option value="{{ $class->name }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">ស្ថានភាព</label>
            <select id="filterStatus" onchange="filterTable()"
                class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none">
                <option value="">ទាំងអស់</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
            </select>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">
            {{ $records->where('status','present')->count() }}
        </p>
        <p class="text-sm text-gray-500">Present</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">
            {{ $records->where('status','absent')->count() }}
        </p>
        <p class="text-sm text-gray-500">Absent</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">
            {{ $records->where('status','late')->count() }}
        </p>
        <p class="text-sm text-gray-500">Late</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm p-5">
    <table class="w-full text-sm" id="reportTable">
        <thead>
            <tr class="text-left text-gray-400 border-b">
                <th class="pb-3">ថ្ងៃ</th>
                <th class="pb-3">សិស្ស</th>
                <th class="pb-3">ថ្នាក់</th>
                <th class="pb-3">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
        @forelse($records as $att)
        <tr class="border-b last:border-0 hover:bg-gray-50 table-row"
            data-class="{{ $att->class->name ?? '' }}"
            data-status="{{ $att->status }}">
            <td class="py-2">
                {{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}
            </td>
            <td class="py-2">{{ $att->student->user->name ?? '-' }}</td>
            <td class="py-2">{{ $att->class->name ?? '-' }}</td>
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
        <tr>
            <td colspan="4" class="py-8 text-center text-gray-400">
                មិនទាន់មានទិន្នន័យ
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
function filterTable() {
    const filterClass  = document.getElementById('filterClass').value.toLowerCase();
    const filterStatus = document.getElementById('filterStatus').value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');

    rows.forEach(row => {
        const cls    = row.dataset.class.toLowerCase();
        const status = row.dataset.status.toLowerCase();
        const matchClass  = !filterClass  || cls.includes(filterClass);
        const matchStatus = !filterStatus || status === filterStatus;
        row.style.display = matchClass && matchStatus ? '' : 'none';
    });
}
</script>

<style>
@media print {
    aside, nav, button, select { display: none !important; }
    .shadow-sm { box-shadow: none !important; }
}
</style>
@endpush