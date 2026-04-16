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

function fail(string $msg, $loc) {
    header('Location: ' . $loc . '?error=' . urlencode($msg));
    exit;
}

// We must define the target directory: 
$target_dir = "./user/".$creds["username"]."/";
$store_dir = "/".$creds["username"]."/";


// 1. FORM CREDENTIALS
if(isset($_POST["submit_cred"])) {
    // a. Check that username is changed
    if ($_POST["username"] != $creds["username"]) {
        // check username uniqueness
        $username = $_POST["username"];
        
        $stmt_sel = $conn->prepare("SELECT user_id FROM credentials WHERE username = ? AND user_id != ?");
        $stmt_sel->bind_param("si", $username, $user_id);
        $stmt_sel->execute();
        $result = $stmt_sel->get_result();
        $exist = $result->fetch_assoc();
        if ($exist) { // username taken, cannot change
            fail("Username already taken!", $loc);

        } else {
            $stmt = $conn->prepare("UPDATE credentials SET username = ? WHERE user_id = ?;");
            $stmt->bind_param("si", $username, $user_id);
            $stmt->execute();

            // in addition, we have to rename their resources folder
            $new_dir = "./user/" . $username . "/";
            if (!rename($target_dir, $new_dir)) {
                die("Folder rename failed");
            }
        }
    }


    // b. password:
    if ($_POST["password"] != $creds["password"] &&
            $_POST["password"] == $_POST["password2"]) {
        // if new and both are equal:
        $stmt = $conn->prepare("UPDATE credentials SET password = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("s", $_POST["password"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    } elseif ($_POST["password"] != $creds["password"] &&
            $_POST["password"] != $_POST["password2"]) {
        fail("Different passwords", $loc);
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
    if ($_POST["c_year"] != $uni["c_year"]) {
        $stmt = $conn->prepare("UPDATE academic_info SET c_year = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("i", $_POST["c_year"]);
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
// We must also define the sql logic to store the image properly:
// SELECT we have from user_queries or user_view_queries
// UPDATE must be prepared inside the loop, because column name changes each time

// 5. Delete images: unlink
for ($i=1; $i<=5; $i++) {
    if (isset($_POST["rmv{$i}"])){
        $pic = "pic_" . $i;
        // check if there was a value in that position:
        if ($images[$pic] == null) { // if null, do nothing
            continue;
        } else {
            $num = $images["pic_num"] - 1; 
            // get image path; images stored as: /<username>/<filename>.<extension>
            $file_name = $target_dir . $images[$pic];
            unlink($file_name);
            // update database:
            $null = null;
            $stmt_img = $conn->prepare("UPDATE images SET {$pic} = ?, pic_num = ? WHERE user_id = ?");
            $stmt_img->bind_param("sii", $null, $num, $user_id);
            if (!$stmt_img->execute()) {
                die("SELECT failed: " . $stmt_img->error);
            }
        }
    }
}

//
// 6. Image upload
// We use $_FILE variable, where the file is store at submit
// CODE EXTRACTED AND ADAPTED FROM PAGES:
// https://www.educative.io/answers/what-is-php-files-constant
// https://www.w3schools.com/php/php_file_upload.asp
//

function readyForUpload($pic, $target_file) {
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
    return $uploadOk;
}

if (isset($_POST["submit_img"])) {
    // a. TODO: PROFILE PIC
    $pic = "profile_pic";
    if ((isset($_FILES[$pic]) && $_FILES[$pic]["error"] != 4)) {
        $target_file = $target_dir . basename($_FILES[$pic]["name"]);
        $store_name = basename($_FILES[$pic]["name"]);
        
        $uploadOk = readyForUpload($pic, $target_file);
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES[$pic]["tmp_name"], $target_file)) {
                // Success! Now we have to update the database:
                $stmt_img = $conn->prepare("UPDATE images SET profile_pic = ? WHERE user_id = ?");
                $stmt_img->bind_param("si", $store_name, $user_id);
                if (!$stmt_img->execute()) {
                    die("SELECT failed: " . $stmt_img->error);
                }
                //echo "File uploaded!<br>";
            }
        }
    }
    
    // b. other pics
    $num = $images["pic_num"];
    for ($i=1; $i<=5; $i++){
        $pic = "pic_".$i;

        if (!isset($_FILES[$pic]) || $_FILES[$pic]["error"] == 4) {
            //echo "no file uploaded";
            continue; // file not set: skip
        }
        $target_file = $target_dir . basename($_FILES[$pic]["name"]);
        $store_name = basename($_FILES[$pic]["name"]);
        
        $uploadOk = readyForUpload($pic, $target_file);

        // Check uploadOk: if 0, there was an error
        if ($uploadOk == 0) {
            //echo "Sorry, file was not uploaded<br>";
        }
        else { // attempt to move file to folder
            if (move_uploaded_file($_FILES[$pic]["tmp_name"], $target_file)) {
                // Success! Now we have to update the database:
                // a) Check that the file was originally null or not:
                if ($images[$pic] == null) { // if originally null, +1 to pic_num
                    $num++;
                }

                // b) update databse
                $stmt_img = $conn->prepare("UPDATE images SET {$pic} = ?, pic_num = ? WHERE user_id = ?");
                $stmt_img->bind_param("sii", $store_name, $num, $user_id);
                if (!$stmt_img->execute()) {
                    die("SELECT failed: " . $stmt_img->error);
                }
                echo "File uploaded!<br>";
            } else echo "Error uploading file $i<br>";
        }
    }
}

// redirect back to settings
header("Location: {$loc}");
exit;
