@php
    unset($attributes['element']);
    $items = $element->getItems();
@endphp

{!! $element->getBefore() !!}

@foreach($items as $item)
    <x-formbuilder::element :element="$item"></x-formbuilder::element>
@endforeach

{!! $element->getAfter() !!}