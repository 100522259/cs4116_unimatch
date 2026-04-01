<?php
    //echo "<h1>Trying to query</h1>";
    $servername = "sql108.infinityfree.com";
    $username = "if0_41207740";
    $password = "hpQ2aHXoodn";

    $dbname = "if0_41207740_unimatch_db";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: ".$conn->connect_error);
    }

    //echo "Connected successfully<br>";

    // 1. select reported users
    $sql = "select * from (select username, user_id from credentials)
            as K inner join (select username as username1, report_id, user_id2, 
            timestamp, report_msg, category from credentials as C 
            inner join (select * from reports) as R on C.user_id = R.user_id1) 
            as U on K.user_id = U.user_id2;";
    $res_reports = $conn->query($sql);
    // Columns we have: username (reportee), user_id (reported), username1 (reported),
    // report_id, user_id2 (reportee), timestamp, report_msg, category

    $sql = "select * from (select username, user_id from credentials) as C 
            inner join (select * from offense) as O on C.user_id = O.user_id;";
    $res_offense = $conn->query($sql);
    // Columns we have: username, user_id, phone_warning, offence_num, blocked,
    // reported, last modified, ban_time


