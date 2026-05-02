BEGIN TRANSACTION;

INSERT INTO user (id, username, name, email, password, photo, role) VALUES (1, 'anasilva', 'Ana Silva', 'anasilva@gmail.com', '1234', 'trainer1.jpg', 'trainer'),
(2, 'joaocosta', 'João Costa', 'joaocosta@gmail.com', '1234', 'trainer2.jpg', 'trainer'),
(3, 'mariofernandes', 'Mário Fernandes', 'mariofernandes@gmail.com', '1234', 'trainer3.jpg', 'trainer'),
(4, 'ruibarbosa', 'Rui Barbosa', 'ruibarbosa@gmail.com', '1234', 'trainer6.jpg', 'trainer'),
(5, 'nunomendes', 'Nuno Mendes', 'nunomendes@gmail.com', '1234', 'trainer4.jpg', 'trainer'),
(6, 'rafaelleao', 'Rafael Leão', 'rafaelleao@gmail.com', '1234', 'trainer5.jpg', 'trainer');

INSERT INTO trainer_profile (id_user, formation, certification) VALUES (1, 'Licenciatura em Ciências do Desporto – FADEUP', 'Yoga Alliance RYT-200, STOTT Pilates'),
(2, 'Licenciatura em Educação Física – ISMAI', 'CrossFit Level 1, Kettlebell Instructor'),
(3, 'Licenciatura em Ciências do Desporto – UTAD', 'CrossFit Level 2, Precision Nutrition L1'),
(4, 'Mestrado em Treino Desportivo – FMH Lisboa', 'Functional Movement Screen, FRC Mobility Specialist'),
(5, 'Licenciatura em Desporto e Lazer – ESDRM', 'Spinning® Certified Instructor, Athletics Coach'),
(6, 'Licenciatura em Educação Física – FADEUP', 'CrossFit Level 1, Mobility WOD Coach');

COMMIT;