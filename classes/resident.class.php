<?php 

    require_once('main.class.php');
    

    class ResidentClass extends BMISClass {
        //------------------------------------ RESIDENT CRUD FUNCTIONS ----------------------------------------

 public function create_resident() {
    if(isset($_POST['add_resident'])) {
        // Capture the new generic identity field
        $login_identity = $_POST['login_identity'];
        $plain_password = $_POST['password'];
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // Capture other fields
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : 'No';
        $sex = $_POST['sex'];
        $status = $_POST['status'];
        $houseno = $_POST['houseno'];
        $street = $_POST['street'];
        $brgy = $_POST['brgy'];
        $municipal = $_POST['municipal'];
        $contact = $_POST['contact']; // Profile contact info
        $bdate = $_POST['bdate'];
        $bplace = $_POST['bplace'];
        $nationality = $_POST['nationality'];
        $voter = $_POST['voter'];
        $familyrole = $_POST['family_role'];
        $addedby = isset($_POST['addedby']) ? $_POST['addedby'] : 'Resident';
        $role = isset($_POST['role']) ? $_POST['role'] : 'resident';

        // Initialize login columns
        $email_to_save = NULL;
        $phone_to_save = NULL;

        // Logic: If it has an '@', treat as email; otherwise, treat as phone
        if (filter_var($login_identity, FILTER_VALIDATE_EMAIL)) {
            $email_to_save = $login_identity;
        } else {
            $phone_to_save = $login_identity;
        }

        // 1. Check if this identity is already taken (active residents + pending applications)
        if ($this->check_resident($login_identity) == 0) {

            // 2. Validate the required Valid ID upload before creating the pending application
            $upload_error   = '';
            $new_filename   = null;
            $original_name  = null;
            $file_type      = null;

            if (!isset($_FILES['valid_id_file']) || $_FILES['valid_id_file']['error'] !== UPLOAD_ERR_OK) {
                $upload_error = 'Please upload a valid government ID to complete your registration.';
            } else {
                $file = $_FILES['valid_id_file'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if (!in_array($file['type'], $allowed_types)) {
                    $upload_error = 'Only JPG, PNG, and PDF files are allowed for the valid ID.';
                } elseif ($file['size'] > $max_size) {
                    $upload_error = 'Valid ID file size must not exceed 5MB.';
                } else {
                    $upload_dir = 'uploads/valid_ids/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = 'pendingreg_' . time() . '_' . uniqid() . '.' . $ext;
                    $dest = $upload_dir . $new_filename;

                    if (!move_uploaded_file($file['tmp_name'], $dest)) {
                        $upload_error = 'Valid ID upload failed. Please try again.';
                    } else {
                        $original_name = $file['name'];
                        $file_type     = $file['type'];
                    }
                }
            }

            if ($upload_error) {
                echo "<script>alert('" . addslashes($upload_error) . "'); window.history.back();</script>";
                return;
            }

            $connection = $this->openConn();
            // 3. Insert into the PENDING table (NOT tbl_resident) until an admin approves the ID
            $stmt = $connection->prepare("INSERT INTO tbl_resident_pending (
                `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `pwd`, `sex`,
                `status`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `bdate`,
                `bplace`, `nationality`, `voter`, `family_role`, `role`, `addedby`,
                `valid_id_file`, `valid_id_original_name`, `valid_id_file_type`, `application_status`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");

            $stmt->execute([
                $email_to_save,
                $phone_to_save,
                $hashed_password,
                $lname, $fname, $mi, $pwd, $sex, $status,
                $houseno, $street, $brgy, $municipal, $contact,
                $bdate, $bplace, $nationality, $voter, $familyrole, $role, $addedby,
                $new_filename, $original_name, $file_type
            ]);

            echo "
            <div id='toast' style='
                position:fixed; top:24px; right:24px; z-index:9999;
                background:#fff; border-left:4px solid #1D9E75;
                border-radius:10px; box-shadow:0 8px 32px rgba(0,0,0,0.13);
                padding:16px 20px 16px 18px; min-width:300px; max-width:380px;
                display:flex; align-items:flex-start; gap:14px;
                font-family:Georgia,serif;
                animation:slideIn .4s cubic-bezier(.22,1,.36,1) both;
            '>
                <div style='width:36px;height:36px;border-radius:50%;background:#E1F5EE;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;'>
                    <svg width='18' height='18' fill='none' viewBox='0 0 24 24'>
                        <circle cx='12' cy='12' r='10' fill='#1D9E75'/>
                        <path d='M7.5 12.5l3 3 6-6' stroke='#fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/>
                    </svg>
                </div>
                <div>
                    <div style='font-weight:700;color:#085041;font-size:15px;margin-bottom:3px;'>Registration Submitted!</div>
                    <div style='color:#0F6E56;font-size:13px;'>Your account is pending admin approval. You'll be able to log in once your ID is verified.</div>
                </div>
                <button onclick=\"document.getElementById('toast').remove()\" style='margin-left:auto;background:none;border:none;cursor:pointer;color:#1D9E75;font-size:20px;line-height:1;padding:0;flex-shrink:0;'>&times;</button>
            </div>
            <style>
                @keyframes slideIn { from{opacity:0;transform:translateX(60px)} to{opacity:1;transform:translateX(0)} }
                @keyframes slideOut { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(60px)} }
            </style>
            <script>
                setTimeout(function(){
                    var t=document.getElementById('toast');
                    if(t){ t.style.animation='slideOut .35s ease forwards'; setTimeout(function(){ window.location.href='index.php'; },350); }
                }, 1500);
            </script>";

        } else {

            // ── Already registered ────────────────────────────────────
            echo "
            <div id='toast' style='
                position:fixed; top:24px; right:24px; z-index:9999;
                background:#fff; border-left:4px solid #E24B4A;
                border-radius:10px; box-shadow:0 8px 32px rgba(0,0,0,0.13);
                padding:16px 20px 16px 18px; min-width:300px; max-width:380px;
                display:flex; align-items:flex-start; gap:14px;
                font-family:Georgia,serif;
                animation:slideIn .4s cubic-bezier(.22,1,.36,1) both;
            '>
                <div style='width:36px;height:36px;border-radius:50%;background:#FCEBEB;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;'>
                    <svg width='18' height='18' fill='none' viewBox='0 0 24 24'>
                        <circle cx='12' cy='12' r='10' fill='#E24B4A'/>
                        <path d='M15 9l-6 6M9 9l6 6' stroke='#fff' stroke-width='2' stroke-linecap='round'/>
                    </svg>
                </div>
                <div>
                    <div style='font-weight:700;color:#501313;font-size:15px;margin-bottom:3px;'>Already Registered</div>
                    <div style='color:#A32D2D;font-size:13px;'>This email or phone number is already in use.</div>
                </div>
                <button onclick=\"document.getElementById('toast').remove()\" style='margin-left:auto;background:none;border:none;cursor:pointer;color:#E24B4A;font-size:20px;line-height:1;padding:0;flex-shrink:0;'>&times;</button>
            </div>
            <style>
                @keyframes slideIn { from{opacity:0;transform:translateX(60px)} to{opacity:1;transform:translateX(0)} }
                @keyframes slideOut { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(60px)} }
            </style>
            <script>
                setTimeout(function(){
                    var t=document.getElementById('toast');
                    if(t){ t.style.animation='slideOut .35s ease forwards'; setTimeout(function(){ t&&t.remove(); },350); }
                }, 1000);
            </script>";
        }
    }
}

        public function view_resident(){
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * from tbl_resident");
            $stmt->execute();
            $view = $stmt->fetchAll();
            return $view;
        }

        public function update_resident() {
            if (isset($_POST['update_resident'])) {
                $id_resident = $_GET['id_resident'];
                $email = $_POST['email'];
                $password = ($_POST['password']);
                $lname = $_POST['lname'];
                $fname = $_POST['fname'];
                $mi = $_POST['mi'];
                $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : 'No';
                $sex = $_POST['sex'];
                $status = $_POST['status'];
                $houseno = $_POST['houseno'];
                $street = $_POST['street'];
                $brgy = $_POST['brgy'];
                $municipal = $_POST['municipal'];
                $contact = $_POST['contact'];
                $bdate = $_POST['bdate'];
                $bplace = $_POST['bplace'];
                $nationality = $_POST['nationality'];
                $voter = $_POST['voter'];
                $family_role = $_POST['family_role'];
                $role = $_POST['role'];
                $addedby = $_POST['addedby'];

                $connection = $this->openConn();

// 1. Check if the password is being changed
if (!empty($password)) {
    // If password is NOT empty, update EVERYTHING including the new password
    $stmt = $connection->prepare("UPDATE tbl_resident SET `password` =?, `lname` =?, 
        `fname` = ?, `mi` =?, `pwd` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_resident` = ?");
    
    $stmt->execute([$password, $lname, $fname, $mi, $pwd, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $family_role, $role, $addedby, $id_resident]);

} else {
    // 2. If password is empty, update everything EXCEPT the password column
    $stmt = $connection->prepare("UPDATE tbl_resident SET `lname` =?, 
        `fname` = ?, `mi` =?, `pwd` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_resident` = ?");
    
    // Note: $password is removed from the array below
    $stmt->execute([$lname, $fname, $mi, $pwd, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $family_role, $role, $addedby, $id_resident]);
}

$message2 = "Resident Data Updated";
echo "<script type='text/javascript'>alert('$message2');</script>";
header("refresh: 0");
            }
        }

        public function delete_resident(){
    $id_resident = $_POST['id_resident'];

    if(isset($_POST['delete_resident'])) {
        $this->archive_record('tbl_resident', 'id_resident', $id_resident, 'resident');

        $connection = $this->openConn();
        $stmt = $connection->prepare("DELETE FROM tbl_resident WHERE id_resident = ?");
        $stmt->execute([$id_resident]);

        $this->log_activity('DELETE_Resident', 'Resident', "Deleted Resident Record #$id_resident");

        $_SESSION['swal'] = json_encode([
            'icon'  => 'success',
            'title' => 'Archived!',
            'text'  => 'Resident has been moved to archive.'
        ]);
        header('Location: admn_resident_crud.php');
        exit;
    }
}

    //-------------------------------- EXTRA FUNCTIONS FOR RESIDENT CLASS ---------------------------------

    


    public function get_single_resident($id_resident){

        $id_resident = isset($_GET['id_resident']) ? $_GET['id_resident'] : $id_resident;
        
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch(PDO::FETCH_ASSOC);

        if($resident)  {
            return $resident;
        }
        else{
            return false;
        }
    }
   
    public function check_resident($login_identity) {

        $connection = $this->openConn();
        // Check both email and phone_number columns so duplicates are caught
        // regardless of whether the user registered with an email or phone number
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE email = ? OR phone_number = ?");
        $stmt->Execute([$login_identity, $login_identity]);
        $total = $stmt->rowCount(); 

        if ($total == 0) {
            // Also block duplicates that are still awaiting admin approval
            $stmt2 = $connection->prepare("SELECT * FROM tbl_resident_pending WHERE (email = ? OR phone_number = ?) AND application_status = 'pending'");
            $stmt2->execute([$login_identity, $login_identity]);
            $total = $stmt2->rowCount();
        }

        return $total;
    }

    //------------------------------------ PENDING REGISTRATION FUNCTIONS ----------------------------------------

    public function view_pending_residents() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident_pending WHERE application_status = 'pending' ORDER BY date_submitted DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count_pending_residents() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_resident_pending WHERE application_status = 'pending'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function get_single_pending($id_pending) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident_pending WHERE id_pending = ?");
        $stmt->execute([$id_pending]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    /**
     * Approves a pending registration: moves the applicant's data into tbl_resident,
     * removes the pending record, and deletes the reviewed ID file from storage.
     */
    public function approve_pending_resident($id_pending, $admin_name) {
        $connection = $this->openConn();
        try {
            $connection->beginTransaction();

            $stmt = $connection->prepare("SELECT * FROM tbl_resident_pending WHERE id_pending = ?");
            $stmt->execute([$id_pending]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$p) {
                $connection->rollBack();
                return false;
            }

            $insert = $connection->prepare("INSERT INTO tbl_resident (
                `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `pwd`, `sex`,
                `status`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `bdate`,
                `bplace`, `nationality`, `voter`, `family_role`, `role`, `addedby`,
                `is_verified`, `verified_at`, `verified_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)");

            $insert->execute([
                $p['email'], $p['phone_number'], $p['password'], $p['lname'], $p['fname'], $p['mi'],
                $p['pwd'], $p['sex'], $p['status'], $p['houseno'], $p['street'], $p['brgy'],
                $p['municipal'], $p['contact'], $p['bdate'], $p['bplace'], $p['nationality'],
                $p['voter'], $p['family_role'], $p['role'], $p['addedby'], $admin_name
            ]);

            $new_id_resident = $connection->lastInsertId();

            $del = $connection->prepare("DELETE FROM tbl_resident_pending WHERE id_pending = ?");
            $del->execute([$id_pending]);

            // Welcome notification, visible to the resident once they log in
            $notice = "✅ Your registration has been approved! You can now log in to your account.";
            $msg = $connection->prepare("INSERT INTO resident_messages (id_resident, message_text, date_sent) VALUES (?, ?, NOW())");
            $msg->execute([$new_id_resident, $notice]);

            $connection->commit();

            // Remove the uploaded ID file from storage now that it has been reviewed
            if (!empty($p['valid_id_file'])) {
                $filename = trim($p['valid_id_file']);
                $paths = [
                    __DIR__ . '/../uploads/valid_ids/' . $filename,
                    $_SERVER['DOCUMENT_ROOT'] . '/uploads/valid_ids/' . $filename,
                    'uploads/valid_ids/' . $filename,
                ];
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                        break;
                    }
                }
            }

            return true;
        } catch (PDOException $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            error_log("approve_pending_resident Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejects a pending registration: keeps the record for reference (status = rejected)
     * and deletes the uploaded ID file from storage.
     */
    public function reject_pending_resident($id_pending, $admin_name, $reason = '') {
        $connection = $this->openConn();
        try {
            $stmt = $connection->prepare("SELECT valid_id_file FROM tbl_resident_pending WHERE id_pending = ?");
            $stmt->execute([$id_pending]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($p && !empty($p['valid_id_file'])) {
                $filename = trim($p['valid_id_file']);
                $paths = [
                    __DIR__ . '/../uploads/valid_ids/' . $filename,
                    $_SERVER['DOCUMENT_ROOT'] . '/uploads/valid_ids/' . $filename,
                    'uploads/valid_ids/' . $filename,
                ];
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                        break;
                    }
                }
            }

            $upd = $connection->prepare("UPDATE tbl_resident_pending SET application_status = 'rejected', reject_reason = ?, reviewed_by = ? WHERE id_pending = ?");
            return $upd->execute([$reason, $admin_name, $id_pending]);
        } catch (PDOException $e) {
            error_log("reject_pending_resident Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete_pending_resident($id_pending) {
        $connection = $this->openConn();
        try {
            $stmt = $connection->prepare("SELECT valid_id_file FROM tbl_resident_pending WHERE id_pending = ?");
            $stmt->execute([$id_pending]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($p && !empty($p['valid_id_file'])) {
                $filename = trim($p['valid_id_file']);
                $paths = [
                    __DIR__ . '/../uploads/valid_ids/' . $filename,
                    $_SERVER['DOCUMENT_ROOT'] . '/uploads/valid_ids/' . $filename,
                    'uploads/valid_ids/' . $filename,
                ];
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                        break;
                    }
                }
            }

            $del = $connection->prepare("DELETE FROM tbl_resident_pending WHERE id_pending = ?");
            return $del->execute([$id_pending]);
        } catch (PDOException $e) {
            error_log("delete_pending_resident Error: " . $e->getMessage());
            return false;
        }
    }

    public function count_resident() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();
        return $rescount;
    }

    public function check_household($lname, $mi) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE lname = ? AND mi = ?");
        $stmt->Execute([$lname, $mi]);
        $total = $stmt->rowCount(); 
        return $total;
    }

    public function view_household_list() {
        $lname = $_POST['lname'];
        $mi = $_POST['mi'];

        if(isset($_POST['search_household'])) {
            $connection = $this->openConn();
            $stmt1 = $connection->prepare("SELECT * FROM `tbl_resident` WHERE `lname` LIKE '%$lname%' and  `mi` LIKE '%$mi%'");
            $stmt1->execute();
        }
    }

    public function count_male_resident() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident where sex = 'male' ");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

    public function count_female_resident() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident where sex = 'female'");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

    public function count_head_resident() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident where family_role = 'Yes'");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

    public function count_member_resident() {
        $connection = $this->openConn();

        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident where family_role = 'Family Member'");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }

    public function profile_update() {
        $id_resident = $_GET['id_resident'];
        $status = $_POST['status'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];

        if (isset($_POST['profile_update'])) {
           
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_resident SET `status` = ?, 
            `address` = ?, `contact` = ? WHERE id_resident = ?");
            $stmt->execute([ $status, $address,
            $contact, $id_resident]);
               
            $message2 = "Resident Profile Updated";
                
            echo "<script type='text/javascript'>alert('$message2');</script>";
            header("Refresh:0");

        }

    }
    

    //------------------------------------- RESIDENT FILTERING QUERIES --------------------------------------

    public function view_resident_minor(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE `age` <= 17");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_adult(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE `age` >= 18 AND `age` <= 59");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_senior(){
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE TIMESTAMPDIFF(YEAR, `bdate`, CURDATE()) >= 60");
    $stmt->execute();
    $view = $stmt->fetchAll();
    return $view;
}

public function count_resident_senior() {
    $connection = $this->openConn();
    $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_resident WHERE TIMESTAMPDIFF(YEAR, `bdate`, CURDATE()) >= 60");
    $stmt->execute();
    $rescount = $stmt->fetchColumn();
    return $rescount;
}

    public function view_resident_pwd() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE `pwd` = 'Yes' AND `is_archived` = 0");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count_pwd() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_resident WHERE `pwd` = 'Yes' AND `is_archived` = 0");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function count_non_pwd() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_resident WHERE `pwd` = 'No' AND `is_archived` = 0");
        $stmt->execute();
        return $stmt->fetchColumn();
    }



    //-------------------------------------- EXTRA FUNCTIONS ------------------------------------------------

public function resident_changepass() {
    // 1. Only run logic if the form was actually submitted
    if(isset($_POST['resident_changepass'])) {
        
        // Use ?? to prevent "Undefined index" notices
        // It's safer to get the ID from a session or a POST field rather than GET for a sensitive action
        $id_resident = $_POST['id_resident'] ?? $_GET['id_resident'] ?? null;
        $oldpassword_input = $_POST['oldpassword'] ?? '';
        $newpassword = $_POST['newpassword'] ?? '';
        $checkpassword = $_POST['checkpassword'] ?? '';

        if (!$id_resident) {
            echo "<script>alert('Error: Resident ID is missing.');</script>";
            return;
        }

        $connection = $this->openConn();
        
        // 2. Fetch the hashed password from the database
        $stmt = $connection->prepare("SELECT `password` FROM tbl_resident WHERE id_resident = ?");
        $stmt->execute([$id_resident]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Validation Logic
        if(!$result) {
            echo "<script>alert('Resident not found.');</script>";
        } 
        // Use password_verify to check against the hashed DB password
        elseif (!password_verify($oldpassword_input, $result['password'])) {
            echo "<script>alert('Old Password is Incorrect');</script>";
        } 
        elseif ($newpassword !== $checkpassword) {
            echo "<script>alert('New Password and Verification Password do not Match');</script>";
        } 
        elseif (empty($newpassword)) {
            echo "<script>alert('New password cannot be empty');</script>";
        } 
        else {
            // 4. Update the password using a NEW hash
            $hashed_password = password_hash($newpassword, PASSWORD_DEFAULT);
            
            $stmt = $connection->prepare("UPDATE tbl_resident SET password = ? WHERE id_resident = ?");
            $success = $stmt->execute([$hashed_password, $id_resident]);
            
            if ($success) {
                echo "<script type='text/javascript'>
                        alert('Password Updated Successfully');
                        window.location.href = window.location.href; // Refresh page cleanly
                      </script>";
                exit();
            } else {
                echo "<script>alert('Database Error: Could not update password.');</script>";
            }
        }
    }
}





    //-------------------------------------- PROMOTE RESIDENT TO STAFF ----------------------------------------

    public function promote_resident() {
        if (isset($_POST['promote_resident'])) {
            $id_resident = $_POST['promote_id_resident'];
            $position    = $_POST['position'];

            $connection = $this->openConn();

            // 1. Fetch the resident's data
            $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE id_resident = ?");
            $stmt->execute([$id_resident]);
            $resident = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resident) {
                $_SESSION['swal'] = json_encode([
                    'icon'  => 'error',
                    'title' => 'Not Found',
                    'text'  => 'Resident record could not be found.'
                ]);
                header('Location: admn_resident_crud.php');
                exit;
            }

            // 2. Build address and login_identity
            $address  = trim($resident['houseno'] . ' ' . $resident['street'] . ', ' . $resident['brgy'] . ', ' . $resident['municipal']);
            $login_identity = !empty($resident['email']) ? $resident['email'] : $resident['phone_number'];
            $email    = $resident['email'] ?? null;
            $phone    = $resident['phone_number'] ?? null;

            // 3. Check if already a staff member
            $checkStmt = $connection->prepare("SELECT COUNT(*) FROM tbl_user WHERE email = ? OR phone_number = ?");
            $checkStmt->execute([$email, $phone]);
            $exists = $checkStmt->fetchColumn();

            if ($exists > 0) {
                $_SESSION['swal'] = json_encode([
                    'icon'  => 'warning',
                    'title' => 'Already a Staff Member',
                    'text'  => $resident['fname'] . ' ' . $resident['lname'] . ' is already registered as a staff member.'
                ]);
                header('Location: admn_resident_crud.php');
                exit;
            }

            // 4. Determine addedby from session
            $userdetails = $this->get_userdata();
            $addedby = ($userdetails['surname'] ?? '') . ', ' . ($userdetails['firstname'] ?? '');

            // 5. Calculate age from bdate
            $age = 0;
            if (!empty($resident['bdate'])) {
                $birthDate = new DateTime($resident['bdate']);
                $today     = new DateTime();
                $age       = $birthDate->diff($today)->y;
            }

            // 6. Insert into tbl_user (staff table)
            $insertStmt = $connection->prepare("INSERT INTO tbl_user 
                (login_identity, email, phone_number, password, lname, fname, mi, age, sex, address, contact, position, role, addedby)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'user', ?)");

            $insertStmt->execute([
                $login_identity,
                $email,
                $phone,
                $resident['password'],
                $resident['lname'],
                $resident['fname'],
                $resident['mi'],
                $age,
                $resident['sex'],
                $address,
                $resident['contact'],
                $position,
                $addedby
            ]);

            $this->log_activity('PROMOTE_Resident', 'Resident', "Promoted Resident #{$id_resident} ({$resident['lname']}, {$resident['fname']}) to Staff — Position: {$position}");

            $_SESSION['swal'] = json_encode([
                'icon'  => 'success',
                'title' => 'Promoted Successfully!',
                'text'  => $resident['fname'] . ' ' . $resident['lname'] . ' has been promoted to Barangay Staff as ' . $position . '.'
            ]);
            header('Location: admn_resident_crud.php');
            exit;
        }
    }

    //========================================== SCOPE CHANGED FUNCTIONS ===========================================

    public function view_resident_household(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `family_role` = 'Yes'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_voters(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `voter` = 'Yes'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_male(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `sex` = 'Male'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function view_resident_female(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `sex` = 'Female'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function count_voters() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) from tbl_resident where `voter` = 'Yes' ");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
    }


    
    

    public function search_admn_voter() {
        
        $search = $_GET['search'];

        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_resident WHERE `fname` = '$search'");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;

            


            
        
        

    }

     private function paginate(string $sql, array $params, int $perPage = 10): array {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;

        $connection = $this->openConn();

        $countStmt = $connection->prepare("SELECT COUNT(*) FROM ({$sql}) AS sub");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $dataStmt = $connection->prepare("{$sql} LIMIT {$perPage} OFFSET {$offset}");
        $dataStmt->execute($params);
        $rows = $dataStmt->fetchAll();

        return [
            'rows'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'last_page'  => (int)ceil($total / $perPage),
        ];
    }

    public function view_resident_paginated(int $perPage = 10): array {
        return $this->paginate("SELECT * FROM tbl_resident WHERE is_archived = 0 OR is_archived IS NULL", [], $perPage);
    }

    public function view_resident_male_paginated(int $perPage = 10): array {
        return $this->paginate("SELECT * FROM tbl_resident WHERE sex = 'Male' AND (is_archived = 0 OR is_archived IS NULL)", [], $perPage);
    }

    public function view_resident_female_paginated(int $perPage = 10): array {
        return $this->paginate("SELECT * FROM tbl_resident WHERE sex = 'Female' AND (is_archived = 0 OR is_archived IS NULL)", [], $perPage);
    }

    public function view_resident_senior_paginated(int $perPage = 10): array {
        return $this->paginate("SELECT *, TIMESTAMPDIFF(YEAR, bdate, CURDATE()) AS computed_age FROM tbl_resident WHERE TIMESTAMPDIFF(YEAR, bdate, CURDATE()) >= 60 AND (is_archived = 0 OR is_archived IS NULL)", [], $perPage);
    }


    public function view_resident_voters_paginated(int $perPage = 10): array {
        return $this->paginate("SELECT * FROM tbl_resident WHERE voter = 'Yes' AND (is_archived = 0 OR is_archived IS NULL)", [], $perPage);
    }

    public function view_resident_household_paginated(int $perPage = 10): array {
        return $this->paginate("SELECT * FROM tbl_resident WHERE family_role = 'Yes' AND (is_archived = 0 OR is_archived IS NULL)", [], $perPage);
    }








    }

    $residentbmis = new ResidentClass();
?>