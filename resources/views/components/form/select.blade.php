@props([
'name',
'label',
'options' => [],
'value' => '',
'placeholder' => 'Select an option',
'required' => false,
'disabled' => false,
'class' => '',
])

<div class="col-span-6 sm:col-span-3">
    <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    <select name="{{ $name }}" id="{{ $name }}" @if($required) required @endif @if($disabled) disabled @endif
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 {{ $class }}">
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $key => $option)
        <option value="{{ $key }}" {{ $value==$key ? 'selected' : '' }}>{{ $option }}</option>
        @endforeach
    </select>
    @error($name)
    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>
