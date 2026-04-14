<?php

include "connect_server.php";

// 1. select reported users
$sql = "select C.user_id as id_reportee, C.username as u_reportee, 
K.user_id as id_reported, K.username as u_reported,
R.report_id, R.timestamp, R.report_msg, R.category
FROM
reports as R 
        INNER JOIN
credentials as C on R.user_id1 = C.user_id
        INNER JOIN
credentials as K on R.user_id2 = K.user_id;";

$res_reports = $conn->query($sql);


$sql = "select * from (select username, user_id from credentials) as C 
        inner join (select * from offense) as O on C.user_id = O.user_id;";
$res_offense = $conn->query($sql);
$res_offense2 = $conn->query($sql);
// Columns we have: username, user_id, phone_warning, offence_num, blocked,
// reported, last modified, ban_time
    
