<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat about') }} {{ $student->name }}
            @if ($otherParty)
                <span class="text-sm font-normal text-gray-500">{{ __('with') }} {{ $otherParty->name }}</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('chat.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; {{ __('Back to messages') }}</a>

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-3 max-h-[28rem] overflow-y-auto">
                @forelse ($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs rounded-lg px-4 py-2 {{ $message->sender_id === auth()->id() ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                            <p class="text-sm">{{ $message->message }}</p>
                            <p class="mt-1 text-xs {{ $message->sender_id === auth()->id() ? 'text-indigo-200' : 'text-gray-400' }}">{{ $message->created_at->format('M j, g:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">{{ __('No messages yet. Say hello!') }}</p>
                @endforelse
            </div>

            @if ($otherParty)
                <form method="POST" action="{{ route('chat.store', $student) }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="message" placeholder="{{ __('Type a message...') }}" class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required autofocus>
                    <x-primary-button>{{ __('Send') }}</x-primary-button>
                </form>
                @error('message')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            @else
                <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-4 text-sm">
                    {{ __('No one is available to message about this student yet.') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
