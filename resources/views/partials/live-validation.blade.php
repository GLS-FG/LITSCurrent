{{--
    Validación en el navegador para los formularios de creación.
    Muestra el mensaje en rojo debajo del campo mientras se escribe y al salir del campo.
    El servidor sigue validando todo; esto es solo ayuda visual.

    Uso: attachLiveValidation('id_del_campo', { ...reglas })
    Reglas: required, requiredMessage, min, max, numeric, email, regex, regexMessage,
            when (función: si devuelve false la regla no aplica),
            match (id de otro campo con el que debe coincidir), matchMessage,
            watch (ids de otros campos que, al cambiar, vuelven a validar este).
--}}
<script>
    window.attachLiveValidation = function (id, rules = {}) {
        const input = document.getElementById(id);
        if (!input) return;
        const hidden = rules.hidden ? document.getElementById(rules.hidden) : null;
        let errorEl = document.getElementById(id + '-error');
        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.id = id + '-error';
            errorEl.className = 'mt-1 text-sm text-red-500 dark:text-red-400 hidden';
            input.insertAdjacentElement('afterend', errorEl);
        }
        const errorClasses = ['outline-red-500', 'dark:outline-red-500'];
        const idleClasses = ['outline-gray-300', 'dark:outline-gray-600'];
        let touched = false;

        const messageFor = () => {
            if (rules.when && !rules.when()) return '';
            const requiredMessage = rules.requiredMessage || 'Este campo es obligatorio.';
            if (hidden) {
                return hidden.value.trim() === '' ? requiredMessage : '';
            }
            const value = input.value.trim();
            if (value.length === 0) {
                return rules.required ? requiredMessage : '';
            }
            if (rules.numeric && !/^\d+$/.test(value)) {
                return 'Solo se permiten números.';
            }
            if (rules.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                return 'Ingresa un email válido.';
            }
            if (rules.regex && !rules.regex.test(value)) {
                return rules.regexMessage || 'Contiene caracteres no permitidos.';
            }
            if (rules.min && value.length < rules.min) {
                return `Debe tener al menos ${rules.min} caracteres.`;
            }
            if (rules.max && value.length > rules.max) {
                return `Debe tener máximo ${rules.max} caracteres.`;
            }
            if (rules.match && value !== document.getElementById(rules.match)?.value) {
                return rules.matchMessage || 'Los valores no coinciden.';
            }
            return '';
        };

        const check = () => {
            const message = messageFor();
            if (message) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
                input.classList.remove(...idleClasses);
                input.classList.add(...errorClasses);
            } else {
                errorEl.classList.add('hidden');
                input.classList.remove(...errorClasses);
                input.classList.add(...idleClasses);
            }
        };

        if (hidden) {
            // Los autocompletes llenan el input oculto después del blur, por eso se espera un momento.
            input.addEventListener('blur', () => { touched = true; setTimeout(check, 250); });
            input.addEventListener('change', () => setTimeout(check, 250));
        } else {
            input.addEventListener('input', () => { touched = true; check(); });
            input.addEventListener('change', () => { touched = true; check(); });
            input.addEventListener('blur', () => { touched = true; check(); });
        }

        (rules.watch || []).forEach((watchedId) => {
            const watched = document.getElementById(watchedId);
            if (!watched) return;
            ['input', 'change'].forEach((evt) => watched.addEventListener(evt, () => {
                if (touched) setTimeout(check, 0);
            }));
        });

        // Registro para enableFormValidation (validar todo al enviar y pintar errores del servidor).
        // Es solo un registro: no cambia nada para quien no llame a esa función.
        window.__liveFields = window.__liveFields || [];
        window.__liveFields.push({
            input,
            serverKey: rules.serverKey || input.name,
            validate: () => { touched = true; check(); return messageFor() === ''; },
            showMessage: (message) => {
                touched = true;
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
                input.classList.remove(...idleClasses);
                input.classList.add(...errorClasses);
            },
        });
    };

    /*
        Opt-in por formulario. Uso: enableFormValidation('id_del_form', { errors: @json($errors->messages()) })
        - Al enviar valida todos los campos registrados; si alguno falla cancela el envío,
          resalta todos los inválidos y lleva el foco al primero.
        - Pinta bajo su campo los errores que devolvió el servidor (la clave es el name del input,
          o rules.serverKey si se declaró). Se borran solos en cuanto el usuario edita el campo.
        Devuelve las claves de error que no pertenecen a ningún campo registrado.
    */
    window.enableFormValidation = function (formId, options = {}) {
        const form = document.getElementById(formId);
        if (!form) return [];
        const fields = () => (window.__liveFields || []).filter((field) => field.input.form === form);

        form.addEventListener('submit', (event) => {
            const invalid = fields().filter((field) => !field.validate());
            if (invalid.length === 0) return;
            event.preventDefault();
            invalid[0].input.focus({ preventScroll: true });
            invalid[0].input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        const errors = options.errors || {};
        const matched = new Set();
        let firstWithError = null;
        fields().forEach((field) => {
            const messages = errors[field.serverKey];
            if (!messages) return;
            matched.add(field.serverKey);
            field.showMessage(Array.isArray(messages) ? messages[0] : messages);
            firstWithError = firstWithError || field;
        });
        if (firstWithError) {
            firstWithError.input.scrollIntoView({ block: 'center' });
        }
        return Object.keys(errors).filter((key) => !matched.has(key));
    };
</script>
