@props(['element'])

@foreach($element['inputWrapper'] as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $element['before'] !!}

@if($element['viewOnly'])
    @if($element['view']) 
        {!! $element['view'] !!}
    @else
        @if($element['type'] === 'checkbox' || $element['type'] === 'radio')
            <input type="{{ $element['type'] }}" {{ $attributes->merge($element['attributes']) }}
                readonly="readonly" disabled="disabled" data-view-only="true"/>
        @else
            <div class="form-control frm-view-only type-{{ $element['type'] }}" data-view-only="true">
                {{ $element['value'] }}
            </div>
        @endif
    @endif
@else
    @if($element['type'] === 'textarea')
        <textarea {{ $attributes->merge($element['attributes']) }}
            @if ($element['rules']) data-rules="{{ json_encode($element['rules']) }}" @endif
            @if ($element['messages']) data-messages="{{ json_encode($element['messages']) }}" @endif
            >{{ $element['value'] }}</textarea>
    @else
        <input type="{{ $element['type'] }}" {{ $attributes->merge($element['attributes']) }}
            @if ($element['rules']) data-rules="{{ json_encode($element['rules']) }}" @endif
            @if ($element['messages']) data-messages="{{ json_encode($element['messages']) }}" @endif
            @if ($element['value'] !== null) value="{{ $element['value'] }}" @endif
            />
    @endif
@endif

{!! $element['after'] !!}

@foreach(array_reverse($element['inputWrapper']) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach