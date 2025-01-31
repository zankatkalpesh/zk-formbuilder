@php
    unset($attributes['error']);
    $errors = $error->getErrors();
    $wrapper = $error->getWrapper();
@endphp

@foreach($wrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

@foreach($errors as $errorText)
    <{{ $error->getTagName() }} {!! $error->printAttributes() !!}>
        {!! $error->getBefore() !!}
        {!! $errorText !!}
        {!! $error->getAfter() !!}
    </{{ $error->getTagName() }}>
@endforeach

@foreach(array_reverse($wrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach