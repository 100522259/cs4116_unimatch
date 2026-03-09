drop table interests;
drop table academic_info;
drop table personal_info;
drop table credentials;

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

