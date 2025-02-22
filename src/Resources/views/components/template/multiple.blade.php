@php
    unset($attributes['element']);
    $actionComponent = $element->getActionComponent();
    $contentWrapper = $element->getWrapper('contentWrapper');
    $viewOnly = $element->hasViewOnly(); 
@endphp

{!! $element->getBefore() !!}

@foreach($contentWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach
@foreach($element->getRows() as $row)
    @php
        $removeAction = $row['removeAction'] ?? [];
        $wrapper = $row['wrapper']['wrapper'] ?? [];
        $fieldWrapper = $row['wrapper']['fieldWrapper'] ?? [];
        $fieldWrapper = $fieldWrapper['wrapper'] ?? [];
    @endphp
    @foreach($wrapper as $wrap)
        {!! $wrap['before'] ?? '' !!}
        <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
    @endforeach

    @if(!$viewOnly && $removeAction && $removeAction['show'] && $removeAction['position'] == 'before')
        <x-dynamic-component :component="$actionComponent" :element="$element" :action="$removeAction"></x-dynamic-component>
    @endif

    @foreach($fieldWrapper as $wrap)
        {!! $wrap['before'] ?? '' !!}
        <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
    @endforeach

    @foreach($row['fields'] as $field)
        <x-formbuilder::element :element="$field"></x-formbuilder::element>
    @endforeach

    @foreach(array_reverse($fieldWrapper) as $wrap)
        </{{ $wrap['tag'] }}>
        {!! $wrap['after'] ?? '' !!}
    @endforeach

    @if(!$viewOnly && $removeAction && $removeAction['show'] && $removeAction['position'] == 'after')
        <x-dynamic-component :component="$actionComponent" :element="$element" :action="$removeAction"></x-dynamic-component>
    @endif

    @foreach(array_reverse($wrapper) as $wrap)
        </{{ $wrap['tag'] }}>
        {!! $wrap['after'] ?? '' !!}
    @endforeach
@endforeach
@foreach(array_reverse($contentWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach
{!! $element->getAfter() !!}