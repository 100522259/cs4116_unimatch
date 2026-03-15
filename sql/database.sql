drop table interests;
drop table academic_info;
drop table personal_info;
drop table credentials;
drop table images;
drop table blocked;
drop table offense;
drop table reports;

create table credentials (
    user_id number(9),
    email char(26) unique not null, 
    -- studentmail emails are 26 characters long
    username varchar(20) unique not null,
    password varchar(20) not null,
    -- we should php to limit min len of password

    primary key(user_id)
);

create table personal_info (
    user_id number(9),
    first_name varchar(30) not null,
    last_name varchar(30) not null,
    age number(2) not null,
    county varchar(20),
    nationality varchar(20),
    gender varchar(20),
    bio varchar(2500),
    administrator bit not null,
    -- bit for bool: 0 false, 1 true

    primary key(user_id),
    foreign key(user_id) references credentials(user_id)
);

create table academic_info (
    user_id number(9),
    course varchar(20) not null,
    c_year number(1) not null,

    primary key(user_id),
    foreign key(user_id) references credentials(user_id)
);

create table interests (
    user_id number(9),
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
    foreign key(user_id) references credentials(user_id)
);

create table reports (
    report_id number(9) not null,
    user_id1 number(9) not null,
    user_id2 number(9) not null,
    timestamp timestamp not null,
    report_msg varchar(2500) not null,
    category varchar(250) not null,

    primary key(report_id),
    foreign key(user_id1) references credentials(user_id),
    foreign key(user_id2) references credentials(user_id)
);

create table offense (
    user_id number(9),
    phone_warning bit not null,
    offence_num number(2) not null,
    blocked bit not null,
    reported bit not null,
    last_modified timestamp not null,
    ban_time number(9),

    primary key(user_id),
    foreign key(user_id) references credentials(user_id)
);

create table blocked (
    user_id number(9),
    student_blocked number(9),

    primary key(user_id, student_blocked),
    foreign key(user_id) references credentials(user_id),
    foreign key(student_blocked) references credentials(user_id)
);

create table images (
    user_id number(9),
    profile_pic varchar(200) not null,
    pic_1 varchar(200),
    pic_2 varchar(200),
    pic_3 varchar(200),
    pic_4 varchar(200),
    pic_5 varchar(200),
    pic_num number(1) not null,

    primary key(user_id),
    foreign key(user_id) references credentials(user_id)
);

CREATE TABLE messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    from_user_id INTEGER NOT NULL,
    to_user_id INTEGER NOT NULL,
    body TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


