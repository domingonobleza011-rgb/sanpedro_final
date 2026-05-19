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
        $age = $_POST['age'];
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

        // 1. Check if this identity is already taken
        if ($this->check_resident($login_identity) == 0) {
            
            // Age validation
            if ($age < 18) {
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
                            <path d='M12 7v5M12 16h.01' stroke='#fff' stroke-width='2' stroke-linecap='round'/>
                        </svg>
                    </div>
                    <div>
                        <div style='font-weight:700;color:#501313;font-size:15px;margin-bottom:3px;'>You must be 18 or older</div>
                        <div style='color:#A32D2D;font-size:13px;'>Sorry, you are underage to register an account.</div>
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
                return(0);
            }

            $connection = $this->openConn();
            // 2. Updated INSERT to include both login columns
            $stmt = $connection->prepare("INSERT INTO tbl_resident (
                `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, 
                `status`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `bdate`, 
                `bplace`, `nationality`, `voter`, `family_role`, `role`, `addedby`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([ 
                $email_to_save, 
                $phone_to_save, 
                $hashed_password, 
                $lname, $fname, $mi, $age, $sex, $status, 
                $houseno, $street, $brgy, $municipal, $contact, 
                $bdate, $bplace, $nationality, $voter, $familyrole, $role, $addedby
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
                    <div style='font-weight:700;color:#085041;font-size:15px;margin-bottom:3px;'>Account Created!</div>
                    <div style='color:#0F6E56;font-size:13px;'>You can now log in to your account.</div>
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
                }, 1000);
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
                $age = $_POST['age'];
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
                $familyrole = $_POST['family_role'];
                $role = $_POST['role'];
                $addedby = $_POST['addedby'];

                $connection = $this->openConn();

// 1. Check if the password is being changed
if (!empty($password)) {
    // If password is NOT empty, update EVERYTHING including the new password
    $stmt = $connection->prepare("UPDATE tbl_resident SET `password` =?, `lname` =?, 
        `fname` = ?, `mi` =?, `age` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_resident` = ?");
    
    $stmt->execute([$password, $lname, $fname, $mi, $age, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $family_role, $role, $addedby, $id_resident]);

} else {
    // 2. If password is empty, update everything EXCEPT the password column
    $stmt = $connection->prepare("UPDATE tbl_resident SET `lname` =?, 
        `fname` = ?, `mi` =?, `age` =?, `sex` =?, `status` =?, `email` =?, `houseno` =?, `street` =?,
        `brgy` =?, `municipal` =?, `contact` =?,
        `bdate` =?, `bplace` =?, `nationality` =?, `voter` =?, `family_role` =?, `role` =?, `addedby` =? WHERE `id_resident` = ?");
    
    // Note: $password is removed from the array below
    $stmt->execute([$lname, $fname, $mi, $age, $sex, $status, $email, $houseno, 
        $street, $brgy, $municipal, $contact, $bdate, $bplace, $nationality, $voter, $familyrole, $role, $addedby, $id_resident]);
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

        return $total;
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
        $age = $_POST['age'];
        $status = $_POST['status'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];

        if (isset($_POST['profile_update'])) {
           
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_resident SET  `age` = ?,  `status` = ?, 
            `address` = ?, `contact` = ? WHERE id_resident = ?");
            $stmt->execute([ $age, $status, $address,
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
        $stmt = $connection->prepare("SELECT * FROM tbl_resident WHERE `age` >= 60");
        $stmt->execute();
        $view = $stmt->fetchAll();
        return $view;
    }

    public function count_resident_senior() {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM tbl_resident WHERE `age` >= 60");
        $stmt->execute();
        $rescount = $stmt->fetchColumn();

        return $rescount;
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








    }

    $residentbmis = new ResidentClass();
?>