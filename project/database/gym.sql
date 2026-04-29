Pragma FOREIGN_KEYS = ON;

Create TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    photo TEXT,
    role TEXT NOT NULL CHECK (role IN ('admin', 'trainer', 'member','nutritionist'))DEFAULT 'member',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    );

CREATE TABLE membership_plan(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    plan_type TEXT NOT NULL CHECK (plan_type IN ('basic', 'premium', 'vip')),
    description TEXT,
    price REAL NOT NULL,
    duration_months INTEGER NOT NULL
    );

CREATE TABLE subscription (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_member INTEGER NOT NULL,
    id_plan INTEGER NOT NULL,
    start_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    end_date DATETIME NOT NULL,
    status TEXT CHECK (status IN ('ACTIVE', 'EXPIRED', 'CANCELED')) DEFAULT 'ACTIVE',
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_plan) REFERENCES membership_plan(id) ON DELETE CASCADE
    );

CREATE TABLE  nutritionist_profile (
    id_user INTEGER PRIMARY KEY,
    specialization TEXT NOT NULL,
    license_number TEXT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
    );

CREATE TABLE trainer_profile (
    id_user INTEGER PRIMARY KEY,
    formation TEXT NOT NULL,
    certification TEXT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
    );

CREATE TABLE trainer_availabity (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_trainer INTEGER NOT NULL,
    day_of_week TEXT NOT NULL CHECK (day_of_week IN ('Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo')),
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE
    );
CREATE TABLE pt_booking (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_member INTEGER NOT NULL,
    id_trainer INTEGER NOT NULL,
    booking_date DATETIME NOT NULL,
    start_time TIME NOT NULL,
    status TEXT CHECK (status IN ('Pendente', 'Confirmado', 'Cancelado')) DEFAULT 'Pendente',
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE
    );


CREATE TABLE class (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    type TEXT NOT NULL CHECK (type IN ('yoga', 'pilates', 'spinning', 'crossfit', 'zumba')),
    duration_minutes INTEGER NOT NULL,
    id_trainer INTEGER NOT NULL,
    capacity INTEGER NOT NULL,
    local TEXT NOT NULL,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE
    );

CREATE TABLE enrollment (
    id_member INTEGER PRIMARY KEY,
    id_class INTEGER NOT NULL,
    enrollment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_member, id_class),
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_class) REFERENCES class(id) ON DELETE CASCADE,
    );

CREATE TABLE equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    status TEXT CHECK (status IN ('AVAILABLE', 'MAINTENANCE')) DEFAULT 'AVAILABLE' NOT NULL,
    location TEXT NOT NULL
    );

CREATE TABLE muscle_group (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT
    );

CREATE TABLE exercises (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name text NOT NULL,
    description text NOT NULL,
    id_equipament INTEGER,
    id_muscle_group INTEGER,
    FOREIGN KEY (id_equipament) REFERENCES equipment(id) ON DELETE SET NULL,
    FOREIGN KEY (id_muscle_group) REFERENCES muscule_group(id) ON DELETE SET NULL
    );

CREATE TABLE rating (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_member INTEGER NOT NULL,
    id_trainer INTEGER,
    id_class INTEGER,
    rate INTEGER CHECK (rate>=1 AND rate<=5) NOT NULL,
    comment text,
    create_ad DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_class) REFERENCES class(id) ON DELETE CASCADE,
    CHECK ((id_trainer IS NOT NULL AND id_class IS NULL) OR 
           (id_trainer IS NULL AND id_class IS NOT NULL))
    );

CREATE TABLE nutrition_plan (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_member INTEGER NOT NULL,
    id_nutritionist INTEGER NOT NULL,
    goal TEXT NOT NULL,
    notes TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME,
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_nutritionist) REFERENCES nutritionist_profile(id_user) ON DELETE CASCADE
    );

CREATE TABLE meal (
    ID INTEGER PRIMARY KEY AUTOINCREMENT,
    id_plan INTEGER NOT NULL,
    day_of_week TEXT NOT NULL CHECK (day_of_week IN ('Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo')),
    meal_type TEXT NOT NULL CHECK (meal_type IN ('Pequeno-Almoço', 'Almoço', 'Jantar', 'Lanche')),
    description TEXT NOT NULL,
    calories INTEGER,
    FOREIGN KEY (id_plan) REFERENCES nutrition_plan(id) ON DELETE CASCADE
    );
CREATE TABLE waitlist (
    id_member INTEGER NOT NULL,
    id_class INTEGER NOT NULL,
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_member, id_class),
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_class) REFERENCES class(id) ON DELETE CASCADE
    );

CREATE TABLE reports (
    id integer PRIMARY KEY AUTOINCREMENT,
    id_member INTEGER NOT NULL,
    category TEXT NOT NULL CHECK (category IN ('equipment', 'class', 'trainer', 'nutritionist','facilities','cleaning','other')) NOT NULL,
    id_equipament INTEGER,
    id_trainer INTEGER,
    id_class INTEGER,
    id_nutritionist INTEGER,
    description TEXT NOT NULL,
    status TEXT CHECK (status IN ('Aberta', 'Investigando', 'Resolvida')) DEFAULT 'Resolvida' NOT NULL,    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_equipament) REFERENCES equipment(id) ON DELETE SET NULL,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE SET NULL,
    FOREIGN KEY (id_class) REFERENCES class(id) ON DELETE SET NULL,
    FOREIGN KEY (id_nutritionist) REFERENCES nutritionist_profile(id_user) ON DELETE SET NULL
    );