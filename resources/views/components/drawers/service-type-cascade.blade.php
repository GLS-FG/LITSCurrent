@props(['prefix', 'classTarget', 'modeTarget' => null, 'typeTarget' => null, 'levelTarget' => null])
{{--
    Drives the Service Class -> Mode -> Class Type -> Level cascading selects of
    a drawer form. The four <select>s must carry the ids
    "{prefix}service_class_id", "{prefix}service_mode_id",
    "{prefix}class_type_id" and "{prefix}service_level_id".

    The *Target values are the ones to pre-select on the first pass (a saved
    record's values, or old() input after a failed validation); when null the
    cascade simply picks the first option at each level. Dispatch
    "{prefix}cascade-reset" on window to restore that initial state.
--}}
@push('custom_script')
    <script type="module">
        (function () {
            const prefix = @js($prefix);
            const targets = {
                cls: @js((string) $classTarget),
                mode: @js(is_null($modeTarget) ? null : (string) $modeTarget),
                type: @js(is_null($typeTarget) ? null : (string) $typeTarget),
                level: @js(is_null($levelTarget) ? null : (string) $levelTarget),
            };
            const $serviceClass = $('#' + prefix + 'service_class_id');
            const $serviceMode = $('#' + prefix + 'service_mode_id');
            const $classType = $('#' + prefix + 'class_type_id');
            const $serviceLevel = $('#' + prefix + 'service_level_id');
            let serviceModes = [];
            let useModeTarget = targets.mode !== null;
            let useTypeTarget = targets.type !== null;
            let useLevelTarget = targets.level !== null;

            function fill($select, items) {
                $select.empty();
                for (const item of items) {
                    $select.append(new Option(item.name, item.id));
                }
            }
            function fetchServiceTypes(serviceClassId) {
                $.ajax({
                    url: @js(route('autocomplete.serviceTypes')),
                    type: 'GET',
                    dataType: 'json',
                    data: { service_type: serviceClassId },
                    success: function (data) {
                        serviceModes = data;
                        fill($serviceMode, serviceModes);
                        if (useModeTarget) {
                            useModeTarget = false;
                            $serviceMode.val(targets.mode).change();
                        } else {
                            $serviceMode.val(serviceModes[0].id).change();
                        }
                    }
                });
            }
            $serviceClass.change(function () {
                fetchServiceTypes($(this).val());
            });
            $serviceMode.change(function () {
                const classTypes = serviceModes.find(m => m.id == $serviceMode.val()).class_types;
                fill($classType, classTypes);
                if (useTypeTarget) {
                    useTypeTarget = false;
                    $classType.val(targets.type).change();
                } else {
                    $classType.val(classTypes[0].id).change();
                }
            });
            $classType.change(function () {
                const serviceLevels = serviceModes.find(m => m.id == $serviceMode.val()).class_types.find(m => m.id == $classType.val()).service_levels;
                fill($serviceLevel, serviceLevels);
                if (useLevelTarget) {
                    useLevelTarget = false;
                    $serviceLevel.val(targets.level).change();
                } else {
                    $serviceLevel.val(serviceLevels[0].id).change();
                }
            });

            window.addEventListener(prefix + 'cascade-reset', function () {
                useModeTarget = targets.mode !== null;
                useTypeTarget = targets.type !== null;
                useLevelTarget = targets.level !== null;
                $serviceClass.val(targets.cls);
                fetchServiceTypes(targets.cls);
            });

            fetchServiceTypes(targets.cls);
        })();
    </script>
@endpush
