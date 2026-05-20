BEGIN TRANSACTION;

INSERT INTO user (id, username, name, email, password, photo, role, age) VALUES (1, 'anasilva', 'Ana Silva', 'anasilva@gmail.com', '1234', 'trainer1.jpg', 'trainer', 31),
(2, 'joaocosta', 'João Costa', 'joaocosta@gmail.com', '1234', 'trainer2.jpg', 'trainer', 32),
(3, 'mariofernandes', 'Mário Fernandes', 'mariofernandes@gmail.com', '1234', 'trainer3.jpg', 'trainer', 33),
(4, 'ruibarbosa', 'Rui Barbosa', 'ruibarbosa@gmail.com', '1234', 'trainer6.jpg', 'trainer', 34),
(5, 'nunomendes', 'Nuno Mendes', 'nunomendes@gmail.com', '1234', 'trainer4.jpg', 'trainer', 35),
(6, 'rafaelleao', 'Rafael Leão', 'rafaelleao@gmail.com', '1234', 'trainer5.jpg', 'trainer', 36);

INSERT INTO trainer_profile (id_user, formation, certification, specialization, location) VALUES (1, 'Licenciatura em Ciências do Desporto – FADEUP', 'Yoga Alliance RYT-200, STOTT Pilates', 'Yoga,Pilates', 'Porto, Portugal'),
(2, 'Licenciatura em Educação Física – ISMAI', 'CrossFit Level 1, Kettlebell Instructor', 'HIIT,Força', 'Porto, Portugal'),
(3, 'Licenciatura em Ciências do Desporto – UTAD', 'CrossFit Level 2, Precision Nutrition L1', 'Stretch,Calistenia', 'Porto, Portugal'),
(4, 'Mestrado em Treino Desportivo – FMH Lisboa', 'Functional Movement Screen, FRC Mobility Specialist', 'Spinning,Sprint', 'Porto, Portugal'),
(5, 'Licenciatura em Desporto e Lazer – ESDRM', 'Spinning® Certified Instructor, Athletics Coach', 'CrossFit,Cardio', 'Porto, Portugal'),
(6, 'Licenciatura em Educação Física – FADEUP', 'CrossFit Level 1, Mobility WOD Coach', 'CrossFit,Mobilidade', 'Porto, Portugal');

INSERT INTO class (id, name, type, duration_minutes, id_trainer, capacity, local, active) VALUES (1, 'Yoga', 'yoga', 60, 1, 12, 'Porto', 1),
(2, 'CrossFit', 'crossfit', 60, 2, 8, 'Porto', 1),
(3, 'Spinning', 'spinning', 45, 3, 10, 'Porto', 1);

INSERT INTO class_session (id, id_class, session_datetime, status) VALUES (1, 1, '2026-05-04 09:00:00', 'Agendada'),
(2, 2, '2026-05-05 18:00:00', 'Agendada'),
(3, 3, '2026-05-06 19:00:00', 'Agendada');

INSERT INTO user (id, username, name, email, password, photo, role) 
VALUES (7, 'nutri_ana', 'Dra. Ana Silva', 'ana@nutri.pt', '1234', 'nutritionist.jpg', 'nutritionist');
INSERT INTO nutritionist_profile (id_user, specialization, license_number) 
VALUES (7, 'Nutrição Desportiva', 'ORDEM-12345');
INSERT INTO user (id, username, name, email, password, photo, role) 
VALUES (8, 'nutri_pedro', 'Dr. Pedro Rocha', 'pedro@nutri.pt', '1234', 'nutri_pedro.jpg', 'nutritionist');

INSERT INTO nutritionist_profile (id_user, specialization, license_number) 
VALUES (8, 'Nutrição Clínica e Funcional', 'ORDEM-99887');
-- 1. Inserir Grupos Musculares
INSERT INTO muscle_group (id, name, description) VALUES 
(1, 'Quadríceps e Glúteos', 'Músculos da parte inferior do corpo.'),
(2, 'Peitoral e Tríceps', 'Músculos de empurrar da parte superior.');

-- 2. Inserir Equipamentos (com os vídeos e fotos)
INSERT INTO equipment (id, name, description, status, location, photo, video_url) VALUES 
(1, 'Leg Press 45º', 'Máquina de empurrar com os pés.', 'Disponível', 'Piso 1 - Força', 'leg_press.jpg', 'https://www.youtube.com/embed/yZmx_Ac3880'),
(2, 'Chest Press', 'Máquina de empurrar sentado.', 'Disponível', 'Piso 1 - Força', 'chest_press.jpg', 'https://www.youtube.com/embed/xUm0BiZCWlQ');

-- 3. Fazer a ligação (O Exercício na Máquina X treina o Músculo Y)
INSERT INTO exercises (id, name, description, id_equipment, id_muscle_group) VALUES 
(1, 'Prensa de Pernas', 'Execução focada na amplitude', 1, 1),
(2, 'Supino Máquina', 'Foco na contração do peito', 2, 2);
COMMIT;
