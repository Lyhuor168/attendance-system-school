<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimetableRequest;
use App\Http\Requests\UpdateTimetableRequest;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function index(): View
    {
        return view('timetables.index', [
            'timetables' => Timetable::with(['schoolClass', 'subject', 'teacher.user'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('timetables.create', $this->formOptions());
    }

    public function store(StoreTimetableRequest $request): RedirectResponse
    {
        Timetable::create($request->validated());

        return redirect()->route('timetables.index')->with('status', 'Timetable entry created.');
    }

    public function edit(Timetable $timetable): View
    {
        return view('timetables.edit', ['timetable' => $timetable, ...$this->formOptions()]);
    }

    public function update(UpdateTimetableRequest $request, Timetable $timetable): RedirectResponse
    {
        $timetable->update($request->validated());

        return redirect()->route('timetables.index')->with('status', 'Timetable entry updated.');
    }

    public function destroy(Timetable $timetable): RedirectResponse
    {
        $timetable->delete();

        return redirect()->route('timetables.index')->with('status', 'Timetable entry deleted.');
    }

    /**
     * @return array<string, Collection<int, mixed>>
     */
    private function formOptions(): array
    {
        return [
            'classes' => SchoolClass::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => Teacher::with('user')->get(),
        ];
    }
}
