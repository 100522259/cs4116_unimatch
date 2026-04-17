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
        inner join (select * from offense) as O on C.user_id = O.user_id;";
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

$sql = "SELECT count(*) as num_pending from relationship where r_status=false or f_status=false";
$res = $conn->query($sql);
$num_pending = $res->fetch_assoc();

$sql = "SELECT count(*) as num_both from relationship where
    romantic=true and r_status=true and friendship=true and f_status=true";
$res = $conn->query($sql);
$num_both = $res->fetch_assoc();