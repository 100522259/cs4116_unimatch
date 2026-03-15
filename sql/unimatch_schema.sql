-- 3. credentials (main user table)

CREATE TABLE credentials (
    user_id INTEGER PRIMARY KEY,
    email TEXT NOT NULL UNIQUE,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
);



-- 1. personal info

CREATE TABLE personal_info (
    user_id INTEGER PRIMARY KEY,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    age INTEGER NOT NULL,
    county TEXT,
    nationality TEXT,
    gender TEXT,
    bio TEXT,
    admin BOOLEAN NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES credentials(user_id)
);


-- 2. academic info

CREATE TABLE academic_info (
    user_id INTEGER PRIMARY KEY,
    course TEXT NOT NULL,
    year INTEGER NOT NULL,

    FOREIGN KEY (user_id) REFERENCES credentials(user_id)
);



-- 4. interests

CREATE TABLE interests (
    user_id INTEGER PRIMARY KEY,
    drink TEXT NOT NULL,
    drink_display BOOLEAN NOT NULL,
    smoke TEXT NOT NULL,
    smoke_display BOOLEAN NOT NULL,
    food_lifestyle TEXT NOT NULL,
    food_display BOOLEAN NOT NULL,
    personality TEXT NOT NULL,
    personality_display BOOLEAN NOT NULL,
    sexuality TEXT NOT NULL,
    sexuality_display BOOLEAN NOT NULL,
    interest1 TEXT,
    interest2 TEXT,
    interest3 TEXT,
    interest4 TEXT,
    interest5 TEXT,

    FOREIGN KEY (user_id) REFERENCES credentials(user_id)
);



-- 5. clubs / societies

CREATE TABLE clubs_societies (
    user_id INTEGER PRIMARY KEY,
    club_society1 TEXT,
    club_society2 TEXT,
    club_society3 TEXT,

    FOREIGN KEY (user_id) REFERENCES credentials(user_id)
);



-- 12. images

CREATE TABLE images (
    user_id INTEGER PRIMARY KEY,
    profile_pic TEXT NOT NULL,
    pic_1 TEXT,
    pic_2 TEXT,
    pic_3 TEXT,
    pic_4 TEXT,
    pic_5 TEXT,
    pic_num INTEGER NOT NULL,

    FOREIGN KEY (user_id) REFERENCES credentials(user_id)
);



-- 8. activity (likes)

CREATE TABLE activity (
    activity_id INTEGER PRIMARY KEY,
    user_id1 INTEGER NOT NULL,
    user_id2 INTEGER NOT NULL,
    liked BOOLEAN NOT NULL,
    timestamp DATETIME NOT NULL,

    FOREIGN KEY (user_id1) REFERENCES credentials(user_id),
    FOREIGN KEY (user_id2) REFERENCES credentials(user_id)
);



-- 7. relationship (matches)

CREATE TABLE relationship (
    relationship_id INTEGER PRIMARY KEY,
    user_id1 INTEGER NOT NULL,
    user_id2 INTEGER NOT NULL,
    romantic BOOLEAN NOT NULL,
    friendship BOOLEAN NOT NULL,
    status TEXT NOT NULL,
    created_at DATETIME NOT NULL,

    FOREIGN KEY (user_id1) REFERENCES credentials(user_id),
    FOREIGN KEY (user_id2) REFERENCES credentials(user_id)
);



-- 6. text messages

CREATE TABLE text_messages (
    conversation_id INTEGER PRIMARY KEY,
    user_id1 INTEGER NOT NULL,
    user_id2 INTEGER NOT NULL,
    created_at DATETIME NOT NULL,
    message TEXT NOT NULL,
    last_message_at DATETIME,

    FOREIGN KEY (user_id1) REFERENCES credentials(user_id),
    FOREIGN KEY (user_id2) REFERENCES credentials(user_id)
);



-- 9. reports

CREATE TABLE reports (
    report_id INTEGER PRIMARY KEY,
    user_id1 INTEGER NOT NULL,
    user_id2 INTEGER NOT NULL,
    timestamp DATETIME NOT NULL,
    report_msg TEXT NOT NULL,
    category TEXT NOT NULL,

    FOREIGN KEY (user_id1) REFERENCES credentials(user_id),
    FOREIGN KEY (user_id2) REFERENCES credentials(user_id)
);



-- 10. offense

CREATE TABLE offense (
    user_id INTEGER PRIMARY KEY,
    phone_warning BOOLEAN NOT NULL,
    offence_num INTEGER NOT NULL,
    blocked BOOLEAN NOT NULL,
    reported BOOLEAN NOT NULL,
    last_modified DATETIME NOT NULL,
    ban_time INTEGER,

    FOREIGN KEY (user_id) REFERENCES credentials(user_id)
);



-- 11. blocked users

CREATE TABLE blocked (
    user_id INTEGER NOT NULL,
    student_blocked INTEGER NOT NULL,

    PRIMARY KEY (user_id, student_blocked),

    FOREIGN KEY (user_id) REFERENCES credentials(user_id),
    FOREIGN KEY (student_blocked) REFERENCES credentials(user_id)
);