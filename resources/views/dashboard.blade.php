<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    {{ session('status') }}
                </div>
            @endif

            @if (auth()->user()->isAdmin())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Teachers') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $counts['teachers'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Subjects') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $counts['subjects'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Classes') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $counts['classes'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Students') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $counts['students'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('students.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Student') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Add / Edit / Delete students') }}</div>
                    </a>
                    <a href="{{ route('teachers.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Teacher') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Add / Edit / Delete teachers') }}</div>
                    </a>
                    <a href="{{ route('classes.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Classes') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Add / Edit / Delete classes') }}</div>
                    </a>
                    <a href="{{ route('subjects.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Subjects') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Add / Edit / Delete subjects') }}</div>
                    </a>
                    <a href="{{ route('attendance.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Attendance') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Record / View attendance') }}</div>
                    </a>
                    <a href="{{ route('timetables.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Timetable') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Add / Edit / Delete timetable entries') }}</div>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('attendance.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Attendance') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Record / View attendance for your class') }}</div>
                    </a>
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <div class="text-lg font-semibold text-gray-900">{{ __('Your class(es)') }}</div>
                        @forelse ($homeroomClasses as $class)
                            <div class="text-sm text-gray-600">{{ $class->name }} ({{ $class->grade_level }})</div>
                        @empty
                            <div class="text-sm text-gray-500">{{ __('No homeroom class assigned yet.') }}</div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
