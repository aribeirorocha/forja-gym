BEGIN TRANSACTION;

-- Limpar dados existentes (ordem respeitando FK)
DELETE FROM weight_log;
DELETE FROM workout_exercise;
DELETE FROM workout_day;
DELETE FROM workout_plan;
DELETE FROM meal;
DELETE FROM nutrition_plan;
DELETE FROM appointment;
DELETE FROM message;
DELETE FROM notification;
DELETE FROM waitlist;
DELETE FROM enrollment;
DELETE FROM rating;
DELETE FROM pt_booking;
DELETE FROM checkin;
DELETE FROM class_session;
DELETE FROM class;
DELETE FROM trainer_availability;
DELETE FROM trainer_profile;
DELETE FROM nutritionist_profile;
DELETE FROM subscription;
DELETE FROM exercises;
DELETE FROM equipment;
DELETE FROM muscle_group;
DELETE FROM reports;
DELETE FROM user;
DELETE FROM sqlite_sequence;


-- ==========================================
-- 1. UTILIZADORES: TRAINERS + ADMIN
-- ==========================================
INSERT INTO user (id, username, name, email, password, photo, role, age, created_at) VALUES 
(1,  'anasilva',      'Ana Silva',       'anasilvatrainer@gmail.com',      '1234', 'trainer1.jpg', 'trainer',      31, '2024-01-10 09:00:00'),
(2,  'joaocosta',     'João Costa',      'joaocostatrainer@gmail.com',     '1234', 'trainer2.jpg', 'trainer',      32, '2024-01-10 09:00:00'),
(3,  'mariofernandes','Mário Fernandes',  'mariofernandestrainer@gmail.com','1234', 'trainer3.jpg', 'trainer',      33, '2024-01-10 09:00:00'),
(4,  'ruibarbosa',    'Rui Barbosa',      'ruibarbosatrainer@gmail.com',    '1234', 'trainer6.jpg', 'trainer',      34, '2024-01-10 09:00:00'),
(5,  'nunomendes',    'Nuno Mendes',      'nunomendestrainer@gmail.com',    '1234', 'trainer4.jpg', 'trainer',      35, '2024-01-10 09:00:00'),
(6,  'rafaelleao',    'Rafael Leão',      'rafaelleaotrainer@gmail.com',    '1234', 'trainer5.jpg', 'trainer',      36, '2024-01-10 09:00:00'),
(9,  'martagomes',    'Marta Gomes',      'martagomestrainer@gmail.com',    '1234', 'trainer7.jpg', 'trainer',      28, '2024-01-10 09:00:00'),
(10, 'tiagosilva',    'Tiago Silva',      'tiagosilvatrainer@gmail.com',    '1234', 'trainer8.jpg', 'trainer',      41, '2024-01-10 09:00:00'),
(11, 'sofiaalmeida',  'Sofia Almeida',    'sofiatrainer@gmail.com',         '1234', 'trainer9.jpg', 'trainer',      26, '2024-01-10 09:00:00'),
(12, 'gustavosantos', 'Gustavo Santos',   'gustavosantosadmin@gmail.com',   '1234', 'admin.jpg',    'admin',        30, '2024-01-05 08:00:00');

INSERT INTO trainer_profile (id_user, sex, formation, certification, specialization, location) VALUES 
(1,  'feminino',  'Licenciatura em Ciências do Desporto – FADEUP',     'Yoga Alliance RYT-200, STOTT Pilates',              'Yoga,Pilates',         'Porto, Portugal'),
(2,  'masculino', 'Licenciatura em Educação Física – ISMAI',           'CrossFit Level 1, Kettlebell Instructor',           'HIIT,Força',           'Porto, Portugal'),
(3,  'masculino', 'Licenciatura em Ciências do Desporto – UTAD',       'CrossFit Level 2, Precision Nutrition L1',          'Stretch,Calistenia',   'Porto, Portugal'),
(4,  'masculino', 'Mestrado em Treino Desportivo – FMH Lisboa',        'Functional Movement Screen, FRC Mobility Specialist','Spinning,Sprint',      'Porto, Portugal'),
(5,  'masculino', 'Licenciatura em Desporto e Lazer – ESDRM',          'Spinning® Certified Instructor, Athletics Coach',   'CrossFit,Cardio',      'Porto, Portugal'),
(6,  'masculino', 'Licenciatura em Educação Física – FADEUP',          'CrossFit Level 1, Mobility WOD Coach',              'CrossFit,Mobilidade',  'Porto, Portugal'),
(9,  'feminino',  'Licenciatura em Dança – ESD',                       'Zumba Instructor Network (ZIN), Aeróbica',          'Zumba,Cardio',         'Porto, Portugal'),
(10, 'masculino', 'Mestrado em Reabilitação – FADEUP',                 'Certified Personal Trainer (NSCA)',                 'Musculação,Recuperação','Porto, Portugal'),
(11, 'feminino',  'Licenciatura em Ciências do Desporto – ISMAI',      'Pilates Matwork Level 1 e 2',                       'Pilates,Postura',      'Porto, Portugal');

-- ==========================================
-- 2. NUTRICIONISTAS
-- ==========================================
INSERT INTO user (id, username, name, email, password, photo, role, created_at) VALUES 
(7, 'nutri_ana',   'Dra. Ana Silva',   'ananutri@gmail.com',   '1234', 'nutritionist.jpg', 'nutritionist', '2024-01-10 09:00:00'),
(8, 'nutri_pedro', 'Dr. Pedro Rocha',  'pedronutri@gmail.com', '1234', 'nutri_pedro.jpg',  'nutritionist', '2024-01-10 09:00:00');

INSERT INTO nutritionist_profile (id_user, specialization, license_number) VALUES 
(7, 'Nutrição Desportiva',            'ORDEM-455567'),
(8, 'Nutrição Clínica e Funcional',   'ORDEM-99887');

-- ==========================================
-- 3. MEMBROS (20 membros com datas variadas)
-- ==========================================
INSERT INTO user (id, username, name, email, password, photo, role, age, membership, created_at) VALUES
(13, 'pedro_m',      'Pedro Martins',     'pedro.martins@gmail.com',    '1234', NULL, 'member', 27, 'normal', '2024-02-03 10:15:00'),
(14, 'rita_f',       'Rita Ferreira',     'rita.ferreira@gmail.com',    '1234', NULL, 'member', 24, 'vip',    '2024-02-10 11:30:00'),
(15, 'carlos_o',     'Carlos Oliveira',   'carlos.oliveira@gmail.com',  '1234', NULL, 'member', 35, 'basico', '2024-02-15 09:00:00'),
(16, 'ines_s',       'Inês Santos',       'ines.santos@gmail.com',      '1234', NULL, 'member', 22, 'vip',    '2024-03-01 14:00:00'),
(17, 'tomas_r',      'Tomás Rodrigues',   'tomas.rodrigues@gmail.com',  '1234', NULL, 'member', 30, 'normal', '2024-03-12 16:45:00'),
(18, 'beatriz_n',    'Beatriz Neves',     'beatriz.neves@gmail.com',    '1234', NULL, 'member', 26, 'normal', '2024-03-20 10:00:00'),
(19, 'diogo_c',      'Diogo Carvalho',    'diogo.carvalho@gmail.com',   '1234', NULL, 'member', 29, 'basico', '2024-04-05 09:30:00'),
(20, 'mariana_l',    'Mariana Lopes',     'mariana.lopes@gmail.com',    '1234', NULL, 'member', 23, 'vip',    '2024-04-18 11:00:00'),
(21, 'rui_a',        'Rui Azevedo',       'rui.azevedo@gmail.com',      '1234', NULL, 'member', 38, 'normal', '2024-05-02 08:00:00'),
(22, 'francisca_p',  'Francisca Pinto',   'francisca.pinto@gmail.com',  '1234', NULL, 'member', 21, 'basico', '2024-05-14 13:00:00'),
(23, 'andre_v',      'André Vieira',      'andre.vieira@gmail.com',     '1234', NULL, 'member', 33, 'vip',    '2024-06-01 10:30:00'),
(24, 'catarina_m',   'Catarina Moreira',  'catarina.moreira@gmail.com', '1234', NULL, 'member', 28, 'normal', '2024-06-20 15:00:00'),
(25, 'miguel_b',     'Miguel Braga',      'miguel.braga@gmail.com',     '1234', NULL, 'member', 31, 'basico', '2024-07-08 09:00:00'),
(26, 'leonor_g',     'Leonor Goncalves',  'leonor.goncalves@gmail.com', '1234', NULL, 'member', 25, 'vip',    '2024-07-22 11:45:00'),
(27, 'hugo_t',       'Hugo Teixeira',     'hugo.teixeira@gmail.com',    '1234', NULL, 'member', 40, 'normal', '2024-08-05 14:00:00'),
(28, 'sofia_r',      'Sofia Ribeiro',     'sofia.ribeiro@gmail.com',    '1234', NULL, 'member', 29, 'basico', '2024-09-10 10:00:00'),
(29, 'joao_d',       'João Dias',         'joao.dias@gmail.com',        '1234', NULL, 'member', 36, 'normal', '2024-10-01 09:15:00'),
(30, 'ana_c',        'Ana Coelho',        'ana.coelho@gmail.com',       '1234', NULL, 'member', 27, 'vip',    '2024-10-15 12:00:00'),
(31, 'bruno_s',      'Bruno Sousa',       'bruno.sousa@gmail.com',      '1234', NULL, 'member', 32, 'normal', '2024-11-03 08:30:00'),
(32, 'marta_a',      'Marta Alves',       'marta.alves@gmail.com',      '1234', NULL, 'member', 24, 'basico', '2024-11-20 17:00:00');

-- ==========================================
-- 4. AULAS E SESSÕES
-- ==========================================
INSERT INTO class (id, name, type, duration_minutes, id_trainer, capacity, local, active) VALUES 
(1, 'Yoga',          'yoga',     60, 1,  12, 'Porto', 1),
(2, 'CrossFit',      'crossfit', 60, 2,  8,  'Porto', 1),
(3, 'Spinning',      'spinning', 45, 3,  10, 'Porto', 1),
(4, 'Zumba',         'zumba',    50, 9,  20, 'Porto', 1),
(5, 'Pilates',       'pilates',  50, 11, 15, 'Porto', 1),
(6, 'HIIT Training', 'crossfit', 45, 2,  12, 'Porto', 1);

-- Sessões passadas (Concluídas) + futuras (Agendadas)
INSERT INTO class_session (id, id_class, session_datetime, status) VALUES 
-- Sessões passadas Yoga
(1,  1, '2025-01-06 09:00:00', 'Concluída'),
(2,  1, '2025-01-13 09:00:00', 'Concluída'),
(3,  1, '2025-02-03 09:00:00', 'Concluída'),
(4,  1, '2025-02-17 09:00:00', 'Concluída'),
(5,  1, '2025-03-10 09:00:00', 'Concluída'),
(6,  1, '2025-04-07 09:00:00', 'Concluída'),
(7,  1, '2025-05-05 09:00:00', 'Concluída'),
-- Sessões futuras Yoga
(8,  1, '2026-06-08 09:00:00', 'Agendada'),
(9,  1, '2026-06-15 09:00:00', 'Agendada'),
(10, 1, '2026-06-22 09:00:00', 'Agendada'),

-- Sessões passadas CrossFit
(11, 2, '2025-01-07 18:00:00', 'Concluída'),
(12, 2, '2025-01-21 18:00:00', 'Concluída'),
(13, 2, '2025-02-04 18:00:00', 'Concluída'),
(14, 2, '2025-03-04 18:00:00', 'Concluída'),
(15, 2, '2025-04-01 18:00:00', 'Concluída'),
(16, 2, '2025-05-06 18:00:00', 'Concluída'),
-- Sessões futuras CrossFit
(17, 2, '2026-06-09 18:00:00', 'Agendada'),
(18, 2, '2026-06-16 18:00:00', 'Agendada'),

-- Sessões passadas Spinning
(19, 3, '2025-01-08 19:00:00', 'Concluída'),
(20, 3, '2025-02-05 19:00:00', 'Concluída'),
(21, 3, '2025-03-05 19:00:00', 'Concluída'),
(22, 3, '2025-04-02 19:00:00', 'Concluída'),
(23, 3, '2025-05-07 19:00:00', 'Concluída'),
-- Sessões futuras Spinning
(24, 3, '2026-06-10 19:00:00', 'Agendada'),
(25, 3, '2026-06-17 19:00:00', 'Agendada'),

-- Sessões passadas Zumba
(26, 4, '2025-01-09 18:30:00', 'Concluída'),
(27, 4, '2025-02-06 18:30:00', 'Concluída'),
(28, 4, '2025-03-06 18:30:00', 'Concluída'),
(29, 4, '2025-05-08 18:30:00', 'Concluída'),
-- Sessões futuras Zumba
(30, 4, '2026-06-11 18:30:00', 'Agendada'),
(31, 4, '2026-06-18 18:30:00', 'Agendada'),

-- Sessões passadas Pilates
(32, 5, '2025-01-10 10:00:00', 'Concluída'),
(33, 5, '2025-02-07 10:00:00', 'Concluída'),
(34, 5, '2025-03-07 10:00:00', 'Concluída'),
(35, 5, '2025-04-04 10:00:00', 'Concluída'),
(36, 5, '2025-05-09 10:00:00', 'Concluída'),
-- Sessões futuras Pilates
(37, 5, '2026-06-12 10:00:00', 'Agendada'),
(38, 5, '2026-06-19 10:00:00', 'Agendada'),

-- Sessões passadas HIIT
(39, 6, '2025-01-11 12:30:00', 'Concluída'),
(40, 6, '2025-02-08 12:30:00', 'Concluída'),
(41, 6, '2025-03-08 12:30:00', 'Concluída'),
(42, 6, '2025-04-05 12:30:00', 'Concluída'),
(43, 6, '2025-05-10 12:30:00', 'Concluída'),
-- Sessões futuras HIIT
(44, 6, '2026-06-13 12:30:00', 'Agendada'),
(45, 6, '2026-06-20 12:30:00', 'Agendada');

-- ==========================================
-- 5. INSCRIÇÕES EM AULAS
-- ==========================================
INSERT INTO enrollment (id_member, id_session, enrollment_date) VALUES
-- Yoga passado
(13, 1, '2025-01-05 20:00:00'), (14, 1, '2025-01-05 20:10:00'), (16, 1, '2025-01-05 20:20:00'),
(13, 2, '2025-01-12 20:00:00'), (14, 2, '2025-01-12 20:10:00'), (20, 2, '2025-01-12 20:30:00'),
(13, 3, '2025-02-02 20:00:00'), (16, 3, '2025-02-02 20:15:00'), (23, 3, '2025-02-02 20:30:00'),
(14, 4, '2025-02-16 20:00:00'), (20, 4, '2025-02-16 20:10:00'), (26, 4, '2025-02-16 20:20:00'),
(13, 5, '2025-03-09 20:00:00'), (16, 5, '2025-03-09 20:15:00'),
(13, 6, '2025-04-06 20:00:00'), (14, 6, '2025-04-06 20:05:00'), (20, 6, '2025-04-06 20:10:00'), (26, 6, '2025-04-06 20:20:00'),
(13, 7, '2025-05-04 20:00:00'), (16, 7, '2025-05-04 20:10:00'),
-- Yoga futuro
(13, 8,  '2026-05-28 10:00:00'), (14, 8,  '2026-05-28 10:05:00'), (16, 8, '2026-05-28 10:10:00'),
(13, 9,  '2026-05-28 10:00:00'), (20, 9,  '2026-05-28 10:05:00'),
(14, 10, '2026-05-28 10:00:00'), (26, 10, '2026-05-28 10:05:00'),

-- CrossFit passado
(17, 11, '2025-01-06 20:00:00'), (19, 11, '2025-01-06 20:10:00'), (21, 11, '2025-01-06 20:20:00'),
(17, 12, '2025-01-20 20:00:00'), (23, 12, '2025-01-20 20:10:00'),
(17, 13, '2025-02-03 20:00:00'), (19, 13, '2025-02-03 20:10:00'), (29, 13, '2025-02-03 20:20:00'),
(17, 14, '2025-03-03 20:00:00'), (21, 14, '2025-03-03 20:10:00'),
(17, 15, '2025-03-31 20:00:00'), (23, 15, '2025-03-31 20:10:00'), (31, 15, '2025-03-31 20:20:00'),
(17, 16, '2025-05-05 20:00:00'), (19, 16, '2025-05-05 20:10:00'),
-- CrossFit futuro
(17, 17, '2026-05-28 10:00:00'), (19, 17, '2026-05-28 10:05:00'), (21, 17, '2026-05-28 10:10:00'),
(23, 18, '2026-05-28 10:00:00'), (29, 18, '2026-05-28 10:05:00'),

-- Spinning passado
(15, 19, '2025-01-07 20:00:00'), (18, 19, '2025-01-07 20:10:00'), (22, 19, '2025-01-07 20:20:00'),
(15, 20, '2025-02-04 20:00:00'), (24, 20, '2025-02-04 20:10:00'),
(15, 21, '2025-03-04 20:00:00'), (18, 21, '2025-03-04 20:10:00'), (27, 21, '2025-03-04 20:20:00'),
(15, 22, '2025-04-01 20:00:00'), (22, 22, '2025-04-01 20:10:00'),
(15, 23, '2025-05-06 20:00:00'), (18, 23, '2025-05-06 20:10:00'),
-- Spinning futuro
(15, 24, '2026-05-28 10:00:00'), (18, 24, '2026-05-28 10:05:00'),
(22, 25, '2026-05-28 10:00:00'), (27, 25, '2026-05-28 10:05:00'),

-- Zumba passado
(14, 26, '2025-01-08 20:00:00'), (16, 26, '2025-01-08 20:10:00'), (20, 26, '2025-01-08 20:20:00'), (28, 26, '2025-01-08 20:30:00'),
(14, 27, '2025-02-05 20:00:00'), (16, 27, '2025-02-05 20:10:00'), (30, 27, '2025-02-05 20:20:00'),
(16, 28, '2025-03-05 20:00:00'), (20, 28, '2025-03-05 20:10:00'), (28, 28, '2025-03-05 20:20:00'),
(14, 29, '2025-05-07 20:00:00'), (16, 29, '2025-05-07 20:10:00'),
-- Zumba futuro
(14, 30, '2026-05-28 10:00:00'), (16, 30, '2026-05-28 10:05:00'), (20, 30, '2026-05-28 10:10:00'),
(28, 31, '2026-05-28 10:00:00'), (30, 31, '2026-05-28 10:05:00'),

-- Pilates passado
(13, 32, '2025-01-09 20:00:00'), (14, 32, '2025-01-09 20:10:00'), (16, 32, '2025-01-09 20:20:00'),
(13, 33, '2025-02-06 20:00:00'), (20, 33, '2025-02-06 20:10:00'),
(14, 34, '2025-03-06 20:00:00'), (16, 34, '2025-03-06 20:10:00'), (26, 34, '2025-03-06 20:20:00'),
(13, 35, '2025-04-03 20:00:00'), (14, 35, '2025-04-03 20:10:00'),
(16, 36, '2025-05-08 20:00:00'), (20, 36, '2025-05-08 20:10:00'),
-- Pilates futuro
(13, 37, '2026-05-28 10:00:00'), (14, 37, '2026-05-28 10:05:00'),
(16, 38, '2026-05-28 10:00:00'), (20, 38, '2026-05-28 10:05:00'), (26, 38, '2026-05-28 10:10:00');

-- ==========================================
-- 6. AVALIAÇÕES DE AULAS (sessões passadas)
-- ==========================================
INSERT INTO rating (id_member, id_session, rate, comment, created_at) VALUES
-- Yoga
(13, 1, 5, 'Aula fantástica, saí completamente relaxada. A Ana é incrível!', '2025-01-06 11:00:00'),
(14, 1, 5, 'Melhor aula de yoga que já tive. Ambiente perfeito.', '2025-01-06 11:30:00'),
(16, 1, 4, 'Muito boa aula, aprendi posturas novas. Recomendo!', '2025-01-06 12:00:00'),
(13, 2, 5, 'Cada sessão é uma experiência diferente. Adorei!', '2025-01-13 11:00:00'),
(14, 2, 4, 'Ótima professora, explica tudo com muita paciência.', '2025-01-13 11:15:00'),
(13, 3, 5, 'Excelente ritmo na aula, não é demasiado intenso nem lento.', '2025-02-03 11:00:00'),
(16, 3, 5, 'Adoro as aulas da Ana! Já me inscrevi para o próximo mês.', '2025-02-03 11:20:00'),
(14, 4, 4, 'Muito bom, a música escolhida foi perfeita para relaxar.', '2025-02-17 11:00:00'),
(13, 6, 5, 'Já são 3 meses a vir e cada aula supera a anterior!', '2025-04-07 11:00:00'),
(14, 6, 5, 'Professora com muita experiência e dedicação.', '2025-04-07 11:10:00'),
(13, 7, 4, 'Aula muito completa. Aprendi técnicas de respiração novas.', '2025-05-05 11:00:00'),

-- CrossFit
(17, 11, 5, 'Treino muito intenso, exatamente o que precisava!', '2025-01-07 20:00:00'),
(19, 11, 4, 'Exigente mas justo. O João sabe o que faz.', '2025-01-07 20:10:00'),
(21, 11, 5, 'Senti os resultados logo após a primeira sessão.', '2025-01-07 20:20:00'),
(17, 12, 5, 'Progressão bem estruturada. Volta semana após semana!', '2025-01-21 20:00:00'),
(17, 13, 4, 'Boa variedade de exercícios, nunca é a mesma coisa.', '2025-02-04 20:00:00'),
(19, 13, 5, 'O João é muito atento à forma de cada um. Excelente!', '2025-02-04 20:10:00'),
(29, 13, 5, 'Aula fantástica! Estou a melhorar o meu cardio.', '2025-02-04 20:20:00'),
(17, 14, 5, 'Muito difícil, mas valeu a pena!', '2025-03-04 20:00:00'),
(21, 14, 4, 'Sempre a puxar por nós. Recomendo.', '2025-03-04 20:10:00'),
(17, 15, 5, 'Já perdi 4kg desde que comecei. Obrigado!', '2025-04-01 20:00:00'),
(23, 15, 4, 'Aula muito bem organizada e o ambiente é ótimo.', '2025-04-01 20:10:00'),
(31, 15, 5, 'Top! O ritmo foi de loucos mas senti-me ótimo.', '2025-04-01 20:20:00'),
(17, 16, 5, 'O melhor crossfit que já experimentei em Porto.', '2025-05-06 20:00:00'),
(19, 16, 5, 'Estou viciado nestas aulas.', '2025-05-06 20:10:00'),

-- Spinning
(15, 19, 5, 'Sessão incrível! O Mário escolheu músicas perfeitas.', '2025-01-08 21:00:00'),
(18, 19, 4, 'Muito energético, saí a suar mas satisfeita!', '2025-01-08 21:10:00'),
(22, 19, 5, 'As luzes e a música motivam imenso.', '2025-01-08 21:20:00'),
(15, 20, 5, 'Já é a minha aula favorita da semana.', '2025-02-05 21:00:00'),
(24, 20, 5, 'Perfeito para queimar calorias. Adorei!', '2025-02-05 21:10:00'),
(15, 21, 4, 'Boa intensidade, ritmo bem calibrado.', '2025-03-05 21:00:00'),
(18, 21, 5, 'Adorei! Muito melhor do que spinning noutros ginásios.', '2025-03-05 21:10:00'),
(27, 21, 5, 'Saí daqui esgotado mas super feliz.', '2025-03-05 21:20:00'),
(15, 22, 5, 'A música é top.', '2025-04-02 21:00:00'),
(22, 22, 4, 'A aula foi espetacular.', '2025-04-02 21:10:00'),
(15, 23, 5, 'Professor extraordinário, adapta o treino a toda a gente.', '2025-05-07 21:00:00'),
(18, 23, 5, 'Como sempre, espetacular.', '2025-05-07 21:10:00'),

-- Zumba
(14, 26, 5, 'Divertidíssimo! A Marta tem uma energia contagiante.', '2025-01-09 20:30:00'),
(16, 26, 5, 'Nem parece exercício, é uma festa! Adoro.', '2025-01-09 20:40:00'),
(20, 26, 4, 'Muito animado, vai voltar com certeza.', '2025-01-09 20:50:00'),
(14, 27, 5, 'Melhor aula de dança que alguma vez fiz.', '2025-02-06 20:30:00'),
(16, 28, 5, 'A Marta lembra-se sempre do nível de cada um. Incrível!', '2025-03-06 20:30:00'),
(14, 29, 4, 'Sessão fantástica, músicas novas e animadas.', '2025-05-08 20:30:00'),

-- Pilates
(13, 32, 5, 'A Sofia corrige a postura com muita atenção. Recomendo!', '2025-01-10 11:30:00'),
(14, 32, 5, 'Nunca tinha feito pilates, fiquei rendida!', '2025-01-10 11:40:00'),
(16, 32, 5, 'Espetacular para relaxar as costas.', '2025-01-10 11:50:00'),
(13, 33, 4, 'Ótima para melhorar a postura no dia a dia.', '2025-02-07 11:30:00'),
(20, 33, 5, 'Aula maravilhosa.', '2025-02-07 11:40:00'),
(14, 34, 5, 'Aula muito bem estruturada, sinto diferença nas costas.', '2025-03-07 11:30:00'),
(16, 34, 4, 'Professora muito paciente e atenciosa com os iniciantes.', '2025-03-07 11:40:00'),
(26, 34, 5, 'Nunca pensei que pilates fosse tão bom!', '2025-03-07 11:50:00'),
(13, 35, 5, 'Já são 4 meses e a minha postura melhorou imenso!', '2025-04-04 11:30:00'),
(14, 35, 5, 'Incrível!', '2025-04-04 11:40:00'),
(16, 36, 5, 'Sinto-me muito mais flexível.', '2025-05-09 11:30:00'),
(20, 36, 5, 'Aula fantástica.', '2025-05-09 11:40:00');

-- ==========================================
-- 7. AVALIAÇÕES DE TRAINERS
-- ==========================================
INSERT INTO rating (id_member, id_trainer, rate, comment, created_at) VALUES
-- Ana Silva (Yoga/Pilates)
(13, 1, 5, 'A Ana transformou a minha relação com o exercício. Trainer do ano!', '2025-03-15 14:00:00'),
(14, 1, 5, 'Profissionalismo e dedicação ao mais alto nível.', '2025-03-20 10:00:00'),
(16, 1, 5, 'Recomendo a toda a gente que queira começar yoga. Fantástica!', '2025-04-10 11:00:00'),
(20, 1, 4, 'Muito boa trainer, aprendi imenso em pouco tempo.', '2025-04-25 09:00:00'),
(26, 1, 5, 'Nunca pensei gostar de yoga. A Ana mudou isso. Obrigada!', '2025-05-12 10:30:00'),

-- João Costa (CrossFit/HIIT)
(17, 2, 5, 'O João é rigoroso mas justo. Os resultados falam por si.', '2025-02-20 19:00:00'),
(19, 2, 5, 'Treinos sempre variados, nunca me canso de vir.', '2025-03-10 19:30:00'),
(21, 2, 4, 'Excelente trainer, muito atento à técnica de cada um.', '2025-04-05 18:00:00'),
(23, 2, 5, 'Perdi 6kg em 3 meses. O João é o melhor!', '2025-04-28 19:00:00'),
(29, 2, 4, 'Muito profissional e motivador. Treinos de alta qualidade.', '2025-05-15 20:00:00'),
(31, 2, 5, 'Ambiente incrível nas aulas dele. Energia a mais!', '2025-05-20 19:30:00'),

-- Mário Fernandes (Spinning)
(15, 3, 5, 'O Mário é o melhor professor de spinning que já tive.', '2025-02-10 20:00:00'),
(18, 3, 4, 'Muito bom, a seleção musical é sempre perfeita.', '2025-03-15 20:30:00'),
(22, 3, 5, 'Aulas intensas mas sempre com bom ambiente. Recomendo!', '2025-04-20 21:00:00'),
(27, 3, 4, 'Ótimo professor, explica bem as técnicas de respiração.', '2025-05-05 20:00:00'),

-- Marta Gomes (Zumba)
(14, 9, 5, 'A Marta faz de cada aula uma festa! Imperdível.', '2025-02-15 19:00:00'),
(16, 9, 5, 'Melhor aula de cardio que existe. Não parece sequer exercício!', '2025-03-20 19:30:00'),
(20, 9, 4, 'Muito animada e bem-disposta. As aulas passam a voar.', '2025-04-15 19:00:00'),
(28, 9, 5, 'A Marta lembra os nomes de todos. Faz-nos sentir em casa.', '2025-05-01 19:30:00'),

-- Sofia Almeida (Pilates)
(13, 11, 5, 'A Sofia é excepcional. A minha dor nas costas desapareceu!', '2025-03-25 12:00:00'),
(14, 11, 5, 'Muito atenciosa e com imenso conhecimento. 5 estrelas!', '2025-04-12 11:30:00'),
(16, 11, 4, 'Ótima professora, sempre disponível para tirar dúvidas.', '2025-04-30 12:00:00'),
(26, 11, 5, 'Recomendo a qualquer pessoa com problemas posturais.', '2025-05-18 11:00:00'),

-- Tiago Silva (Musculação)
(21, 10, 5, 'O Tiago criou-me um plano perfeito para os meus objetivos.', '2025-03-05 18:00:00'),
(23, 10, 4, 'Muito experiente, especialmente em recuperação de lesões.', '2025-04-10 17:30:00'),
(25, 10, 5, 'Sigo o plano dele há 4 meses e os resultados são visíveis.', '2025-05-10 18:00:00'),

-- Nuno Mendes (CrossFit/Cardio)
(19, 5, 4, 'Treinos bem planeados e com muito cardio. Excelente!', '2025-04-08 19:00:00'),
(22, 5, 5, 'O Nuno é muito motivador. Nunca desiste de nós!', '2025-05-03 19:30:00');

-- ==========================================
-- 8. PT BOOKINGS
-- ==========================================
INSERT INTO pt_booking (id_member, id_trainer, booking_date, start_time, end_time, notes, status, created_at) VALUES
-- Confirmados (passados)
(13, 2,  '2025-02-10', '09:00', '10:00', 'Quero melhorar a técnica de agachamento e aumentar força nas pernas.', 'Confirmado', '2025-02-08 20:00:00'),
(17, 10, '2025-02-15', '10:00', '11:00', 'Recuperação de lesão no ombro direito. Preciso de treino adaptado.', 'Confirmado', '2025-02-13 18:00:00'),
(14, 1,  '2025-02-20', '11:00', '12:00', 'Gostava de aprender yoga de forma mais individualizada.', 'Confirmado', '2025-02-18 19:00:00'),
(21, 10, '2025-03-05', '09:00', '10:00', 'Plano de hipertrofia muscular para os próximos 3 meses.', 'Confirmado', '2025-03-03 20:00:00'),
(20, 2,  '2025-03-12', '18:00', '19:00', 'Quero melhorar o meu tempo nos WODs de CrossFit.', 'Confirmado', '2025-03-10 19:00:00'),
(23, 10, '2025-03-20', '17:00', '18:00', 'Treino focado em perda de peso e definição muscular.', 'Confirmado', '2025-03-18 20:00:00'),
(16, 11, '2025-04-02', '10:00', '11:00', 'Dores na lombar, preciso de exercícios corretivos.', 'Confirmado', '2025-03-31 21:00:00'),
(20, 1,  '2025-04-08', '11:00', '12:00', 'Primeira sessão de yoga, nunca experimentei antes.', 'Confirmado', '2025-04-06 20:00:00'),
(26, 10, '2025-04-15', '09:00', '10:00', 'Plano completo de musculação para principiante.', 'Confirmado', '2025-04-13 19:00:00'),
(29, 2,  '2025-04-22', '18:00', '19:00', 'Preparação para uma corrida de 10km daqui a 2 meses.', 'Confirmado', '2025-04-20 20:00:00'),
(13, 10, '2025-05-06', '09:00', '10:00', 'Avaliação física e definição de objetivos para o verão.', 'Confirmado', '2025-05-04 18:00:00'),
(24, 11, '2025-05-13', '10:00', '11:00', 'Pilates para melhorar a postura. Trabalho muito ao computador.', 'Confirmado', '2025-05-11 20:00:00'),
(31, 2,  '2025-05-19', '17:00', '18:00', 'Treino de força funcional, quero ficar mais ágil.', 'Confirmado', '2025-05-17 19:00:00'),
(30, 1,  '2025-05-25', '11:00', '12:00', 'Continuação do plano de yoga. Evolução para nível intermédio.', 'Confirmado', '2025-05-23 20:00:00'),

-- Pendentes (futuros próximos)
(14, 2,  '2026-06-10', '18:00', '19:00', 'Quero experimentar CrossFit mas com acompanhamento individual primeiro.', 'Pendente', '2026-05-28 09:00:00'),
(26, 11, '2026-06-11', '10:00', '11:00', 'Pilates de reabilitação após cirurgia ao joelho.', 'Pendente', '2026-05-27 20:00:00'),
(26, 9,  '2026-06-12', '18:30', '19:30', 'Aulas de zumba privadas para perder vergonha antes das aulas em grupo.', 'Pendente', '2026-05-26 21:00:00'),
(23, 10, '2026-06-13', '09:00', '10:00', 'Precisava de orientação avançada para um evento.', 'Pendente', '2026-05-28 10:00:00'),
(20, 3,  '2026-06-14', '19:00', '20:00', 'Melhorar a resistência cardiovascular com spinning personalizado.', 'Pendente', '2026-05-27 18:00:00'),

-- Cancelados
(18, 2,  '2025-03-01', '18:00', '19:00', 'Treino de força e condicionamento físico geral.', 'Cancelado', '2025-02-27 20:00:00'),
(16, 1,  '2025-03-15', '09:00', '10:00', 'Queria experimentar yoga mas tive de cancelar.', 'Cancelado', '2025-03-13 19:00:00');

-- ==========================================
-- 9. DISPONIBILIDADE DOS TRAINERS
-- ==========================================
INSERT INTO trainer_availability (id_trainer, day_of_week, start_time, end_time) VALUES
(1,  'Segunda', '08:00', '12:00'),
(1,  'Quarta',  '08:00', '12:00'),
(1,  'Sexta',   '08:00', '12:00'),
(2,  'Segunda', '17:00', '21:00'),
(2,  'Terça',   '17:00', '21:00'),
(2,  'Quinta',  '17:00', '21:00'),
(3,  'Terça',   '18:00', '21:00'),
(3,  'Quinta',  '18:00', '21:00'),
(3,  'Sábado',  '09:00', '13:00'),
(9,  'Segunda', '17:00', '20:00'),
(9,  'Quarta',  '17:00', '20:00'),
(9,  'Sexta',   '17:00', '20:00'),
(10, 'Segunda', '08:00', '13:00'),
(10, 'Quarta',  '08:00', '13:00'),
(10, 'Sexta',   '08:00', '13:00'),
(11, 'Terça',   '09:00', '12:00'),
(11, 'Quinta',  '09:00', '12:00'),
(11, 'Sábado',  '09:00', '12:00');

-- ==========================================
-- 10. PLANOS DE NUTRIÇÃO
-- ==========================================
INSERT INTO nutrition_plan (id_member, id_nutritionist, goal, notes, start_date, end_date, status) VALUES
(13, 7, 'Ganho de massa muscular', 'Membro com treino intenso 4x por semana. Foco em proteína pós-treino.', '2025-02-01', NULL, 'Ativo'),
(16, 8, 'Perda de peso saudável', 'Reduzir 5kg em 3 meses sem perder massa muscular.', '2025-03-01', '2025-06-01', 'Concluído'),
(17, 7, 'Desempenho desportivo', 'Atleta de crossfit. Otimizar energia para treinos intensos.', '2025-02-15', NULL, 'Ativo'),
(23, 8, 'Recomposição corporal', 'Perder gordura e ganhar músculo simultaneamente.', '2025-04-01', NULL, 'Ativo'),
(30, 7, 'Alimentação equilibrada', 'Sem objetivos específicos, apenas melhorar hábitos alimentares.', '2025-03-15', NULL, 'Ativo'),
(14, 8, 'Perda de peso', 'Reduzir 8kg para o verão com dieta mediterrânica.', '2025-04-10', '2025-07-10', 'Ativo');

-- ==========================================
-- 10.1. REFEIÇÕES
-- ==========================================
INSERT INTO meal (id_plan, day_of_week, meal_type, description, calories, protein_g, carbs_g, fat_g) VALUES
-- Plano 1 (Pedro Martins - Ganho de massa)
(1, 'Segunda', 'Pequeno-Almoço', 'Papas de aveia com whey protein e frutos vermelhos. 1 ovo cozido.', 450, 35, 50, 10),
(1, 'Segunda', 'Almoço', '150g peito de frango grelhado, 200g arroz basmati, brócolos a vapor.', 600, 45, 80, 10),
(1, 'Segunda', 'Lanche', 'Iogurte grego natural com 30g de nozes e mel.', 300, 15, 10, 20),
(1, 'Segunda', 'Jantar', '200g de salmão grelhado, 150g batata doce, espargos e azeite.', 650, 40, 40, 30),
(1, 'Terça', 'Pequeno-Almoço', 'Pão integral com 2 ovos mexidos e meio abacate.', 500, 25, 40, 25),
(1, 'Terça', 'Almoço', '150g peru grelhado, 150g massa integral, salada com tomate.', 550, 40, 60, 15),
(1, 'Terça', 'Lanche', 'Batido de proteína e 1 banana.', 250, 25, 30, 2),
(1, 'Terça', 'Jantar', 'Bife de vaca magro, 150g quinoa, legumes salteados.', 600, 45, 50, 20),
(1, 'Quarta', 'Pequeno-Almoço', 'Panquecas de aveia e claras de ovo com manteiga de amendoim.', 400, 30, 40, 15),
(1, 'Quarta', 'Almoço', '1 Lata de atum ao natural, feijão frade, ovo cozido e cebola.', 500, 40, 45, 15),

-- Plano 6 (Rita Ferreira - Perda de peso)
(6, 'Segunda', 'Pequeno-Almoço', '1 fatia de pão integral com queijo fresco magro e chá verde.', 200, 10, 25, 5),
(6, 'Segunda', 'Almoço', '100g peito frango desfiado, salada mista à vontade, 1 c. chá azeite.', 350, 30, 15, 15),
(6, 'Segunda', 'Lanche', '1 peça de fruta (maçã) e 10 amêndoas torradas.', 150, 3, 20, 8),
(6, 'Segunda', 'Jantar', '1 posta de pescada cozida, curgete e cenoura cozidas, sem batata.', 300, 25, 15, 10),
(6, 'Terça', 'Pequeno-Almoço', 'Iogurte natural magro com 2 c. sopa de flocos de aveia.', 200, 12, 25, 4),
(6, 'Terça', 'Almoço', 'Omelete de 2 ovos com espinafres, salada de tomate, sem pão.', 350, 20, 10, 20),
(6, 'Terça', 'Jantar', 'Sopa de legumes sem batata. 100g de tofu salteado.', 250, 15, 20, 10),
(6, 'Quarta', 'Almoço', 'Lombos de salmão no forno com brócolos e couve-flor.', 400, 30, 10, 25);


-- ==========================================
-- 11. CONSULTAS COM NUTRICIONISTAS
-- ==========================================
INSERT INTO appointment (id_member, id_nutritionist, appointment_date, status) VALUES
(13, 7, '2025-02-01 10:00:00', 'confirmada'),
(13, 7, '2025-03-01 10:00:00', 'confirmada'),
(13, 7, '2025-04-01 10:00:00', 'confirmada'),
(13, 7, '2026-06-10 10:00:00', 'pendente'),
(16, 8, '2025-03-01 11:00:00', 'confirmada'),
(16, 8, '2025-04-01 11:00:00', 'confirmada'),
(16, 8, '2025-05-01 11:00:00', 'confirmada'),
(17, 7, '2025-02-15 14:00:00', 'confirmada'),
(17, 7, '2025-03-15 14:00:00', 'confirmada'),
(17, 7, '2026-06-15 14:00:00', 'pendente'),
(23, 8, '2025-04-05 09:00:00', 'confirmada'),
(23, 8, '2025-05-05 09:00:00', 'confirmada'),
(23, 8, '2026-06-05 09:00:00', 'pendente'),
(30, 7, '2025-03-20 15:00:00', 'confirmada'),
(30, 7, '2025-04-20 15:00:00', 'confirmada'),
(14, 8, '2025-04-12 16:00:00', 'confirmada'),
(14, 8, '2025-05-12 16:00:00', 'confirmada'),
(14, 8, '2026-06-12 16:00:00', 'pendente'),
(24, 7, '2026-06-08 11:00:00', 'pendente'),
(28, 8, '2026-06-09 10:00:00', 'pendente');

-- ==========================================
-- 12. REGISTO DE PESOS (membros ativos)
-- ==========================================
INSERT INTO weight_log (id_member, weight_kg, logged_at, notes) VALUES
-- Pedro Martins (objetivo: ganhar músculo)
(13, 74.2, '2025-02-01', 'Início do plano nutricional'),
(13, 74.8, '2025-03-01', 'Ligeiro aumento'),
(13, 75.5, '2025-04-01', 'A progredir bem'),
(13, 76.1, '2025-05-01', 'Mais 2kg de músculo'),
(13, 76.8, '2026-01-01', NULL),
(13, 77.2, '2026-02-01', 'Consistente'),
(13, 77.9, '2026-03-01', NULL),
(13, 78.1, '2026-04-01', 'Objetivo quase atingido'),
(13, 78.4, '2026-05-01', NULL),

-- Rita Ferreira (objetivo: manter peso)
(14, 62.1, '2025-02-10', 'Início do plano'),
(14, 61.5, '2025-03-10', 'A melhorar'),
(14, 60.8, '2025-04-10', NULL),
(14, 60.2, '2025-05-10', 'Ótimo progresso'),
(14, 59.9, '2026-01-10', NULL),
(14, 59.5, '2026-02-10', NULL),
(14, 59.1, '2026-03-10', 'Quase no objetivo'),
(14, 58.8, '2026-04-10', NULL),
(14, 58.5, '2026-05-10', 'Objetivo atingido!'),

-- Carlos Oliveira
(15, 88.0, '2025-02-15', 'Começo'),
(15, 87.0, '2025-04-15', NULL),
(15, 86.2, '2025-06-15', NULL),
(15, 85.5, '2026-01-15', 'Bom progresso'),
(15, 84.8, '2026-03-15', NULL),
(15, 84.0, '2026-05-15', NULL),

-- Inês Santos
(16, 57.8, '2025-03-01', NULL),
(16, 57.0, '2025-04-01', NULL),
(16, 56.2, '2025-05-01', NULL),
(16, 55.8, '2026-01-01', 'Mantendo'),
(16, 55.5, '2026-03-01', NULL),
(16, 55.3, '2026-05-01', NULL),

-- Tomás Rodrigues (crossfit)
(17, 82.5, '2025-03-12', 'Início de crossfit'),
(17, 82.0, '2025-04-12', NULL),
(17, 81.5, '2025-05-12', 'Mais definição'),
(17, 81.0, '2026-01-12', NULL),
(17, 80.5, '2026-03-12', NULL),
(17, 80.0, '2026-05-12', 'Muito satisfeito'),

-- Ana Coelho
(30, 65.0, '2025-03-15', 'Início do plano nutricional'),
(30, 64.5, '2025-04-15', NULL),
(30, 64.0, '2025-05-15', NULL),
(30, 63.5, '2026-01-15', NULL),
(30, 63.2, '2026-03-15', NULL),
(30, 63.0, '2026-05-15', 'Estável e saudável');

-- ==========================================
-- 13. GRUPOS MUSCULARES E EQUIPAMENTOS
-- ==========================================
INSERT INTO muscle_group (id, name, description) VALUES 
(1, 'Quadríceps e Glúteos',      'Músculos da parte inferior do corpo.'),
(2, 'Peitoral e Tríceps',        'Músculos de empurrar da parte superior.'),
(3, 'Dorsais e Bíceps',          'Músculos de puxar da parte superior do corpo (costas e braços).'),
(4, 'Ombros',                    'Deltóides (frontal, lateral e posterior).'),
(5, 'Isquiotibiais e Panturrilhas','Parte posterior da perna e gémeos.'),
(6, 'Core e Abdominais',         'Centro de gravidade e estabilização do tronco.');

INSERT INTO equipment (id, name, description, status, location, photo, video_url) VALUES 
(1,  'Leg Press 45º',                   'Máquina de empurrar com os pés.',                                                        'Disponível',  'Piso 1 - Força',    'leg_press.jpg',      'https://youtu.be/q4W4_VJbKW0?si=JgLu2NUFYaOxO-Yg'),
(2,  'Chest Press',                     'Máquina de empurrar sentado.',                                                           'Disponível',  'Piso 1 - Força',    'chest_press.jpg',    'https://www.youtube.com/embed/xUm0BiZCWlQ'),
(3,  'Lat Pulldown (Puxada Frontal)',    'Máquina fundamental para desenvolver a largura das costas.',                            'Disponível',  'Piso 1 - Força',    'lat_pulldown.jpg',   'https://www.youtube.com/embed/EUIri47Epcg'),
(4,  'Leg Extension (Cadeira Extensora)','Isola os quadríceps para máxima hipertrofia frontal da perna.',                        'Disponível',  'Piso 1 - Força',    'leg_extension.jpg',  'https://www.youtube.com/embed/YyvSfVlfRZA'),
(5,  'Lying Leg Curl (Mesa Flexora)',    'Foca-se no isolamento da parte de trás da coxa.',                                      'Disponível',  'Piso 1 - Força',    'leg_curl.jpg',       'https://www.youtube.com/embed/1Tq3QdYUuHs'),
(6,  'Shoulder Press Machine',          'Desenvolvimento de ombros guiado e seguro para a articulação.',                        'Disponível',  'Piso 1 - Força',    'shoulder_press.jpg', 'https://www.youtube.com/embed/WvLMauqrnK8'),
(7,  'Cable Crossover (Polia Dupla)',    'Máquina super versátil com cabos para treinar o corpo todo.',                         'Manutenção',  'Piso 2 - Funcional','cable_crossover.jpg','https://www.youtube.com/embed/taI4XduLpTk'),
(8,  'Abdominal Crunch Machine',        'Máquina com peso para adicionar resistência ao treino de core.',                       'Disponível',  'Piso 2 - Funcional','ab_machine.jpg',     'https://www.youtube.com/embed/9MqwSjCEnos'),
(9,  'Smith Machine',                   'Barra guiada excelente para agachamentos e supinos com maior segurança.',              'Disponível',  'Piso 1 - Força',    'smith_machine.jpg',  'https://www.youtube.com/embed/DIxwGikEQ1E'),
(10, 'Pec Deck (Voador)',               'Isolamento perfeito do peitoral sem recrutar tanto o tríceps.',                        'Disponível',  'Piso 1 - Força',    'pec_deck.jpg',       'https://www.youtube.com/embed/O-A6hYg_N2Y'),
(11, 'Seated Row (Remada Sentada)',      'Máquina de polia baixa para trabalhar a espessura das costas.',                       'Disponível',  'Piso 1 - Força',    'seated_row.jpg',     'https://www.youtube.com/embed/GZbfZ033f74'),
(12, 'Hack Squat',                      'Variação de agachamento que retira a carga direta da coluna e foca nas pernas.',      'Disponível',  'Piso 1 - Força',    'hack_squat.jpg',     'https://www.youtube.com/embed/0tn5K9NlCfo'),
(13, 'Calf Raise Machine (Gémeos)',     'Máquina para treino focado nas panturrilhas em pé.',                                   'Disponível',  'Piso 1 - Força',    'calf_raise.jpg',     'https://www.youtube.com/embed/gwLz0ytvrjo'),
(14, 'Biceps Curl Machine',             'Banco Scott em máquina para isolamento total do bíceps.',                              'Disponível',  'Piso 1 - Força',    'biceps_machine.jpg', 'https://www.youtube.com/embed/wXk-i-DlyEw'),
(15, 'Triceps Press Machine',           'Máquina que simula fundos de tríceps (dips) sentado.',                                 'Disponível',  'Piso 1 - Força',    'triceps_machine.jpg','https://www.youtube.com/embed/c1TusD4N7hY'),
(16, 'Lateral Raise Machine',           'Elevações laterais guiadas para ombros redondos e largos.',                           'Disponível',  'Piso 1 - Força',    'lateral_raise.jpg',  'https://www.youtube.com/embed/kdH9P3D8ZkM'),
(17, 'Hip Thrust Machine',              'A melhor forma de treinar glúteos com cargas pesadas de forma segura.',               'Disponível',  'Piso 1 - Força',    'hip_thrust.jpg',     'https://www.youtube.com/embed/SEdqd1n0cvg'),
(18, 'Graviton (Elevações Assistidas)', 'Máquina com contrapeso para ajudar nas elevações (pull-ups) e fundos (dips).',        'Disponível',  'Piso 2 - Funcional','graviton.jpg',       'https://youtu.be/36Zr0ffv0k4?si=dZt0pKTLuQhRDxVX');

INSERT INTO exercises (id, name, description, id_equipment, id_muscle_group) VALUES 
(1,  'Prensa de Pernas',                'Execução focada na amplitude',                                                              1,  1),
(2,  'Supino Máquina',                  'Foco na contração do peito',                                                                2,  2),
(3,  'Puxada ao Peito',                 'Manter o tronco ligeiramente inclinado para trás e puxar com as costas.',                  3,  3),
(4,  'Extensão de Pernas',              'Apertar bem o músculo no topo do movimento.',                                               4,  1),
(5,  'Flexão de Pernas Deitado',        'Movimento controlado para não usar o balanço da lombar.',                                   5,  5),
(6,  'Desenvolvimento Sentado',         'Empurrar até quase esticar os braços, descendo até à linha das orelhas.',                  6,  4),
(7,  'Crucifixo nos Cabos',             'Juntar as mãos à frente do peito com os cotovelos ligeiramente dobrados.',                 7,  2),
(8,  'Crunch com Carga',                'Enrolar a coluna fletindo o abdominal, sem puxar com os braços.',                          8,  6),
(9,  'Agachamento no Smith',            'Pés ligeiramente à frente da barra, descer até quebrar a paralela.',                       9,  1),
(10, 'Crucifixo no Pec Deck',           'Manter o peito aberto e apertar as almofadas à frente sem usar embalo.',                  10, 2),
(11, 'Remada Baixa com Triângulo',      'Puxar o triângulo em direção ao umbigo, apertando bem as omoplatas.',                     11, 3),
(12, 'Agachamento Hack',                'Apoiar bem as costas, descer devagar e empurrar com a força toda nos calcanhares.',        12, 1),
(13, 'Elevação de Gémeos',              'Descer os calcanhares até sentir alongar e subir ao máximo em pontas dos pés.',           13, 5),
(14, 'Curl de Bíceps',                  'Manter os tríceps encostados ao apoio, não esticar o braço a 100% no fundo.',             14, 3),
(15, 'Extensão de Tríceps Sentado',     'Empurrar as pegas para baixo isolando os tríceps, cotovelos junto ao tronco.',            15, 2),
(16, 'Elevação Lateral na Máquina',     'Elevar os braços até à linha dos ombros, controlando muito a fase da descida.',           16, 4),
(17, 'Elevação Pélvica',                'Apertar os glúteos no topo do movimento e manter a contração por 1 segundo.',             17, 1),
(18, 'Elevações Assistidas (Pull-ups)', 'Pegar na barra bem afastado para focar nas dorsais e puxar até o queixo passar.',        18, 3);

COMMIT;