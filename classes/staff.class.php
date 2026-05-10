<?php 

    require_once('main.class.php');

    class StaffClass extends BMISClass {

        
        //authentication method for residents to enter
        public function residentlogin() {
        if(isset($_POST['residentlogin'])) {

            $username = $_POST['email'];
            $password = $_POST['password']; 
        
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * FROM tbl_residents WHERE email = ? AND password = ?");
            $stmt->Execute([$username, $password]);
            $user = $stmt->fetch();
            $total = $stmt->rowCount();
            
                //calls the set_userdata function 
                if($total > 0) {
                    $this->set_userdata($user);
                    header('Location: resident_homepage.php');
                }
                
                else {
                    echo '<script>alert("Email or Password is Invalid")</script>';
                }
            }
        }
    

    
    //------------------------------------- CRUD FUNCTIONS FOR STAFF -----------------------------------------------

public function create_staff() {
    if(isset($_POST['add_staff'])) {
        $login_identity = $_POST['login_identity'];
        $plain_password = $_POST['password'];
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $age = $_POST['age'];
        $sex = $_POST['sex'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $position = $_POST['position'];
        $role = $_POST['role'];
        $addedby = $_POST['addedby'];

        $email_to_save = "";
        $phone_to_save = "";

        // Logic: If it has an '@', treat as email; otherwise, treat as phone
        if (filter_var($login_identity, FILTER_VALIDATE_EMAIL)) {
            $email_to_save = $login_identity;
        } else {
            $phone_to_save = $login_identity;
        }

        if ($this->check_staff($login_identity) == 0) {
            
            if ($age < 18) {
                echo "<script>alert('Sorry, you are underaged to register an account');</script>";
                return;
            }

            // --- PHOTO UPLOAD LOGIC ---
            $photo = ""; 
            if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $target_dir = "uploads/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $filename = time() . "_" . basename($_FILES["photo"]["name"]);
                $target_file = $target_dir . $filename;
                
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $photo = $target_file; 
                }
            }

            // 2. Insert into tbl_user
            $connection = $this->openConn();
            // ADDED `login_identity` to the column list below
            $stmt = $connection->prepare("INSERT INTO tbl_user (
                `login_identity`, `email`, `phone_number`, `password`, `lname`, `fname`, 
                `mi`, `age`, `sex`, `address`, `contact`, `position`, `role`, `addedby`, `photo`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // ADDED $login_identity to the execution array below
            $stmt->execute([  
                $login_identity, 
                $email_to_save, 
                $phone_to_save, 
                $hashed_password, 
                $lname, $fname, $mi, $age, $sex, 
                $address, $contact, $position, $role, $addedby, $photo
            ]);

            echo "<script>alert('New Staff Added Successfully'); window.location.href='admn_staff_crud.php';</script>";
        } else {
            echo "<script>alert('This Identity is already registered.');</script>";
        }
    } 
}

        public function view_staff(){

            $connection = $this->openConn();

            $stmt = $connection->prepare("SELECT * from tbl_user");
            $stmt->execute();
            $view = $stmt->fetchAll();
            //$rows = $stmt->
            return $view;
           
        }

        public function view_single_staff(){

            $id_staff = $_GET['id_staff'];
            
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * FROM tbl_user where id_user = '$id_staff'");
            $stmt->execute();
            $view = $stmt->fetch(); 
            $total = $stmt->rowCount();
 
            //eto yung condition na i ch check kung may laman si products at i re return niya kapag meron
            if($total > 0 )  {
                return $view;
            }
            else{
                return false;
            }
        }

public function update_staff() {
    if (isset($_POST['update_staff'])) {
        $id_user = $_GET['id_user'];
        $login_identity = $_POST['login_identity'];
        
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mi = $_POST['mi'];
        $age = $_POST['age'];
        $sex = $_POST['sex'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $position = $_POST['position'];
        $role = $_POST['role'];
        $addedby = $_POST['addedby'];

        // Split identity for storage
        $email_to_save = "";
        $phone_to_save = "";
        if (filter_var($login_identity, FILTER_VALIDATE_EMAIL)) {
            $email_to_save = $login_identity;
        } else {
            $phone_to_save = $login_identity;
        }

        $connection = $this->openConn();

        // --- PASSWORD LOGIC ---
        $password_query = "";
        $password_param = [];
        if (!empty($_POST['password'])) {
            $password_query = " `password` = ?, ";
            $password_param[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        // --- PHOTO UPLOAD LOGIC ---
        $photo_query = "";
        $photo_param = [];

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            // Define your upload path here
            $target_dir = "uploads/"; 
            $target_file = $target_dir . time() . "_" . basename($_FILES["photo"]["name"]);

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // Delete old photo
                $stmt_old = $connection->prepare("SELECT photo FROM tbl_user WHERE id_user = ?");
                $stmt_old->execute([$id_user]);
                $old_photo = $stmt_old->fetch();
                
                if ($old_photo && !empty($old_photo['photo']) && file_exists($old_photo['photo'])) {
                    unlink($old_photo['photo']); 
                }

                $photo_query = ", `photo` = ?";
                $photo_param[] = $target_file;
            }
        }

        // --- PREPARE SQL & PARAMS ---
        $params = array_merge(
            $password_param, 
            [$login_identity, $email_to_save, $phone_to_save, $lname, $fname, $mi, $age, $sex, $address, $contact, $position, $role, $addedby],
            $photo_param
        );
        $params[] = $id_user; 

        $sql = "UPDATE tbl_user SET 
                $password_query
                `login_identity` = ?, 
                `email` = ?, 
                `phone_number` = ?, 
                `lname` = ?, 
                `fname` = ?, 
                `mi` = ?, 
                `age` = ?, 
                `sex` = ?, 
                `address` = ?, 
                `contact` = ?, 
                `position` = ?, 
                `role` = ?, 
                `addedby` = ? 
                $photo_query 
                WHERE id_user = ?";
        
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        
        // --- THE FIX: Refresh the current page with the ID ---
        echo "<script type='text/javascript'>
                alert('Staff Account Updated Successfully');
                window.location.href = window.location.pathname + window.location.search;
              </script>";
    }
}
        public function delete_staff(){
    if(isset($_POST['delete_staff'])) {
        $id_user = $_POST['id_user'];
        $connection = $this->openConn();

        // 1. Get the photo path before deleting the record
        $stmt_find = $connection->prepare("SELECT photo FROM tbl_user WHERE id_user = ?");
        $stmt_find->execute([$id_user]);
        $user = $stmt_find->fetch();

        // 2. Delete the physical file if it exists
        if ($user && !empty($user['photo']) && file_exists($user['photo'])) {
            unlink($user['photo']);
        }

        // 3. Now delete the database record
        $stmt = $connection->prepare("DELETE FROM tbl_user WHERE id_user = ?");
        $stmt->execute([$id_user]);
        
        echo "<script type='text/javascript'>alert('Staff Account Deleted');</script>";
        echo "<script type='text/javascript'>window.location.href='admn_staff_crud.php';</script>";
    }
}

    //--------------------------------------------- EXTRA FUNCTIONS FOR STAFF -------------------------------------------------

            public function get_single_staff($id_user){

                $id_user = $_GET['id_user'];
                
                $connection = $this->openConn();
                $stmt = $connection->prepare("SELECT * FROM tbl_user where id_user = ?");
                $stmt->execute([$id_user]);
                $user = $stmt->fetch();
                $total = $stmt->rowCount();

                if($total > 0 )  {
                    return $user;
                }
                else{
                    return false;
                }
            }


        public function check_staff($id_user) {

            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * FROM tbl_user WHERE id_user = ?");
            $stmt->Execute([$id_user]);
            $total = $stmt->rowCount(); 

            return $total;
        }

        public function count_staff() {
            $connection = $this->openConn();

            $stmt = $connection->prepare("SELECT COUNT(*) from tbl_user");
            $stmt->execute();
            $staffcount = $stmt->fetchColumn();

            return $staffcount;
        }

        public function count_mstaff() {
            $connection = $this->openConn();

            $stmt = $connection->prepare("SELECT COUNT(*) from tbl_user where sex = 'male'");
            $stmt->execute();
            $staffcount = $stmt->fetchColumn();

            return $staffcount;
        }

        public function count_fstaff() {
            $connection = $this->openConn();

            $stmt = $connection->prepare("SELECT COUNT(*) from tbl_user where sex = 'female'");
            $stmt->execute();
            $staffcount = $stmt->fetchColumn();

            return $staffcount;
        }


        //===================================== SCOPE CHANGED FEATURES =======================================

        public function view_staff_male(){
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * from tbl_user WHERE `sex` = 'Male'");
            $stmt->execute();   
            $view = $stmt->fetchAll();
            return $view;
        }
    
        public function view_staff_female(){
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT * from tbl_user WHERE `sex` = 'Female'");
            $stmt->execute();
            $view = $stmt->fetchAll();
            return $view;
        }





    }
    $staffbmis = new StaffClass();
?>