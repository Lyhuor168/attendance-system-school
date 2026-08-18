<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Messages') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden divide-y">
                @forelse ($students as $student)
                    <a href="{{ route('chat.show', $student) }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                        <div>
                            <div class="font-medium text-gray-800">{{ $student->name }}</div>
                            <div class="text-sm text-gray-500">{{ $student->schoolClass->name }}</div>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">{{ __('No conversations available yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
