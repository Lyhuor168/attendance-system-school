@props(['subject' => null])

<div>
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $subject?->name)" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="code" :value="__('Code')" />
    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $subject?->code)" required />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>
