<?php // here we process the forms:
session_start();

// DEPENDING ON WHETHER WE COME FROM USER OR ADMIN_EDIT, WE USE USER_QUERIES OR USER_VIEW_QUERIES
// WE CHECK IF TARGET_ID HAS BEEN SET ON POST
if (isset($_POST["target_id"])) { // include user_view_queries
    include "user_view_queries.php";
    $user_id = $_POST["target_id"];
    $loc = "settings_admin.php";
} else {
    include "user_queries.php";
    $user_id = $_SESSION["user_id"];
    $loc = "settings.php";
}


// 1. FORM CREDENTIALS
if(isset($_POST["submit_cred"])) {
    
    // a. Check that username is changed
    // TODO: CHECK AGAINST EXISTING USERNAMES
    if ($_POST["username"] != $creds["username"]) {
        $stmt = $conn->prepare("UPDATE credentials SET username = ? WHERE user_id = ?;");
        $stmt->bind_param("si", $_POST["username"], $user_id);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }


    // b. password:
    if ($_POST["password"] != $creds["password"] &&
            $_POST["password"] == $_POST["password2"]) {
        // if new and both are equal:
        $stmt = $conn->prepare("UPDATE credentials SET password = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("s", $_POST["password"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
}


// 2. FORM PERSONAL INFO
if (isset($_POST["submit_pers"])) {
    $p_info = array("first_name", "last_name", "county", "nationality", "gender", "bio");
    
    foreach($p_info as $val) {
        if ($_POST[$val] != $pers_info[$val]) {
            $stmt = $conn->prepare("UPDATE personal_info SET {$val} = ? WHERE user_id = {$user_id};");
            $stmt->bind_param("s", $_POST[$val]);
            if ($stmt === false) echo "Something bad happened :( <br>";
            $stmt->execute();
        }
    }

    // Age (becase it's an int)
    if ($_POST["age"] != $pers_info["age"]) {
        $stmt = $conn->prepare("UPDATE personal_info SET age = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("i", $_POST["age"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
}


// 3. FORM ACADEMIC INFO
if (isset($_POST["submit_acad"])) {
    // a. course:
    if ($_POST["course"] != $uni["course"]) {
        $stmt = $conn->prepare("UPDATE academic_info SET course = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("s", $_POST["course"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
    
    // b. year
    if ($_POST["C_year"] != $uni["c_year"]) {
        $stmt = $conn->prepare("UPDATE academic_info SET c_year = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("i", $_POST["year"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
}


// 4. FORM INTERESTS
if (isset($_POST["submit_int"])) {
    $interests = array("drink", "smoke", "food_lifestyle", "personality", 
        "sexuality", "interest1", "interest2", "interest3", 
        "interest4", "interest5");

    foreach($interests as $val) {
        if ($_POST[$val] != $ints[$val]) {
            $stmt = $conn->prepare("UPDATE interests SET {$val} = ? WHERE user_id = {$user_id};");
            $stmt->bind_param("s", $_POST[$val]);
            if ($stmt === false) echo "Something bad happened :( <br>";
            $stmt->execute();
        }
    }

    $display = array("food_display", "personality_display", "sexuality_display");
    foreach($display as $val) {
        if (!isset($_POST[$val])) { // see if box is unchcked
            echo $val." unchecked<br>";
            // if unchecked: (it doesn't exist in post)
            $bit = 0; // bit set to 0
        } else $bit = 1; // bit set to 1
        // now update the set:
        if ($bit != $ints[$val]) { // update if different
            $stmt = $conn->prepare("UPDATE interests SET {$val} = ? WHERE user_id = {$user_id};");
            $stmt->bind_param("i", $bit);
            if ($stmt === false) echo "Something bad happened :( <br>";
            $stmt->execute();
        }
    }
}



// HERE WE HANDLE ALL IMAGE RELATED!
// We must define the target directory: 
$target_dir = "./user/".$_SESSION["username"]."/";
$store_dir = "/".$_SESSION["username"]."/";
// We must also define the sql logic to store the image properly:
// SELECT we have from user_queries or user_view_queries
// UPDATE must be prepared inside the loop, because column name changes each time

// 5. Delete images
for ($i=1; $i<=5; $i++) {
    if (isset($_POST["rmv{$i}"])){
        echo "hello";
    }
}

//
// 6. Image upload
// We use $_FILE variable, where the file is store at submit
// CODE EXTRACTED AND ADAPTED FROM PAGES:
// https://www.educative.io/answers/what-is-php-files-constant
// https://www.w3schools.com/php/php_file_upload.asp
//
// a. TODO: PROFILE PIC

// b. other pics
if (isset($_POST["submit_img"])) {
    for ($i=1; $i<=5; $i++){
        $pic = "pic_".$i;

        if (!isset($_FILES[$pic]) || $_FILES[$pic]["error"] == 4) {
            //echo "no file uploaded";
            continue; // file not set: skip
        }
        $target_file = $target_dir . basename($_FILES[$pic]["name"]);
        $store_name = $store_dir . basename($_FILES[$pic]["name"]);
        $uploadOk = 1;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // ..["tmp_name"] is the temporary file that holds the content
        $check = getimagesize($_FILES[$pic]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1; // file is an image
        } else {
            $uploadOk = 0;
            //echo "File is not an image!<br>"; // TODO TEMPORAL CODE 
        }

        // check if file already exists in folder:
        if (file_exists($target_file)) {
            //echo "File already exists<br>";
            $uploadOk = 0;
        }

        // Check file size: max of 500KB
        if ($_FILES[$pic]["size"] > 500000){
            //echo "File is too large<br>";
            $uploadOk = 0;
        }

        // Limit file type (already done in form, but it can be bypassed)
        // Second check for increased safety
        if($file_type != "jpg" && $file_type != "png" && 
                $file_type != "jpeg" && $file_type != "gif") {
            //echo "Wrong file extension<br>";
            $uploadOk = 0;
        }

        // Check uploadOk: if 0, there was an error
        if ($uploadOk == 0) {
            //echo "Sorry, file was not uploaded<br>";
        }
        else { // attempt to move file to folder
            if (move_uploaded_file($_FILES[$pic]["tmp_name"], $target_file)) {
                // Success! Now we have to update the database:
                // a) Check that the file was originally null or not:
                if ($images[$pic] == null) { // if originally null, +1 to pic_num
                    $num = $images["pic_num"] + 1;
                } else $num = $images["pic_num"];

                // b) update databse
                $stmt_img = $conn->prepare("UPDATE images SET {$pic} = ?, pic_num = ? WHERE user_id = ?");
                $stmt_img->bind_param("sii", $store_name, $num, $user_id);
                if (!$stmt_img->execute()) {
                    die("SELECT failed: " . $stmt_img->error);
                }
                //echo "File uploaded!<br>";
            } //else echo "Error uploading file<br>";
        }
    }
}

// redirect back to settings
header("Location: {$loc}");
exit;
