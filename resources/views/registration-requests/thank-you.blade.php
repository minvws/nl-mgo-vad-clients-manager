<x-guest-layout>
    <section class="centered">
        <div class="visually-grouped">
            <h2 class="text-2xl font-semibold mb-2">{{ __('registration_request.thank_you.title') }}</h2>
            <p class="text-gray-600 mb-8">{{ __('registration_request.thank_you.subtitle') }}</p>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('registration_request.thank_you.next_steps.title') }}</h3>
                
                <ol class="space-y-3">
                    <li class="flex items-start">
                        <span class="text-gray-600">{{ __('registration_request.thank_you.next_steps.step1') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-gray-600">{{ __('registration_request.thank_you.next_steps.step2') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-gray-600">{{ __('registration_request.thank_you.next_steps.step3') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-gray-600">{{ __('registration_request.thank_you.next_steps.step4') }}</span>
                    </li>
                </ol>

                <p class="mt-6 text-gray-600">{{ __('registration_request.thank_you.contact') }}</p>
            </div>
        </div>
    </section>
</x-guest-layout> 