@php
    unset($attributes['element']);
    $fieldWrapper = $element->getWrapper('fieldWrapper');
@endphp

{!! $element->getBefore() !!}

@foreach($fieldWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

@foreach($element->getFields() as $field)
    <x-formbuilder::element :element="$field"></x-formbuilder::element>
@endforeach

@foreach(array_reverse($fieldWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach

{!! $element->getAfter() !!}