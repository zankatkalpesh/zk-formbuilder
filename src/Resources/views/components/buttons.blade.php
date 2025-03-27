@props(['buttons'])

@foreach($buttons['wrapper'] as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $buttons['before'] !!}

@foreach($buttons['actions'] as $action)
    <x-dynamic-component :component="$action['component']" :action="$action"></x-dynamic-component>
@endforeach

{!! $buttons['after'] !!}

@foreach(array_reverse($buttons['wrapper']) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach