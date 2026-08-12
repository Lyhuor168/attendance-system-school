<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Timetable') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('timetables.create') }}">
                    <x-primary-button type="button">{{ __('Add Timetable Entry') }}</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">{{ __('Class') }}</th>
                            <th class="px-6 py-3">{{ __('Subject') }}</th>
                            <th class="px-6 py-3">{{ __('Teacher') }}</th>
                            <th class="px-6 py-3">{{ __('Day') }}</th>
                            <th class="px-6 py-3">{{ __('Time') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($timetables as $timetable)
                            <tr>
                                <td class="px-6 py-4">{{ $timetable->schoolClass->name }}</td>
                                <td class="px-6 py-4">{{ $timetable->subject->name }}</td>
                                <td class="px-6 py-4">{{ $timetable->teacher->user->name }}</td>
                                <td class="px-6 py-4">{{ ucfirst($timetable->day_of_week->value) }}</td>
                                <td class="px-6 py-4">{{ $timetable->start_time->format('H:i') }} - {{ $timetable->end_time->format('H:i') }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('timetables.edit', $timetable) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                                    <form action="{{ route('timetables.destroy', $timetable) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this entry?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-gray-500">{{ __('No timetable entries yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $timetables->links() }}
        </div>
    </div>
</x-app-layout>
