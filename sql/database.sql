drop table interests;
drop table messages;
drop table academic_info;
drop table personal_info;
drop table images;
drop table blocked;
drop table offense;
drop table reports;
drop table credentials;


create table credentials (
    user_id int(9),
    email char(27) unique not null, 
    -- studentmail emails are 26 characters long
    username varchar(20) unique not null,
    password varchar(20) not null,
    -- we should php to limit min len of password

    primary key(user_id)
);

create table personal_info (
    user_id int(9),
    first_name varchar(30) not null,
    last_name varchar(30) not null,
    age int(2) not null,
    county varchar(20),
    nationality varchar(20),
    gender varchar(20),
    bio varchar(2500),
    administrator bit not null,
    -- bit for bool: 0 false, 1 true

    primary key(user_id),
    foreign key(user_id) references credentials(user_id) on delete cascade on delete cascade
);

create table academic_info (
    user_id int(9),
    course varchar(20) not null,
    c_year int(1) not null,

    primary key(user_id),
    foreign key(user_id) references credentials(user_id) on delete cascade
);

create table interests (
    user_id int(9),
    drink varchar(20) not null,
    drink_display bit not null,
    smoke varchar(20) not null,
    smoke_display bit not null,
    food_lifestyle varchar(20) not null,
    food_display bit not null,
    personality varchar(20) not null,
    personality_display bit not null,
    sexuality varchar(20) not null,
    sexuality_display bit not null,
    interest1 varchar(20),
    interest2 varchar(20),
    interest3 varchar(20),
    interest4 varchar(20),
    interest5 varchar(20),

    primary key(user_id),
    foreign key(user_id) references credentials(user_id) on delete cascade
);

create table reports (
    report_id int(9) not null,
    user_id1 int(9) not null,
    user_id2 int(9) not null,
    timestamp timestamp not null,
    report_msg varchar(2500) not null,
    category varchar(250) not null,

    primary key(report_id),
    foreign key(user_id1) references credentials(user_id) on delete cascade,
    foreign key(user_id2) references credentials(user_id) on delete cascade
);

create table offense (
    user_id int(9),
    phone_warning bit not null,
    offence_num int(2) not null,
    blocked bit not null,
    reported bit not null,
    last_modified timestamp not null,
    ban_time int(9),

    primary key(user_id),
    foreign key(user_id) references credentials(user_id) on delete cascade
);

create table blocked (
    user_id int(9),
    student_blocked int(9),

    primary key(user_id, student_blocked),
    foreign key(user_id) references credentials(user_id) on delete cascade,
    foreign key(student_blocked) references credentials(user_id) on delete cascade
);

create table images (
    user_id int(9),
    profile_pic varchar(200) not null,
    pic_1 varchar(200),
    pic_2 varchar(200),
    pic_3 varchar(200),
    pic_4 varchar(200),
    pic_5 varchar(200),
    pic_num int(1) not null,

    primary key(user_id),
    foreign key(user_id) references credentials(user_id) on delete cascade
);

CREATE TABLE messages (
    id int(9) PRIMARY KEY AUTO_INCREMENT,
    from_user_id int(9) NOT NULL,
    to_user_id int(9) NOT NULL,
    body TEXT NOT NULL,
    sent_at timestamp DEFAULT CURRENT_TIMESTAMP,

    foreign key(from_user_id) references credentials(user_id) on delete cascade,
    foreign key (to_user_id) references credentials(user_id) on delete cascade

);


-- 7. relationship (matches)

CREATE TABLE relationship (
    relationship_id int(9) AUTO_INCREMENT PRIMARY KEY,
    user_id1 int(9) NOT NULL,
    user_id2 int(9) NOT NULL,
    romantic bit NOT NULL,
    friendship bit NOT NULL,
    r_status bit NOT NULL,
    f_status bit NOT NULL,
    created_at timestamp NOT NULL,

    FOREIGN KEY (user_id1) REFERENCES credentials(user_id),
    FOREIGN KEY (user_id2) REFERENCES credentials(user_id)
);

CREATE TABLE matches (
    relationship_id int(9) AUTO_INCREMENT PRIMARY KEY,
    user_id1 int(9) NOT NULL,
    user_id2 int(9) NOT NULL,
    romantic bit NOT NULL,
    friendship bit NOT NULL,
    created_at timestamp NOT NULL,

    FOREIGN KEY (user_id1) REFERENCES credentials(user_id),
    FOREIGN KEY (user_id2) REFERENCES credentials(user_id)
);