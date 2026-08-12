@props(['teacher' => null])

<div>
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $teacher?->user?->name)" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $teacher?->user?->email)" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

@unless ($teacher)
    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
    </div>
@endunless

<div class="mt-4">
    <x-input-label for="employee_number" :value="__('Employee Number')" />
    <x-text-input id="employee_number" name="employee_number" type="text" class="mt-1 block w-full" :value="old('employee_number', $teacher?->employee_number)" required />
    <x-input-error :messages="$errors->get('employee_number')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="phone" :value="__('Phone')" />
    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $teacher?->phone)" />
    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="hired_at" :value="__('Hired At')" />
    <x-text-input id="hired_at" name="hired_at" type="date" class="mt-1 block w-full" :value="old('hired_at', $teacher?->hired_at?->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('hired_at')" class="mt-2" />
</div>
