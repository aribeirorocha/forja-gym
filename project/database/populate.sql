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

COMMIT;
