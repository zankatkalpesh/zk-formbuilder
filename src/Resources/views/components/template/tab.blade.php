@php
    unset($attributes['element']);
    $tabWrapper = $element->getWrapper('tabWrapper');
    $contentWrapper = $element->getWrapper('contentWrapper');
    $tabs = $element->getTabs();
@endphp

{!! $element->getBefore() !!}
@foreach($tabWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach
@foreach($tabs as $tab)
    @foreach($tab['itemWrapper']['wrapper'] as $wrap)
        {!! $wrap['before'] ?? '' !!}
        <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
    @endforeach
    {!! $tab['label'] !!}
    @foreach(array_reverse($tab['itemWrapper']['wrapper']) as $wrap)
        </{{ $wrap['tag'] }}>
        {!! $wrap['after'] ?? '' !!}
    @endforeach
@endforeach
@foreach(array_reverse($tabWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach

@foreach($contentWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach
@foreach($tabs as $tab)
    @foreach($tab['panelWrapper']['wrapper'] as $wrap)
        {!! $wrap['before'] ?? '' !!}
        <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
    @endforeach
    @foreach($tab['fields'] as $field)
        <x-formbuilder::element :element="$field"></x-formbuilder::element>
    @endforeach
    @foreach(array_reverse($tab['panelWrapper']['wrapper']) as $wrap)
        </{{ $wrap['tag'] }}>
        {!! $wrap['after'] ?? '' !!}
    @endforeach
@endforeach
@foreach(array_reverse($contentWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach

{!! $element->getAfter() !!}