@extends('layouts.app')
@section('title', 'ស្នើសុំច្បាប់')
@section('content')

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">❌ {{ session('error') }}</div>
@endif

<h1 class="text-xl font-bold text-gray-800 mb-6">📋 ស្នើសុំច្បាប់អវត្តមាន</h1>

<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">+ ស្នើសុំថ្មី</h2>
    <form method="POST" action="{{ route('student.permissions.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm text-gray-600 mb-1">ឈ្មោះសិស្ស</label>
            <input type="text" value="{{ Auth::user()->name }}" readonly
                class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>
        <div class="mb-4">
            <label class="block text-sm text-gray-600 mb-1">ថ្នាក់</label>
            <select name="class_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="">-- ជ្រើស ថ្នាក់ --</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">ពីថ្ងៃ</label>
                <input type="date" name="date_from" value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">ដល់ថ្ងៃ</label>
                <input type="date" name="date_to" value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm text-gray-600 mb-1">មូលហេតុ</label>
            <textarea name="reason" rows="3" placeholder="ពន្យល់មូលហេតុអវត្តមាន..."
                class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-xl text-sm font-medium">
                ស្នើសុំ
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm p-5">
    <h2 class="font-semibold text-gray-700 mb-4">ប្រវត្តិស្នើសុំ</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b">
                <th class="pb-3">ឈ្មោះ</th>
                <th class="pb-3">ពីថ្ងៃ</th>
                <th class="pb-3">ដល់ថ្ងៃ</th>
                <th class="pb-3">ថ្នាក់</th>
                <th class="pb-3">មូលហេតុ</th>
                <th class="pb-3">ស្ថានភាព</th>
                <th class="pb-3">Teacher Note</th>
            </tr>
        </thead>
        <tbody>
        @forelse($requests as $req)
        <tr class="border-b last:border-0 hover:bg-gray-50">
            <td class="py-2 font-medium">{{ Auth::user()->name }}</td>
            <td class="py-2">{{ \Carbon\Carbon::parse($req->date_from ?? $req->date)->format('d/m/Y') }}</td>
            <td class="py-2">{{ $req->date_to ? \Carbon\Carbon::parse($req->date_to)->format('d/m/Y') : '-' }}</td>
            <td class="py-2">{{ $req->class->name ?? '-' }}</td>
            <td class="py-2">{{ $req->reason }}</td>
            <td class="py-2">
                @if($req->status == 'pending')
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">⏳ Pending</span>
                @elseif($req->status == 'approved')
                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">✅ Approved</span>
                @else
                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">❌ Rejected</span>
                @endif
            </td>
            <td class="py-2 text-gray-500">{{ $req->teacher_note ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="py-4 text-center text-gray-400">មិនទាន់មាន Request ទេ</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
