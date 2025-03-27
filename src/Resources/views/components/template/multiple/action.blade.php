@props(['action'])
@php
    $rowObject = (!empty($action['rowObject'])) ? json_encode($action['rowObject']) : null;
@endphp

@foreach($action['wrapper'] as $wrap)
{!! $wrap['before'] ?? '' !!}
<{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

    <{{ $action['tag'] }} {{ $attributes->merge($action['attributes']) }}
        @if ($rowObject) data-row-object="{{ $rowObject }}" @endif>
        {!! $action['label'] !!}
    </{{ $action['tag'] }}>

@foreach(array_reverse($action['wrapper']) as $wrap)
</{{ $wrap['tag'] }}>
{!! $wrap['after'] ?? '' !!}
@endforeach