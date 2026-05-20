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

INSERT INTO muscle_group (id, name, description) VALUES 
(1, 'Quadríceps e Glúteos', 'Músculos da parte inferior do corpo.'),
(2, 'Peitoral e Tríceps', 'Músculos de empurrar da parte superior.');


INSERT INTO equipment (id, name, description, status, location, photo, video_url) VALUES 
(1, 'Leg Press 45º', 'Máquina de empurrar com os pés.', 'Disponível', 'Piso 1 - Força', 'leg_press.jpg', 'https://www.youtube.com/embed/yZmx_Ac3880'),
(2, 'Chest Press', 'Máquina de empurrar sentado.', 'Disponível', 'Piso 1 - Força', 'chest_press.jpg', 'https://www.youtube.com/embed/xUm0BiZCWlQ');


INSERT INTO exercises (id, name, description, id_equipment, id_muscle_group) VALUES 
(1, 'Prensa de Pernas', 'Execução focada na amplitude', 1, 1),
(2, 'Supino Máquina', 'Foco na contração do peito', 2, 2);


INSERT INTO muscle_group (id, name, description) VALUES 
(3, 'Dorsais e Bíceps', 'Músculos de puxar da parte superior do corpo (costas e braços).'),
(4, 'Ombros', 'Deltóides (frontal, lateral e posterior).'),
(5, 'Isquiotibiais e Panturrilhas', 'Parte posterior da perna e gémeos.'),
(6, 'Core e Abdominais', 'Centro de gravidade e estabilização do tronco.');


INSERT INTO equipment (id, name, description, status, location, photo, video_url) VALUES 
(3, 'Lat Pulldown (Puxada Frontal)', 'Máquina fundamental para desenvolver a largura das costas.', 'Disponível', 'Piso 1 - Força', 'lat_pulldown.jpg', 'https://www.youtube.com/embed/EUIri47Epcg'),
(4, 'Leg Extension (Cadeira Extensora)', 'Isola os quadríceps para máxima hipertrofia frontal da perna.', 'Disponível', 'Piso 1 - Força', 'leg_extension.jpg', 'https://www.youtube.com/embed/YyvSfVlfRZA'),
(5, 'Lying Leg Curl (Mesa Flexora)', 'Foca-se no isolamento da parte de trás da coxa.', 'Disponível', 'Piso 1 - Força', 'leg_curl.jpg', 'https://www.youtube.com/embed/1Tq3QdYUuHs'),
(6, 'Shoulder Press Machine', 'Desenvolvimento de ombros guiado e seguro para a articulação.', 'Disponível', 'Piso 1 - Força', 'shoulder_press.jpg', 'https://www.youtube.com/embed/WvLMauqrnK8'),
(7, 'Cable Crossover (Polia Dupla)', 'Máquina super versátil com cabos para treinar o corpo todo, muito focada no peito.', 'Disponível', 'Piso 2 - Funcional', 'cable_crossover.jpg', 'https://www.youtube.com/embed/taI4XduLpTk'),
(8, 'Abdominal Crunch Machine', 'Máquina com peso para adicionar resistência ao treino de core.', 'Disponível', 'Piso 2 - Funcional', 'ab_machine.jpg', 'https://www.youtube.com/embed/9MqwSjCEnos');

INSERT INTO exercises (id, name, description, id_equipment, id_muscle_group) VALUES 
(3, 'Puxada ao Peito', 'Manter o tronco ligeiramente inclinado para trás e puxar com as costas.', 3, 3),
(4, 'Extensão de Pernas', 'Apertar bem o músculo no topo do movimento.', 4, 1),
(5, 'Flexão de Pernas Deitado', 'Movimento controlado para não usar o balanço da lombar.', 5, 5),
(6, 'Desenvolvimento Sentado', 'Empurrar até quase esticar os braços, descendo até à linha das orelhas.', 6, 4),
(7, 'Crucifixo nos Cabos', 'Juntar as mãos à frente do peito com os cotovelos ligeiramente dobrados.', 7, 2),
(8, 'Crunch com Carga', 'Enrolar a coluna fletindo o abdominal, sem puxar com os braços.', 8, 6);



INSERT INTO equipment (id, name, description, status, location, photo, video_url) VALUES 
(9, 'Smith Machine', 'Barra guiada excelente para agachamentos e supinos com maior segurança.', 'Disponível', 'Piso 1 - Força', 'smith_machine.jpg', 'https://www.youtube.com/embed/DIxwGikEQ1E'),
(10, 'Pec Deck (Voador)', 'Isolamento perfeito do peitoral sem recrutar tanto o tríceps.', 'Disponível', 'Piso 1 - Força', 'pec_deck.jpg', 'https://www.youtube.com/embed/O-A6hYg_N2Y'),
(11, 'Seated Row (Remada Sentada)', 'Máquina de polia baixa para trabalhar a espessura das costas.', 'Disponível', 'Piso 1 - Força', 'seated_row.jpg', 'https://www.youtube.com/embed/GZbfZ033f74'),
(12, 'Hack Squat', 'Variação de agachamento que retira a carga direta da coluna e foca nas pernas.', 'Disponível', 'Piso 1 - Força', 'hack_squat.jpg', 'https://www.youtube.com/embed/0tn5K9NlCfo'),
(13, 'Calf Raise Machine (Gémeos)', 'Máquina para treino focado nas panturrilhas em pé.', 'Disponível', 'Piso 1 - Força', 'calf_raise.jpg', 'https://www.youtube.com/embed/gwLz0ytvrjo'),
(14, 'Biceps Curl Machine', 'Banco Scott em máquina para isolamento total do bíceps.', 'Disponível', 'Piso 1 - Força', 'biceps_machine.jpg', 'https://www.youtube.com/embed/wXk-i-DlyEw'),
(15, 'Triceps Press Machine', 'Máquina que simula fundos de tríceps (dips) sentado.', 'Disponível', 'Piso 1 - Força', 'triceps_machine.jpg', 'https://www.youtube.com/embed/c1TusD4N7hY'),
(16, 'Lateral Raise Machine', 'Elevações laterais guiadas para ombros redondos e largos.', 'Disponível', 'Piso 1 - Força', 'lateral_raise.jpg', 'https://www.youtube.com/embed/kdH9P3D8ZkM'),
(17, 'Hip Thrust Machine', 'A melhor forma de treinar glúteos com cargas pesadas de forma confortável e segura.', 'Disponível', 'Piso 1 - Força', 'hip_thrust.jpg', 'https://www.youtube.com/embed/SEdqd1n0cvg'),
(18, 'Graviton (Elevações Assistidas)', 'Máquina com contrapeso para ajudar nas elevações (pull-ups) e fundos (dips).', 'Disponível', 'Piso 2 - Funcional', 'graviton.jpg', 'https://www.youtube.com/embed/XqE-3jI14n4');



INSERT INTO exercises (id, name, description, id_equipment, id_muscle_group) VALUES 
(9, 'Agachamento no Smith', 'Pés ligeiramente à frente da barra, descer até quebrar a paralela.', 9, 1),
(10, 'Crucifixo no Pec Deck', 'Manter o peito aberto e apertar as almofadas à frente sem usar embalo.', 10, 2),
(11, 'Remada Baixa com Triângulo', 'Puxar o triângulo em direção ao umbigo, apertando bem as omoplatas.', 11, 3),
(12, 'Agachamento Hack', 'Apoiar bem as costas, descer devagar e empurrar com a força toda nos calcanhares.', 12, 1),
(13, 'Elevação de Gémeos', 'Descer os calcanhares até sentir alongar e subir ao máximo em pontas dos pés.', 13, 5),
(14, 'Curl de Bíceps', 'Manter os tríceps encostados ao apoio, não esticar o braço a 100% no fundo.', 14, 3),
(15, 'Extensão de Tríceps Sentado', 'Empurrar as pegas para baixo isolando os tríceps, cotovelos junto ao tronco.', 15, 2),
(16, 'Elevação Lateral na Máquina', 'Elevar os braços até à linha dos ombros, controlando muito a fase da descida.', 16, 4),
(17, 'Elevação Pélvica', 'Apertar os glúteos no topo do movimento e manter a contração por 1 segundo.', 17, 1),
(18, 'Elevações Assistidas (Pull-ups)', 'Pegar na barra bem afastado para focar nas dorsais e puxar até o queixo passar.', 18, 3);
COMMIT;
