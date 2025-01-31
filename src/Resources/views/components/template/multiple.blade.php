@php
    unset($attributes['element']);
    $actionComponent = $element->getActionComponent();
    $viewOnly = $element->hasViewOnly(); 
@endphp

{!! $element->getBefore() !!}

@foreach($element->getRows() as $row)
    @php
        $removeAction = $row['removeAction'] ?? [];
        $wrapper = $row['wrapper']['wrapper'] ?? [];
    @endphp
    @foreach($wrapper as $wrap)
        {!! $wrap['before'] ?? '' !!}
        <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
    @endforeach

    @if(!$viewOnly && $removeAction && $removeAction['show'] && $removeAction['position'] == 'before')
        <x-dynamic-component :component="$actionComponent" :element="$element" :action="$removeAction"></x-dynamic-component>
    @endif

    @foreach($row['fields'] as $field)
        <x-formbuilder::element :element="$field"></x-formbuilder::element>
    @endforeach

    @if(!$viewOnly && $removeAction && $removeAction['show'] && $removeAction['position'] == 'after')
        <x-dynamic-component :component="$actionComponent" :element="$element" :action="$removeAction"></x-dynamic-component>
    @endif

    @foreach(array_reverse($wrapper) as $wrap)
        </{{ $wrap['tag'] }}>
        {!! $wrap['after'] ?? '' !!}
    @endforeach
@endforeach

{!! $element->getAfter() !!}