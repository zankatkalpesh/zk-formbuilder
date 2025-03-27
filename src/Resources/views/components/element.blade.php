@props(['element'])
@php
    $label = $element['label'] ?? null;
    $error = $element['error'] ?? null;
    $addAction = ($element['elementType'] == 'multiple') ? ($element['addAction'] ?? null) : null;
@endphp

@if($label && $label['position'] == 'before-wrapper')
    <x-dynamic-component :component="$label['component']" :label="$label"></x-dynamic-component>
@endif

@if($error && $error['position'] == 'before-wrapper')
    <x-dynamic-component :component="$error['component']" :error="$error"></x-dynamic-component>
@endif

@if(!$element['viewOnly'] && $addAction && $addAction['show'] && $addAction['position'] == 'before-wrapper')
    <x-dynamic-component :component="$addAction['component']" :action="$addAction"></x-dynamic-component>
@endif

@foreach($element['wrapper'] as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

@if($label && $label['position'] == 'before-input')
    <x-dynamic-component :component="$label['component']" :label="$label"></x-dynamic-component>
@endif

@if($error && $error['position'] == 'before-input')
    <x-dynamic-component :component="$error['component']" :error="$error"></x-dynamic-component>
@endif

@if(!$element['viewOnly'] && $addAction && $addAction['show'] && $addAction['position'] == 'before')
    <x-dynamic-component :component="$addAction['component']" :action="$addAction"></x-dynamic-component>
@endif

<x-dynamic-component :component="$element['component']" :element="$element"></x-dynamic-component>

@if(!$element['viewOnly'] && $addAction && $addAction['show'] && $addAction['position'] == 'after')
    <x-dynamic-component :component="$addAction['component']" :action="$addAction"></x-dynamic-component>
@endif

@if($error && $error['position'] == 'after-input')
    <x-dynamic-component :component="$error['component']" :error="$error"></x-dynamic-component>
@endif

@if($label && $label['position'] == 'after-input')
    <x-dynamic-component :component="$label['component']" :label="$label"></x-dynamic-component>
@endif

@foreach(array_reverse($element['wrapper']) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach

@if(!$element['viewOnly'] && $addAction && $addAction['show'] && $addAction['position'] == 'after-wrapper')
    <x-dynamic-component :component="$addAction['component']" :action="$addAction"></x-dynamic-component>
@endif

@if($label && $label['position'] == 'after-wrapper')
    <x-dynamic-component :component="$label['component']" :label="$label"></x-dynamic-component>
@endif

@if($error && $error['position'] == 'after-wrapper')
    <x-dynamic-component :component="$error['component']" :error="$error"></x-dynamic-component>
@endif