@php
unset($attributes['action']);
$wrapper = $action->getWrapper();
@endphp

@foreach($wrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

<{{ $action->getTagName() }} {!! $action->printAttributes() !!}>
    {!! $action->getBefore() !!}
    {{ $action->getText() }}
    {!! $action->getAfter() !!}
</{{ $action->getTagName() }}>

@foreach(array_reverse($wrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach