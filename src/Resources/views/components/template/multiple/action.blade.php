@props(['action'])
@php
    if(!empty($action['rowObject'])) {
        $action['attributes']['data-row-object'] = json_encode($action['rowObject']);
    }
@endphp

@foreach($action['wrapper'] as $wrap)
{!! $wrap['before'] ?? '' !!}
<{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

    <{{ $action['tag'] }} {{ $attributes->merge($action['attributes']) }}>
        {!! $action['before'] ?? '' !!}
        {{ $action['text'] }}
        {!! $action['after'] ?? '' !!}
    </{{ $action['tag'] }}>

@foreach(array_reverse($action['wrapper']) as $wrap)
</{{ $wrap['tag'] }}>
{!! $wrap['after'] ?? '' !!}
@endforeach