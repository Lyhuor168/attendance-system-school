<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-slate-900 text-slate-300 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
>
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-slate-800 px-6">
        <x-application-logo class="h-8 w-8 shrink-0 fill-current text-indigo-400" />
        <span class="text-lg font-semibold text-white">{{ config('app.name', 'School MS') }}</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @php
            $links = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'pattern' => 'dashboard', 'admin_only' => false],
                ['route' => 'students.index', 'label' => 'Students', 'pattern' => 'students.*', 'admin_only' => true],
                ['route' => 'teachers.index', 'label' => 'Teachers', 'pattern' => 'teachers.*', 'admin_only' => true],
                ['route' => 'classes.index', 'label' => 'Classes', 'pattern' => 'classes.*', 'admin_only' => true],
                ['route' => 'subjects.index', 'label' => 'Subjects', 'pattern' => 'subjects.*', 'admin_only' => true],
                ['route' => 'timetables.index', 'label' => 'Timetable', 'pattern' => 'timetables.*', 'admin_only' => true],
                ['route' => 'attendance.index', 'label' => 'Attendance', 'pattern' => 'attendance.*', 'admin_only' => false, 'hidden_from_students' => true],
                ['route' => 'payments.index', 'label' => 'Payments', 'pattern' => 'payments.*', 'admin_only' => true],
            ];

            $icons = [
                'Dashboard' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'Students' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
                'Teachers' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 10-1.13-7.85M5 12a4 4 0 011.13-7.85',
                'Classes' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m4-8h2m4 0h2M7 9h2m4 0h2',
                'Subjects' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                'Timetable' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'Attendance' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'Payments' => 'M12 8c-1.657 0-3 .672-3 1.5S10.343 11 12 11s3 .672 3 1.5-1.343 1.5-3 1.5m0-6c1.11 0 2.08.402 2.599 1M12 8V6.5m0 9V17m0-9c-1.11 0-2.08.402-2.599 1M12 6.5a9.5 9.5 0 100 11',
            ];
        @endphp

        @foreach ($links as $link)
            @continue($link['admin_only'] && ! auth()->user()->isAdmin())
            @continue(($link['hidden_from_students'] ?? false) && auth()->user()->isStudent())
            @php $active = request()->routeIs($link['pattern']); @endphp
            <a
                href="{{ route($link['route']) }}"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ $active ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$link['label']] }}" />
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-slate-800 px-3 py-4">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors duration-150 hover:bg-slate-800 hover:text-white">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ __('Profile') }}
        </a>
    </div>
</aside>
