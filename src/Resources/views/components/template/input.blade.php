@php
    unset($attributes['element']);
    $inputWrapper = $element->getWrapper('inputWrapper');
    $rules = $element->getRules('frontend');
    $messages = $element->getMessages();
@endphp

@foreach($inputWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $element->getBefore() !!}

@if($element->hasViewOnly())
    @if($element->hasView()) 
        {!! $element->getView() !!}
    @else
        @if($element->getType() === 'checkbox' || $element->getType() === 'radio')
            <input
                type="{{ $element->getType() }}"
                {!! $element->printAttributes() !!}
                readonly="readonly"
                disabled="disabled"
                data-view-only="true"
            />
        @else
            <div class="form-control frm-view-only type-{{ $element->getType() }}" data-view-only="true">
                {{ $element->getValue() }}
            </div>
        @endif
    @endif
@else
    @if($element->getType() === 'textarea')
        <textarea
            {!! $element->printAttributes() !!}
            @if ($rules) data-rules="{{ json_encode($rules) }}" @endif
            @if ($messages) data-messages="{{ json_encode($messages) }}" @endif
            >{{ $element->getValue() }}</textarea>
    @else
        <input
            type="{{ $element->getType() }}"
            {!! $element->printAttributes() !!}
            @if ($rules) data-rules="{{ json_encode($rules) }}" @endif
            @if ($messages) data-messages="{{ json_encode($messages) }}" @endif
            @if ($element->getValue() !== null) value="{{ $element->getValue() }}" @endif
            />
    @endif
@endif

{!! $element->getAfter() !!}

@foreach(array_reverse($inputWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach