<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance') }} — {{ $schoolClass->name }} ({{ $date }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">{{ __('Student') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Remarks') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($records as $record)
                            <tr>
                                <td class="px-6 py-4">{{ $record->student->name }}</td>
                                <td class="px-6 py-4">{{ ucfirst($record->status->value) }}</td>
                                <td class="px-6 py-4">{{ $record->remarks }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-gray-500">{{ __('No attendance recorded for this date.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
