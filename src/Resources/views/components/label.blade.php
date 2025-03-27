@props(['label'])

@foreach($label['wrapper'] as $wrap)
{!! $wrap['before'] ?? '' !!}
<{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

    <{{ $label['tag'] }} {{ $attributes->merge($label['attributes']) }}>
        {!! $label['before'] !!}
        {{ $label['text'] }}
        {!! $label['after'] !!}
    </{{ $label['tag'] }}>

@foreach(array_reverse($label['wrapper']) as $wrap)
</{{ $wrap['tag'] }}>
{!! $wrap['after'] ?? '' !!}
@endforeach