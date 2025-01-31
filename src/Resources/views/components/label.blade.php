@php
unset($attributes['label']);
$wrapper = $label->getWrapper();
@endphp

@foreach($wrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

<{{ $label->getTagName() }} {!! $label->printAttributes() !!}>
    {!! $label->getBefore() !!}
    {{ $label->getText() }}
    {!! $label->getAfter() !!}
</{{ $label->getTagName() }}>

@foreach(array_reverse($wrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach