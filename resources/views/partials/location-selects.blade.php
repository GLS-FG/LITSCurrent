{{--
    Selects en cascada País -> Estado -> Ciudad (mismo comportamiento que address/create).
    Uso: initLocationSelects({ old: { country, state, city } })
    Los ids esperados son country_id, state_id y (opcional) city_id.
--}}
<script>
    window.initLocationSelects = function (options = {}) {
        const old = options.old || {};
        const countrySelect = document.getElementById('country_id');
        const stateSelect = document.getElementById('state_id');
        const citySelect = document.getElementById('city_id');

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

        if (old.country) {
            countrySelect.value = old.country;
            loadStates(old.country, old.state, old.city);
        }
    };
</script>
