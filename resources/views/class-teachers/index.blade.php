<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Assign Teachers') }} &mdash; {{ $schoolClass->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">{{ __('Teacher') }}</th>
                            <th class="px-6 py-3">{{ __('Subject') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td class="px-6 py-4">{{ $assignment->teacher->user->name }}</td>
                                <td class="px-6 py-4">{{ $assignment->subject->name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('class-teachers.destroy', $assignment) }}" method="POST" onsubmit="return confirm('{{ __('Remove this assignment?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Remove') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-gray-500">{{ __('No teachers assigned to this class yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('Assign a Teacher') }}</h3>
                <form method="POST" action="{{ route('class-teachers.store', $schoolClass) }}" class="flex flex-wrap items-end gap-4">
                    @csrf

                    <div>
                        <x-input-label for="teacher_id" :value="__('Teacher')" />
                        <select id="teacher_id" name="teacher_id" class="mt-1 block w-56 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">{{ __('Select a teacher') }}</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->user->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="subject_id" :value="__('Subject')" />
                        <select id="subject_id" name="subject_id" class="mt-1 block w-56 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">{{ __('Select a subject') }}</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
                    </div>

                    <x-primary-button>{{ __('Assign') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
