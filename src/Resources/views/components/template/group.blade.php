@props(['element'])

{!! $element['before'] !!}

@foreach($element['fieldWrapper'] as $wrap)
{!! $wrap['before'] ?? '' !!}
<{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

    @foreach($element['fields'] as $field)
        <x-formbuilder::element :element="$field"></x-formbuilder::element>
    @endforeach

@foreach(array_reverse($element['fieldWrapper']) as $wrap)
</{{ $wrap['tag'] }}>
{!! $wrap['after'] ?? '' !!}
@endforeach

{!! $element['after'] !!}