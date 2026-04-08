Pragma FOREIGN_KEYS = ON;

Create TABLE USER (
    id INTEGER PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    photo TEXT,
    role TEXT CHECK(role in ('member','trainer','admin'))NOT NULL DEFAULT 'member',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    )

CREATE TABLE AULAS (
    id integer PRIMARY KEY,
    name text NOT NULL,
    tipo TEXT CHECK (tipo IN ('Cardio','Força'))
    
    
    )

