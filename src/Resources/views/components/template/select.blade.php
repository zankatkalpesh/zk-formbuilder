@php
    unset($attributes['element']);
    $inputWrapper = $element->getWrapper('inputWrapper');
    $options = $element->getOptions();
    $rules = $element->getRules('frontend');
    $messages = $element->getMessages();
@endphp

@foreach($inputWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $element->getBefore() !!}

@if($element->hasViewOnly())
    <div class="frm-view-only type-{{ $element->getType() }}">
        <ul class="list-unstyled">
        @foreach($options as $option)
            @if(isset($option['optgroup']) && $option['optgroup'])
                @foreach($option['options'] as $gpOption)
                    @if($element->isSelected($gpOption['value']))
                        <li>{{ $gpOption['label'] }}</li>
                    @endif
                @endforeach
                @continue
            @endif
            @if($element->isSelected($option['value']))
                <li>{{ $option['label'] }}</li>
            @endif
        @endforeach
        </ul>
    </div>
@else
    <select
        {!! $element->printAttributes() !!}
        @if ($rules) data-rules="{{ json_encode($rules) }}" @endif
        @if ($messages) data-messages="{{ json_encode($messages) }}" @endif
        >
        @foreach($options as $option)
            @if(isset($option['optgroup']) && $option['optgroup'])
                <optgroup label="{{ $option['label'] }}" {!! $element->printAttributes($option, ['label', 'optgroup', 'options']) !!}>
                    @foreach($option['options'] as $gpOption)
                        <option
                            {!! $element->optionAttributes($gpOption, ['label', 'value']) !!}
                            value="{{ $gpOption['value'] }}">
                            {{ $gpOption['label'] }}
                        </option>
                    @endforeach
                </optgroup>
                @continue
            @endif
            <option
                {!! $element->optionAttributes($option, ['label', 'value']) !!}
                value="{{ $option['value'] }}">
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
@endif 
{!! $element->getAfter() !!}

@foreach(array_reverse($inputWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach