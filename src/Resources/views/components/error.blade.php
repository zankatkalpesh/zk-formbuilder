@props(['error'])

@foreach($error['wrapper'] as $wrap)
{!! $wrap['before'] ?? '' !!}
<{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

    @foreach($error['errors'] as $errorText)
        <{{ $error['tag'] }} {{ $attributes->merge($error['attributes']) }}>
            {!! $error['before'] !!}
            {!! $errorText !!}
            {!! $error['after'] !!}
        </{{ $error['tag'] }}>
    @endforeach

@foreach(array_reverse($error['wrapper']) as $wrap)
</{{ $wrap['tag'] }}>
{!! $wrap['after'] ?? '' !!}
@endforeach