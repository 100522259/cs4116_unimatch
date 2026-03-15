<?php
// SQLite connection
$database = new SQLite3("unimatch.db");

// Create tables
$database->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user',
    status TEXT NOT NULL DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS profiles (
    user_id INTEGER PRIMARY KEY,
    display_name TEXT NOT NULL,
    bio TEXT,
    seeking TEXT DEFAULT 'any',
    location TEXT
);

CREATE TABLE IF NOT EXISTS interests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS user_interests (
    user_id INTEGER NOT NULL,
    interest_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, interest_id)
);

CREATE TABLE IF NOT EXISTS connections (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    from_user_id INTEGER NOT NULL,
    to_user_id INTEGER NOT NULL,
    status TEXT DEFAULT 'pending'
);

CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    from_user_id INTEGER NOT NULL,
    to_user_id INTEGER NOT NULL,
    body TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
");

// Sample data only if empty
$userCount = $database->querySingle("SELECT COUNT(*) FROM users");
if ($userCount == 0) {

    // Users
    $database->exec("
        INSERT INTO users (email,password_hash) VALUES
        ('alice@ul.ie','hash1'),
        ('bob@ul.ie','hash2'),
        ('charlie@ul.ie','hash3')
    ");

    // Profiles
    $database->exec("
        INSERT INTO profiles (user_id,display_name,bio,seeking,location) VALUES
        (1,'Alice','I love studying and meeting new people!','any','Limerick, Ireland'),
        (2,'Bob','Football fan. Looking for someone special','female','Limerick, Ireland'),
        (3,'Charlie','Loves hiking and music. Always up for an adventure!','any','Limerick, Ireland')
    ");

    // Interests
    $database->exec("
        INSERT INTO interests (name) VALUES ('Football'),('Coding'),('Hiking')
    ");

    // User interests
    $database->exec("
        INSERT INTO user_interests (user_id,interest_id) VALUES
        (1,1),(1,2),
        (2,1),
        (3,3)
    ");

    // Connections
    $database->exec("
        INSERT INTO connections (from_user_id,to_user_id,status) VALUES
        (1,2,'matched'),  -- Alice -> Bob (dating)
        (1,3,'matched')   -- Alice -> Charlie (friendship)
    ");

    // Messages
    $database->exec("
        INSERT INTO messages (from_user_id,to_user_id,body) VALUES
        (1,2,'Hey Bob!'),
        (2,1,'Hi Alice, how are you?'),
        (1,3,'Hey Charlie!'),
        (3,1,'Hi Alice! Ready to go hiking?')
    ");
}
?>
