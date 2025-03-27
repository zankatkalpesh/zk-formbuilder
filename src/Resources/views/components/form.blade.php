@props(['form'])
@php
    $form = $form->toArray();
    $buttons = $form['buttons'] ?? null;
@endphp

@foreach($form['wrapper'] as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $form['beforeRender'] !!}

<form 
    method="{{ $form['method'] === 'GET' ? 'GET' : 'POST' }}"
    action="{{ $form['action'] }}"
    {{ $attributes->merge($form['attributes']) }}>

    @if($buttons && $buttons['position'] == 'form-top')
        <x-dynamic-component :component="$buttons['component']" :buttons="$buttons"></x-dynamic-component>
    @endif

    @if ($form['csrf']) @csrf @endunless
    <input type="hidden" name="_form" value="{{ $form['frmKey'] }}"/>
    @if (! in_array($form['method'], ['GET', 'POST'])) @method($form['method']) @endif

    {!! $form['before'] !!}
    
    @foreach($form['fields'] as $field)
        <x-formbuilder::element :element="$field"></x-formbuilder::element>
    @endforeach
    
    @if($buttons && $buttons['position'] == 'form-bottom')
        <x-dynamic-component :component="$buttons['component']" :buttons="$buttons"></x-dynamic-component>
    @endif

    {!! $form['after'] !!}

</form>

{!! $form['afterRender'] !!}

@foreach(array_reverse($form['wrapper']) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach
