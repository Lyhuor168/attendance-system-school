@props(['schoolClass' => null, 'teachers'])

<div>
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $schoolClass?->name)" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="grade_level" :value="__('Grade Level')" />
    <x-text-input id="grade_level" name="grade_level" type="text" class="mt-1 block w-full" :value="old('grade_level', $schoolClass?->grade_level)" required />
    <x-input-error :messages="$errors->get('grade_level')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="homeroom_teacher_id" :value="__('Homeroom Teacher')" />
    <select id="homeroom_teacher_id" name="homeroom_teacher_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">{{ __('None') }}</option>
        @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id', $schoolClass?->homeroom_teacher_id) == $teacher->id)>
                {{ $teacher->user->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('homeroom_teacher_id')" class="mt-2" />
</div>
