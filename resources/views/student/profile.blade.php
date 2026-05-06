@extends('layouts.app')
@section('title', 'Profile')
@section('content')

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
    ✅ {{ session('success') }}
</div>
@endif

<h1 class="text-xl font-bold text-gray-800 mb-6">👤 Profile ខ្លួនឯង</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl shadow-sm p-6 text-center">
        {{-- Avatar --}}
        <div class="w-20 h-20 rounded-full bg-yellow-100 text-yellow-700 text-3xl
                    flex items-center justify-center mx-auto mb-4 font-medium">
            {{ mb_substr(Auth::user()->name, 0, 1) }}
        </div>
        <h2 class="font-semibold text-gray-800 text-lg">{{ $user->name }}</h2>
        <p class="text-gray-500 text-sm mb-4">{{ $user->email }}</p>

        <div class="space-y-2 text-left">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Role</span>
                <span class="text-sm font-medium">
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">
                        និស្សិត
                    </span>
                </span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">លេខសម្គាល់</span>
                <span class="text-sm font-medium">{{ $student->student_code ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">ថ្នាក់</span>
                <span class="text-sm font-medium">
                    @if($student && $student->class)
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">
                            {{ $student->class->name }}
                        </span>
                    @else
                        <span class="text-gray-400">មិនទាន់មានថ្នាក់</span>
                    @endif
                </span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-sm text-gray-500">ចូលរួមពី</span>
                <span class="text-sm font-medium">
                    {{ $user->created_at->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="md:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-semibold text-gray-700 mb-5">✏️ កែ Profile</h2>

        <form method="POST" action="{{ route('student.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">ឈ្មោះ</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-yellow-400
                           @error('name') border-red-400 @enderror" required>
                @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">អ៊ីមែល</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-yellow-400
                           @error('email') border-red-400 @enderror" required>
                @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-5 border-gray-200">
            <p class="text-sm text-gray-400 mb-4">
                បំពេញតែប្រសិនបើចង់ប្តូរ Password
            </p>

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Password ថ្មី</label>
                <input type="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="យ៉ាងហោចណាស់ 8 តួ">
                @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm text-gray-600 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="វាយម្តងទៀត">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-2.5
                           rounded-xl text-sm font-medium">
                    រក្សាទុក
                </button>
            </div>
        </form>
    </div>

</div>

@endsection