@php
unset($attributes['buttons']);
$wrapper = $buttons->getWrapper();
$actions = $buttons->getActions();
@endphp

@foreach($wrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $buttons->getBefore() !!}

@foreach($actions as $action)
    <x-dynamic-component :component="$action->getComponent()" :action="$action"></x-dynamic-component>
@endforeach

{!! $buttons->getAfter() !!}

@foreach(array_reverse($wrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach