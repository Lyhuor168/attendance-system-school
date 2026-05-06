@extends('layouts.app')
@section('title', 'Permission Requests')
@section('content')

<h1 class="text-xl font-bold text-gray-800 mb-6">📋 Permission Requests សិស្ស</h1>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm p-5">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-400 border-b">
                <th class="pb-3">សិស្ស</th>
                <th class="pb-3">ថ្នាក់</th>
                <th class="pb-3">ថ្ងៃ</th>
                <th class="pb-3">មូលហេតុ</th>
                <th class="pb-3">ស្ថានភាព</th>
                <th class="pb-3">Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($requests as $req)
        <tr class="border-b last:border-0 hover:bg-gray-50">
            <td class="py-3 font-medium">{{ $req->student->user->name ?? '-' }}</td>
            <td class="py-3">{{ $req->class->name ?? '-' }}</td>
            <td class="py-3">{{ \Carbon\Carbon::parse($req->date)->format('d/m/Y') }}</td>
            <td class="py-3">{{ $req->reason }}</td>
            <td class="py-3">
                @if($req->status == 'pending')
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">⏳ Pending</span>
                @elseif($req->status == 'approved')
                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">✅ Approved</span>
                @else
                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">❌ Rejected</span>
                @endif
            </td>
            <td class="py-3">
                @if($req->status == 'pending')
                <form method="POST" action="{{ route('teacher.permissions.update', $req->id) }}"
                    class="flex gap-2 items-center">
                    @csrf
                    @method('PUT')
                    <input type="text" name="teacher_note" placeholder="Note..."
                        class="border border-gray-300 rounded-lg px-2 py-1 text-xs w-28 focus:outline-none">
                    <button type="submit" name="status" value="approved"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs">
                        ✅ Approve
                    </button>
                    <button type="submit" name="status" value="rejected"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs">
                        ❌ Reject
                    </button>
                </form>
                @else
                    <span class="text-gray-400 text-xs">{{ $req->teacher_note ?? '-' }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="py-8 text-center text-gray-400">មិនទាន់មាន Request ទេ</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection