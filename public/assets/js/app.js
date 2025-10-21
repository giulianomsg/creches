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
            });
        }
    });

    // sem lógica adicional para unidades; telas dedicadas cuidam do preenchimento
});
