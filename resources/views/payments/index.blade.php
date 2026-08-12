<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payments') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('payments.create') }}">
                    <x-primary-button type="button">{{ __('Collect Fee') }}</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">{{ __('Student') }}</th>
                            <th class="px-6 py-3">{{ __('Amount') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Paid At') }}</th>
                            <th class="px-6 py-3">{{ __('Reference') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="px-6 py-4">{{ $payment->student->name }}</td>
                                <td class="px-6 py-4">${{ number_format($payment->amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClasses = match ($payment->status) {
                                            \App\Enums\PaymentStatus::Paid => 'bg-emerald-100 text-emerald-700',
                                            \App\Enums\PaymentStatus::Partial => 'bg-amber-100 text-amber-700',
                                            \App\Enums\PaymentStatus::Unpaid => 'bg-rose-100 text-rose-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full {{ $badgeClasses }} px-2.5 py-0.5 text-xs font-medium capitalize">{{ $payment->status->value }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $payment->paid_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4">{{ $payment->reference }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-gray-500">{{ __('No payments recorded yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $payments->links() }}
        </div>
    </div>
</x-app-layout>
