-- GYM MANAGEMENT SYSTEM - DATABASE SCHEMA
-- Engine  : SQLite 3

PRAGMA foreign_keys = ON;
PRAGMA encoding    = 'UTF-8';

--UTILIZADORES

CREATE TABLE user (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    username   TEXT     NOT NULL UNIQUE,
    name       TEXT     NOT NULL,
    age       INTEGER  CHECK (age >= 0),
    email      TEXT     NOT NULL UNIQUE,
    password   TEXT     NOT NULL, 
    photo      TEXT,              
    role       TEXT     NOT NULL
                        CHECK (role IN ('admin', 'trainer', 'member', 'nutritionist'))
                        DEFAULT 'member',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    membership TEXT NOT NULL DEFAULT 'basico'
);


-- PERFIS 

CREATE TABLE trainer_profile (
    id_user       INTEGER PRIMARY KEY,
    specialization  TEXT    NOT NULL,
    formation     TEXT    NOT NULL,
    certification TEXT    NOT NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    location TEXT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE nutritionist_profile (
    id_user        INTEGER PRIMARY KEY,
    specialization TEXT    NOT NULL,
    license_number TEXT    NOT NULL UNIQUE, 
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
);

-- PLANOS DE SUBSCRIÇÃO

CREATE TABLE membership_plan (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    plan_type       TEXT    NOT NULL CHECK (plan_type IN ('basic', 'premium', 'vip')),
    description     TEXT,
    price           REAL    NOT NULL CHECK (price >= 0),
    duration_months INTEGER NOT NULL CHECK (duration_months > 0),
    active          INTEGER NOT NULL DEFAULT 1 -- 1 = disponível, 0 = descontinuado
);

-- SUBSCRIÇÕES

CREATE TABLE subscription (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member  INTEGER  NOT NULL,
    id_plan    INTEGER  NOT NULL,
    start_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- O PHP calcula: end_date = start_date + membership_plan.duration_months
    end_date   DATETIME NOT NULL,
    status     TEXT     NOT NULL
                        CHECK (status IN ('Ativo', 'Expirado', 'Cancelado'))
                        DEFAULT 'Ativo',
    FOREIGN KEY (id_member) REFERENCES user(id)            ON DELETE CASCADE,
    FOREIGN KEY (id_plan)   REFERENCES membership_plan(id) ON DELETE RESTRICT
);

-- DISPONIBILIDADE DE TRAINERS

CREATE TABLE trainer_availability (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    id_trainer  INTEGER NOT NULL,
    day_of_week TEXT    NOT NULL
                        CHECK (day_of_week IN ('Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo')),
    start_time  TEXT    NOT NULL, -- formato 'HH:MM'
    end_time    TEXT    NOT NULL, -- formato 'HH:MM', deve ser > start_time [PHP]
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE
);

-- RESERVAS DE PERSONAL TRAINING

CREATE TABLE pt_booking (
    id           INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member    INTEGER  NOT NULL,
    id_trainer   INTEGER  NOT NULL,
    booking_date DATE     NOT NULL,       -- apenas a data (YYYY-MM-DD)
    start_time   TEXT     NOT NULL,       -- formato 'HH:MM'
    end_time     TEXT     NOT NULL,       -- formato 'HH:MM', calculado no PHP 
    notes        TEXT,                    -- observações do membro (objetivos, lesões)
    status       TEXT     NOT NULL
                          CHECK (status IN ('Pendente', 'Confirmado', 'Cancelado'))
                          DEFAULT 'Pendente',
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member)  REFERENCES user(id)                 ON DELETE CASCADE,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE
);

-- AULAS DE GRUPO

CREATE TABLE class (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    name             TEXT    NOT NULL,
    type             TEXT    NOT NULL
                             CHECK (type IN ('yoga', 'pilates', 'spinning', 'crossfit', 'zumba')),
    duration_minutes INTEGER NOT NULL CHECK (duration_minutes > 0),
    id_trainer       INTEGER NOT NULL,
    capacity         INTEGER NOT NULL CHECK (capacity > 0),
    local            TEXT    NOT NULL,
    active           INTEGER NOT NULL DEFAULT 1, -- 0 = aula descontinuada (soft-delete)
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE RESTRICT
);

-- SESSÕES CONCRETAS DE AULAS

CREATE TABLE class_session (
    id               INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_class         INTEGER  NOT NULL,
    session_datetime DATETIME NOT NULL, -- data e hora de início da sessão
    status           TEXT     NOT NULL
                              CHECK (status IN ('Agendada', 'Concluída', 'Cancelada'))
                              DEFAULT 'Agendada',
    FOREIGN KEY (id_class) REFERENCES class(id) ON DELETE CASCADE
);

-- INSCRIÇÕES EM SESSÕES DE AULAS

CREATE TABLE enrollment (
    id_member        INTEGER  NOT NULL,
    id_session       INTEGER  NOT NULL,
    enrollment_date  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_member, id_session),
    FOREIGN KEY (id_member)  REFERENCES user(id)          ON DELETE CASCADE,
    FOREIGN KEY (id_session) REFERENCES class_session(id) ON DELETE CASCADE
);

-- LISTA DE ESPERA PARA SESSÕES DE AULAS

CREATE TABLE waitlist (
    id_member  INTEGER  NOT NULL,
    id_session INTEGER  NOT NULL,
    joined_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_member, id_session),
    FOREIGN KEY (id_member)  REFERENCES user(id)          ON DELETE CASCADE,
    FOREIGN KEY (id_session) REFERENCES class_session(id) ON DELETE CASCADE
);

-- EQUIPAMENTOS

CREATE TABLE equipment (
    id               INTEGER  PRIMARY KEY AUTOINCREMENT,
    name             TEXT     NOT NULL,
    photo TEXT DEFAULT 'default-equipment.png',
    video_url TEXT,
    description      TEXT,
    status           TEXT     NOT NULL
                              CHECK (status IN ('Disponível', 'Manutenção'))
                              DEFAULT 'Disponível',
    location         TEXT     NOT NULL,
    purchase_date    DATE,               -- para controlo de vida útil
    last_maintenance DATE                -- data da última manutenção
);

-- GRUPOS MUSCULARES

CREATE TABLE muscle_group (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        TEXT    NOT NULL UNIQUE,
    description TEXT
);

-- EXERCÍCIOS

CREATE TABLE exercises (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    name            TEXT    NOT NULL,
    description     TEXT    NOT NULL,
    id_equipment    INTEGER,
    id_muscle_group INTEGER,
    FOREIGN KEY (id_equipment)    REFERENCES equipment(id)    ON DELETE SET NULL,
    FOREIGN KEY (id_muscle_group) REFERENCES muscle_group(id) ON DELETE SET NULL
);

-- PLANOS DE TREINO

CREATE TABLE workout_plan (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member  INTEGER  NOT NULL,
    id_trainer INTEGER  NOT NULL,
    goal       TEXT     NOT NULL,  -- ex: 'Hipertrofia', 'Perda de peso', 'Resistência'
    notes      TEXT,
    start_date DATE     NOT NULL,
    end_date   DATE,               -- NULL = plano contínuo sem data de fim
    status     TEXT     NOT NULL
                        CHECK (status IN ('Ativo', 'Concluído', 'Pausado'))
                        DEFAULT 'Ativo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member)  REFERENCES user(id)                 ON DELETE CASCADE,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE RESTRICT
);

-- Dias de treino dentro de um plano (ex: 'Dia A - Peito e Tríceps')
CREATE TABLE workout_day (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    id_plan INTEGER NOT NULL,
    name    TEXT    NOT NULL,  -- ex: 'Dia A', 'Dia B'
    day_of_week TEXT CHECK (day_of_week IN ('Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo')),
    FOREIGN KEY (id_plan) REFERENCES workout_plan(id) ON DELETE CASCADE
);

-- Exercícios prescritos em cada dia de treino
CREATE TABLE workout_exercise (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    id_day       INTEGER NOT NULL,
    id_exercise  INTEGER NOT NULL,
    sets         INTEGER NOT NULL CHECK (sets > 0),
    reps         INTEGER NOT NULL CHECK (reps > 0),
    rest_seconds INTEGER NOT NULL CHECK (rest_seconds >= 0) DEFAULT 60,
    notes        TEXT,   -- ex: 'Executar lentamente na fase excêntrica'
    order_index  INTEGER NOT NULL DEFAULT 0, -- ordem de execução dentro do dia
    FOREIGN KEY (id_day)      REFERENCES workout_day(id) ON DELETE CASCADE,
    FOREIGN KEY (id_exercise) REFERENCES exercises(id)   ON DELETE RESTRICT
);

-- AVALIAÇÕES

CREATE TABLE rating (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member  INTEGER  NOT NULL,
    id_trainer INTEGER,           -- NULL se for avaliação de aula
    id_session INTEGER,           -- NULL se for avaliação de trainer
    rate       INTEGER  NOT NULL CHECK (rate >= 1 AND rate <= 5),
    comment    TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member)  REFERENCES user(id)                 ON DELETE CASCADE,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_session) REFERENCES class_session(id)        ON DELETE CASCADE,
    CHECK (
        (id_trainer IS NOT NULL AND id_session IS NULL) OR
        (id_trainer IS NULL     AND id_session IS NOT NULL)
    )
);

-- PLANOS NUTRICIONAIS

CREATE TABLE nutrition_plan (
    id               INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member        INTEGER  NOT NULL,
    id_nutritionist  INTEGER  NOT NULL,
    goal             TEXT     NOT NULL,
    notes            TEXT,
    start_date       DATE     NOT NULL,
    end_date         DATE,              -- NULL = plano contínuo
    status           TEXT     NOT NULL
                              CHECK (status IN ('Ativo', 'Concluído', 'Pausado'))
                              DEFAULT 'Ativo',
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member)       REFERENCES user(id)                      ON DELETE CASCADE,
    FOREIGN KEY (id_nutritionist) REFERENCES nutritionist_profile(id_user) ON DELETE RESTRICT
);

-- REFEIÇÕES

CREATE TABLE meal (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    id_plan     INTEGER NOT NULL,
    day_of_week TEXT    NOT NULL
                        CHECK (day_of_week IN ('Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo')),
    meal_type   TEXT    NOT NULL
                        CHECK (meal_type IN ('Pequeno-Almoço', 'Almoço', 'Jantar', 'Lanche')),
    description TEXT    NOT NULL,
    calories    INTEGER CHECK (calories IS NULL OR calories > 0),
    protein_g   INTEGER CHECK (protein_g  IS NULL OR protein_g  >= 0), -- macronutriente
    carbs_g     INTEGER CHECK (carbs_g    IS NULL OR carbs_g    >= 0), -- macronutriente
    fat_g       INTEGER CHECK (fat_g      IS NULL OR fat_g      >= 0), -- macronutriente
    FOREIGN KEY (id_plan) REFERENCES nutrition_plan(id) ON DELETE CASCADE
);

-- OCORRÊNCIAS E RECLAMAÇÕES

CREATE TABLE reports (
    id              INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member       INTEGER  NOT NULL,
    category        TEXT     NOT NULL
                             CHECK (category IN ('equipment', 'class', 'trainer', 'nutritionist', 'facilities', 'cleaning', 'other')),
    id_equipament   INTEGER,           -- preenchido se category = 'equipment'
    id_trainer      INTEGER,           -- preenchido se category = 'trainer'
    id_class        INTEGER,           -- preenchido se category = 'class'
    id_nutritionist INTEGER,           -- preenchido se category = 'nutritionist'
    description     TEXT     NOT NULL,
    status          TEXT     NOT NULL
                             CHECK (status IN ('Aberta', 'Investigando', 'Resolvida'))
                             DEFAULT 'Aberta',
    id_admin        INTEGER,           -- admin responsável pela resolução
    resolved_at     DATETIME,          -- preenchido quando status = 'Resolvida'
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member)       REFERENCES user(id)                      ON DELETE CASCADE,
    FOREIGN KEY (id_equipament)   REFERENCES equipment(id)                 ON DELETE SET NULL,
    FOREIGN KEY (id_trainer)      REFERENCES trainer_profile(id_user)      ON DELETE SET NULL,
    FOREIGN KEY (id_class)        REFERENCES class(id)                     ON DELETE SET NULL,
    FOREIGN KEY (id_nutritionist) REFERENCES nutritionist_profile(id_user) ON DELETE SET NULL,
    FOREIGN KEY (id_admin)        REFERENCES user(id)                      ON DELETE SET NULL
);

-- CHECK-IN

CREATE TABLE checkin (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member  INTEGER  NOT NULL,
    checked_in DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE
);

-- NOTIFICAÇÕES

CREATE TABLE notification (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_user    INTEGER  NOT NULL,
    message    TEXT     NOT NULL,
    type       TEXT     NOT NULL
                        CHECK (type IN ('booking', 'subscription', 'waitlist', 'workout', 'nutrition', 'general')),
    is_read    INTEGER  NOT NULL DEFAULT 0, -- 0 = não lida, 1 = lida
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
);


CREATE TABLE weight_log (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_member  INTEGER  NOT NULL,
    weight_kg  REAL     NOT NULL CHECK (weight_kg > 0 AND weight_kg < 500),
    logged_at  DATE     NOT NULL DEFAULT (DATE('now')),
    notes      TEXT,
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE message (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_sender INTEGER NOT NULL,
    id_receiver INTEGER NOT NULL,
    content TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sender) REFERENCES user(id),
    FOREIGN KEY (id_receiver) REFERENCES user(id)
);

CREATE TABLE appointment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_member INTEGER NOT NULL,
    id_nutritionist INTEGER NOT NULL,
    appointment_date DATETIME NOT NULL,
    status TEXT CHECK(status IN ('pendente', 'confirmada', 'cancelada')) DEFAULT 'pendente',
    FOREIGN KEY (id_member) REFERENCES user(id),
    FOREIGN KEY (id_nutritionist) REFERENCES user(id)
);

-- Apenas um registo por dia por membro
CREATE UNIQUE INDEX uq_weight_log_member_date ON weight_log (id_member, logged_at);

-- Índice para queries de histórico
CREATE INDEX idx_weight_log_member ON weight_log (id_member, logged_at);
-- ÍNDICES


-- user
CREATE INDEX idx_user_role              ON user (role);

-- subscription
CREATE INDEX idx_subscription_member    ON subscription (id_member);
CREATE INDEX idx_subscription_status    ON subscription (status);
CREATE INDEX idx_subscription_end_date  ON subscription (end_date); -- para o cron job [R9]

-- trainer_availability
CREATE INDEX idx_availability_trainer   ON trainer_availability (id_trainer, day_of_week);

-- pt_booking
CREATE INDEX idx_pt_booking_trainer     ON pt_booking (id_trainer, booking_date);
CREATE INDEX idx_pt_booking_member      ON pt_booking (id_member);

-- class_session
CREATE INDEX idx_session_class          ON class_session (id_class);
CREATE INDEX idx_session_datetime       ON class_session (session_datetime); -- para calendário

-- enrollment
CREATE INDEX idx_enrollment_session     ON enrollment (id_session); -- para contar inscritos [R4]

-- waitlist
CREATE INDEX idx_waitlist_session       ON waitlist (id_session, joined_at); -- para promoção [R5]

-- exercises
CREATE INDEX idx_exercise_muscle        ON exercises (id_muscle_group);
CREATE INDEX idx_exercise_equipment     ON exercises (id_equipment);

-- workout
CREATE INDEX idx_workout_plan_member    ON workout_plan (id_member);
CREATE INDEX idx_workout_plan_trainer   ON workout_plan (id_trainer);
CREATE INDEX idx_workout_day_plan       ON workout_day (id_plan);
CREATE INDEX idx_workout_exercise_day   ON workout_exercise (id_day);

-- rating
CREATE INDEX idx_rating_trainer         ON rating (id_trainer);
CREATE INDEX idx_rating_session         ON rating (id_session);

-- nutrition_plan
CREATE INDEX idx_nutrition_member       ON nutrition_plan (id_member);
CREATE INDEX idx_nutrition_nutritionist ON nutrition_plan (id_nutritionist);

-- meal
CREATE INDEX idx_meal_plan              ON meal (id_plan);

-- reports
CREATE INDEX idx_reports_member         ON reports (id_member);
CREATE INDEX idx_reports_status         ON reports (status);

-- checkin
CREATE INDEX idx_checkin_member         ON checkin (id_member);
CREATE INDEX idx_checkin_date           ON checkin (checked_in); -- para relatórios de frequência

-- notification
CREATE INDEX idx_notification_user      ON notification (id_user, is_read);

-- ÍNDICES ÚNICOS PARCIAIS

CREATE UNIQUE INDEX uq_rating_trainer ON rating (id_member, id_trainer)
    WHERE id_trainer IS NOT NULL;

CREATE UNIQUE INDEX uq_rating_session ON rating (id_member, id_session)
    WHERE id_session IS NOT NULL;