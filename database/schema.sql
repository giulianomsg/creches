-- Schema bootstrap para o sistema de creches
-- Executa criacao inicial e garante atualizacao de estruturas existentes

-- Usuarios do sistema
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(64) NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NULL,
    role ENUM('admin','cadastro') NOT NULL DEFAULT 'cadastro',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY idx_users_google_id (google_id),
    UNIQUE KEY idx_users_email (email)
);

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS google_id VARCHAR(64) NULL,
    ADD COLUMN IF NOT EXISTS name VARCHAR(150) NOT NULL,
    ADD COLUMN IF NOT EXISTS email VARCHAR(150) NOT NULL,
    ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS role ENUM('admin','cadastro') NOT NULL DEFAULT 'cadastro',
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE users
    MODIFY COLUMN name VARCHAR(150) NOT NULL,
    MODIFY COLUMN email VARCHAR(150) NOT NULL,
    MODIFY COLUMN role ENUM('admin','cadastro') NOT NULL DEFAULT 'cadastro';

ALTER TABLE users
    ADD UNIQUE INDEX IF NOT EXISTS idx_users_google_id (google_id),
    ADD UNIQUE INDEX IF NOT EXISTS idx_users_email (email);

-- Unidades escolares
CREATE TABLE IF NOT EXISTS units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    macrorregiao VARCHAR(100) NOT NULL,
    cep VARCHAR(20) NOT NULL,
    latitude VARCHAR(50) NULL,
    longitude VARCHAR(50) NULL,
    capacidade INT DEFAULT 0,
    telefone_fixo VARCHAR(20) NULL,
    telefone_celular VARCHAR(20) NULL,
    whatsapp VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE units
    ADD COLUMN IF NOT EXISTS name VARCHAR(150) NOT NULL,
    ADD COLUMN IF NOT EXISTS endereco VARCHAR(255) NOT NULL,
    ADD COLUMN IF NOT EXISTS numero VARCHAR(20) NOT NULL,
    ADD COLUMN IF NOT EXISTS bairro VARCHAR(100) NOT NULL,
    ADD COLUMN IF NOT EXISTS macrorregiao VARCHAR(100) NOT NULL,
    ADD COLUMN IF NOT EXISTS cep VARCHAR(20) NOT NULL,
    ADD COLUMN IF NOT EXISTS latitude VARCHAR(50) NULL,
    ADD COLUMN IF NOT EXISTS longitude VARCHAR(50) NULL,
    ADD COLUMN IF NOT EXISTS capacidade INT DEFAULT 0,
    ADD COLUMN IF NOT EXISTS telefone_fixo VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS telefone_celular VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS whatsapp VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS email VARCHAR(150) NULL,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE units
    MODIFY COLUMN name VARCHAR(150) NOT NULL,
    MODIFY COLUMN endereco VARCHAR(255) NOT NULL,
    MODIFY COLUMN numero VARCHAR(20) NOT NULL,
    MODIFY COLUMN bairro VARCHAR(100) NOT NULL,
    MODIFY COLUMN macrorregiao VARCHAR(100) NOT NULL,
    MODIFY COLUMN cep VARCHAR(20) NOT NULL,
    MODIFY COLUMN capacidade INT DEFAULT 0,
    MODIFY COLUMN telefone_fixo VARCHAR(20) NULL,
    MODIFY COLUMN telefone_celular VARCHAR(20) NULL,
    MODIFY COLUMN whatsapp VARCHAR(20) NULL,
    MODIFY COLUMN email VARCHAR(150) NULL;

-- Alunos
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    data_nascimento DATE NOT NULL,
    sexo ENUM('Masculino','Feminino','Outro') NOT NULL,
    nome_mae VARCHAR(150) NOT NULL,
    nome_pai VARCHAR(150) NULL,
    telefone VARCHAR(50) NOT NULL,
    requerente ENUM('PAI','MÃE','RESPONSÁVEL LEGAL','AVÓS') NOT NULL,
    possui_gemeo TINYINT(1) DEFAULT 0,
    nome_gemeo VARCHAR(150) NULL,
    possui_irmao_lista TINYINT(1) DEFAULT 0,
    necessidades_especiais TINYINT(1) DEFAULT 0,
    mae_trabalha TINYINT(1) DEFAULT 0,
    mae_adolescente TINYINT(1) DEFAULT 0,
    sob_guarda_avo TINYINT(1) DEFAULT 0,
    pais_deficientes TINYINT(1) DEFAULT 0,
    filho_servidor TINYINT(1) DEFAULT 0,
    servidor_municipal TINYINT(1) DEFAULT 0,
    bolsa_familia TINYINT(1) DEFAULT 0,
    nis VARCHAR(20) NULL,
    alta_vulnerabilidade TINYINT(1) DEFAULT 0,
    media_vulnerabilidade TINYINT(1) DEFAULT 0,
    endereco VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(150) NULL,
    bairro VARCHAR(100) NOT NULL,
    cep VARCHAR(20) NOT NULL,
    latitude VARCHAR(50) NULL,
    longitude VARCHAR(50) NULL,
    status ENUM('analise','documentacao_incompleta','lista_espera','vaga_concedida') DEFAULT 'analise',
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

ALTER TABLE students
    ADD COLUMN IF NOT EXISTS nome VARCHAR(150) NOT NULL,
    ADD COLUMN IF NOT EXISTS data_nascimento DATE NOT NULL,
    ADD COLUMN IF NOT EXISTS sexo ENUM('Masculino','Feminino','Outro') NOT NULL,
    ADD COLUMN IF NOT EXISTS nome_mae VARCHAR(150) NOT NULL,
    ADD COLUMN IF NOT EXISTS nome_pai VARCHAR(150) NULL,
    ADD COLUMN IF NOT EXISTS telefone VARCHAR(50) NOT NULL,
    ADD COLUMN IF NOT EXISTS requerente ENUM('PAI','MÃE','RESPONSÁVEL LEGAL','AVÓS') NOT NULL,
    ADD COLUMN IF NOT EXISTS possui_gemeo TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS nome_gemeo VARCHAR(150) NULL,
    ADD COLUMN IF NOT EXISTS possui_irmao_lista TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS necessidades_especiais TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS mae_trabalha TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS mae_adolescente TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS sob_guarda_avo TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS pais_deficientes TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS filho_servidor TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS servidor_municipal TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS bolsa_familia TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS nis VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS alta_vulnerabilidade TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS media_vulnerabilidade TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS endereco VARCHAR(255) NOT NULL,
    ADD COLUMN IF NOT EXISTS numero VARCHAR(20) NOT NULL,
    ADD COLUMN IF NOT EXISTS complemento VARCHAR(150) NULL,
    ADD COLUMN IF NOT EXISTS bairro VARCHAR(100) NOT NULL,
    ADD COLUMN IF NOT EXISTS cep VARCHAR(20) NOT NULL,
    ADD COLUMN IF NOT EXISTS latitude VARCHAR(50) NULL,
    ADD COLUMN IF NOT EXISTS longitude VARCHAR(50) NULL,
    ADD COLUMN IF NOT EXISTS status ENUM('analise','documentacao_incompleta','lista_espera','vaga_concedida') DEFAULT 'analise',
    ADD COLUMN IF NOT EXISTS created_by INT NULL,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE students
    MODIFY COLUMN nome VARCHAR(150) NOT NULL,
    MODIFY COLUMN data_nascimento DATE NOT NULL,
    MODIFY COLUMN sexo ENUM('Masculino','Feminino','Outro') NOT NULL,
    MODIFY COLUMN nome_mae VARCHAR(150) NOT NULL,
    MODIFY COLUMN telefone VARCHAR(50) NOT NULL,
    MODIFY COLUMN requerente ENUM('PAI','MÃE','RESPONSÁVEL LEGAL','AVÓS') NOT NULL,
    MODIFY COLUMN possui_gemeo TINYINT(1) DEFAULT 0,
    MODIFY COLUMN possui_irmao_lista TINYINT(1) DEFAULT 0,
    MODIFY COLUMN necessidades_especiais TINYINT(1) DEFAULT 0,
    MODIFY COLUMN mae_trabalha TINYINT(1) DEFAULT 0,
    MODIFY COLUMN mae_adolescente TINYINT(1) DEFAULT 0,
    MODIFY COLUMN sob_guarda_avo TINYINT(1) DEFAULT 0,
    MODIFY COLUMN pais_deficientes TINYINT(1) DEFAULT 0,
    MODIFY COLUMN filho_servidor TINYINT(1) DEFAULT 0,
    MODIFY COLUMN servidor_municipal TINYINT(1) DEFAULT 0,
    MODIFY COLUMN bolsa_familia TINYINT(1) DEFAULT 0,
    MODIFY COLUMN alta_vulnerabilidade TINYINT(1) DEFAULT 0,
    MODIFY COLUMN media_vulnerabilidade TINYINT(1) DEFAULT 0,
    MODIFY COLUMN endereco VARCHAR(255) NOT NULL,
    MODIFY COLUMN numero VARCHAR(20) NOT NULL,
    MODIFY COLUMN bairro VARCHAR(100) NOT NULL,
    MODIFY COLUMN cep VARCHAR(20) NOT NULL,
    MODIFY COLUMN status ENUM('analise','documentacao_incompleta','lista_espera','vaga_concedida') DEFAULT 'analise';

-- Documentos enviados
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    arquivo VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

ALTER TABLE documents
    ADD COLUMN IF NOT EXISTS student_id INT NOT NULL,
    ADD COLUMN IF NOT EXISTS tipo VARCHAR(100) NOT NULL,
    ADD COLUMN IF NOT EXISTS arquivo VARCHAR(200) NOT NULL,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE documents
    MODIFY COLUMN tipo VARCHAR(100) NOT NULL,
    MODIFY COLUMN arquivo VARCHAR(200) NOT NULL;

-- Escolhas de unidades pelos alunos
CREATE TABLE IF NOT EXISTS student_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    unit_id INT NOT NULL,
    priority INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

ALTER TABLE student_units
    ADD COLUMN IF NOT EXISTS student_id INT NOT NULL,
    ADD COLUMN IF NOT EXISTS unit_id INT NOT NULL,
    ADD COLUMN IF NOT EXISTS priority INT NOT NULL;

ALTER TABLE student_units
    MODIFY COLUMN priority INT NOT NULL;

-- Irmaos aguardando vaga
CREATE TABLE IF NOT EXISTS student_waiting_siblings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

ALTER TABLE student_waiting_siblings
    ADD COLUMN IF NOT EXISTS student_id INT NOT NULL,
    ADD COLUMN IF NOT EXISTS nome VARCHAR(150) NOT NULL;

ALTER TABLE student_waiting_siblings
    MODIFY COLUMN nome VARCHAR(150) NOT NULL;

-- Irmaos matriculados
CREATE TABLE IF NOT EXISTS student_enrolled_siblings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    escola VARCHAR(150) NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

ALTER TABLE student_enrolled_siblings
    ADD COLUMN IF NOT EXISTS student_id INT NOT NULL,
    ADD COLUMN IF NOT EXISTS nome VARCHAR(150) NOT NULL,
    ADD COLUMN IF NOT EXISTS escola VARCHAR(150) NULL;

ALTER TABLE student_enrolled_siblings
    MODIFY COLUMN nome VARCHAR(150) NOT NULL;

-- Regras de prioridade
CREATE TABLE IF NOT EXISTS priority_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(150) NOT NULL,
    peso INT NOT NULL DEFAULT 0,
    ordem INT NOT NULL DEFAULT 0
);

ALTER TABLE priority_rules
    ADD COLUMN IF NOT EXISTS chave VARCHAR(50) NOT NULL,
    ADD COLUMN IF NOT EXISTS descricao VARCHAR(150) NOT NULL,
    ADD COLUMN IF NOT EXISTS peso INT NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS ordem INT NOT NULL DEFAULT 0;

ALTER TABLE priority_rules
    MODIFY COLUMN chave VARCHAR(50) NOT NULL,
    MODIFY COLUMN descricao VARCHAR(150) NOT NULL,
    MODIFY COLUMN peso INT NOT NULL DEFAULT 0,
    MODIFY COLUMN ordem INT NOT NULL DEFAULT 0;

ALTER TABLE priority_rules
    ADD UNIQUE INDEX IF NOT EXISTS idx_priority_rules_chave (chave);

INSERT INTO priority_rules (chave, descricao, peso, ordem) VALUES
    ('mae_trabalha', 'Mãe trabalha', 10, 1),
    ('alta_vulnerabilidade', 'Alta vulnerabilidade social', 15, 2),
    ('media_vulnerabilidade', 'Média vulnerabilidade social', 10, 3),
    ('necessidades_especiais', 'Criança com necessidades especiais', 20, 4),
    ('irmao_lista', 'Irmão aguardando vaga', 8, 5),
    ('filho_servidor', 'Filho de servidor municipal', 6, 6),
    ('servidor_municipal', 'Responsável é servidor municipal', 4, 7),
    ('bolsa_familia', 'Beneficiário do Bolsa Família', 12, 8),
    ('mae_adolescente', 'Mãe adolescente matriculada em ensino público', 10, 9),
    ('pais_deficientes', 'Pais portadores de deficiência', 10, 10)
ON DUPLICATE KEY UPDATE
    descricao = VALUES(descricao),
    peso = VALUES(peso),
    ordem = VALUES(ordem);

-- Logs de auditoria
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    acao VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    ip VARCHAR(50) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE logs
    ADD COLUMN IF NOT EXISTS user_id INT NULL,
    ADD COLUMN IF NOT EXISTS acao VARCHAR(50) NOT NULL,
    ADD COLUMN IF NOT EXISTS descricao TEXT NOT NULL,
    ADD COLUMN IF NOT EXISTS ip VARCHAR(50) NULL,
    ADD COLUMN IF NOT EXISTS criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE logs
    MODIFY COLUMN acao VARCHAR(50) NOT NULL,
    MODIFY COLUMN descricao TEXT NOT NULL;

