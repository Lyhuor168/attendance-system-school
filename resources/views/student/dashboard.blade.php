@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('content')

<h1 class="text-xl font-bold text-gray-800 mb-1">វត្តមានខ្លួនឯង</h1>
<p class="text-sm text-gray-500 mb-6">{{ now()->format('d/m/Y') }}</p>


<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-400">
        <p class="text-2xl font-bold text-gray-700">{{ $total }}</p>
        <p class="text-sm text-gray-500 mt-1">សរុប</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $present }}</p>
        <p class="text-sm text-gray-500 mt-1">Present</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">{{ $absent }}</p>
        <p class="text-sm text-gray-500 mt-1">Absent</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $late }}</p>
        <p class="text-sm text-gray-500 mt-1">Late</p>
    </div>
</div>


<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <div class="flex justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">វត្តមានសរុបទាំងអស់</span>
        <span class="text-sm font-bold {{ $percent >= 80 ? 'text-green-600' : 'text-red-600' }}">
            {{ $percent }}%
        </span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3">
        <div class="h-3 rounded-full transition-all {{ $percent >= 80 ? 'bg-green-500' : 'bg-red-500' }}"
             style="width: {{ $percent }}%"></div>
    </div>
    @if($percent < 80)
    <p class="text-xs text-red-500 mt-2">⚠️ វត្តមានក្រោម 80% — សូមប្រុងប្រយ័ត្ន!</p>
    @else
    <p class="text-xs text-green-500 mt-2">✅ វត្តមានល្អ!</p>
    @endif
</div>


<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">📊 % វត្តមានតាមមុខវិជ្ជា</h2>
    @forelse($bySubject as $item)
    <div class="mb-4">
        <div class="flex justify-between mb-1">
            <div>
                <span class="text-sm font-medium text-gray-700">{{ $item['subject'] }}</span>
                <span class="text-xs text-gray-400 ml-2">({{ $item['class'] }})</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-green-600">✓ {{ $item['present'] }}</span>
                <span class="text-xs text-red-500">✗ {{ $item['absent'] }}</span>
                <span class="text-xs text-yellow-500">! {{ $item['late'] }}</span>
                <span class="text-sm font-bold {{ $item['percent'] >= 80 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $item['percent'] }}%
                </span>
            </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full {{ $item['percent'] >= 80 ? 'bg-green-500' : 'bg-red-500' }}"
                 style="width: {{ $item['percent'] }}%"></div>
        </div>
    </div>
    @empty
    <p class="text-gray-400 text-sm text-center py-4">មិនទាន់មានទិន្នន័យ</p>
    @endforelse
</div>

{{-- History Table --}}
<div class="bg-white rounded-xl shadow-sm p-5">
    <h2 class="font-semibold text-gray-700 mb-4">ប្រវត្តិវត្តមាន</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b">
                <th class="pb-2">ថ្ងៃ</th>
                <th class="pb-2">មុខវិជ្ជា</th>
                <th class="pb-2">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
        @forelse($attendances as $att)
        <tr class="border-b last:border-0 hover:bg-gray-50">
            <td class="py-2">{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
            <td class="py-2">{{ $att->class->subject ?? $att->class->name ?? '-' }}</td>
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
        <tr><td colspan="3" class="py-4 text-center text-gray-400">មិនទាន់មានទិន្នន័យ</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection