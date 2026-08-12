@props(['timetable' => null, 'classes', 'subjects', 'teachers'])

<div>
    <x-input-label for="school_class_id" :value="__('Class')" />
    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">{{ __('Select a class') }}</option>
        @foreach ($classes as $class)
            <option value="{{ $class->id }}" @selected(old('school_class_id', $timetable?->school_class_id) == $class->id)>{{ $class->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="subject_id" :value="__('Subject')" />
    <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">{{ __('Select a subject') }}</option>
        @foreach ($subjects as $subject)
            <option value="{{ $subject->id }}" @selected(old('subject_id', $timetable?->subject_id) == $subject->id)>{{ $subject->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="teacher_id" :value="__('Teacher')" />
    <select id="teacher_id" name="teacher_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">{{ __('Select a teacher') }}</option>
        @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}" @selected(old('teacher_id', $timetable?->teacher_id) == $teacher->id)>{{ $teacher->user->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="day_of_week" :value="__('Day of Week')" />
    <select id="day_of_week" name="day_of_week" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">{{ __('Select a day') }}</option>
        @foreach (\App\Enums\DayOfWeek::cases() as $day)
            <option value="{{ $day->value }}" @selected(old('day_of_week', $timetable?->day_of_week?->value) == $day->value)>{{ ucfirst($day->value) }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('day_of_week')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="start_time" :value="__('Start Time')" />
        <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" :value="old('start_time', $timetable?->start_time?->format('H:i'))" required />
        <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="end_time" :value="__('End Time')" />
        <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" :value="old('end_time', $timetable?->end_time?->format('H:i'))" required />
        <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
    </div>
</div>
