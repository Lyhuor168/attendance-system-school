<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-800">{{ __('Dashboard') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('Welcome back, :name.', ['name' => auth()->user()->name]) }}</p>
            </div>

            @unless ($isStudent || $isGuardian)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 hover:shadow-md">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ __('Mark Attendance') }}
                    </a>

                    @if ($isAdmin)
                        <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-500 hover:shadow-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .672-3 1.5S10.343 11 12 11s3 .672 3 1.5-1.343 1.5-3 1.5m0-6c1.11 0 2.08.402 2.599 1M12 8V6.5m0 9V17m0-9c-1.11 0-2.08.402-2.599 1M12 6.5a9.5 9.5 0 100 11" /></svg>
                            {{ __('Collect Fee') }}
                        </a>
                        <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:shadow-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            {{ __('Add Student') }}
                        </a>
                    @endif
                </div>
            @endunless
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('status') }}</div>
        @endif

        @if ($isStudent)
            @if ($student)
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-sm">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500">{{ __('My Attendance (this month)') }}</p>
                                <p class="text-2xl font-bold text-slate-800">{{ $kpis['attendancePercentage'] }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-sm">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .672-3 1.5S10.343 11 12 11s3 .672 3 1.5-1.343 1.5-3 1.5m0-6c1.11 0 2.08.402 2.599 1M12 8V6.5m0 9V17m0-9c-1.11 0-2.08.402-2.599 1M12 6.5a9.5 9.5 0 100 11" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500">{{ __('Total Fees Paid') }}</p>
                                <p class="text-2xl font-bold text-slate-800">${{ number_format($kpis['totalFeesPaid'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-sm font-semibold text-slate-700">{{ __('My Attendance Trend (last 7 days)') }}</h3>
                    <canvas id="attendanceTrendChart" height="220"></canvas>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <h3 class="text-sm font-semibold text-slate-700">{{ __('My Recent Attendance') }}</h3>
                        </div>
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-5 py-2">{{ __('Date') }}</th>
                                    <th class="px-5 py-2">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentAttendance as $record)
                                    <tr class="transition hover:bg-slate-50">
                                        <td class="px-5 py-3 text-slate-600">{{ $record->date->format('M j, Y') }}</td>
                                        <td class="px-5 py-3">
                                            @php
                                                $attendanceBadge = match ($record->status) {
                                                    \App\Enums\AttendanceStatus::Present => 'bg-emerald-100 text-emerald-700',
                                                    \App\Enums\AttendanceStatus::Late => 'bg-amber-100 text-amber-700',
                                                    \App\Enums\AttendanceStatus::Excused => 'bg-sky-100 text-sky-700',
                                                    \App\Enums\AttendanceStatus::Absent => 'bg-rose-100 text-rose-700',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full {{ $attendanceBadge }} px-2.5 py-0.5 text-xs font-medium capitalize">{{ $record->status->value }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-5 py-6 text-center text-slate-500">{{ __('No attendance recorded yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <h3 class="text-sm font-semibold text-slate-700">{{ __('My Payments') }}</h3>
                        </div>
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-5 py-2">{{ __('Date') }}</th>
                                    <th class="px-5 py-2">{{ __('Amount') }}</th>
                                    <th class="px-5 py-2">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($payments as $payment)
                                    <tr class="transition hover:bg-slate-50">
                                        <td class="px-5 py-3 text-slate-600">{{ $payment->paid_at->format('M j, Y') }}</td>
                                        <td class="px-5 py-3 text-slate-600">${{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-5 py-3">
                                            @php
                                                $paymentBadge = match ($payment->status) {
                                                    \App\Enums\PaymentStatus::Paid => 'bg-emerald-100 text-emerald-700',
                                                    \App\Enums\PaymentStatus::Partial => 'bg-amber-100 text-amber-700',
                                                    \App\Enums\PaymentStatus::Unpaid => 'bg-rose-100 text-rose-700',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full {{ $paymentBadge }} px-2.5 py-0.5 text-xs font-medium capitalize">{{ $payment->status->value }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-6 text-center text-slate-500">{{ __('No payments recorded yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-500 shadow-sm">
                    {{ __('No student profile is linked to your account yet. Contact your school administrator.') }}
                </div>
            @endif
        @elseif ($isGuardian)
            @if ($guardian)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">{{ __('Your Children') }}</h3>
                    @forelse ($children as $child)
                        <div class="text-sm text-slate-600">{{ $child->name }} &mdash; {{ $child->schoolClass->name }}</div>
                    @empty
                        <div class="text-sm text-slate-500">{{ __('No students linked to your account yet.') }}</div>
                    @endforelse
                </div>

                <a href="{{ route('chat.index') }}" class="block rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ __('Messages with Teachers') }}</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $unreadMessages }} {{ __('unread') }}</p>
                        </div>
                    </div>
                </a>
            @else
                <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-500 shadow-sm">
                    {{ __('No guardian profile is linked to your account yet. Contact your school administrator.') }}
                </div>
            @endif
        @else

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 {{ $isAdmin ? 'lg:grid-cols-4' : 'lg:grid-cols-2' }}">
            <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('Total Students') }}</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $kpis['totalStudents'] }}</p>
                    </div>
                </div>
            </div>

            @if ($isAdmin)
                <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 10-1.13-7.85M5 12a4 4 0 011.13-7.85" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ __('Total Staff') }}</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $kpis['totalStaff'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __("Today's Attendance") }}</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $kpis['todayAttendancePercentage'] }}%</p>
                    </div>
                </div>
            </div>

            @if ($isAdmin)
                <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .672-3 1.5S10.343 11 12 11s3 .672 3 1.5-1.343 1.5-3 1.5m0-6c1.11 0 2.08.402 2.599 1M12 8V6.5m0 9V17m0-9c-1.11 0-2.08.402-2.599 1M12 6.5a9.5 9.5 0 100 11" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ __('Revenue This Month') }}</p>
                            <p class="text-2xl font-bold text-slate-800">${{ number_format($kpis['monthlyRevenue'], 2) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @unless ($isAdmin)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-slate-700">{{ __('Your Classes') }}</h3>
                @forelse ($assignedClasses as $class)
                    <div class="text-sm text-slate-600">{{ $class->name }} ({{ $class->grade_level }})</div>
                @empty
                    <div class="text-sm text-slate-500">{{ __('No classes assigned yet.') }}</div>
                @endforelse
            </div>
        @endunless

        {{-- Charts --}}
        <div class="grid grid-cols-1 gap-5 {{ $isAdmin ? 'lg:grid-cols-2' : '' }}">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-slate-700">{{ __('Attendance Trend (last 7 days)') }}</h3>
                <canvas id="attendanceTrendChart" height="220"></canvas>
            </div>

            @if ($isAdmin)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-sm font-semibold text-slate-700">{{ __('Revenue vs Target (last 6 months)') }}</h3>
                    <canvas id="revenueChart" height="220"></canvas>
                </div>
            @endif
        </div>

        {{-- Tables --}}
        <div class="grid grid-cols-1 gap-5 {{ $isAdmin ? 'lg:grid-cols-2' : '' }}">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-700">{{ __("Today's Attendance Overview") }}</h3>
                    <p class="text-xs text-slate-500">{{ __('Students absent today') }}</p>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-5 py-2">{{ __('Student') }}</th>
                            <th class="px-5 py-2">{{ __('Class') }}</th>
                            <th class="px-5 py-2">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($absentToday as $record)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-3 font-medium text-slate-700">{{ $record->student->name }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $record->student->schoolClass->name }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-700">{{ __('Absent') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-6 text-center text-slate-500">{{ __('No absences recorded today.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($isAdmin)
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h3 class="text-sm font-semibold text-slate-700">{{ __('Recent Financial Payments') }}</h3>
                        <p class="text-xs text-slate-500">{{ __('Last 5 transactions') }}</p>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-400">
                            <tr>
                                <th class="px-5 py-2">{{ __('Student') }}</th>
                                <th class="px-5 py-2">{{ __('Amount') }}</th>
                                <th class="px-5 py-2">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentPayments as $payment)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-3 font-medium text-slate-700">{{ $payment->student->name }}</td>
                                    <td class="px-5 py-3 text-slate-600">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-5 py-3">
                                        @php
                                            $badgeClasses = match ($payment->status) {
                                                \App\Enums\PaymentStatus::Paid => 'bg-emerald-100 text-emerald-700',
                                                \App\Enums\PaymentStatus::Partial => 'bg-amber-100 text-amber-700',
                                                \App\Enums\PaymentStatus::Unpaid => 'bg-rose-100 text-rose-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full {{ $badgeClasses }} px-2.5 py-0.5 text-xs font-medium capitalize">{{ $payment->status->value }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-6 text-center text-slate-500">{{ __('No payments recorded yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const attendanceTrendCanvas = document.getElementById('attendanceTrendChart');

                @unless ($isGuardian)
                    if (attendanceTrendCanvas) {
                        new Chart(attendanceTrendCanvas, {
                            type: 'line',
                            data: {
                                labels: @json($attendanceTrend['labels']),
                                datasets: [{
                                    label: 'Attendance %',
                                    data: @json($attendanceTrend['data']),
                                    borderColor: 'rgb(79, 70, 229)',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 3,
                                }],
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true, max: 100, ticks: { callback: (v) => v + '%' } } },
                            },
                        });
                    }
                @endunless

                @if ($isAdmin)
                    new Chart(document.getElementById('revenueChart'), {
                        type: 'bar',
                        data: {
                            labels: @json($revenueVsTarget['labels']),
                            datasets: [
                                {
                                    label: 'Actual',
                                    data: @json($revenueVsTarget['actual']),
                                    backgroundColor: 'rgb(79, 70, 229)',
                                    borderRadius: 6,
                                },
                                {
                                    label: 'Target',
                                    data: @json($revenueVsTarget['target']),
                                    backgroundColor: 'rgb(203, 213, 225)',
                                    borderRadius: 6,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } },
                            scales: { y: { beginAtZero: true } },
                        },
                    });
                @endif
            });
        </script>
    @endpush
</x-app-layout>
