<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Leave Requests') }}</h2>
            @if (auth()->user()->isStudent())
                <a href="{{ route('leave-requests.create') }}">
                    <x-primary-button type="button">{{ __('Submit Leave Request') }}</x-primary-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            @unless (auth()->user()->isStudent())
                                <th class="px-6 py-3">{{ __('Student') }}</th>
                            @endunless
                            <th class="px-6 py-3">{{ __('Class') }}</th>
                            <th class="px-6 py-3">{{ __('From') }}</th>
                            <th class="px-6 py-3">{{ __('To') }}</th>
                            <th class="px-6 py-3">{{ __('Reason') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($leaveRequests as $leaveRequest)
                            <tr>
                                @unless (auth()->user()->isStudent())
                                    <td class="px-6 py-4">{{ $leaveRequest->student->name }}</td>
                                @endunless
                                <td class="px-6 py-4">{{ $leaveRequest->schoolClass->name }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->from_date->format('M j, Y') }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->to_date->format('M j, Y') }}</td>
                                <td class="px-6 py-4 max-w-xs truncate">{{ $leaveRequest->reason }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClasses = match ($leaveRequest->status) {
                                            \App\Enums\LeaveRequestStatus::Pending => 'bg-amber-100 text-amber-700',
                                            \App\Enums\LeaveRequestStatus::Approved => 'bg-emerald-100 text-emerald-700',
                                            \App\Enums\LeaveRequestStatus::Rejected => 'bg-rose-100 text-rose-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full {{ $badgeClasses }} px-2.5 py-0.5 text-xs font-medium capitalize">{{ $leaveRequest->status->value }}</span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @can('respond', $leaveRequest)
                                        @if ($leaveRequest->status === \App\Enums\LeaveRequestStatus::Pending)
                                            <form action="{{ route('leave-requests.respond', $leaveRequest) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="text-emerald-600 hover:underline">{{ __('Approve') }}</button>
                                            </form>
                                            <form action="{{ route('leave-requests.respond', $leaveRequest) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="text-rose-600 hover:underline">{{ __('Reject') }}</button>
                                            </form>
                                        @endif
                                    @endcan
                                    @can('delete', $leaveRequest)
                                        <form action="{{ route('leave-requests.destroy', $leaveRequest) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Cancel this request?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-600 hover:underline">{{ __('Cancel') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-gray-500">{{ __('No leave requests yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $leaveRequests->links() }}
        </div>
    </div>
</x-app-layout>
