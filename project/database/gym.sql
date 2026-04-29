Pragma FOREIGN_KEYS = ON;

Create TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    photo TEXT,
    role TEXT NOT NULL CHECK (role IN ('admin', 'trainer', 'member','nutritionist')) NOT NULL DEFAULT 'member',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    );

CREATE TABLE trainer_profile (
    id_user INTEGER PRIMARY KEY,
    formation TEXT NOT NULL,
    certification TEXT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
    );

CREATE TABLE class (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    type TEXT NOT NULL CHECK (type IN ('yoga', 'pilates', 'spinning', 'crossfit', 'zumba')) NOT NULL,
    duration TEXT NOT NULL,
    id_trainer INTEGER NOT NULL,
    capacity INTEGER NOT NULL,
    local TEXT NOT NULL,
    FOREIGN KEY (id_trainer) REFERENCES trainer_profile(id_user) ON DELETE CASCADE
    );

CREATE TABLE enrollment (
    id_member INTEGER PRIMARY KEY AUTOINCREMENT,
    id_class INTEGER NOT NULL,
    enrollment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_member, id_class),
    FOREIGN KEY (id_member) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_class) REFERENCES class(id) ON DELETE CASCADE
    );

CREATE TABLE equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    status TEXT CHECK (status IN ('AVAILABLE', 'MAINTENANCE')DEFAULT 'AVAILABLE') NOT NULL,
    location TEXT NOT NULL
    );

CREATE TABLE muscule_group (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
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
    FOREIGN KEY (id_trainer) REFERENCES trainer_id(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_class) REFERENCES class(id) ON DELETE CASCADE;
    CHECK ((id_trainer IS NOT NULL AND id_class IS NULL) OR 
           (id_trainer IS NULL AND id_class IS NOT NULL))
    );

