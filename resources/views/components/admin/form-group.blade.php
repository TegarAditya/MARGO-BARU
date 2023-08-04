@props([
    'left',
    'right',
    'label',
])

@php
$for = $attributes->get('id');
$containerClass = $attributes->get('containerClass', '');
$boxClass = $attributes->get('boxClass', '');
$multiline = $attributes->get('multiline', false);
$size = $attributes->get('size', 'default');
$disabled = $attributes->get('disabled', false);

switch ($size) {
    case 'sm':
        $containerClass .= ' text-field-sm';
        break;
}

if ($multiline) {
    $boxClass .= ' align-items-start';
}
@endphp

<div class="form-group text-field{!! $containerClass !!}">
    @if (isset($label))
        {!! $label !!}
    @endif

    <div class="text-field-input{!! $boxClass !!}">
        @if (isset($left))
            {!! $left !!}
        @endif

        @if (!empty($slot->toHtml()))
            {!! $slot !!}
        @elseif (!$multiline)
            <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => "form-control"]) !!} />
        @else
            <textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => "form-control"]) !!}>{!! $attributes->get('value') !!}</textarea>
        @endif

        @if (isset($right))
            {!! $right !!}
        @endif

        <label for="{{ $for }}" class="text-field-border"></label>
    </div>

    @if (isset($errors))
        @error($for)
            <p {{ $attributes->merge(['class' => 'text-danger text-sm mb-0 mt-1']) }}>{{ $message }}</p>
        @enderror
    @endif
</div>
