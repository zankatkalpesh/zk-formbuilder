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
        <div class="frm-view-only type-{{ $element['type'] }}" data-view-only="true" data-multiple="{{ $element['multiple'] ? 'true' : 'false' }}">
            <ul class="list-unstyled">
                @foreach($element['files'] as $file)
                    <li class="file-item">
                        <a href="{{ $file }}" target="_blank">{{ basename($file) }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@else
    <input type="{{ $element['type'] }}" {{ $attributes->merge($element['attributes']) }}
        @if ($element['rules']) data-rules="{{ json_encode($element['rules']) }}" @endif
        @if ($element['messages']) data-messages="{{ json_encode($element['messages']) }}" @endif
        />
@endif

{!! $element['after'] !!}

@foreach(array_reverse($element['inputWrapper']) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach