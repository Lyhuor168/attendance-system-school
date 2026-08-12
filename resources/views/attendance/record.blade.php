<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record Attendance') }} — {{ $schoolClass->name }} ({{ $date->toFormattedDateString() }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('attendance.store', $schoolClass) }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date->toDateString() }}">

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">{{ __('Student') }}</th>
                                @foreach ($statuses as $status)
                                    <th class="px-4 py-3 text-center">{{ ucfirst($status->value) }}</th>
                                @endforeach
                                <th class="px-6 py-3">{{ __('Remarks') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($students as $index => $student)
                                <tr>
                                    <td class="px-6 py-4">
                                        {{ $student->name }}
                                        <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    @foreach ($statuses as $status)
                                        <td class="px-4 py-4 text-center">
                                            <input
                                                type="radio"
                                                name="attendance[{{ $index }}][status]"
                                                value="{{ $status->value }}"
                                                @checked(old("attendance.$index.status", $student->existingRecord?->status?->value ?? 'present') === $status->value)
                                                required
                                            >
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4">
                                        <input
                                            type="text"
                                            name="attendance[{{ $index }}][remarks]"
                                            value="{{ old("attendance.$index.remarks", $student->existingRecord?->remarks) }}"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-6">
                    <x-primary-button>{{ __('Save Attendance') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
