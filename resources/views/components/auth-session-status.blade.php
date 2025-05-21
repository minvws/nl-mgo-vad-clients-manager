@props(['status'])

@if ($status)
<section class="confirmation">
    <div>
        {{ $status }}
    </div>
</section>
@endif
