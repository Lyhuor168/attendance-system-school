<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Classes') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('classes.create') }}">
                    <x-primary-button type="button">{{ __('Add Class') }}</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">{{ __('Name') }}</th>
                            <th class="px-6 py-3">{{ __('Grade Level') }}</th>
                            <th class="px-6 py-3">{{ __('Homeroom Teacher') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($classes as $class)
                            <tr>
                                <td class="px-6 py-4">{{ $class->name }}</td>
                                <td class="px-6 py-4">{{ $class->grade_level }}</td>
                                <td class="px-6 py-4">{{ $class->homeroomTeacher?->user?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('class-teachers.index', $class) }}" class="text-indigo-600 hover:underline">{{ __('Teachers') }}</a>
                                    <a href="{{ route('classes.edit', $class) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                                    <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this class?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-gray-500">{{ __('No classes yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $classes->links() }}
        </div>
    </div>
</x-app-layout>
