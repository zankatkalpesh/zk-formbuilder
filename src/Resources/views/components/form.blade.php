@php
    unset($attributes['form']);
    $wrapper = $form->getWrapper();
    $buttons = $form->getButtons();
    $buttonsComponent = ($buttons) ? $buttons->getComponent() : null;
@endphp

@foreach($wrapper as $wrap)
    {!! $wrap['before'] ?? '' !!}
    <{{ $wrap['tag'] }} {!! $wrap['attributes'] !!}>
@endforeach

{!! $form->getBeforeRender() !!}

<form 
    method="{{ $form->getMethod() === 'GET' ? 'GET' : 'POST' }}"
    action="{{ $form->getAction() }}"
    {!! $form->printAttributes() !!}>
    @if($buttons && $buttons->hasPosition('form-top'))
        <x-dynamic-component :component="$buttonsComponent" :buttons="$buttons"></x-dynamic-component>
    @endif

    @if ($form->csrf)
        @csrf
    @endunless
    <input type="hidden" name="_form" value="{{ $form->getFormKey() }}"/>
    @if (! in_array($form->getMethod(), ['GET', 'POST']))
        @method($form->getMethod())
    @endif

    {!! $form->getBefore() !!}
    
    @foreach($form->getFields() as $field)
        <x-formbuilder::element :element="$field"></x-formbuilder::element>
    @endforeach
    
    @if($buttons && $buttons->hasPosition('form-bottom'))
        <x-dynamic-component :component="$buttonsComponent" :buttons="$buttons"></x-dynamic-component>
    @endif

    {!! $form->getAfter() !!}

</form>

{!! $form->getAfterRender() !!}

@foreach(array_reverse($wrapper) as $wrap)
    </{{ $wrap['tag'] }}>
    {!! $wrap['after'] ?? '' !!}
@endforeach

{{--
<!-- Add Scripts -->
@push('scripts')
    <script src="{{ asset('zk/assets/js/validator.min.js') }}"></script>    
    <script src="{{ asset('zk/assets/js/formbuilder.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('{{ $form->getId() }}');
            const formBuilder = new ZkFormBuilder();
            formBuilder.setForm(form);
            const frmValidator = formBuilder.getValidator();
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                var isValid = await frmValidator.validate();
                if(!isValid) {
                    return;
                }
            });
            form.addEventListener('reset', function(event) {
                frmValidator.reset();
            });
        });
    </script>
@endpush
--}}