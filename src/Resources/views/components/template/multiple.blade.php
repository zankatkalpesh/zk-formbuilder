@props(['element'])

{!! $element['before'] !!}

@foreach($element['contentWrapper'] as $wrap)
{!! $wrap['before'] ?? '' !!}
<{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

@foreach($element['rows'] as $row)
    
    @php
        $removeAction = $row['removeAction'] ?? null;
        $rowWrapper = $row['wrapper']['wrapper'] ?? [];
        $fieldWrapper = $row['wrapper']['fieldWrapper']['wrapper'] ?? [];
    @endphp

    @foreach($rowWrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
    @endforeach

        @if(!$element['viewOnly'] && $removeAction && $removeAction['show'] && $removeAction['position'] == 'before')
            <x-dynamic-component :component="$removeAction['component']" :action="$removeAction"></x-dynamic-component>
        @endif

        @foreach($fieldWrapper as $wrap)
        {!! $wrap['before'] ?? '' !!}
        <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
        @endforeach

            @foreach($row['fields'] as $field)
                <x-formbuilder::element :element="$field"></x-formbuilder::element>
            @endforeach

        @foreach(array_reverse($fieldWrapper) as $wrap)
        </{{ $wrap['tag'] }}>
        {!! $wrap['after'] ?? '' !!}
        @endforeach

        @if(!$element['viewOnly'] && $removeAction && $removeAction['show'] && $removeAction['position'] == 'after')
            <x-dynamic-component :component="$removeAction['component']" :action="$removeAction"></x-dynamic-component>
        @endif

    @foreach(array_reverse($rowWrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
    @endforeach

@endforeach

@foreach(array_reverse($element['contentWrapper']) as $wrap)
</{{ $wrap['tag'] }}>
{!! $wrap['after'] ?? '' !!}
@endforeach

{!! $element['after'] !!}