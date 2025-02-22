@php
    unset($attributes['element']);
    $itemWrapper = $element->getWrapper('itemWrapper');
    $items = $element->getItems();
@endphp

{!! $element->getBefore() !!}

@foreach($itemWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

@foreach($items as $item)
    <x-formbuilder::element :element="$item"></x-formbuilder::element>
@endforeach

@foreach(array_reverse($itemWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach

{!! $element->getAfter() !!}