@props(['id', 'name', 'options', 'labelKey', 'selected' => null, 'required' => false, 'multiple' => false, 'valueKey' => 'id'])

<select id="{{ $id }}" {!! $attributes->merge(['class' => 'form-control']) !!} name="{{ $name }}" @if($required) required @endif @if($multiple) multiple @endif>
    @foreach($options as $option)
        <option value="{{ $option[$valueKey] }}" @if($selected == $option[$valueKey]) selected @endif>{{ $option[$labelKey] }}</option>
    @endforeach
</select>
