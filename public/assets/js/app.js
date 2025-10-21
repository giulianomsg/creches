$(document).ready(function () {
    if ($('#tableAlunos').length) {
        new DataTable('#tableAlunos', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            }
        });
    }
    if ($('#tableRelatorio').length) {
        new DataTable('#tableRelatorio', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            }
        });
    }
    if ($('#tableLogs').length) {
        new DataTable('#tableLogs', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            }
        });
    }
    if ($('#tableUnits').length) {
        new DataTable('#tableUnits', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            }
        });
    }

    const chartEl = document.getElementById('chartVulnerabilidade');
    if (chartEl) {
        const ctx = chartEl.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Alta', 'Média', 'Necessidades Especiais'],
                datasets: [{
                    data: [chartEl.dataset.alta, chartEl.dataset.media, chartEl.dataset.especiais],
                    backgroundColor: ['#dc3545', '#ffc107', '#0d6efd'],
                }]
            }
        });
    }

    $('#addIrmaoLista').on('click', function () {
        $('#listaIrmaos').append(`
            <div class="input-group mb-2">
                <input type="text" name="irmaos_lista[]" class="form-control" placeholder="Nome do irmão">
                <button class="btn btn-outline-secondary remove-linha" type="button">Remover</button>
            </div>`);
    });

    $('#addIrmaoMatriculado').on('click', function () {
        $('#listaIrmaosMatriculados').append(`
            <div class="row g-2 align-items-center mb-2">
                <div class="col-md-5">
                    <input type="text" name="irmaos_matriculados[nome][]" class="form-control" placeholder="Nome">
                </div>
                <div class="col-md-5">
                    <input type="text" name="irmaos_matriculados[escola][]" class="form-control" placeholder="Escola">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary remove-linha" type="button">Remover</button>
                </div>
            </div>`);
    });

    $(document).on('click', '.remove-linha', function () {
        $(this).closest('.input-group, .row').remove();
    });

    $('#cep').on('blur', function () {
        const cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`).then(r => r.json()).then(data => {
                if (data.erro) return;
                $('#endereco').val(data.logradouro);
                $('#bairro').val(data.bairro);
                $('#endereco').trigger('change');
                $('#bairro').trigger('change');
            });
        }
    });

    function debounce(fn, wait) {
        let timeout;
        return function () {
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                fn.apply(null, args);
            }, wait);
        };
    }

    function parseCoordinate(value) {
        if (typeof value !== 'string') {
            return Number.NaN;
        }
        const normalized = value.replace(',', '.');
        const parsed = parseFloat(normalized);
        return Number.isFinite(parsed) ? parsed : Number.NaN;
    }

    function initCoordinateMaps() {
        if (typeof L === 'undefined') {
            return;
        }

        document.querySelectorAll('.coordinate-map').forEach(function (container) {
            if (container.dataset.mapInitialized) {
                return;
            }
            container.dataset.mapInitialized = '1';

            const latSelector = container.getAttribute('data-lat-input');
            const lngSelector = container.getAttribute('data-lng-input');
            const latInput = latSelector ? document.querySelector(latSelector) : null;
            const lngInput = lngSelector ? document.querySelector(lngSelector) : null;
            const hasInputs = Boolean(latInput && lngInput);
            const latValueAttr = container.getAttribute('data-lat-value');
            const lngValueAttr = container.getAttribute('data-lng-value');
            const context = container.getAttribute('data-geocode-context') || '';

            const defaultPosition = [-20.811307, -49.375781];
            let lat = hasInputs ? parseCoordinate(latInput.value) : parseCoordinate(latValueAttr || '');
            let lng = hasInputs ? parseCoordinate(lngInput.value) : parseCoordinate(lngValueAttr || '');
            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                lat = defaultPosition[0];
                lng = defaultPosition[1];
            }

            const zoom = parseInt(container.getAttribute('data-zoom'), 10) || 14;
            const map = L.map(container).setView([lat, lng], zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap colaboradores'
            }).addTo(map);

            const marker = L.marker([lat, lng], { draggable: hasInputs }).addTo(map);

            function setMarker(newLat, newLng, recenter) {
                if (Number.isNaN(newLat) || Number.isNaN(newLng)) {
                    return;
                }
                marker.setLatLng([newLat, newLng]);
                if (recenter) {
                    map.setView([newLat, newLng], map.getZoom());
                }
                if (hasInputs) {
                    latInput.value = newLat.toFixed(6);
                    lngInput.value = newLng.toFixed(6);
                }
            }

            if (hasInputs) {
                marker.on('dragend', function (event) {
                    const position = event.target.getLatLng();
                    latInput.value = position.lat.toFixed(6);
                    lngInput.value = position.lng.toFixed(6);
                    map.panTo(position);
                });

                const updateFromInputs = function () {
                    const parsedLat = parseCoordinate(latInput.value);
                    const parsedLng = parseCoordinate(lngInput.value);
                    if (!Number.isNaN(parsedLat) && !Number.isNaN(parsedLng)) {
                        setMarker(parsedLat, parsedLng, true);
                    }
                };

                ['change', 'blur'].forEach(function (evt) {
                    latInput.addEventListener(evt, updateFromInputs);
                    lngInput.addEventListener(evt, updateFromInputs);
                });

                const addressSelectors = (container.getAttribute('data-address-fields') || '')
                    .split(',')
                    .map(function (selector) { return selector.trim(); })
                    .filter(function (selector) { return selector.length > 0; });

                if (addressSelectors.length) {
                    const addressInputs = addressSelectors
                        .map(function (selector) { return document.querySelector(selector); })
                        .filter(function (input) { return input; });

                    let lastQuery = '';
                    const updateFromAddress = debounce(function () {
                        const parts = addressInputs
                            .map(function (input) { return input.value.trim(); })
                            .filter(function (value) { return value.length > 0; });

                        if (parts.length < 2) {
                            return;
                        }

                        const query = parts.join(', ') + (context ? ', ' + context : '');
                        if (query === lastQuery) {
                            return;
                        }
                        lastQuery = query;

                        fetch('https://geocode.maps.co/search?q=' + encodeURIComponent(query))
                            .then(function (response) { return response.ok ? response.json() : null; })
                            .then(function (data) {
                                if (!data || !data.length) {
                                    return;
                                }
                                const point = data[0];
                                const resultLat = parseFloat(point.lat);
                                const resultLng = parseFloat(point.lon);
                                if (Number.isNaN(resultLat) || Number.isNaN(resultLng)) {
                                    return;
                                }
                                setMarker(resultLat, resultLng, true);
                            })
                            .catch(function () {
                                // ignora falhas de geocodificação
                            });
                    }, 800);

                    addressInputs.forEach(function (input) {
                        ['change', 'blur', 'keyup'].forEach(function (evt) {
                            input.addEventListener(evt, updateFromAddress);
                        });
                    });
                }
            }

            setTimeout(function () {
                map.invalidateSize();
            }, 250);
        });
    }

    initCoordinateMaps();
});
