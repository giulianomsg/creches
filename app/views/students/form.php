<?php
    $isEdit = !empty($student);
    $action = $isEdit ? '/?route=students/update' : '/?route=students/store';
    $token = App\Helpers\CSRFHelper::token();
    $old = $_SESSION['form_old'] ?? [];
    $errors = $_SESSION['form_errors'] ?? [];
    unset($_SESSION['form_old'], $_SESSION['form_errors']);
?>
<h1 class="h3 mb-3"><?= $isEdit ? 'Editar Cadastro' : 'Novo Cadastro' ?></h1>
<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
    <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= $token ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $student['id'] ?>">
    <?php endif; ?>
    <div class="card mb-3">
        <div class="card-header">Dados da Criança</div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Nome do Aluno *</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($old['nome'] ?? $student['nome'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Nascimento *</label>
                <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($old['data_nascimento'] ?? $student['data_nascimento'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sexo *</label>
                <select name="sexo" class="form-select" required>
                    <?php $sexoSel = $old['sexo'] ?? $student['sexo'] ?? ''; ?>
                    <option value="">Selecione</option>
                    <?php foreach (['Masculino','Feminino','Outro'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $sexoSel === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nome da Mãe *</label>
                <input type="text" name="nome_mae" class="form-control" value="<?= htmlspecialchars($old['nome_mae'] ?? $student['nome_mae'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nome do Pai</label>
                <input type="text" name="nome_pai" class="form-control" value="<?= htmlspecialchars($old['nome_pai'] ?? $student['nome_pai'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Telefone / WhatsApp *</label>
                <input type="tel" name="telefone" class="form-control" value="<?= htmlspecialchars($old['telefone'] ?? $student['telefone'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Requerente *</label>
                <?php $reqSel = $old['requerente'] ?? $student['requerente'] ?? ''; ?>
                <select name="requerente" class="form-select" required>
                    <option value="">Selecione</option>
                    <?php foreach (['PAI','MÃE','RESPONSÁVEL LEGAL','AVÓS'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $reqSel === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">NIS</label>
                <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($old['nis'] ?? $student['nis'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <?php $checked = ($old['possui_gemeo'] ?? $student['possui_gemeo'] ?? 0) ? 'checked' : ''; ?>
                    <input type="checkbox" name="possui_gemeo" class="form-check-input" <?= $checked ?>>
                    <label class="form-check-label">Aluno possui irmão gêmeo</label>
                </div>
            </div>
            <div class="col-md-8">
                <label class="form-label">Nome do Irmão Gêmeo</label>
                <input type="text" name="nome_gemeo" class="form-control" value="<?= htmlspecialchars($old['nome_gemeo'] ?? $student['nome_gemeo'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Endereço</div>
        <div class="card-body row g-3">
            <div class="col-md-3">
                <label class="form-label">CEP *</label>
                <input type="text" name="cep" id="cep" class="form-control" value="<?= htmlspecialchars($old['cep'] ?? $student['cep'] ?? '') ?>" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Endereço *</label>
                <input type="text" name="endereco" id="endereco" class="form-control" value="<?= htmlspecialchars($old['endereco'] ?? $student['endereco'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Número *</label>
                <input type="text" name="numero" class="form-control" value="<?= htmlspecialchars($old['numero'] ?? $student['numero'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Complemento</label>
                <input type="text" name="complemento" class="form-control" value="<?= htmlspecialchars($old['complemento'] ?? $student['complemento'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bairro *</label>
                <input type="text" name="bairro" id="bairro" class="form-control" value="<?= htmlspecialchars($old['bairro'] ?? $student['bairro'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Latitude</label>
                <input type="text" name="latitude" class="form-control" value="<?= htmlspecialchars($old['latitude'] ?? $student['latitude'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Longitude</label>
                <input type="text" name="longitude" class="form-control" value="<?= htmlspecialchars($old['longitude'] ?? $student['longitude'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Comprovante de Residência</label>
                <input type="file" name="comprovante_residencia" class="form-control" accept=".pdf,image/*">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Critérios de Prioridade</div>
        <div class="card-body row g-3">
            <?php
            $checks = [
                'possui_irmao_lista' => 'Tem irmão aguardando vaga em creche',
                'necessidades_especiais' => 'Possui necessidades especiais',
                'mae_trabalha' => 'Mãe trabalha',
                'mae_adolescente' => 'Filha de mãe adolescente matriculada no ensino público',
                'sob_guarda_avo' => 'Sob guarda/tutela dos avós',
                'pais_deficientes' => 'Filho de portadores de deficiência',
                'filho_servidor' => 'Filho de servidor municipal',
                'servidor_municipal' => 'Responsável é servidor municipal',
                'bolsa_familia' => 'Beneficiário do Bolsa Família',
                'alta_vulnerabilidade' => 'Alta vulnerabilidade social',
                'media_vulnerabilidade' => 'Média vulnerabilidade social',
            ];
            ?>
            <?php foreach ($checks as $field => $label): ?>
                <?php $checked = ($old[$field] ?? $student[$field] ?? 0) ? 'checked' : ''; ?>
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" name="<?= $field ?>" class="form-check-input" <?= $checked ?>>
                        <label class="form-check-label"><?= $label ?></label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Declaração Necessidades Especiais</label>
                <input type="file" name="doc_necessidades" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Declaração Trabalho da Mãe</label>
                <input type="file" name="doc_trabalho_mae" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Declaração Mãe Menor</label>
                <input type="file" name="doc_mae_menor" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Declaração Guarda/Tutela</label>
                <input type="file" name="doc_guarda_avo" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Laudo Pais com Deficiência</label>
                <input type="file" name="doc_pais_deficientes" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Holerite Servidor</label>
                <input type="file" name="doc_holerite" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Declaração de Vulnerabilidade</label>
                <input type="file" name="doc_vulnerabilidade" class="form-control" accept=".pdf,image/*">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Documentos Obrigatórios</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Certidão de Nascimento</label>
                <input type="file" name="certidao_nascimento" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-4">
                <label class="form-label">RG</label>
                <input type="file" name="rg" class="form-control" accept=".pdf,image/*">
            </div>
            <div class="col-md-4">
                <label class="form-label">CPF</label>
                <input type="file" name="cpf" class="form-control" accept=".pdf,image/*">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Irmãos Aguardando Vaga</div>
        <div class="card-body">
            <div id="listaIrmaos">
                <?php $lista = $old['irmaos_lista'] ?? array_column($waitingSiblings, 'nome'); ?>
                <?php if (empty($lista)) $lista = ['']; ?>
                <?php foreach ($lista as $nome): ?>
                    <div class="input-group mb-2">
                        <input type="text" name="irmaos_lista[]" class="form-control" placeholder="Nome do irmão" value="<?= htmlspecialchars($nome) ?>">
                        <button class="btn btn-outline-secondary remove-linha" type="button">Remover</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-sm btn-outline-primary" type="button" id="addIrmaoLista">Adicionar irmão</button>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Irmãos Matriculados na Unidade</div>
        <div class="card-body">
            <div id="listaIrmaosMatriculados">
                <?php
                $listaMat = $old['irmaos_matriculados']['nome'] ?? array_column($enrolledSiblings, 'nome');
                $listaEscola = $old['irmaos_matriculados']['escola'] ?? array_column($enrolledSiblings, 'escola');
                if (empty($listaMat)) {
                    $listaMat = [''];
                    $listaEscola = [''];
                }
                ?>
                <?php foreach ($listaMat as $i => $nome): ?>
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-5">
                            <input type="text" name="irmaos_matriculados[nome][]" class="form-control" placeholder="Nome" value="<?= htmlspecialchars($nome) ?>">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="irmaos_matriculados[escola][]" class="form-control" placeholder="Escola" value="<?= htmlspecialchars($listaEscola[$i] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary remove-linha" type="button">Remover</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-sm btn-outline-primary" type="button" id="addIrmaoMatriculado">Adicionar irmão matriculado</button>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Unidades Pretendidas (até 5)</div>
        <div class="card-body">
            <?php
            $prefs = $old['unidades'] ?? array_column($preferences ?? [], 'unit_id');
            $quantidade = max(5, count($units));
            ?>
            <?php for ($i = 0; $i < 5; $i++): ?>
                <div class="mb-2">
                    <label class="form-label">Preferência <?= $i + 1 ?></label>
                    <select name="unidades[]" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach ($units as $unit): ?>
                            <?php $selected = ($prefs[$i] ?? '') == $unit['id'] ? 'selected' : ''; ?>
                            <option value="<?= $unit['id'] ?>" <?= $selected ?>><?= htmlspecialchars($unit['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Status do Cadastro</div>
        <div class="card-body">
            <?php $status = $old['status'] ?? $student['status'] ?? 'analise'; ?>
            <select name="status" class="form-select">
                <option value="analise" <?= $status === 'analise' ? 'selected' : '' ?>>Em análise</option>
                <option value="documentacao_incompleta" <?= $status === 'documentacao_incompleta' ? 'selected' : '' ?>>Documentação incompleta</option>
                <option value="lista_espera" <?= $status === 'lista_espera' ? 'selected' : '' ?>>Em lista de espera</option>
                <option value="vaga_concedida" <?= $status === 'vaga_concedida' ? 'selected' : '' ?>>Vaga concedida</option>
            </select>
        </div>
    </div>

    <div class="text-end">
        <button class="btn btn-success">Salvar</button>
        <a href="/?route=students" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
