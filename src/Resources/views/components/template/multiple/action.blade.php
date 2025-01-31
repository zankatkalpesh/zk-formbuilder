@php
    unset($attributes['action']);
    unset($attributes['element']);
    $wrapper = $action['wrapper'];
    $tag = $action['tag'];
    $rowObject = (!empty($action['rowObject'])) ? json_encode($action['rowObject']) : null;
    // $jsElement = (!empty($action['rowObject']['jsElement'])) ? $action['rowObject']['jsElement'] : null;
@endphp

@foreach($wrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach
    <{{ $action['tag'] }} {!! $element->printAttributes($action['attributes']) !!}
        @if ($rowObject) data-row-object="{{ $rowObject }}" @endif>
        {{ $action['label'] }}
    </{{ $action['tag'] }}>

@foreach(array_reverse($wrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach