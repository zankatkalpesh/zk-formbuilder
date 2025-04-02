@props(['element'])

@php
    $options = $element['options'] ?? [];
@endphp

@foreach($element['inputWrapper'] as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $element['before'] !!}

@if($element['viewOnly'])
    @if($element['view']) 
        {!! $element['view'] !!}
    @else
        <div class="frm-view-only type-{{ $element['type'] }}" data-view-only="true" data-multiselect="{{ $element['multiselect'] ? 'true' : 'false' }}">
            <ul class="list-unstyled">
                @foreach($options as $option)
                    @if(!empty($option['optgroup']))
                        @foreach($option['options'] as $gpOption)
                            @if(!empty($gpOption['selected']))
                                <li>{{ $gpOption['label'] }}</li>
                            @endif
                        @endforeach
                    @elseif(!empty($option['selected']))
                        <li>{{ $option['label'] }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
@else
    <select {{ $attributes->merge($element['attributes']) }}
        @if ($element['rules']) data-rules="{{ json_encode($element['rules']) }}" @endif
        @if ($element['messages']) data-messages="{{ json_encode($element['messages']) }}" @endif
        >
        @foreach($options as $option)
            @if(!empty($option['optgroup']))
                <optgroup label="{{ $option['label'] }}" {{ $attributes->merge($option)->except(['label', 'optgroup', 'options']) }}>
                    @foreach($option['options'] as $gpOption)
                        <option value="{{ $gpOption['value'] }}" {{ $attributes->merge($gpOption)->except(['label', 'value']) }}>
                            {{ $gpOption['label'] }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option value="{{ $option['value'] }}" {{ $attributes->merge($option)->except(['label', 'value']) }}>
                    {{ $option['label'] }}
                </option>
            @endif
        @endforeach
    </select>
@endif 

{!! $element['after'] !!}

@foreach(array_reverse($element['inputWrapper']) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach