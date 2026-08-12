@props(['student' => null, 'classes'])

<div>
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $student?->name)" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="student_number" :value="__('Student Number')" />
    <x-text-input id="student_number" name="student_number" type="text" class="mt-1 block w-full" :value="old('student_number', $student?->student_number)" required />
    <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="school_class_id" :value="__('Class')" />
    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">{{ __('Select a class') }}</option>
        @foreach ($classes as $class)
            <option value="{{ $class->id }}" @selected(old('school_class_id', $student?->school_class_id) == $class->id)>
                {{ $class->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
    <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', $student?->date_of_birth?->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="guardian_name" :value="__('Guardian Name')" />
    <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full" :value="old('guardian_name', $student?->guardian_name)" />
    <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="guardian_phone" :value="__('Guardian Phone')" />
    <x-text-input id="guardian_phone" name="guardian_phone" type="text" class="mt-1 block w-full" :value="old('guardian_phone', $student?->guardian_phone)" />
    <x-input-error :messages="$errors->get('guardian_phone')" class="mt-2" />
</div>

@if ($student)
    @if ($student->hasPortalAccess())
        <div class="mt-6 rounded-md bg-slate-50 border border-slate-200 p-4 text-sm text-slate-600">
            {{ __('Portal login enabled') }}: <span class="font-medium">{{ $student->user->email }}</span>
        </div>
    @endif
@else
    <div class="mt-6 border-t pt-4">
        <h3 class="text-sm font-semibold text-slate-700">{{ __('Portal Access (optional)') }}</h3>
        <p class="text-xs text-slate-500 mb-3">{{ __('Give this student their own login to view attendance and payments. Leave blank for no portal access.') }}</p>

        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
        </div>
    </div>
@endif
