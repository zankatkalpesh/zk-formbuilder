@php
    unset($attributes['element']);
    $wrapper = $element->getWrapper();
    $label = $element->getLabel();
    $error = $element->getError();
    $component = $element->getComponent();
    $labelComponent = ($label) ? $label->getComponent() : null;
    $errorComponent = ($error) ? $error->getComponent() : null;
    $actionComponent = ($element->getType() == 'multiple') ? $element->getActionComponent() : null;
    $addAction = ($element->getType() == 'multiple') ? $element->getAddAction() : null;
    $viewOnly = $element->hasViewOnly();
@endphp

@if($label && $label->hasPosition('before-wrapper'))
    <x-dynamic-component :component="$labelComponent" :label="$label"></x-dynamic-component>
@endif

@if($error && $error->hasPosition('before-wrapper'))
    <x-dynamic-component :component="$errorComponent" :error="$error"></x-dynamic-component>
@endif

@if(!$viewOnly && $addAction && $addAction['show'] && $addAction['position'] == 'before-wrapper')
    <x-dynamic-component :component="$actionComponent" :element="$element" :action="$addAction"></x-dynamic-component>
@endif

@foreach($wrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

@if($label && $label->hasPosition('before-input'))
    <x-dynamic-component :component="$labelComponent" :label="$label"></x-dynamic-component>
@endif

@if($error && $error->hasPosition('before-input'))
    <x-dynamic-component :component="$errorComponent" :error="$error"></x-dynamic-component>
@endif

@if(!$viewOnly && $addAction && $addAction['show'] && $addAction['position'] == 'before')
    <x-dynamic-component :component="$actionComponent" :element="$element" :action="$addAction"></x-dynamic-component>
@endif

<x-dynamic-component :component="$component" :element="$element"></x-dynamic-component>


@if(!$viewOnly && $addAction && $addAction['show'] && $addAction['position'] == 'after')
    <x-dynamic-component :component="$actionComponent" :element="$element" :action="$addAction"></x-dynamic-component>
@endif

@if($error && $error->hasPosition('after-input'))
    <x-dynamic-component :component="$errorComponent" :error="$error"></x-dynamic-component>
@endif

@if($label && $label->hasPosition('after-input'))
    <x-dynamic-component :component="$labelComponent" :label="$label"></x-dynamic-component>
@endif

@foreach(array_reverse($wrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach

@if(!$viewOnly && $addAction && $addAction['show'] && $addAction['position'] == 'after-wrapper')
    <x-dynamic-component :component="$actionComponent" :element="$element" :action="$addAction"></x-dynamic-component>
@endif

@if($label && $label->hasPosition('after-wrapper'))
    <x-dynamic-component :component="$labelComponent" :label="$label"></x-dynamic-component>
@endif

@if($error && $error->hasPosition('after-wrapper'))
    <x-dynamic-component :component="$errorComponent" :error="$error"></x-dynamic-component>
@endif