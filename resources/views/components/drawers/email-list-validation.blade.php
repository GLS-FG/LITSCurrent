@props(['id', 'events' => []])
{{--
    Live validation for a field that takes emails separated by ";" (mirrors the
    server rule App\Rules\SemicolonSeparatedEmails: split by ";", skip blanks,
    every item must be an email). While the user types it flags the first bad
    address, colours the field red and blocks the native submit with the same
    message; the server rule stays the final authority.

    The input needs the id given in $id and a <p id="{$id}_error" class="hidden">
    under it for the message. $events are window events after which the value may
    have changed programmatically (drawer closed / record loaded), so it is
    re-checked.
--}}
@push('custom_script')
    <script type="module">
        (function () {
            const input = document.getElementById(@js($id));
            const message = document.getElementById(@js($id . '_error'));
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            function firstInvalid(value) {
                for (const part of value.split(';')) {
                    const email = part.trim();
                    if (email !== '' && !emailPattern.test(email)) {
                        return email;
                    }
                }
                return null;
            }

            // Sets the native validity (blocks submit) and, when asked, shows the
            // message under the field. Returns true when the value is valid.
            function validate(show) {
                const bad = firstInvalid(input.value);
                if (bad === null) {
                    input.setCustomValidity('');
                    message.textContent = '';
                    message.classList.add('hidden');
                    return true;
                }
                const text = '"' + bad + '" no es un email válido. Sepáralos con punto y coma ";".';
                input.setCustomValidity(text);
                if (show) {
                    message.textContent = text;
                    message.classList.remove('hidden');
                }
                return false;
            }

            input.addEventListener('input', function () {
                if (validate(true)) {
                    input.removeAttribute('data-touched');
                } else {
                    input.setAttribute('data-touched', 'true');
                }
            });
            input.addEventListener('blur', function () {
                if (!validate(true)) {
                    input.setAttribute('data-touched', 'true');
                }
            });
            input.addEventListener('invalid', function () { validate(true); });

            @foreach($events as $event)
                window.addEventListener(@js($event), function () {
                    // Runs after the drawer has restored/filled the value.
                    setTimeout(function () {
                        input.removeAttribute('data-touched');
                        validate(true);
                    }, 0);
                });
            @endforeach

            validate(false);
        })();
    </script>
@endpush
