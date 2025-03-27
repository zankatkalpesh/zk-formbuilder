<x-formbuilder::form :form="$form"></x-formbuilder::form>
<!-- Add Scripts -->
@push('scripts')
<script src="{{ asset('js/validator.min.js') }}"></script>
<script src="{{ asset('js/formbuilder.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('{{ $form->getId() }}');
            const formBuilder = new ZkFormBuilder();
            formBuilder.setForm(form);
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                var isValid = await formBuilder.validate();
                if(!isValid) {
                    return;
                }
                form.submit();
            });
            form.addEventListener('reset', function(event) {
                frmValidator.reset();
            });
        });
</script>
@endpush