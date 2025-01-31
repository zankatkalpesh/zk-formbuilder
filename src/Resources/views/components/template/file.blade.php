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
    <div class="frm-view-only type-{{ $element->getType() }}">
        <ul class="list-unstyled">
        @foreach($element->getFiles() as $file)
            @php
                $fileName = basename($file);
            @endphp
            <li class="file-item">
                <a href="{{ $file }}" target="_blank">{{ $fileName }}</a>
            </li>
        @endforeach
        </ul>
    </div>
@else
    <input
        type="{{ $element->getType() }}"
        {!! $element->printAttributes() !!}
        @if ($rules) data-rules="{{ json_encode($rules) }}" @endif
        @if ($messages) data-messages="{{ json_encode($messages) }}" @endif
        @if ($element->getValue()) value="{{ $element->getValue() }}" @endif/>
@endif
{!! $element->getAfter() !!}

@foreach(array_reverse($inputWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach