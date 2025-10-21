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
    if ($('#tableUsers').length) {
        new DataTable('#tableUsers', {
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

    function formatCep(value) {
        if (typeof value !== 'string') {
            return '';
        }
        const digits = value.replace(/\D/g, '');
        if (digits.length !== 8) {
            return value;
        }
        return digits.substring(0, 5) + '-' + digits.substring(5);
    }

    function fillInput(selector, value) {
        if (!selector) {
            return;
        }
        const $target = $(selector);
        if (!$target.length) {
            return;
        }
        $target.val(value || '');
        $target.trigger('change');
        $target.trigger('blur');
        $target.trigger('keyup');
    }

    function setupCepAutocomplete() {
        $('[data-cep-autocomplete]').each(function () {
            const $input = $(this);

            const initial = $input.val();
            if (initial) {
                const formattedInitial = formatCep(initial.toString());
                if (formattedInitial !== initial) {
                    $input.val(formattedInitial);
                }
            }

            let lastDigits = '';

            const handleCepLookup = function () {
                const raw = $input.val();
                const digits = raw ? raw.toString().replace(/\D/g, '') : '';
                if (digits.length !== 8) {
                    lastDigits = '';
                    return;
                }

                if (digits === lastDigits) {
                    return;
                }
                lastDigits = digits;

                fetch(`https://viacep.com.br/ws/${digits}/json/`)
                    .then(function (response) { return response.ok ? response.json() : null; })
                    .then(function (data) {
                        if (!data || data.erro) {
                            lastDigits = '';
                            return;
                        }
                        const formatted = formatCep(digits);
                        $input.val(formatted);
                        fillInput($input.data('cepLogradouro'), data.logradouro || '');
                        fillInput($input.data('cepBairro'), data.bairro || '');
                    })
                    .catch(function () {
                        lastDigits = '';
                        // ignora falhas de rede
                    });
            };

            $input.on('blur', handleCepLookup);
            $input.on('change', handleCepLookup);
        });
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
    setupCepAutocomplete();

    const macroChartEl = document.getElementById('chartMacroRegiao');
    if (macroChartEl) {
        try {
            const labels = JSON.parse(macroChartEl.dataset.labels || '[]');
            const values = JSON.parse(macroChartEl.dataset.values || '[]').map(function (value) {
                return Number.parseInt(value, 10) || 0;
            });

            if (labels.length && values.length) {
                new Chart(macroChartEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Solicitações',
                            data: values,
                            backgroundColor: '#0d6efd',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.warn('Não foi possível inicializar o gráfico de macrorregiões.', error);
        }
    }

    function initHeatmap() {
        if (typeof L === 'undefined') {
            return;
        }

        const container = document.getElementById('dashboardHeatmap');
        if (!container) {
            return;
        }

        let points = [];
        try {
            points = JSON.parse(container.dataset.points || '[]');
        } catch (error) {
            console.warn('Não foi possível ler os pontos do mapa de calor.', error);
        }

        const defaultCenterAttr = container.getAttribute('data-default-center') || '-20.811307,-49.375781';
        const centerParts = defaultCenterAttr.split(',').map(function (value) {
            return parseFloat(value);
        });
        const defaultCenter = [
            Number.isFinite(centerParts[0]) ? centerParts[0] : -20.811307,
            Number.isFinite(centerParts[1]) ? centerParts[1] : -49.375781
        ];

        const map = L.map(container).setView(defaultCenter, 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap colaboradores'
        }).addTo(map);

        if (points.length) {
            const group = L.layerGroup().addTo(map);
            let maxWeight = 0;
            const bounds = [];

            points.forEach(function (point) {
                const lat = parseFloat(point.lat);
                const lng = parseFloat(point.lng);
                const weight = Number.isFinite(point.weight) ? point.weight : 1;
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                    return;
                }
                maxWeight = Math.max(maxWeight, weight);
                bounds.push([lat, lng]);
            });

            const resolveColor = function (intensity) {
                if (intensity < 0.33) {
                    return '#0d6efd';
                }
                if (intensity < 0.66) {
                    return '#ffc107';
                }
                return '#dc3545';
            };

            points.forEach(function (point) {
                const lat = parseFloat(point.lat);
                const lng = parseFloat(point.lng);
                const weight = Number.isFinite(point.weight) ? point.weight : 1;
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                    return;
                }
                const intensity = maxWeight > 0 ? weight / maxWeight : 0;
                const color = resolveColor(intensity);
                const radius = 150 + (intensity * 350);
                L.circle([lat, lng], {
                    radius: radius,
                    stroke: false,
                    fillColor: color,
                    fillOpacity: 0.45
                }).addTo(group);
            });

            if (bounds.length) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }
        }

        const legend = L.control({ position: 'bottomright' });
        legend.onAdd = function () {
            const div = L.DomUtil.create('div', 'heatmap-legend');
            div.innerHTML = '<div class="fw-semibold">Intensidade</div>' +
                '<div class="gradient-bar"></div>' +
                '<div class="d-flex justify-content-between mt-1"><small>Menor</small><small>Maior</small></div>';
            return div;
        };
        legend.addTo(map);

        setTimeout(function () {
            map.invalidateSize();
        }, 300);
    }

    initHeatmap();

    function buildWhatsappUrl(value) {
        if (!value) {
            return null;
        }

        var digits = value.toString().replace(/\D/g, '');
        if (!digits.length) {
            return null;
        }

        if (digits.substring(0, 2) === '55' && digits.length > 11) {
            digits = digits.substring(2);
        }

        if (digits.length < 10 || digits.length > 11) {
            return null;
        }

        return 'https://wa.me/55' + digits;
    }

    function setupWhatsappLaunchers() {
        document.querySelectorAll('[data-whatsapp-input]').forEach(function (input) {
            var targetSelector = input.getAttribute('data-whatsapp-target');
            if (!targetSelector) {
                return;
            }

            var launcher = document.querySelector(targetSelector);
            if (!launcher) {
                return;
            }

            var updateLink = function () {
                var link = buildWhatsappUrl(input.value);
                if (link) {
                    launcher.classList.remove('disabled');
                    launcher.removeAttribute('aria-disabled');
                    launcher.setAttribute('href', link);
                } else {
                    launcher.classList.add('disabled');
                    launcher.setAttribute('aria-disabled', 'true');
                    launcher.setAttribute('href', '#');
                }
            };

            ['input', 'change', 'blur'].forEach(function (evt) {
                input.addEventListener(evt, updateLink);
            });

            launcher.addEventListener('click', function (event) {
                if (launcher.classList.contains('disabled') || launcher.getAttribute('href') === '#') {
                    event.preventDefault();
                }
            });

            updateLink();
        });
    }

    setupWhatsappLaunchers();
});
