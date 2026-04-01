-- With delete cascade we remove all rows from all tables
delete from table credentials;

--1. credentials
INSERT INTO credentials VALUES (200000001, '200000001@studentmail.ul.ie', 'aidan01', 'pass123');
INSERT INTO credentials VALUES (200000002, '200000002@studentmail.ul.ie', 'emma02', 'pass123');
INSERT INTO credentials VALUES (200000003, '200000003@studentmail.ul.ie', 'liam03', 'pass123');
INSERT INTO credentials VALUES (200000004, '200000004@studentmail.ul.ie', 'sophie04', 'pass123');
INSERT INTO credentials VALUES (200000005, '200000005@studentmail.ul.ie', 'noah05', 'pass123');
INSERT INTO credentials VALUES (200000006, '200000006@studentmail.ul.ie', 'ava06', 'pass123');
INSERT INTO credentials VALUES (200000007, '200000007@studentmail.ul.ie', 'jack07', 'pass123');
INSERT INTO credentials VALUES (200000008, '200000008@studentmail.ul.ie', 'mia08', 'pass123');
INSERT INTO credentials VALUES (200000009, '200000009@studentmail.ul.ie', 'leo09', 'pass123');
INSERT INTO credentials VALUES (200000010, '200000010@studentmail.ul.ie', 'admin01', 'admin123');


--2. personal info
INSERT INTO personal_info VALUES (200000001,'Aidan','Murphy',23,'Limerick','Irish','Male','Love sports and gaming',0);
INSERT INTO personal_info VALUES (200000002,'Emma','Kelly',21,'Dublin','Irish','Female','Coffee and travel lover',0);
INSERT INTO personal_info VALUES (200000003,'Liam','Ryan',22,'Cork','Irish','Male','Tech enthusiast',0);
INSERT INTO personal_info VALUES (200000004,'Sophie','Byrne',20,'Galway','Irish','Female','Art and music',0);
INSERT INTO personal_info VALUES (200000005,'Noah','Walsh',24,'Tipperary','Irish','Male','Fitness and food',0);
INSERT INTO personal_info VALUES (200000006,'Ava','Doyle',21,'Kerry','Irish','Female','Photography & nature',0);
INSERT INTO personal_info VALUES (200000007,'Jack','OBrien',23,'Clare','Irish','Male','Movies & gaming',0);
INSERT INTO personal_info VALUES (200000008,'Mia','Fitz',22,'Waterford','Irish','Female','Dance & fashion',0);
INSERT INTO personal_info VALUES (200000009,'Leo','Quinn',25,'Meath','Irish','Male','Reading & writing',0);
INSERT INTO personal_info VALUES (200000010,'Admin','User',30,'Limerick','Irish','Other','System administrator',1);


--3. academic info
INSERT INTO academic_info VALUES (200000001,'Computer Science',3);
INSERT INTO academic_info VALUES (200000002,'Business',2);
INSERT INTO academic_info VALUES (200000003,'Engineering',3);
INSERT INTO academic_info VALUES (200000004,'Arts',1);
INSERT INTO academic_info VALUES (200000005,'Sports Science',4);
INSERT INTO academic_info VALUES (200000006,'Biology',2);
INSERT INTO academic_info VALUES (200000007,'Law',3);
INSERT INTO academic_info VALUES (200000008,'Design',2);
INSERT INTO academic_info VALUES (200000009,'English',4);
INSERT INTO academic_info VALUES (200000010,'Admin',5);

--4. interests
INSERT INTO interests VALUES (200000001,'No',1,'No',1,'Normal',1,'Extrovert',1,'Straight',1,'Sports','Gaming','Fitness','Movies','Technology'),
INSERT INTO interests VALUES (200000002,'Yes',1,'No',1,'Vegan',1,'Introvert',0,'Bisexual',1,'Travel','Cooking','Music','Art','Photography'),
INSERT INTO interests VALUES (200000003,'No',0,'No',1,'Normal',1,'Ambivert',1,'Straight',0,'Technology','Gaming','Reading','Movies','Writing'),
INSERT INTO interests VALUES (200000004,'Socially',1,'No',1,'Vegetarian',1,'Extrovert',1,'Lesbian',1,'Art','Music','Dance','Fashion','Photography'),
INSERT INTO interests VALUES (200000005,'No',1,'Yes',0,'Normal',1,'Extrovert',1,'Straight',1,'Fitness','Sports','Cooking','Nature','Travel'),
INSERT INTO interests VALUES (200000006,'Yes',0,'No',1,'Pescatarian',1,'Introvert',1,'Bisexual',1,'Photography','Nature','Travel','Reading','Art'),
INSERT INTO interests VALUES (200000007,'No',1,'No',1,'Normal',1,'Extrovert',0,'Straight',1,'Movies','Gaming','Sports','Technology','Music'),
INSERT INTO interests VALUES (200000008,'Socially',1,'No',0,'Normal',1,'Extrovert',1,'Pansexual',1,'Dance','Fashion','Music','Travel','Art'),
INSERT INTO interests VALUES (200000009,'No',1,'No',1,'Normal',0,'Introvert',1,'Asexual',1,'Reading','Writing','Technology','Movies','Art'),
INSERT INTO interests VALUES (200000010,'No',0,'No',0,'Normal',0,'Ambivert',0,'Other',0,'Technology','Reading','Writing','Movies','Art');

--5. Images
INSERT INTO images VALUES (200000001,'p1.jpg','1.jpg','2.jpg','3.jpg',NULL,NULL,3);
INSERT INTO images VALUES (200000002,'p2.jpg','1.jpg','2.jpg',NULL,NULL,NULL,2);
INSERT INTO images VALUES (200000003,'p3.jpg','1.jpg',NULL,NULL,NULL,NULL,1);
INSERT INTO images VALUES (200000004,'p4.jpg','1.jpg','2.jpg','3.jpg','4.jpg',NULL,4);
INSERT INTO images VALUES (200000005,'p5.jpg','1.jpg',NULL,NULL,NULL,NULL,1);
INSERT INTO images VALUES (200000006,'p6.jpg','1.jpg','2.jpg',NULL,NULL,NULL,2);
INSERT INTO images VALUES (200000007,'p7.jpg','1.jpg','2.jpg','3.jpg',NULL,NULL,3);
INSERT INTO images VALUES (200000008,'p8.jpg','1.jpg',NULL,NULL,NULL,NULL,1);
INSERT INTO images VALUES (200000009,'p9.jpg','1.jpg','2.jpg',NULL,NULL,NULL,2);
INSERT INTO images VALUES (200000010,'admin.jpg',NULL,NULL,NULL,NULL,NULL,0);


--6. Blocked
INSERT INTO blocked VALUES (200000001,200000002);
INSERT INTO blocked VALUES (200000003,200000004);
INSERT INTO blocked VALUES (200000005,200000006);
INSERT INTO blocked VALUES (200000007,200000008);
INSERT INTO blocked VALUES (200000009,200000001);


--7.  Reports
INSERT INTO reports VALUES (1,200000001,200000002,CURRENT_TIMESTAMP,'Spam messages','Spam');
INSERT INTO reports VALUES (2,200000003,200000004,CURRENT_TIMESTAMP,'Offensive language','Abuse');
INSERT INTO reports VALUES (3,200000005,200000006,CURRENT_TIMESTAMP,'Fake profile','Fake');
INSERT INTO reports VALUES (4,200000007,200000008,CURRENT_TIMESTAMP,'Harassment','Abuse');
INSERT INTO reports VALUES (5,200000009,200000001,CURRENT_TIMESTAMP,'Inappropriate content','Content');
INSERT INTO reports VALUES (6,200000004,200000002,CURRENT_TIMESTAMP,'Spam messages','Spam');
INSERT INTO reports VALUES (7,200000009,200000006,CURRENT_TIMESTAMP,'Spamming','Spam');

--8. Offense
INSERT INTO offense VALUES (200000002,1,1,0,1,CURRENT_TIMESTAMP,NULL);
INSERT INTO offense VALUES (200000004,1,2,0,1,CURRENT_TIMESTAMP,NULL);
INSERT INTO offense VALUES (200000006,0,1,0,1,CURRENT_TIMESTAMP,NULL);
INSERT INTO offense VALUES (200000008,1,3,1,1,CURRENT_TIMESTAMP,7);
INSERT INTO offense VALUES (200000001,0,0,0,0,CURRENT_TIMESTAMP,NULL);


--9. Messages
INSERT INTO messages (from_user_id,to_user_id,body) VALUES (200000001,200000002,'Hey!');
INSERT INTO messages (from_user_id,to_user_id,body) VALUES (200000002,200000001,'Hi!');
INSERT INTO messages (from_user_id,to_user_id,body) VALUES (200000003,200000004,'Hello!');
INSERT INTO messages (from_user_id,to_user_id,body) VALUES (200000004,200000003,'Hey there!');


