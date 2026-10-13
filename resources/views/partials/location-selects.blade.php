{{--
    Selects en cascada País -> Estado -> Ciudad (mismo comportamiento que address/create).
    Uso: initLocationSelects({ old: { country, state, city }, prefix: 'edit_' })
    Los ids esperados son country_id, state_id y (opcional) city_id; `prefix` (opcional) se
    antepone a los tres, para tener dos juegos de selects en la misma página (crear y editar).
    Devuelve { set(country, state, city), clear() } para llenar o vaciar los selects desde JS
    (set no dispara eventos `change`, así que no activa la validación en vivo).
--}}
<script>
    window.initLocationSelects = function (options = {}) {
        const old = options.old || {};
        const prefix = options.prefix || '';
        const countrySelect = document.getElementById(prefix + 'country_id');
        const stateSelect = document.getElementById(prefix + 'state_id');
        const citySelect = document.getElementById(prefix + 'city_id');

        const resetSelect = (select, placeholder) => {
            if (!select) return;
            select.innerHTML = '';
            const option = document.createElement('option');
            option.value = '';
            option.textContent = placeholder;
            select.appendChild(option);
        };

        const populateSelect = (select, items, selectedId) => {
            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                if (selectedId && String(item.value) === String(selectedId)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        };

        const loadCities = (stateId, selectedCityId = null) => {
            resetSelect(citySelect, 'Selecciona una ciudad');
            if (!citySelect || !stateId) return;
            fetch(`{{ route('autocomplete.cities') }}?all=1&state_id=${stateId}`)
                .then((res) => res.json())
                .then((data) => populateSelect(citySelect, data, selectedCityId));
        };

        const loadStates = (countryId, selectedStateId = null, selectedCityId = null) => {
            resetSelect(stateSelect, 'Selecciona un estado');
            resetSelect(citySelect, 'Selecciona una ciudad');
            if (!countryId) return;
            fetch(`{{ route('autocomplete.states') }}?all=1&country_id=${countryId}`)
                .then((res) => res.json())
                .then((data) => {
                    populateSelect(stateSelect, data, selectedStateId);
                    if (selectedStateId) {
                        loadCities(selectedStateId, selectedCityId);
                    }
                });
        };

        countrySelect.addEventListener('change', () => loadStates(countrySelect.value));
        stateSelect.addEventListener('change', () => loadCities(stateSelect.value));

        const api = {
            set(country, state = null, city = null) {
                countrySelect.value = country || '';
                if (country) {
                    loadStates(country, state, city);
                } else {
                    resetSelect(stateSelect, 'Selecciona un estado');
                    resetSelect(citySelect, 'Selecciona una ciudad');
                }
            },
            clear() { api.set(''); },
        };

        if (old.country) {
            api.set(old.country, old.state, old.city);
        }
        return api;
    };
</script>
