@props(['element'])

{!! $element['before'] !!}

@foreach($element['itemWrapper'] as $wrap)
{!! $wrap['before'] ?? '' !!}
<{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

@foreach($element['items'] as $item)
    <x-formbuilder::element :element="$item"></x-formbuilder::element>
@endforeach

@foreach(array_reverse($element['itemWrapper']) as $wrap)
</{{ $wrap['tag'] }}>
{!! $wrap['after'] ?? '' !!}
@endforeach

{!! $element['after'] !!}