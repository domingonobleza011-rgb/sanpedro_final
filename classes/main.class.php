<?php 

class BMISClass {

//------------------------------------------ DATABASE CONNECTION ----------------------------------------------------
    
    protected $server = "mysql:host=localhost;dbname=bmis";
    protected $user = "root";
    protected $pass = "";
    protected $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
    protected $con;



    public function show_404()
    {
        http_response_code(404);
        echo "Page is currently unavailable";
        die;
    }

    public function openConn() {
        try {
            $this->con = new PDO($this->server, $this->user, $this->pass, $this->options);
            return $this->con;
        }

        catch(PDOException $e) {
            echo "Datbase Connection Error! ", $e->getMessage();
        }
    }

    //eto yung nag c close ng connection ng db
    public function closeConn() {
        $this->con = null;
    }


    //------------------------------------------ AUTHENTICATION & SESSION HANDLING --------------------------------------------
        //authentication function para sa sa tatlong type ng accounts
   public function login() {
    if(isset($_POST['login'])) {
        // Change $email to a generic identity variable
        $identity = $_POST['login_identity']; 
        $password_input = $_POST['password']; 
        
        $connection = $this->openConn();

        // 1. Check ADMIN Table (Usually admins only use email, but we'll update it for consistency)
        $stmt = $connection->prepare("SELECT * FROM tbl_admin WHERE email = ? OR phone_number = ?");
        $stmt->execute([$identity, $identity]);
        $user = $stmt->fetch();

        if($user && password_verify($password_input, $user['password'])) {
            if($user['role'] == 'Admin' || $user['role'] == 'administrator') {
                $this->set_userdata($user);
                header('Location: admn_dashboard.php');
                exit(); 
            }
        }

        // 2. Check USER Table (Staff)
        $stmt = $connection->prepare("SELECT * FROM tbl_user WHERE email = ? OR phone_number = ?");
        $stmt->execute([$identity, $identity]);
        $user = $stmt->fetch();

        // Added password_verify here as it was missing in your original snippet for the user table
        if($user && password_verify($password_input, $user['password'])) {
            if($user['role'] == 'user') {
                $this->set_userdata($user);
                echo "<script>window.location.href='staff_dashboard.php';</script>";
                exit(); 
            }
        }

        // 3. Check RESIDENT Table
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE email = ? OR phone_number = ?");
        $stmt->execute([$identity, $identity]);
        $user = $stmt->fetch();

        if($user && password_verify($password_input, $user['password'])) {
            if($user['role'] == 'resident') {
                $this->set_userdata($user);
                header('Location: resident_homepage.php');
                exit();
            }
        }

        // Error handling
        $message = "Invalid Credentials. Please check your Email/Phone and Password.";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
}

    //eto yung function na mag e end ng session tas i l logout ka 
    public function logout(){
        if(!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['userdata'] = null;
        unset($_SESSION['userdata']); 
        
    }

    // etong method na get_userdata() kukuha ng session mo na 'userdata' mo na i identify sino yung naka login sa site 
    public function get_userdata(){
    
        //i ch check niya ulit kung naka start na ba session o hindi, kapag hindi pa ay i s start niya para surebol
        if(!isset($_SESSION)) {
            session_start();
        }

        return $_SESSION['userdata'];

        //eto naman i ch check niya kung yung 'userdata' naka set na ba sa session natin
        if(!isset($_SESSION['userdata'])) {
            return $_SESSION['userdata'];
        } 

        else {
            return null;
        }
    }

    //eto yung condition na mag s set userdata na gagamiting pagkakakilala sayo sa buong session kapag nag login in ka
    public function set_userdata($array) {

        //i ch check nito kung naka set naba yung session, kapag hindi pa naka set i r run niya yung session_start();
        if(!isset($_SESSION)) {
            session_start();
        }

        //eto si userdata yung mag s set ng name mo tsaka role/access habang ikaw ay nag b browse at gumagamit ng store management
        $_SESSION['userdata'] = array(
            "id_admin" => $array['id_admin'],
            "id_resident" => $array['id_resident'],
            "id_user" => $array['id_user'],
            "emailadd" => $array['email'],
            "password" => $array['password'],
            //"fullname" => $array['lname']. " ".$array['fname']. " ".$array['mi'],
            "surname" => $array['lname'],
            "firstname" => $array['fname'],
            "mname" => $array['mi'],
            "age" => $array['age'],
            "sex" => $array['sex'],
            "status" => $array['status'],
            "address" => $array['address'],
            "contact" => $array['contact'],
            "bdate" => $array['bdate'],
            "bplace" => $array['bplace'],
            "nationality" => $array['nationality'],
            "family_role" => $array['family_role'],
            "role" => $array['role'],
            "houseno" => $array['houseno'],
            "street" => $array['street'],
            "brgy" => $array['brgy'],
            "municipal" => $array['municipal']
        );
        return $_SESSION['userdata'];
    }



 //----------------------------------------------------- ADMIN CRUD ---------------------------------------------------------
    public function create_admin() {
    if(isset($_POST['add_admin'])) {
        $email = $_POST['email'];
        
        // CHANGE THIS LINE from md5 to password_hash
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
        
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $role = $_POST['role'];

        if ($this->check_admin($email) == 0 ) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_admin (`email`,`password`,`lname`,`fname`, `mi`, `role` ) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $password, $lname, $fname, $mi, $role]);
            
            echo "<script>alert('Administrator account added');</script>";
        } else {
            echo "<script>alert('Account already exists');</script>";
        }
    }
}

    public function get_single_admin($id_admin){

        $id_admin = $_GET['id_admin'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_admin where id_admin = ?");
        $stmt->execute([$id_admin]);
        $admin = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $admin;
        }
        else{
            return false;
        }
    }

  public function admin_changepass() {
    if(isset($_POST['admin_changepass'])) {
        
        // 1. Capture the ID and password inputs
        $id_admin = $_POST['id_admin'] ?? null;
        $oldpassword = $_POST['oldpassword'] ?? '';
        $newpassword = $_POST['newpassword'] ?? '';
        $checkpassword = $_POST['checkpassword'] ?? '';

        if (empty($id_admin)) {
            echo "<script>alert('Error: Admin ID is missing. Please re-login.');</script>";
            return;
        }

        $connection = $this->openConn();
        
        // 2. Fetch the current hashed password from the database
        $stmt = $connection->prepare("SELECT `password` FROM tbl_admin WHERE id_admin = ?");
        $stmt->execute([$id_admin]);
        $result = $stmt->fetch();

        if (!$result) {
            echo "<script>alert('Admin user not found.');</script>";
            return;
        }

        // 3. Verify Old Password (checks input against the Bcrypt hash)
        if (!password_verify($oldpassword, $result['password'])) { 
            echo "<script>alert('Old Password is Incorrect');</script>";
        } 
        // 4. Ensure New Password and Confirm Password match
        elseif ($newpassword !== $checkpassword) {
            echo "<script>alert('New Passwords do not match');</script>";
        } 
        // 5. Ensure the new password isn't empty
        elseif (empty($newpassword)) {
            echo "<script>alert('New password cannot be empty');</script>";
        }
        else {
            // 6. Success: Hash the NEW password and update
            $hashed_new = password_hash($newpassword, PASSWORD_DEFAULT);
            $stmt = $connection->prepare("UPDATE tbl_admin SET password = ? WHERE id_admin = ?");
            $stmt->execute([$hashed_new, $id_admin]);
            
            echo "<script type='text/javascript'>
                alert('Password Updated Successfully'); 
                window.location.href='admn_dashboard.php';
            </script>";
        }
    }
}


 //  ----------------------------------------------- ANNOUNCEMENT CRUD ---------------------------------------------------------

public function create_announcement() {
    if(isset($_POST['create_announce'])) {
        // We don't usually manually set id_announcement if it's Auto-Increment
        $event = $_POST['event'];
        $start_date = $_POST['start_date'];
        $addedby = $_POST['addedby'];
        $image_name = null; // Default if no image is uploaded

        // Check if an image was actually uploaded
        if(isset($_FILES['announcement_img']) && $_FILES['announcement_img']['error'] == 0) {
            $upload_dir = "uploads/";
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Generate a unique name to prevent overwriting files with the same name
            $file_ext = pathinfo($_FILES['announcement_img']['name'], PATHINFO_EXTENSION);
            $image_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $upload_dir . $image_name;

            // Move the file from temporary storage to your uploads folder
            move_uploaded_file($_FILES['announcement_img']['tmp_name'], $target_file);
        }

        $connection = $this->openConn();
        // Added 'image' column to your INSERT statement
        $stmt = $connection->prepare("INSERT INTO tbl_announcement (`event`, `start_date`, `addedby`, `image`, `status`) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$event, $start_date, $addedby, $image_name]);

        echo "<script type='text/javascript'>alert('Announcement Added');</script>";
        header('refresh:0');
    }
}
   public function view_announcement(){
    $connection = $this->openConn();
    // Adding DESC (Descending) makes the newest items appear at the top
    $stmt = $connection->prepare("SELECT * from tbl_announcement ORDER BY id_announcement DESC");
    $stmt->execute();
    $view = $stmt->fetchAll();
    return $view;
}

    public function update_announcement() {
        if (isset($_POST['update_announce'])) {
            $id_announcement = $_GET['id_announcement'];
            $event = $_POST['event'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $addedby = $_POST['addedby'];

            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_announcement SET event =?, start_date =?, 
            end_date = ?, addedby =? WHERE id_announcement = ?");
            $stmt->execute([ $event, $start_date, $end_date, $addedby, $id_announcement]);
               
            $message2 = "Announcement Updated";
            echo "<script type='text/javascript'>alert('$message2');</script>";
             header("refresh: 0");
        }

        else {
        }
    }
public function admin_delete_announcement(){
    if(isset($_POST['delete_announcement'])) {
        $id_announcement = $_POST['id_announcement'];
        $connection = $this->openConn(); 

        // 1. Get the filename first
        $stmt = $connection->prepare("SELECT image FROM tbl_announcement WHERE id_announcement = ?");
        $stmt->execute([$id_announcement]);
        $row = $stmt->fetch();

        // 2. Delete the actual file from the folder if it exists
        if($row && !empty($row['image'])) {
            $file_path = "uploads/" . $row['image'];
            if(file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // 3. Delete from database
        $stmt = $connection->prepare("DELETE FROM tbl_announcement WHERE id_announcement = ?");
        $stmt->execute([$id_announcement]);

        echo "<script>alert('Announcement Deleted'); window.location.href='".basename($_SERVER['PHP_SELF'])."';</script>";
        exit();
    }
}
public function delete_announcement($user_id){
    if(isset($_POST['delete_announcement'])) {
        $id_announcement = $_POST['id_announcement'];
        
        // Record the hide action for this specific user
        $this->hide_announcement($user_id, $id_announcement);

        // Get the current page name dynamically
        $current_page = basename($_SERVER['PHP_SELF']);

        echo "<script>
                alert('Announcement Deleted'); 
                window.location.href='$current_page';
              </script>";
        exit();
    }
}
public function view_active_announcements($user_id){
    $connection = $this->openConn();
    
    // This query selects all announcements EXCEPT those this specific user has hidden
    $sql = "SELECT * FROM tbl_announcement 
            WHERE id_announcement NOT IN (
                SELECT announcement_id FROM tbl_hidden_announcements WHERE user_id = ?
            ) AND status = 'active' 
            ORDER BY start_date DESC";
            
    $stmt = $connection->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

    public function count_announcement() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_announcement");
        $stmt->execute();
        $ancount = $stmt->fetchColumn();
        return $ancount;
    }

   public function hide_announcement($user_id, $announcement_id) {
    $connection = $this->openConn();
    $stmt = $connection->prepare("INSERT INTO tbl_hidden_announcements (user_id, announcement_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $announcement_id]);
}
public function sendMessage($id_resident, $message_text) {
    try {
        $sql = "INSERT INTO resident_messages (id_resident, message_text, date_sent) 
                VALUES (:id, :msg, NOW())";
        
        // We use openConn() because that is what you defined at the top of the class
        $stmt = $this->openConn()->prepare($sql); 
        
        $stmt->bindParam(':id', $id_resident);
        $stmt->bindParam(':msg', $message_text);
        
        return $stmt->execute(); 
    } catch (PDOException $e) {
        // This helps you debug if the 'resident_messages' table is missing
        error_log("Message Error: " . $e->getMessage());
        return false;
    } finally {
        // Good practice: close the connection after the work is done
        $this->closeConn();
    }
} 

public function getResidentMessages($id_resident) {
    try {
        $sql = "SELECT * FROM resident_messages WHERE id_resident = ? ORDER BY date_sent DESC";
        $stmt = $this->openConn()->prepare($sql);
        $stmt->execute([$id_resident]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    } finally {
        $this->closeConn();
    }
}
// Inside classes/main.class.php
public function deleteResidentMessage($id_msg) {
    try {
        $connection = $this->openConn();
        // Use the exact column name you found in phpMyAdmin here
        $sql = "DELETE FROM resident_messages WHERE id_message = ?"; 
        $stmt = $connection->prepare($sql);
        $result = $stmt->execute([$id_msg]);
        
        return $result;
    } catch (PDOException $e) {
        // This will print the EXACT database error to your screen
        die("Database Error: " . $e->getMessage()); 
    }
}
public function sendMessageToAdmin($id_resident, $message_text) {
    try {
        $connection = $this->openConn();
        $sql = "INSERT INTO admin_messages (id_resident, message_text, date_sent, status) 
                VALUES (?, ?, NOW(), 'unread')";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$id_resident, $message_text]);
    } catch (PDOException $e) {
        // THIS LINE WILL SHOW YOU THE EXACT PROBLEM
        die("Database Error: " . $e->getMessage()); 
    }
}
public function viewMessages() {
    try {
        $connection = $this->openConn();
        // Updated to common column names for this system: res_fname and res_lname
        $sql = "SELECT m.*, r.fname, r.lname 
                FROM admin_messages m 
                JOIN tbl_resident r ON m.id_resident = r.id_resident 
                ORDER BY m.date_sent DESC";
        
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
public function deleteMessage($id_admin_msg) {
    try {
        $connection = $this->openConn();
        $sql = "DELETE FROM admin_messages WHERE id_admin_msg = ?";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$id_admin_msg]);
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
    public function uploadValidID($id_resident, $file_name, $original_name, $file_type, $message_note = '') {
    try {
        $connection = $this->openConn();
        $sql = "INSERT INTO tbl_id_uploads (id_resident, file_name, original_name, file_type, message_note, upload_date, status)
                VALUES (?, ?, ?, ?, ?, NOW(), 'pending')";
        $stmt = $connection->prepare($sql);
        return $stmt->execute([$id_resident, $file_name, $original_name, $file_type, $message_note]);
    } catch (PDOException $e) {
        error_log("uploadValidID Error: " . $e->getMessage());
        return false;
    }
}
public function delete_upload_record($id_upload) {
    try {
        $con = $this->openConn();
        
        // Optional: Get the filename first to delete it from the folder
        $stmt = $con->prepare("SELECT file_name FROM tbl_id_uploads WHERE id_upload = ?");
        $stmt->execute([$id_upload]);
        $file = $stmt->fetch();
        
        if ($file) {
            $path = "uploads/valid_ids/" . $file['file_name'];
            if (file_exists($path)) {
                unlink($path); // This deletes the physical file
            }
        }

        // Delete the record from the database
        $sql = "DELETE FROM tbl_id_uploads WHERE id_upload = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$id_upload]);
        
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Get all pending ID uploads (for admin panel)
 */
public function getPendingIDUploads() {
    try {
        $connection = $this->openConn();
        $sql = "SELECT u.*, r.fname, r.lname, r.email, r.phone_number
                FROM tbl_id_uploads u
                JOIN tbl_resident r ON u.id_resident = r.id_resident
                ORDER BY u.upload_date DESC";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getPendingIDUploads Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all ID uploads for a specific resident
 */
public function getResidentIDUploads($id_resident) {
    try {
        $connection = $this->openConn();
        $sql = "SELECT * FROM tbl_id_uploads WHERE id_resident = ? ORDER BY upload_date DESC";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id_resident]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Admin approves a resident — sets is_verified = 1 and marks the upload as approved
 */
public function approveResidentVerification($id_resident, $id_upload, $admin_name) {
    try {
        $connection = $this->openConn();

        // 1. Update the resident's verified status
        $sql1 = "UPDATE tbl_resident SET is_verified = 1, verified_at = NOW(), verified_by = ? WHERE id_resident = ?";
        $stmt1 = $connection->prepare($sql1);
        $stmt1->execute([$admin_name, $id_resident]);

        // 2. Mark this upload as approved
        $sql2 = "UPDATE tbl_id_uploads SET status = 'approved' WHERE id_upload = ?";
        $stmt2 = $connection->prepare($sql2);
        $stmt2->execute([$id_upload]);

        // 3. Notify the resident via their messages
        $notice = "✅ Your account has been verified! You can now request barangay certificates and other services.";
        $sql3 = "INSERT INTO resident_messages (id_resident, message_text, date_sent) VALUES (?, ?, NOW())";
        $stmt3 = $connection->prepare($sql3);
        $stmt3->execute([$id_resident, $notice]);

        return true;
    } catch (PDOException $e) {
        error_log("approveResidentVerification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Admin rejects a resident's ID upload
 */
public function rejectResidentVerification($id_resident, $id_upload, $admin_name, $reason = '') {
    try {
        $connection = $this->openConn();

        // Mark the upload as rejected
        $sql1 = "UPDATE tbl_id_uploads SET status = 'rejected' WHERE id_upload = ?";
        $stmt1 = $connection->prepare($sql1);
        $stmt1->execute([$id_upload]);

        // Notify the resident
        $notice = "❌ Your valid ID submission was rejected." . ($reason ? " Reason: " . $reason : "") . " Please upload a clearer or valid government-issued ID.";
        $sql2 = "INSERT INTO resident_messages (id_resident, message_text, date_sent) VALUES (?, ?, NOW())";
        $stmt2 = $connection->prepare($sql2);
        $stmt2->execute([$id_resident, $notice]);

        return true;
    } catch (PDOException $e) {
        error_log("rejectResidentVerification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a resident is verified
 */
public function isResidentVerified($id_resident) {
    try {
        $connection = $this->openConn();
        $sql = "SELECT is_verified FROM tbl_resident WHERE id_resident = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id_resident]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['is_verified'] == 1;
    } catch (PDOException $e) {
        return false;
    }
}
    //------------------------------------------ Certificate of Residency CRUD -----------------------------------------------
    public function get_single_certofres($id_resident){

        $id_resident = $_GET['id_resident'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_rescert where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $resident;
        }
        else{
            return false;
        }
    }

    public function create_certofres() {

        if(isset($_POST['create_certofres'])) {
            $id_rescert = $_POST['id_rescert'];
            $id_resident = $_POST['id_resident'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $age = $_POST['age'];
            $nationality = $_POST['nationality']; 
            $houseno = $_POST['houseno'];
            $street = $_POST['street'];
            $brgy = $_POST['brgy'];
            $municipal = $_POST['municipal'];
            $date = $_POST['date'];
            $purpose = $_POST['purpose'];
            


            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_rescert (`id_rescert`, `id_resident`, `lname`, `fname`, `mi`,
             `age`,`nationality`, `houseno`, `street`,`brgy`, `municipal`, `date`,`purpose`)
            VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)");

            $stmt->execute([$id_rescert, $id_resident, $lname, $fname, $mi,  $age, $nationality, $houseno,  $street, $brgy,$municipal, $date,$purpose]);

            $message2 = "Application Applied, you will receive our text message for further details";
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("refresh: 0");
        }
        
        
    }

    public function view_certofres(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_rescert");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }


    public function delete_certofres(){
        $id_rescert = $_POST['id_rescert'];

        if(isset($_POST['delete_certofres'])) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_rescert where id_rescert = ?");
            $stmt->execute([$id_rescert]);

            header("Refresh:0");
        }
    }

     //------------------------------------------ CERT OF INIDIGENCY CRUD -----------------------------------------------

     public function create_certofindigency() {

        if(isset($_POST['create_certofindigency'])) {
            $id_indigency = $_POST['id_indigency'];
            $id_resident = $_POST['id_resident'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $nationality = $_POST['nationality']; 
            $houseno = $_POST['houseno'];
            $street = $_POST['street'];
            $brgy = $_POST['brgy'];
            $municipal = $_POST['municipal'];
            $purpose = $_POST['purpose'];
            $date = $_POST['date'];

            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_indigency (`id_indigency`, `id_resident`, `lname`, `fname`, `mi`,
             `nationality`, `houseno`, `street`,`brgy`, `municipal`,`purpose`, `date`)
            VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");

            $stmt->execute([$id_indigency, $id_resident, $lname, $fname, $mi,  $nationality, $houseno,  $street, $brgy, $municipal,$purpose, $date]);

            $message2 = "Application Applied, you will receive our text message for further details";
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("refresh: 0");
        }
        
        
    }


    

    public function view_certofindigency(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_indigency");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }


    public function delete_certofindigency(){
        $id_indigency = $_POST['id_indigency'];

        if(isset($_POST['delete_certofindigency'])) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_indigency where id_indigency = ?");
            $stmt->execute([$id_indigency]);

            header("Refresh:0");
        }
    }

    public function get_single_certofindigency($id_resident){

        $id_resident = $_GET['id_resident'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_indigency where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $resident;
        }
        else{
            return false;
        }
    }


     //------------------------------------------ BRGY CLEARANCE CRUD -----------------------------------------------

     public function create_brgyclearance() {

        if(isset($_POST['create_brgyclearance'])) {
            $id_clearance = $_POST['id_clearance'];
            $id_resident = $_POST['id_resident'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $purpose = $_POST['purpose'];
            $houseno = $_POST['houseno'];
            $street = $_POST['street'];
            $brgy = $_POST['brgy'];
            $municipal = $_POST['municipal'];
            $status = $_POST['status'];
            $age = $_POST['age'];
            
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_clearance (`id_clearance`, `id_resident`, `lname`, `fname`, `mi`,
             `purpose`, `houseno`, `street`,`brgy`, `municipal`, `status`, `age`)
            VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$id_clearance, $id_resident, $lname, $fname, $mi,  $purpose, 
            $houseno,  $street, $brgy,   $municipal, $status, $age]);

            $message2 = "Application Applied, you will receive our text message for further details";
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("refresh: 0");
        }
        
        
    }

    public function get_single_clearance($id_resident){

        $id_resident = $_GET['id_resident'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_clearance where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $resident;
        }
        else{
            return false;
        }
    }


    public function view_clearance(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_clearance");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }


    public function delete_clearance(){
        $id_clearance = $_POST['id_clearance'];

        if(isset($_POST['delete_clearance'])) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_clearance where id_clearance = ?");
            $stmt->execute([$id_clearance]);

            header("Refresh:0");
        }
    }

    





    
    //------------------------------------------ EXTRA FUNCTIONS ----------------------------------------------

    public function check_admin($email) {

        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_admin WHERE email = ?");
        $stmt->Execute([$email]);
        $total = $stmt->rowCount(); 

        return $total;
    }

    //eto yung function na mag bibigay restriction sa mga admin pages
    public function validate_admin(){
        $userdetails = $this->get_userdata();

        if (isset($userdetails)) {
            
            if($userdetails['role'] != "administrator") {
                $this->show_404();
            }

            else {
                return $userdetails;
            }
        }
    }

    public function validate_staff() {

        if(isset($userdetails)) {
            if($userdetails['role'] != "administrator" || $userdetails['role'] != "user") {
                $this->show_404();
            }

            else {
                return $userdetails;
            }
        }
    }















    //----------------------------------------- DOCUMENT PROCESSING FUNCTIONS -------------------------------------
    //-------------------------------------------------------------------------------------------------------------

    public function create_bspermit() {

        if(isset($_POST['create_bspermit'])) {
            $id_bspermit = $_POST['id_bspermit'];
            $id_resident = $_POST['id_resident'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $bsname = $_POST['bsname']; 
            $houseno = $_POST['houseno'];
            $street = $_POST['street'];
            $brgy = $_POST['brgy'];
            $municipal = $_POST['municipal'];
            $bsindustry = $_POST['bsindustry'];
            $aoe = $_POST['aoe'];


            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_bspermit (`id_bspermit`, `id_resident`, `lname`, `fname`, `mi`,
             `bsname`, `houseno`, `street`,`brgy`, `municipal`, `bsindustry`, `aoe`)
            VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$id_bspermit, $id_resident, $lname, $fname, $mi,  $bsname, $houseno,  $street, $brgy, $municipal, $bsindustry, $aoe]);

            $message2 = "Application Applied, you will receive our text message for further details";
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("refresh: 0");
        }
        
        
    }

    public function get_single_bspermit($id_resident){

        $id_resident = $_GET['id_resident'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_bspermit where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $resident;
        }
        else{
            return false;
        }
    }


    public function view_bspermit(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_bspermit");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }


    public function delete_bspermit(){
        $id_bspermit = $_POST['id_bspermit'];

        if(isset($_POST['delete_bspermit'])) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_bspermit where id_bspermit = ?");
            $stmt->execute([$id_bspermit]);

            header("Refresh:0");
        }
    }

    public function update_bspermit() {
        if (isset($_POST['update_bspermit'])) {
            $id_bspermit = $_GET['id_bspermit'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi'];
            $bsname = $_POST['bsname']; 
            $houseno = $_POST['houseno'];
            $street = $_POST['street'];
            $brgy = $_POST['brgy'];
            $municipal = $_POST['municipal'];
            $bsindustry = $_POST['bsindustry'];
            $aoe = $_POST['aoe'];


            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_bspermit SET lname = ?, fname = ?,
            mi = ?, bsname = ?, houseno = ?, street = ?, brgy = ?, municipal = ?,
            bsindustry = ?, aoe = ? WHERE id_bspermit = ?");
            $stmt->execute([$id_bspermit, $lname, $fname, $mi,  $bsname, $houseno,  $street, $brgy, $municipal, $bsindustry, $aoe]);
            
            $message2 = "Barangay Business Permit Data Updated";
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("refresh: 0");
        }
    }


public function get_single_youth($id_resident){

        $id_resident = $_GET['id_youth'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_youth where id_youth = ?");
        $stmt->execute([$id_youth]);
        $resident = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $youth;
        }
        else{
            return false;
        }
    }

   public function create_youth() {
    if(isset($_POST['create_youth'])) {
        // Remove $id_resident = $_POST['id_resident']; 
        
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $age = $_POST['age']; // Ensure this matches your date format
        $sex = $_POST['sex'];
        $civil_status = $_POST['civil_status']; 
        $contact_number = $_POST['contact_number'];
        $email_address = $_POST['email_address'];
        $educ_attain = $_POST['educ_attain'];
        $emp_status = $_POST['emp_status'];
        $skill_name = $_POST['skill_name'];

        $connection = $this->openConn();
        
        // Remove id_resident from both the column list AND the values list
        $stmt = $connection->prepare("INSERT INTO tbl_youth 
            (lname, fname, mi, age, sex, civil_status, contact_number, email_address, educ_attain, emp_status, skill_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Execute with 11 variables (matching the 11 placeholders above)
        $stmt->execute([$lname, $fname, $mi, $age, $sex, $civil_status, $contact_number, $email_address, $educ_attain, $emp_status, $skill_name]);

        $message2 = "Application Applied!";
        echo "<script type='text/javascript'>alert('$message2');</script>";
        header("refresh: 0");
    }
}

    public function view_youth(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_youth");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }


    public function delete_youth(){
        $id_youth = $_POST['id_youth'];

        if(isset($_POST['delete_youth'])) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_youth where id_youth = ?");
            $stmt->execute([$id_youth]);

            header("Refresh:0");
        }
    }
    

    public function create_brgyid() {

        if(isset($_POST['create_brgyid'])) {
            $id_brgyid = $_POST['id_brgyid'];
            $id_resident = $_POST['id_resident'];
            $lname = $_POST['lname'];
            $fname = $_POST['fname'];
            $mi = $_POST['mi']; 
            $houseno = $_POST['houseno'];
            $street = $_POST['street'];
            $brgy = $_POST['brgy'];
            $municipal = $_POST['municipal'];
            $bplace = $_POST['bplace'];
            $bdate = $_POST['bdate'];
            

            $inc_lname = $_POST['inc_lname']; 
            $inc_fname = $_POST['inc_fname'];
            $inc_mi = $_POST['inc_mi'];
            $inc_contact = $_POST['inc_contact'];
            $inc_houseno = $_POST['municipal'];
            $inc_street = $_POST['bplace'];
            $inc_brgy = $_POST['bdate'];
            $inc_municipal = $_FILES['res_photo'];

            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_brgyid (`id_brgyid`, `id_resident`, `lname`, `fname`, `mi`,
            `houseno`, `street`,`brgy`, `municipal`, `bplace`, `bdate`, `inc_lname`,
            `inc_fname`, `inc_mi`, `inc_contact`, `inc_houseno`, `inc_street`, `inc_brgy`, `inc_municipal`)
            VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$id_brgyid, $id_resident, $lname, $fname, $mi, $houseno,  $street, $brgy, $municipal, 
            $bplace, $bdate, $inc_lname, $inc_fname, $inc_mi, $inc_contact, $inc_houseno, 
            $inc_street, $inc_brgy, $inc_municipal ]);

            $message2 = "Application Applied, you will receive our text message for further details";
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("refresh: 0");
        }  
    }

    public function get_single_brgyid($id_resident){

        $id_resident = $_GET['id_resident'];
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_brgyid where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        $total = $stmt->rowCount();

        if($total > 0 )  {
            return $resident;
        }
        else{
            return false;
        }
    }


    public function view_brgyid(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_brgyid");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }


    public function delete_brgyid(){
        $id_bspermit = $_POST['id_brgyid'];

        if(isset($_POST['delete_brgyid'])) {
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_brgyid where id_brgyid = ?");
            $stmt->execute([$id_bspermit]);

            header("Refresh:0");
        }
    }







     public function create_blotter() {
    if(isset($_POST['create_blotter'])) {
        $id_resident = $_POST['id_resident'];
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi']; 
        $houseno = $_POST['houseno'];
        $street = $_POST['street'];
        $brgy = $_POST['brgy'];
        $municipal = $_POST['municipal'];
        $contact = $_POST['contact'];
        $narrative = $_POST['narrative'];

        $connection = $this->openConn();
        // Removed blot_photo from columns and values
        $stmt = $connection->prepare("INSERT INTO tbl_blotter (`id_resident`, `lname`, `fname`, `mi`,
            `houseno`, `street`,`brgy`, `municipal`, `contact`, `narrative`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([$id_resident, $lname, $fname, $mi, $houseno, $street, $brgy, $municipal, $contact, $narrative]);

        echo "<script type='text/javascript'>alert('Application Applied');</script>";
        header("refresh: 0");
    }  
}

public function get_single_blotter($id_resident){
    $id_resident = $_GET['id_resident'];
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT * FROM tbl_blotter where id_resident = ?");
    $stmt->execute([$id_resident]);
    $resident = $stmt->fetch();
    $total = $stmt->rowCount();

    return ($total > 0) ? $resident : false;
}

public function view_blotter(){
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT * from tbl_blotter");
    $stmt->execute();
    return $stmt->fetchAll();
}

public function delete_blotter(){
    if(isset($_POST['delete_blotter'])) {
        $id_blotter = $_POST['id_blotter'];
        $connection = $this->openConn();
        $stmt = $connection->prepare("DELETE FROM tbl_blotter where id_blotter = ?");
        $stmt->execute([$id_blotter]);

        header("Refresh:0");
    }
}

public function update_blotter() {
    // Note: Changed condition to 'update_blotter' for consistency
    if (isset($_POST['update_blotter'])) {
        $id_blotter = $_GET['id_blotter']; 
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $houseno = $_POST['houseno'];
        $street = $_POST['street'];
        $brgy = $_POST['brgy'];
        $municipal = $_POST['municipal'];
        $contact = $_POST['contact'];
        $narrative = $_POST['narrative'];

        $connection = $this->openConn();
        // Fixed the UPDATE query to use correct blotter fields
        $stmt = $connection->prepare("UPDATE tbl_blotter SET lname = ?, fname = ?,
            mi = ?, houseno = ?, street = ?, brgy = ?, municipal = ?,
            contact = ?, narrative = ? WHERE id_blotter = ?");
        
        $stmt->execute([$lname, $fname, $mi, $houseno, $street, $brgy, $municipal, $contact, $narrative, $id_blotter]);
        
        echo "<script type='text/javascript'>alert('Complain/Blotter Data Updated');</script>";
        header("refresh: 0");
    }
}

    

// ========================= PROGRAMS & ACTIVITIES =========================

    public function create_program() {
        if (isset($_POST['create_program'])) {
            $title        = $_POST['title'];
            $description  = $_POST['description'];
            $category     = $_POST['category'];
            $event_type   = $_POST['event_type'];
            $start_date   = $_POST['start_date'];
            $end_date     = $_POST['end_date'];
            $reg_deadline = $_POST['registration_deadline'];
            $venue        = $_POST['venue'];
            $max_part     = !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null;
            $age_min      = (int)$_POST['target_age_min'];
            $age_max      = (int)$_POST['target_age_max'];
            $requirements = $_POST['requirements'];
            $contact_p    = $_POST['contact_person'];
            $contact_n    = $_POST['contact_number'];
            $status       = $_POST['status'];
            $created_by   = $_SESSION['userdata']['id_resident'] ?? $_SESSION['userdata']['id_user'] ?? 1;

            $prefix = strtoupper(substr($category, 0, 3));
            $year   = date('Y');
            $rand   = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $code   = $prefix . '-' . $year . '-' . $rand;

            // Banner upload
            $banner = null;
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
                $ext    = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
                $fname  = 'banner_' . time() . '.' . $ext;
                $dir    = 'uploads/program_banners/';
                if (!file_exists($dir)) mkdir($dir, 0777, true);
                if (move_uploaded_file($_FILES['banner']['tmp_name'], $dir . $fname)) {
                    $banner = $dir . $fname;
                }
            }

            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_programs
                (program_code, title, description, category, event_type,
                 start_date, end_date, registration_deadline, venue,
                 max_participants, target_age_min, target_age_max,
                 requirements, contact_person, contact_number,
                 status, banner_image, created_by)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $code, $title, $description, $category, $event_type,
                $start_date, $end_date, $reg_deadline, $venue,
                $max_part, $age_min, $age_max,
                $requirements, $contact_p, $contact_n,
                $status, $banner, $created_by
            ]);
            echo "<script>alert('Program created successfully!');</script>";
            header("Refresh:0");
        }
    }

    public function update_program() {
        if (isset($_POST['update_program'])) {
            $id           = (int)$_POST['id_program'];
            $title        = $_POST['title'];
            $description  = $_POST['description'];
            $category     = $_POST['category'];
            $event_type   = $_POST['event_type'];
            $start_date   = $_POST['start_date'];
            $end_date     = $_POST['end_date'];
            $reg_deadline = $_POST['registration_deadline'];
            $venue        = $_POST['venue'];
            $max_part     = !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null;
            $age_min      = (int)$_POST['target_age_min'];
            $age_max      = (int)$_POST['target_age_max'];
            $requirements = $_POST['requirements'];
            $contact_p    = $_POST['contact_person'];
            $contact_n    = $_POST['contact_number'];
            $status       = $_POST['status'];

            $connection = $this->openConn();

            // Handle optional banner update
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
                $ext   = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
                $fname = 'banner_' . time() . '.' . $ext;
                $dir   = 'uploads/program_banners/';
                if (!file_exists($dir)) mkdir($dir, 0777, true);
                if (move_uploaded_file($_FILES['banner']['tmp_name'], $dir . $fname)) {
                    $banner = $dir . $fname;
                    $stmt = $connection->prepare("UPDATE tbl_programs SET banner_image=? WHERE id_program=?");
                    $stmt->execute([$banner, $id]);
                }
            }

            $stmt = $connection->prepare("UPDATE tbl_programs SET
                title=?, description=?, category=?, event_type=?,
                start_date=?, end_date=?, registration_deadline=?, venue=?,
                max_participants=?, target_age_min=?, target_age_max=?,
                requirements=?, contact_person=?, contact_number=?, status=?
                WHERE id_program=?");
            $stmt->execute([
                $title, $description, $category, $event_type,
                $start_date, $end_date, $reg_deadline, $venue,
                $max_part, $age_min, $age_max,
                $requirements, $contact_p, $contact_n, $status, $id
            ]);
            echo "<script>alert('Program updated successfully!');</script>";
            header("Refresh:0");
        }
    }

    public function delete_program() {
        if (isset($_POST['delete_program'])) {
            $id = (int)$_POST['id_program'];
            $connection = $this->openConn();
            // Delete related registrations and attendance first
            $connection->prepare("DELETE FROM tbl_program_attendance WHERE registration_id IN (SELECT id_registration FROM tbl_program_registrations WHERE program_id=?)")->execute([$id]);
            $connection->prepare("DELETE FROM tbl_program_registrations WHERE program_id=?")->execute([$id]);
            $connection->prepare("DELETE FROM tbl_program_gallery WHERE program_id=?")->execute([$id]);
            $connection->prepare("DELETE FROM tbl_programs WHERE id_program=?")->execute([$id]);
            echo "<script>alert('Program deleted.');</script>";
            header("Refresh:0");
        }
    }

    public function view_programs($filters = []) {
        $connection = $this->openConn();
        $sql = "SELECT p.*,
                    COUNT(DISTINCT r.id_registration) as total_registrations,
                    COUNT(DISTINCT CASE WHEN r.attended=1 THEN r.id_registration END) as total_attended
                FROM tbl_programs p
                LEFT JOIN tbl_program_registrations r ON p.id_program = r.program_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['category'])) {
            $sql .= " AND p.category = ?"; $params[] = $filters['category'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?"; $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        $sql .= " GROUP BY p.id_program ORDER BY p.start_date DESC";
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function get_single_program($id_program) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT p.*,
            COUNT(DISTINCT r.id_registration) as total_registrations,
            COUNT(DISTINCT CASE WHEN r.status='Approved' THEN r.id_registration END) as approved_registrations,
            COUNT(DISTINCT CASE WHEN r.attended=1 THEN r.id_registration END) as total_attended
            FROM tbl_programs p
            LEFT JOIN tbl_program_registrations r ON p.id_program = r.program_id
            WHERE p.id_program = ?
            GROUP BY p.id_program");
        $stmt->execute([$id_program]);
        return $stmt->fetch();
    }

    // --- REGISTRATIONS ---
    public function register_for_program() {
        if (isset($_POST['register_program'])) {
            $program_id = (int)$_POST['program_id'];
            $user_id    = $_SESSION['userdata']['id_resident'];
            $connection = $this->openConn();

            // Check already registered
            $chk = $connection->prepare("SELECT COUNT(*) FROM tbl_program_registrations WHERE program_id=? AND user_id=? AND status NOT IN ('Cancelled','Rejected')");
            $chk->execute([$program_id, $user_id]);
            if ($chk->fetchColumn() > 0) {
                echo "<script>alert('You are already registered for this program.');</script>";
                return;
            }

            // Check max participants
            $prog = $connection->prepare("SELECT max_participants FROM tbl_programs WHERE id_program=?");
            $prog->execute([$program_id]);
            $progData = $prog->fetch();
            $cnt = $connection->prepare("SELECT COUNT(*) FROM tbl_program_registrations WHERE program_id=? AND status='Approved'");
            $cnt->execute([$program_id]);
            $currentCount = $cnt->fetchColumn();

            $status = 'Approved';
            if ($progData['max_participants'] && $currentCount >= $progData['max_participants']) {
                $status = 'Waitlisted';
            }

            $code = 'REG-' . $program_id . '-' . strtoupper(substr(md5(time() . rand()), 0, 8));
            $stmt = $connection->prepare("INSERT INTO tbl_program_registrations (program_id, user_id, registration_code, status) VALUES (?,?,?,?)");
            $stmt->execute([$program_id, $user_id, $code, $status]);
            $msg = $status === 'Waitlisted' ? "Program is full. You have been added to the waitlist." : "Registration successful! Your code: $code";
            echo "<script>alert('$msg');</script>";
            header("Refresh:0");
        }
    }

    public function cancel_registration() {
        if (isset($_POST['cancel_registration'])) {
            $id      = (int)$_POST['id_registration'];
            $user_id = $_SESSION['userdata']['id_resident'];
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_program_registrations SET status='Cancelled' WHERE id_registration=? AND user_id=?");
            $stmt->execute([$id, $user_id]);
            echo "<script>alert('Registration cancelled.');</script>";
            header("Refresh:0");
        }
    }

    public function admin_update_registration_status() {
        if (isset($_POST['update_reg_status'])) {
            $id     = (int)$_POST['id_registration'];
            $status = $_POST['reg_status'];
            $connection = $this->openConn();
            $connection->prepare("UPDATE tbl_program_registrations SET status=? WHERE id_registration=?")->execute([$status, $id]);
            echo "<script>alert('Status updated.');</script>";
            header("Refresh:0");
        }
    }

    public function get_program_registrations($program_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT r.*, res.firstname, res.surname, res.email, res.phone_number, res.age
            FROM tbl_program_registrations r
            JOIN tbl_resident res ON r.user_id = res.id_resident
            WHERE r.program_id = ?
            ORDER BY r.registration_date DESC");
        $stmt->execute([$program_id]);
        return $stmt->fetchAll();
    }

    public function get_resident_registrations($user_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT r.*, p.title, p.start_date, p.end_date, p.venue, p.category, p.status as prog_status
            FROM tbl_program_registrations r
            JOIN tbl_programs p ON r.program_id = p.id_program
            WHERE r.user_id = ?
            ORDER BY p.start_date DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // --- ATTENDANCE ---
    public function mark_attendance() {
        if (isset($_POST['mark_attendance'])) {
            $reg_id      = (int)$_POST['registration_id'];
            $attended    = isset($_POST['attended']) ? 1 : 0;
            $marked_by   = $_SESSION['userdata']['id_resident'] ?? $_SESSION['userdata']['id_user'] ?? 1;
            $connection  = $this->openConn();
            $connection->prepare("UPDATE tbl_program_registrations SET attended=?, attendance_date=NOW() WHERE id_registration=?")->execute([$attended, $reg_id]);
            $connection->prepare("INSERT INTO tbl_program_attendance (registration_id, scan_datetime, scan_type, scanned_by, ip_address) VALUES (?,NOW(),'Manual Entry',?,?)")->execute([$reg_id, $marked_by, $_SERVER['REMOTE_ADDR']]);
            echo "<script>alert('Attendance updated.');</script>";
            header("Refresh:0");
        }
    }

    public function get_attendance_report($program_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT r.*, res.firstname, res.surname, res.email, res.phone_number
            FROM tbl_program_registrations r
            JOIN tbl_resident res ON r.user_id = res.id_resident
            WHERE r.program_id = ? AND r.status = 'Approved'
            ORDER BY res.surname ASC");
        $stmt->execute([$program_id]);
        return $stmt->fetchAll();
    }

    // --- GALLERY ---
    public function upload_program_media() {
        if (isset($_POST['upload_media'])) {
            $program_id  = (int)$_POST['program_id'];
            $captions    = $_POST['captions'] ?? [];
            $uploaded_by = $_SESSION['userdata']['id_resident'] ?? $_SESSION['userdata']['id_user'] ?? 1;
            $connection  = $this->openConn();
            $uploadDir   = 'uploads/program_gallery/program_' . $program_id . '/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
            $count = 0;
            if (!empty($_FILES['media_files']['name'][0])) {
                foreach ($_FILES['media_files']['tmp_name'] as $k => $tmp) {
                    if ($_FILES['media_files']['error'][$k] != 0) continue;
                    $type = $_FILES['media_files']['type'][$k];
                    if (!in_array($type, $allowedTypes)) continue;
                    $ext   = pathinfo($_FILES['media_files']['name'][$k], PATHINFO_EXTENSION);
                    $fname = 'media_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmp, $uploadDir . $fname)) {
                        $mediaType = strpos($type, 'image') !== false ? 'Image' : 'Video';
                        $caption   = $captions[$k] ?? '';
                        $connection->prepare("INSERT INTO tbl_program_gallery (program_id, file_name, file_path, file_type, caption, uploaded_by) VALUES (?,?,?,?,?,?)")
                            ->execute([$program_id, $_FILES['media_files']['name'][$k], $uploadDir . $fname, $mediaType, $caption, $uploaded_by]);
                        $count++;
                    }
                }
            }
            echo "<script>alert('$count file(s) uploaded.');</script>";
            header("Refresh:0");
        }
    }

    public function delete_program_media() {
        if (isset($_POST['delete_media'])) {
            $id = (int)$_POST['id_media'];
            $connection = $this->openConn();
            $row = $connection->prepare("SELECT file_path FROM tbl_program_gallery WHERE id_media=?");
            $row->execute([$id]);
            $media = $row->fetch();
            if ($media && file_exists($media['file_path'])) unlink($media['file_path']);
            $connection->prepare("DELETE FROM tbl_program_gallery WHERE id_media=?")->execute([$id]);
            echo "<script>alert('Media deleted.');</script>";
            header("Refresh:0");
        }
    }

    public function get_program_gallery($program_id, $type = null) {
        $connection = $this->openConn();
        $sql = "SELECT * FROM tbl_program_gallery WHERE program_id=?";
        $params = [$program_id];
        if ($type) { $sql .= " AND file_type=?"; $params[] = $type; }
        $sql .= " ORDER BY uploaded_at DESC";
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

}

    

$bmis = new BMISClass(); //variable to call outside of its class

?>