# FORJA Gym — Gym Management Web Platform

![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white) ![SQLite](https://img.shields.io/badge/SQLite-003B57?logo=sqlite&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black) ![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white) ![Grade](https://img.shields.io/badge/Grade-18%2F20-green)

A full web platform that digitizes the day-to-day operations of a modern gym. It is built **without frameworks**, using only PHP, SQLite, vanilla JavaScript and CSS.

> Project for **Web Languages and Technologies (LTW)** — BSc in Informatics and Computing Engineering (L.EIC), FEUP, 2026. **Final grade: 18/20.**

## Features by role
| Role | What they can do |
|---|---|
| **Member** | Sign up and edit their profile, browse and book classes (with capacity control), book personal-training sessions, rate classes and trainers, use subscription plans (Basic / Normal / VIP) |
| **Trainer** | Manage their profile, see their classes and attendees, accept or reject personal-training requests |
| **Nutritionist** *(our own addition)* | Manage patients and create detailed **meal plans** (meals, macros, calories) |
| **Admin** | **Statistics dashboard** (top-rated classes, top trainers), user and role management, class and equipment catalogue |

There is also an **interactive equipment library**, filterable by muscle group, with embedded YouTube tutorials.

## Security
- **SQL injection:** prepared statements only (PDO)
- **XSS:** all user output is escaped
- **CSRF:** tokens on every form and sensitive action
- **Passwords:** stored as hashes with `password_hash()`

## Architecture
MVC-style structure: `pages/` (controllers), `templates/` (views), `database/` (models and SQL), `js/` (AJAX and validation), `css/` (responsive, mobile-first).

## ▶ How to run
```bash
cd project
php -S localhost:9000
```
Open http://localhost:9000. Test accounts are listed in [README_PT.md](README_PT.md#contas-de-teste) (password `1234`).

## My contribution
I was the **main contributor** in the team:
- **Database design** (schema, sample data) and authentication
- Profile, classes, nutrition, trainers, equipment and **admin** pages
- **Anti-CSRF protection** and **AJAX** class booking
- Bug fixing, visual polish and the final delivery

## Team
André Rocha · Ricardo Oliveira · Cândido Junior

 Original documentation (Portuguese): [README_PT.md](README_PT.md)
