<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Attendance') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">{{ __('Class') }}</th>
                            <th class="px-6 py-3">{{ __('Grade Level') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($classes as $class)
                            <tr>
                                <td class="px-6 py-4">{{ $class->name }}</td>
                                <td class="px-6 py-4">{{ $class->grade_level }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('attendance.create', $class) }}" class="text-indigo-600 hover:underline">{{ __('Record') }}</a>
                                    <a href="{{ route('attendance.show', [$class, today()->toDateString()]) }}" class="text-gray-600 hover:underline">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-gray-500">{{ __('No classes available.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
