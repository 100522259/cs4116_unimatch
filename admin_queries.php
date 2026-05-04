<?php

include "connect_server.php";

// 1. select reported users
$sql = "SELECT C.user_id as id_reportee, C.username as u_reportee, 
K.user_id as id_reported, K.username as u_reported,
R.report_id, R.timestamp, R.report_msg, R.category
FROM
reports as R 
        INNER JOIN
credentials as C on R.user_id1 = C.user_id
        INNER JOIN
credentials as K on R.user_id2 = K.user_id;";

$res_reports = $conn->query($sql);


$sql = "SELECT * from (select username, user_id from credentials) as C 
        inner join 
        (select * from offense where offence_num != 0 or reported = 1 or blocked = 1) as O
        on C.user_id = O.user_id;";
$res_offense = $conn->query($sql);
$res_offense2 = $conn->query($sql);
// Columns we have: username, user_id, phone_warning, offence_num, blocked,
// reported, last modified, ban_time
    

$sql = "SELECT count(*)/2 as num_rel from 
        (select user_id1 as u1, user_id2 as u2 from matches where romantic=1) as A
        INNER JOIN
        (select user_id1 as u2, user_id2 as u1 from matches where romantic=1) as B
        ON A.u1 = B.u1 and A.u2 = B.u2;";
$res = $conn->query($sql);
$num_rel = $res->fetch_assoc();

$sql = "SELECT count(*)/2 as num_fr from 
        (select user_id1 as u1, user_id2 as u2 from matches where friendship=1) as A
        INNER JOIN
        (select user_id1 as u2, user_id2 as u1 from matches where friendship=1) as B
        ON A.u1 = B.u1 and A.u2 = B.u2;";
$res = $conn->query($sql);
$num_fr = $res->fetch_assoc();


$sql = "SELECT count(*) as f_pen FROM
        (select user_id1 as u1, user_id2 as u2 from matches where friendship=1) as A
        LEFT OUTER JOIN
        (select user_id1 as u2, user_id2 as u1, friendship from matches) as B
        ON A.u1 = B.u1 and A.u2 = B.u2
        WHERE B.u2 is null or B.friendship=0;";
$res = $conn->query($sql);
$f_pen = $res->fetch_assoc();

$sql = "SELECT count(*) as r_pen FROM
        (select user_id1 as u1, user_id2 as u2 from matches where romantic=1) as A
        LEFT OUTER JOIN
        (select user_id1 as u2, user_id2 as u1, romantic from matches) as B
        ON A.u1 = B.u1 and A.u2 = B.u2
        WHERE B.u2 is null or B.romantic=0;";
$res = $conn->query($sql);
$r_pen = $res->fetch_assoc();

$sql = "SELECT count(*)/2 as num_both FROM
        (select user_id1 as u1, user_id2 as u2 from matches where romantic=1 and friendship=1) as A
        INNER JOIN
        (select user_id1 as b2, user_id2 as b1 from matches where romantic=1 and friendship=1) as B
        ON A.u1 = B.b1 and A.u2 = B.b2;";
$res = $conn->query($sql);
$num_both = $res->fetch_assoc();
