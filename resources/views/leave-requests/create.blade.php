<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Submit Leave Request') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('leave-requests.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="reason" :value="__('Reason')" />
                        <textarea id="reason" name="reason" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="from_date" :value="__('From Date')" />
                            <x-text-input id="from_date" name="from_date" type="date" class="mt-1 block w-full" :value="old('from_date')" required />
                            <x-input-error :messages="$errors->get('from_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="to_date" :value="__('To Date')" />
                            <x-text-input id="to_date" name="to_date" type="date" class="mt-1 block w-full" :value="old('to_date')" required />
                            <x-input-error :messages="$errors->get('to_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-primary-button>{{ __('Submit') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
