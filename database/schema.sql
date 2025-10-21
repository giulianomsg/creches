CREATE TABLE users (
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

CREATE TABLE units (
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
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

CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    arquivo VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE student_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    unit_id INT NOT NULL,
    priority INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

CREATE TABLE student_waiting_siblings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE student_enrolled_siblings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    escola VARCHAR(150) NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE priority_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(150) NOT NULL,
    peso INT NOT NULL DEFAULT 0,
    ordem INT NOT NULL DEFAULT 0
);

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
('pais_deficientes', 'Pais portadores de deficiência', 10, 10);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    acao VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    ip VARCHAR(50) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
