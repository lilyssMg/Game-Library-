# Database Design

This document describes the database design process for the Game Library application, following the steps: ERD → Relational Model → 3rd Normal Form → RDB Tables.

---

## 1. Entity-Relationship Diagram (ERD)

### Entities and Attributes

**members**
- `id` (PK)
- `name`
- `student_id`
- `email`
- `bio`

**games**
- `game_id` (PK)
- `title`
- `genre`
- `description`
- `image`

### Relationships

The `members` and `games` entities are independent in this application. Members are the group contributors displayed on the about page. Games are the entries managed through the library interface. There is no direct relationship between the two entities.

---

## 2. Relational Model
members(id, name, student_id, email, bio)
games(game_id, title, genre, description, image)

- `members.id` — primary key
- `games.game_id` — primary key
- No foreign keys (entities are independent)

---

## 3. Third Normal Form (3NF)

### members table

Functional dependencies:
id → name, student_id, email, bio

- **1NF**: All attributes are atomic. ✓
- **2NF**: No composite keys, no partial dependencies. ✓
- **3NF**: No transitive dependencies. ✓

### games table

Functional dependencies:
game_id → title, genre, description, image

- **1NF**: All attributes are atomic. ✓
- **2NF**: No composite keys, no partial dependencies. ✓
- **3NF**: No transitive dependencies. ✓

---

## 4. RDB Tables

```sql
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    student_id VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    bio TEXT NOT NULL
);

CREATE TABLE games (
    game_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(50),
    description TEXT,
    image VARCHAR(255)
);
```
