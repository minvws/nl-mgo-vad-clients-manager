@props([
    'qrCode',
    'secret',
    'encryptedSecret',
])

<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('profile.2fa.reset_title') }}</h1>
    </x-slot>

    <section class="visually-grouped">
        <div>
            <p>{{ __('profile.2fa.reset_instructions') }}</p>

            <div class="qr-code">
                {!! $qrCode !!}
            </div>

            <form method="POST" action="{{ route('profile.2fa.confirm') }}">
                @csrf
                <input type="hidden" name="qr-code" value="{{ $qrCode }}">
                <input type="hidden" name="encrypted_secret" value="{{ $encryptedSecret }}">
                <div class="form-group">
                    <label for="code">{{ __('profile.2fa.verification_code') }}</label>
                    <input type="text" id="code" name="code" required autocomplete="off" pattern="[0-9]{6}">
                </div>

                <div class="button-group">
                    <button type="submit" class="button">{{ __('profile.2fa.confirm') }}</button>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
