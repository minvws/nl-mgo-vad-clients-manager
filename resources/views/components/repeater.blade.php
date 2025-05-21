@push('styles')
<style nonce="{{ csp_nonce() }}">
    .repeater-form-field {
        border: 1px solid #ccc;
        padding: 10px;
        background-color: #f9f9f9;
    }
</style>
@endpush

<div class="repeater-form-field">
    <div class="repeater-container">
        @foreach ($items as $item)
            <div class="repeater-item two-thirds-one-third">
                <div>
                    <x-text-input type="{{ $type }}" name="{{ $name }}[]" class="form-input" value="{{ $item }}" />
                </div>
                <div>
                    <button type="button" class="repeater-remove" aria-label="{{ __('inputs.repeater.remove') }}">&times;</button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" class="repeater-add" aria-label="{{ __('inputs.repeater.add') }}">{{ __('inputs.repeater.add') }}</button>
</div>

@push('scripts')
<script nonce="{{ csp_nonce() }}">
    document.addEventListener('DOMContentLoaded', function() {
        const repeaterContainer = document.querySelector('.repeater-container');
        const addButton = document.querySelector('.repeater-add');

        addButton.addEventListener('click', function() {
            const newItem = document.createElement('div');
            newItem.classList.add('repeater-item','two-thirds-one-third');
            newItem.innerHTML = `
                <div>
                    <x-text-input type="{{ $type }}" name="{{ $name }}[]" class="form-input" />
                </div>
                <div>
                    <button type="button" class="repeater-remove" aria-label="{{ __('inputs.repeater.remove') }}">&times;</button>
                </div>
            `;
            repeaterContainer.appendChild(newItem);
        });

        repeaterContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('repeater-remove')) {
                event.target.closest('.repeater-item').remove();
            }
        });
    });
</script>
@endpush
