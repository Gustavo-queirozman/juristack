CREATE TABLE users (
    id CHAR(36) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADVOCATE', 'ENTERPRISE', 'CUSTOMER') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    email_verified TINYINT(1) NOT NULL DEFAULT 0,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE datajud_processos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    user_id CHAR(36) NULL,

    datajud_id VARCHAR(255) NULL,

    tribunal VARCHAR(20) NOT NULL,
    numero_processo VARCHAR(40) NOT NULL,
    data_ajuizamento DATETIME NULL,
    grau VARCHAR(10) NULL,
    nivel_sigilo BIGINT UNSIGNED NULL,

    formato_codigo BIGINT UNSIGNED NULL,
    formato_nome VARCHAR(255) NULL,

    sistema_codigo BIGINT UNSIGNED NULL,
    sistema_nome VARCHAR(255) NULL,

    classe_codigo BIGINT UNSIGNED NULL,
    classe_nome VARCHAR(255) NULL,

    orgao_julgador_codigo BIGINT UNSIGNED NULL,
    orgao_julgador_nome VARCHAR(255) NULL,
    orgao_julgador_codigo_municipio_ibge BIGINT UNSIGNED NULL,

    datahora_ultima_atualizacao DATETIME NULL,
    indexed_at DATETIME NULL,

    payload JSON NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_datajud_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    UNIQUE KEY uniq_user_trib_num_grau (user_id, tribunal, numero_processo, grau),

    KEY idx_datajud_id (datajud_id),
    KEY idx_tribunal_numero (tribunal, numero_processo)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE datajud_assuntos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    processo_id BIGINT UNSIGNED NOT NULL,

    codigo BIGINT UNSIGNED NULL,
    nome VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_datajud_assuntos_processo
        FOREIGN KEY (processo_id)
        REFERENCES datajud_processos(id)
        ON DELETE CASCADE,

    UNIQUE KEY uniq_processo_assunto_codigo (processo_id, codigo)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE datajud_movimentos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    processo_id BIGINT UNSIGNED NOT NULL,

    codigo BIGINT UNSIGNED NULL,
    nome VARCHAR(255) NULL,
    data_hora DATETIME NULL,

    orgao_codigo BIGINT UNSIGNED NULL,
    orgao_nome VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_datajud_movimentos_processo
        FOREIGN KEY (processo_id)
        REFERENCES datajud_processos(id)
        ON DELETE CASCADE,

    KEY idx_processo_datahora (processo_id, data_hora)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE datajud_movimento_complementos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    movimento_id BIGINT UNSIGNED NOT NULL,

    codigo BIGINT UNSIGNED NULL,
    descricao VARCHAR(255) NULL,
    valor BIGINT UNSIGNED NULL,
    nome VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_datajud_movimento_complementos_movimento
        FOREIGN KEY (movimento_id)
        REFERENCES datajud_movimentos(id)
        ON DELETE CASCADE,

    KEY idx_movimento_codigo (movimento_id, codigo)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE processo_monitores (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE processo_monitors (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    user_id CHAR(36) NOT NULL,
    processo_id BIGINT UNSIGNED NOT NULL,

    tribunal VARCHAR(255) NOT NULL,
    numero_processo VARCHAR(255) NOT NULL,

    ultima_verificacao DATETIME NULL,
    ultima_atualizacao_datajud DATETIME NULL,

    verificacoes_consecutivas_sem_mudanca INT NOT NULL DEFAULT 0,

    ativo TINYINT(1) NOT NULL DEFAULT 1,

    observacoes TEXT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_processo_monitors_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_processo_monitors_processo
        FOREIGN KEY (processo_id)
        REFERENCES datajud_processos(id)
        ON DELETE CASCADE,

    KEY idx_tribunal (tribunal),
    KEY idx_numero_processo (numero_processo),
    KEY idx_ativo (ativo)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clientes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    user_id CHAR(36) NOT NULL,

    type VARCHAR(2) NOT NULL, -- PF | PJ
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NULL,
    cnpj VARCHAR(18) NULL,
    email VARCHAR(255) NULL,
    telefone VARCHAR(20) NULL,

    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_clientes_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    KEY idx_user_deleted (user_id, deleted_at),
    KEY idx_user_type (user_id, type)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enderecos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    cliente_id BIGINT UNSIGNED NOT NULL,

    logradouro VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NULL,
    complemento VARCHAR(255) NULL,
    bairro VARCHAR(255) NULL,
    cidade VARCHAR(255) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    cep VARCHAR(10) NOT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_enderecos_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE CASCADE,

    KEY idx_cliente_id (cliente_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `customers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- 🔗 Relacionamento com users
    `user_id` CHAR(36) NOT NULL,

    -- Personal Data
    `full_name` VARCHAR(255) NOT NULL,
    `document_number` VARCHAR(255) NULL,
    `rg_number` VARCHAR(255) NULL,
    `rg_issue_date` DATE NULL,

    `driver_license_number` VARCHAR(255) NULL,
    `driver_license_issue_date` DATE NULL,
    `driver_license_expiration_date` DATE NULL,

    `inss_password` VARCHAR(255) NULL,
    `birth_date` DATE NULL,
    `gender` VARCHAR(20) NULL,

    -- Contact Information
    `mobile_phone` VARCHAR(255) NULL,
    `phone` VARCHAR(255) NULL,
    `secondary_phone` VARCHAR(255) NULL,

    -- Address
    `zip_code` VARCHAR(20) NULL,
    `state` VARCHAR(50) NULL,
    `city` VARCHAR(255) NULL,
    `neighborhood` VARCHAR(255) NULL,
    `street` VARCHAR(255) NULL,
    `street_number` VARCHAR(20) NULL,

    -- Additional Information
    `profession` VARCHAR(255) NULL,
    `marital_status` VARCHAR(255) NULL,

    -- Parents
    `father_name` VARCHAR(255) NULL,
    `father_birth_date` DATE NULL,
    `mother_name` VARCHAR(255) NULL,
    `mother_birth_date` DATE NULL,

    -- Tags
    `tags` JSON NULL,

    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `customers_user_id_unique` (`user_id`),
    CONSTRAINT `customers_user_id_foreign`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_files (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    customer_id BIGINT UNSIGNED NOT NULL,

    path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NULL,
    mime VARCHAR(255) NULL,
    size BIGINT UNSIGNED NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_customer_files_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE tribunals (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    cnj_code SMALLINT UNSIGNED NOT NULL,
    acronym VARCHAR(10) NOT NULL,

    name_en VARCHAR(255) NOT NULL,
    state_code CHAR(2) NOT NULL,
    country_code CHAR(2) NOT NULL DEFAULT 'BR',

    homepage_url VARCHAR(255) NULL,
    public_search_url VARCHAR(255) NULL,
     `system` VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    UNIQUE KEY uniq_cnj_code (cnj_code),
    UNIQUE KEY uniq_acronym (acronym)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE document_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    title VARCHAR(255) NOT NULL,

    type ENUM('power_of_attorney', 'contract', 'petition') NOT NULL,

    date DATE NOT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    title VARCHAR(255) NOT NULL, -- titulo_documento

    type ENUM('power_of_attorney', 'contract', 'petition') NOT NULL,

    document_link VARCHAR(255) NOT NULL,
    form_link VARCHAR(255) NULL,

    document_template_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_documents_document_template
        FOREIGN KEY (document_template_id)
        REFERENCES document_templates(id)
        ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE advocates (
    id CHAR(36) NOT NULL,

    user_id CHAR(36) NOT NULL,

    oab_number VARCHAR(255) NOT NULL,
    specialty VARCHAR(255) NULL,
    office_address TEXT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    UNIQUE KEY uniq_advocates_user (user_id),

    CONSTRAINT fk_advocates_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enterprises (
    id CHAR(36) NOT NULL,

    user_id CHAR(36) NOT NULL,

    company_name VARCHAR(255) NOT NULL,
    cnpj VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    UNIQUE KEY uniq_enterprises_user (user_id),
    UNIQUE KEY uniq_enterprises_cnpj (cnpj),

    CONSTRAINT fk_enterprises_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

