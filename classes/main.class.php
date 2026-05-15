<?php 
function notif($message, $type = 'info') {
    $msg = addslashes($message);
    echo "<script>showNotif('$msg', '$type');</script>";
}
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
            notif("Database Connection Error: " . $e->getMessage(), 'error');
        }
    }
 
    // ──────────────────────────────────────────────────────────────────────────
    // ARCHIVE HELPER — call before any DELETE to log to tbl_archive
    // ──────────────────────────────────────────────────────────────────────────
    protected function archive_record($table, $id_col, $id_val, $record_type) {
        try {
            $connection = $this->openConn();
 
            // 1. Fetch the row we are about to delete
            $stmt = $connection->prepare("SELECT * FROM `$table` WHERE `$id_col` = ?");
            $stmt->execute([$id_val]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return; // nothing to archive
 
            // 2. Build display fields
            $lname = $row['lname'] ?? '';
            $fname = $row['fname'] ?? '';
            $mi    = (!empty($row['mi'])) ? ' ' . $row['mi'] . '.' : '';
            $full_name = trim("$lname, $fname$mi");
 
            // Build summary based on type
            switch ($record_type) {
                case 'resident':
                    $summary = implode(' ', array_filter([
                        $row['houseno'] ?? '', $row['street'] ?? '',
                        ($row['brgy'] ?? ''), ($row['municipal'] ?? ''),
                        '|', ($row['sex'] ?? ''), '| Age:', ($row['age'] ?? '')
                    ]));
                    break;
                case 'certofres':
                case 'certofindigency':
                    $summary = 'Purpose: ' . ($row['purpose'] ?? '') . ' | Date: ' . ($row['date'] ?? '');
                    break;
                case 'clearance':
                    $summary = 'Purpose: ' . ($row['purpose'] ?? '') . ' | Status: ' . ($row['status'] ?? '');
                    break;
                case 'bspermit':
                    $summary = 'Business: ' . ($row['bsname'] ?? '') . ' | Industry: ' . ($row['bsindustry'] ?? '');
                    break;
                case 'blotter':
                    $narrative = $row['narrative'] ?? '';
                    $summary = 'Narrative: ' . (strlen($narrative) > 80 ? substr($narrative, 0, 80) . '...' : $narrative);
                    break;
                case 'youth':
                    $summary = 'Age: ' . ($row['age'] ?? '') . ' | Educ: ' . ($row['educ_attain'] ?? '') . ' | Skill: ' . ($row['skill_name'] ?? '');
                    break;
                case 'brgyid':
                    $summary = ($row['houseno'] ?? '') . ' ' . ($row['street'] ?? '') . ', ' . ($row['brgy'] ?? '') . ' | Bdate: ' . ($row['bdate'] ?? '');
                    break;
                case 'staff':
                    $summary = 'Role: ' . ($row['role'] ?? '') . ' | Email: ' . ($row['email'] ?? '');
                    break;
                default:
                    $summary = '';
            }
 
            // 3. Get deleted_by from session if available
            $deleted_by = null;
            if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['fname'], $_SESSION['lname'])) {
                $deleted_by = $_SESSION['lname'] . ', ' . $_SESSION['fname'];
            }
 
            // 4. Insert into archive
            $ins = $connection->prepare(
                "INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data, deleted_by)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $ins->execute([
                $record_type,
                $id_val,
                $full_name,
                $summary,
                json_encode($row, JSON_UNESCAPED_UNICODE),
                $deleted_by
            ]);
 
        } catch (Exception $e) {
            // Never let archiving break a delete — fail silently
            error_log('archive_record error: ' . $e->getMessage());
        }
    }
 
 
    // Close DB connection
    public function closeConn() {
        $this->con = null;
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // AUTHENTICATION & SESSION HANDLING
    // ─────────────────────────────────────────────────────────────────────────
    public function login() {
        if(isset($_POST['login'])) {
            $identity       = $_POST['login_identity'];
            $password_input = $_POST['password'];
 
            $connection = $this->openConn();
 
            // 1. Check ADMIN table
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
 
            // 2. Check USER (Staff) table
            $stmt = $connection->prepare("SELECT * FROM tbl_user WHERE email = ? OR phone_number = ?");
            $stmt->execute([$identity, $identity]);
            $user = $stmt->fetch();
 
            if($user && password_verify($password_input, $user['password'])) {
                if($user['role'] == 'user') {
                    $this->set_userdata($user);
                    echo "<script>window.location.href='staff_dashboard.php';</script>";
                    exit();
                }
            }
 
            // 3. Check RESIDENT table
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
 
            // Invalid credentials
            notif('Invalid Credentials. Please check your Email/Phone and Password.', 'error');
        }
    }
 
    public function logout(){
        if(!isset($_SESSION)) { session_start(); }
        $_SESSION['userdata'] = null;
        unset($_SESSION['userdata']);
    }
 
    public function get_userdata(){
        if(!isset($_SESSION)) { session_start(); }
        return $_SESSION['userdata'];
 
        if(!isset($_SESSION['userdata'])) {
            return $_SESSION['userdata'];
        } else {
            return null;
        }
    }
 
    public function set_userdata($array) {
        if(!isset($_SESSION)) { session_start(); }
 
        $_SESSION['userdata'] = array(
            "id_admin"     => $array['id_admin'],
            "id_resident"  => $array['id_resident'],
            "id_user"      => $array['id_user'],
            "emailadd"     => $array['email'],
            "password"     => $array['password'],
            "surname"      => $array['lname'],
            "firstname"    => $array['fname'],
            "mname"        => $array['mi'],
            "age"          => $array['age'],
            "sex"          => $array['sex'],
            "status"       => $array['status'],
            "address"      => $array['address'],
            "contact"      => $array['contact'],
            "bdate"        => $array['bdate'],
            "bplace"       => $array['bplace'],
            "nationality"  => $array['nationality'],
            "family_role"  => $array['family_role'],
            "role"         => $array['role'],
            "houseno"      => $array['houseno'],
            "street"       => $array['street'],
            "brgy"         => $array['brgy'],
            "relation"     => $array['relation'],
            "municipal"    => $array['municipal']
        );
        return $_SESSION['userdata'];
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // ADMIN CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function create_admin() {
        if(isset($_POST['add_admin'])) {
            $email    = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $lname    = $_POST['lname'];
            $fname    = $_POST['fname'];
            $mi       = $_POST['mi'];
            $role     = $_POST['role'];
 
            if ($this->check_admin($email) == 0) {
                $connection = $this->openConn();
                $stmt = $connection->prepare("INSERT INTO tbl_admin (`email`,`password`,`lname`,`fname`,`mi`,`role`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$email, $password, $lname, $fname, $mi, $role]);
                notif('Administrator account added successfully.', 'success');
            } else {
                notif('An account with that email already exists.', 'warning');
            }
        }
    }
 
    public function get_single_admin($id_admin){
        $id_admin = $_GET['id_admin'];
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_admin where id_admin = ?");
        $stmt->execute([$id_admin]);
        $admin = $stmt->fetch();
        return ($stmt->rowCount() > 0) ? $admin : false;
    }
 
    public function admin_changepass() {
        if(isset($_POST['admin_changepass'])) {
            $id_admin    = $_POST['id_admin'] ?? null;
            $oldpassword = $_POST['oldpassword'] ?? '';
            $newpassword = $_POST['newpassword'] ?? '';
            $checkpassword = $_POST['checkpassword'] ?? '';
 
            if (empty($id_admin)) {
                notif('Error: Admin ID is missing. Please re-login.', 'error');
                return;
            }
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("SELECT `password` FROM tbl_admin WHERE id_admin = ?");
            $stmt->execute([$id_admin]);
            $result = $stmt->fetch();
 
            if (!$result) {
                notif('Admin user not found.', 'error');
                return;
            }
 
            if (!password_verify($oldpassword, $result['password'])) {
                notif('Old Password is Incorrect.', 'error');
            } elseif ($newpassword !== $checkpassword) {
                notif('New Passwords do not match.', 'warning');
            } elseif (empty($newpassword)) {
                notif('New password cannot be empty.', 'warning');
            } else {
                $hashed_new = password_hash($newpassword, PASSWORD_DEFAULT);
                $stmt = $connection->prepare("UPDATE tbl_admin SET password = ? WHERE id_admin = ?");
                $stmt->execute([$hashed_new, $id_admin]);
                echo "<script>showNotif('Password updated successfully!', 'success'); setTimeout(() => window.location.href='admn_dashboard.php', 2000);</script>";
            }
        }
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // ANNOUNCEMENT CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function create_announcement() {
        if(isset($_POST['create_announce'])) {
            $event      = $_POST['event'];
            $start_date = $_POST['start_date'];
            $addedby    = $_POST['addedby'];
            $uploaded_images = [];
 
            if(isset($_FILES['announcement_img']) && !empty($_FILES['announcement_img']['name'][0])) {
                $upload_dir = "uploads/";
                if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
 
                foreach($_FILES['announcement_img']['name'] as $key => $name) {
                    if($_FILES['announcement_img']['error'][$key] == 0) {
                        $file_ext  = pathinfo($name, PATHINFO_EXTENSION);
                        $new_name  = time() . '_' . uniqid() . '.' . $file_ext;
                        $target_file = $upload_dir . $new_name;
                        if(move_uploaded_file($_FILES['announcement_img']['tmp_name'][$key], $target_file)) {
                            $uploaded_images[] = $new_name;
                        }
                    }
                }
            }
 
            $image_string = !empty($uploaded_images) ? implode(',', $uploaded_images) : null;
            $connection   = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_announcement (`event`,`start_date`,`addedby`,`image`,`status`) VALUES (?, ?, ?, ?, 'active')");
            $stmt->execute([$event, $start_date, $addedby, $image_string]);
 
            $count = count($uploaded_images);
            notif("Announcement added with {$count} image(s).", 'success');
            header('refresh:0');
        }
    }
 
    public function get_single_announcement($id_announcement) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_announcement WHERE id_announcement = ?");
        $stmt->execute([$id_announcement]);
        return $stmt->fetch();
    }
 
    public function view_announcement(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_announcement ORDER BY id_announcement DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function update_announcement() {
        if (isset($_POST['update_announce'])) {
            $id_announcement = $_GET['id_announcement'];
            $event      = $_POST['event'];
            $start_date = $_POST['start_date'];
            $end_date   = $_POST['end_date'];
            $addedby    = $_POST['addedby'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_announcement SET event=?, start_date=?, end_date=?, addedby=? WHERE id_announcement=?");
            $stmt->execute([$event, $start_date, $end_date, $addedby, $id_announcement]);
 
            notif('Announcement updated successfully.', 'success');
            header("refresh: 0");
        }
    }
 
public function admin_delete_announcement(){
    if(isset($_POST['delete_announcement'])) {
        $id_announcement = $_POST['id_announcement'];
        $connection = $this->openConn();

        // Fetch image paths before deleting
        $stmt = $connection->prepare("SELECT image FROM tbl_announcement WHERE id_announcement = ?");
        $stmt->execute([$id_announcement]);
        $row = $stmt->fetch();

        // Delete associated image files
        if($row && !empty($row['image'])) {
            foreach(explode(',', $row['image']) as $img) {
                $file_path = "uploads/" . trim($img);
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        // Delete the record
        $stmt = $connection->prepare("DELETE FROM tbl_announcement WHERE id_announcement = ?");
        $stmt->execute([$id_announcement]);

        // Use session flash message instead of inline script
        session_start();
        $_SESSION['notif_message'] = 'Announcement and all images deleted.';
        $_SESSION['notif_type']    = 'success';

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
 
    public function delete_announcement($user_id){
        if(isset($_POST['delete_announcement'])) {
            $id_announcement = $_POST['id_announcement'];
            $this->hide_announcement($user_id, $id_announcement);
            $current_page = basename($_SERVER['PHP_SELF']);
            echo "<script>showNotif('Announcement removed.', 'info'); setTimeout(() => window.location.href='$current_page', 2000);</script>";
            exit();
        }
    }
 
    public function view_active_announcements($user_id){
        $connection = $this->openConn();
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
        return $stmt->fetchColumn();
    }
 
    public function hide_announcement($user_id, $announcement_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("INSERT INTO tbl_hidden_announcements (user_id, announcement_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $announcement_id]);
    }
 
    public function sendMessage($id_resident, $message_text) {
        try {
            $sql  = "INSERT INTO resident_messages (id_resident, message_text, date_sent) VALUES (:id, :msg, NOW())";
            $stmt = $this->openConn()->prepare($sql);
            $stmt->bindParam(':id',  $id_resident);
            $stmt->bindParam(':msg', $message_text);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Message Error: " . $e->getMessage());
            return false;
        } finally {
            $this->closeConn();
        }
    }
 
    public function getResidentMessages($id_resident) {
        try {
            $sql  = "SELECT * FROM resident_messages WHERE id_resident = ? ORDER BY date_sent DESC";
            $stmt = $this->openConn()->prepare($sql);
            $stmt->execute([$id_resident]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        } finally {
            $this->closeConn();
        }
    }
 
    public function deleteResidentMessage($id_msg) {
        try {
            $connection = $this->openConn();
            $sql  = "DELETE FROM resident_messages WHERE id_message = ?";
            $stmt = $connection->prepare($sql);
            return $stmt->execute([$id_msg]);
        } catch (PDOException $e) {
            notif("Database Error: " . $e->getMessage(), 'error');
            die();
        }
    }
 
    public function sendMessageToAdmin($id_resident, $message_text) {
        try {
            $connection = $this->openConn();
            $sql  = "INSERT INTO admin_messages (id_resident, message_text, date_sent, status) VALUES (?, ?, NOW(), 'unread')";
            $stmt = $connection->prepare($sql);
            return $stmt->execute([$id_resident, $message_text]);
        } catch (PDOException $e) {
            notif("Database Error: " . $e->getMessage(), 'error');
            die();
        }
    }
 
    public function viewMessages() {
        try {
            $connection = $this->openConn();
            $sql  = "SELECT m.*, r.fname, r.lname FROM admin_messages m JOIN tbl_resident r ON m.id_resident = r.id_resident ORDER BY m.date_sent DESC";
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            notif("Database Error: " . $e->getMessage(), 'error');
            die();
        }
    }
 
    public function deleteMessage($id_admin_msg) {
        try {
            $connection = $this->openConn();
            $sql  = "DELETE FROM admin_messages WHERE id_admin_msg = ?";
            $stmt = $connection->prepare($sql);
            return $stmt->execute([$id_admin_msg]);
        } catch (PDOException $e) {
            notif("Database Error: " . $e->getMessage(), 'error');
            die();
        }
    }
 
    public function uploadValidID($id_resident, $file_name, $original_name, $file_type, $message_note = '') {
        try {
            $connection = $this->openConn();
            $sql  = "INSERT INTO tbl_id_uploads (id_resident, file_name, original_name, file_type, message_note, upload_date, status) VALUES (?, ?, ?, ?, ?, NOW(), 'pending')";
            $stmt = $connection->prepare($sql);
            return $stmt->execute([$id_resident, $file_name, $original_name, $file_type, $message_note]);
        } catch (PDOException $e) {
            error_log("uploadValidID Error: " . $e->getMessage());
            return false;
        }
    }
 
    public function delete_upload_record($id_upload) {
        try {
            $con  = $this->openConn();
            $stmt = $con->prepare("SELECT file_name FROM tbl_id_uploads WHERE id_upload = ?");
            $stmt->execute([$id_upload]);
            $file = $stmt->fetch();
 
            if ($file) {
                $path = "uploads/valid_ids/" . $file['file_name'];
                if (file_exists($path)) { unlink($path); }
            }
 
            $stmt = $con->prepare("DELETE FROM tbl_id_uploads WHERE id_upload = ?");
            return $stmt->execute([$id_upload]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
 
    public function getPendingIDUploads() {
        try {
            $connection = $this->openConn();
            $sql  = "SELECT u.*, r.fname, r.lname, r.email, r.phone_number FROM tbl_id_uploads u JOIN tbl_resident r ON u.id_resident = r.id_resident ORDER BY u.upload_date DESC";
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getPendingIDUploads Error: " . $e->getMessage());
            return [];
        }
    }
 
    public function getResidentIDUploads($id_resident) {
        try {
            $connection = $this->openConn();
            $sql  = "SELECT * FROM tbl_id_uploads WHERE id_resident = ? ORDER BY upload_date DESC";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id_resident]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
 
    public function approveResidentVerification($id_resident, $id_upload, $admin_name) {
        try {
            $connection = $this->openConn();
 
            $stmt1 = $connection->prepare("UPDATE tbl_resident SET is_verified = 1, verified_at = NOW(), verified_by = ? WHERE id_resident = ?");
            $stmt1->execute([$admin_name, $id_resident]);
 
            $stmt2 = $connection->prepare("UPDATE tbl_id_uploads SET status = 'approved' WHERE id_upload = ?");
            $stmt2->execute([$id_upload]);
 
            $notice = "✅ Your account has been verified! You can now request barangay certificates and other services.";
            $stmt3  = $connection->prepare("INSERT INTO resident_messages (id_resident, message_text, date_sent) VALUES (?, ?, NOW())");
            $stmt3->execute([$id_resident, $notice]);
 
            return true;
        } catch (PDOException $e) {
            error_log("approveResidentVerification Error: " . $e->getMessage());
            return false;
        }
    }
 
    public function rejectResidentVerification($id_resident, $id_upload, $admin_name, $reason = '') {
        try {
            $connection = $this->openConn();
 
            $stmt1 = $connection->prepare("UPDATE tbl_id_uploads SET status = 'rejected' WHERE id_upload = ?");
            $stmt1->execute([$id_upload]);
 
            $notice = "❌ Your valid ID submission was rejected." . ($reason ? " Reason: " . $reason : "") . " Please upload a clearer or valid government-issued ID.";
            $stmt2  = $connection->prepare("INSERT INTO resident_messages (id_resident, message_text, date_sent) VALUES (?, ?, NOW())");
            $stmt2->execute([$id_resident, $notice]);
 
            return true;
        } catch (PDOException $e) {
            error_log("rejectResidentVerification Error: " . $e->getMessage());
            return false;
        }
    }
 
    public function isResidentVerified($id_resident) {
        try {
            $connection = $this->openConn();
            $sql  = "SELECT is_verified FROM tbl_resident WHERE id_resident = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id_resident]);
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row && $row['is_verified'] == 1;
        } catch (PDOException $e) {
            return false;
        }
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // CERTIFICATE OF RESIDENCY CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function get_single_certofres($id_resident){
        $id_resident = $_GET['id_resident'];
        $connection  = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_rescert where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        return ($stmt->rowCount() > 0) ? $resident : false;
    }
 
    public function create_certofres() {
        if(isset($_POST['create_certofres'])) {
            $id_rescert  = $_POST['id_rescert'];
            $id_resident = $_POST['id_resident'];
            $lname       = $_POST['lname'];
            $fname       = $_POST['fname'];
            $mi          = $_POST['mi'];
            $age         = $_POST['age'];
            $nationality = $_POST['nationality'];
            $houseno     = $_POST['houseno'];
            $street      = $_POST['street'];
            $brgy        = $_POST['brgy'];
            $municipal   = $_POST['municipal'];
            $date        = $_POST['date'];
            $purpose     = $_POST['purpose'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_rescert (`id_rescert`,`id_resident`,`lname`,`fname`,`mi`,`age`,`nationality`,`houseno`,`street`,`brgy`,`municipal`,`date`,`purpose`, `remarks`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
           $stmt->execute([$id_rescert, $id_resident, $lname, $fname, $mi, $age, $nationality, $houseno, $street, $brgy, $municipal, $date, $purpose, ""]);
           
            notif('Application submitted! You will receive a text message for further details.', 'success');
            header("refresh: 0");
        }
    }
 
    public function view_certofres(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_rescert");
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function delete_certofres(){
        if(isset($_POST['delete_certofres'])) {
            $id_rescert = $_POST['id_rescert'];
            $this->archive_record('tbl_rescert', 'id_rescert', $id_rescert, 'certofres');
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_rescert where id_rescert = ?");
            $stmt->execute([$id_rescert]);
            header("Refresh:0");
            exit();
        }
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // CERTIFICATE OF INDIGENCY CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function create_certofindigency() {
        if(isset($_POST['create_certofindigency'])) {
            $id_indigency = $_POST['id_indigency'];
            $id_resident  = $_POST['id_resident'];
            $lname        = $_POST['lname'];
            $fname        = $_POST['fname'];
            $mi           = $_POST['mi'];
            $nationality  = $_POST['nationality'];
            $houseno      = $_POST['houseno'];
            $street       = $_POST['street'];
            $brgy         = $_POST['brgy'];
            $municipal    = $_POST['municipal'];
            $purpose      = $_POST['purpose'];
            $date         = $_POST['date'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_indigency (`id_indigency`,`id_resident`,`lname`,`fname`,`mi`,`nationality`,`houseno`,`street`,`brgy`,`municipal`,`purpose`,`date`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$id_indigency, $id_resident, $lname, $fname, $mi, $nationality, $houseno, $street, $brgy, $municipal, $purpose, $date]);
 
            notif('Application submitted! You will receive a text message for further details.', 'success');
            header("refresh: 0");
        }
    }
 
    public function view_certofindigency(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_indigency");
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function delete_certofindigency(){
        if(isset($_POST['delete_certofindigency'])) {
            $id_indigency = $_POST['id_indigency'];
            $this->archive_record('tbl_indigency', 'id_indigency', $id_indigency, 'certofindigency');
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_indigency where id_indigency = ?");
            $stmt->execute([$id_indigency]);
            header("Refresh:0");
            exit();
        }
    }
 
    public function get_single_certofindigency($id_resident){
        $id_resident = $_GET['id_resident'];
        $connection  = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_indigency where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        return ($stmt->rowCount() > 0) ? $resident : false;
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // BARANGAY CLEARANCE CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function create_brgyclearance() {
        if(isset($_POST['create_brgyclearance'])) {
            $id_clearance = $_POST['id_clearance'];
            $id_resident  = $_POST['id_resident'];
            $lname        = $_POST['lname'];
            $fname        = $_POST['fname'];
            $mi           = $_POST['mi'];
            $purpose      = $_POST['purpose'];
            $houseno      = $_POST['houseno'];
            $street       = $_POST['street'];
            $brgy         = $_POST['brgy'];
            $municipal    = $_POST['municipal'];
            $status       = $_POST['status'];
            $age          = $_POST['age'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_clearance (`id_clearance`,`id_resident`,`lname`,`fname`,`mi`,`purpose`,`houseno`,`street`,`brgy`,`municipal`,`status`,`age`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$id_clearance, $id_resident, $lname, $fname, $mi, $purpose, $houseno, $street, $brgy, $municipal, $status, $age]);
 
            notif('Application submitted! You will receive a text message for further details.', 'success');
            header("refresh: 0");
        }
    }
 
    public function get_single_clearance($id_resident){
        $id_resident = $_GET['id_resident'];
        $connection  = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_clearance where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        return ($stmt->rowCount() > 0) ? $resident : false;
    }
 
    public function view_clearance(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_clearance");
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function delete_clearance(){
        if(isset($_POST['delete_clearance'])) {
            $id_clearance = $_POST['id_clearance'];
            $this->archive_record('tbl_clearance', 'id_clearance', $id_clearance, 'clearance');
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_clearance where id_clearance = ?");
            $stmt->execute([$id_clearance]);
            header("Refresh:0");
            exit();
        }
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // EXTRA / UTILITY FUNCTIONS
    // ─────────────────────────────────────────────────────────────────────────
    public function check_admin($email) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_admin WHERE email = ?");
        $stmt->Execute([$email]);
        return $stmt->rowCount();
    }
 
    public function validate_admin(){
        $userdetails = $this->get_userdata();
        if (isset($userdetails)) {
            if($userdetails['role'] != "administrator") {
                $this->show_404();
            } else {
                return $userdetails;
            }
        }
    }
 
    public function validate_staff() {
        if(isset($userdetails)) {
            if($userdetails['role'] != "administrator" || $userdetails['role'] != "user") {
                $this->show_404();
            } else {
                return $userdetails;
            }
        }
    }
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // DOCUMENT PROCESSING FUNCTIONS
    // ─────────────────────────────────────────────────────────────────────────
    public function create_bspermit() {
        if(isset($_POST['create_bspermit'])) {
            $id_bspermit = $_POST['id_bspermit'];
            $id_resident = $_POST['id_resident'];
            $lname       = $_POST['lname'];
            $fname       = $_POST['fname'];
            $mi          = $_POST['mi'];
            $bsname      = $_POST['bsname'];
            $houseno     = $_POST['houseno'];
            $street      = $_POST['street'];
            $brgy        = $_POST['brgy'];
            $municipal   = $_POST['municipal'];
            $bsindustry  = $_POST['bsindustry'];
            $aoe         = $_POST['aoe'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_bspermit (`id_bspermit`,`id_resident`,`lname`,`fname`,`mi`,`bsname`,`houseno`,`street`,`brgy`,`municipal`,`bsindustry`,`aoe`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$id_bspermit, $id_resident, $lname, $fname, $mi, $bsname, $houseno, $street, $brgy, $municipal, $bsindustry, $aoe]);
 
            notif('Application submitted! You will receive a text message for further details.', 'success');
            header("refresh: 0");
        }
    }
 
    public function get_single_bspermit($id_resident){
        $id_resident = $_GET['id_resident'];
        $connection  = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_bspermit where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        return ($stmt->rowCount() > 0) ? $resident : false;
    }
 
    public function view_bspermit(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_bspermit");
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function delete_bspermit(){
        if(isset($_POST['delete_bspermit'])) {
            $id_bspermit = $_POST['id_bspermit'];
            $this->archive_record('tbl_bspermit', 'id_bspermit', $id_bspermit, 'bspermit');
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_bspermit WHERE id_bspermit = ?");
            $stmt->execute([$id_bspermit]);
            header("Refresh:0");
            exit();
        }
    }
 
    public function update_bspermit() {
        if (isset($_POST['update_bspermit'])) {
            $id_bspermit = $_GET['id_bspermit'];
            $lname       = $_POST['lname'];
            $fname       = $_POST['fname'];
            $mi          = $_POST['mi'];
            $bsname      = $_POST['bsname'];
            $houseno     = $_POST['houseno'];
            $street      = $_POST['street'];
            $brgy        = $_POST['brgy'];
            $municipal   = $_POST['municipal'];
            $bsindustry  = $_POST['bsindustry'];
            $aoe         = $_POST['aoe'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_bspermit SET lname=?, fname=?, mi=?, bsname=?, houseno=?, street=?, brgy=?, municipal=?, bsindustry=?, aoe=? WHERE id_bspermit=?");
            $stmt->execute([$lname, $fname, $mi, $bsname, $houseno, $street, $brgy, $municipal, $bsindustry, $aoe, $id_bspermit]);
 
            notif('Barangay Business Permit updated successfully.', 'success');
            header("refresh: 0");
        }
    }
 
    public function get_single_youth($id_resident){
        $id_resident = $_GET['id_youth'];
        $connection  = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_youth where id_youth = ?");
        $stmt->execute([$id_youth]);
        $resident = $stmt->fetch();
        return ($stmt->rowCount() > 0) ? $youth : false;
    }
 
    public function create_youth() {
        if(isset($_POST['create_youth'])) {
            $lname          = $_POST['lname'];
            $fname          = $_POST['fname'];
            $mi             = $_POST['mi'];
            $age            = $_POST['age'];
            $sex            = $_POST['sex'];
            $civil_status   = $_POST['civil_status'];
            $contact_number = $_POST['contact_number'];
            $email_address  = $_POST['email_address'];
            $educ_attain    = $_POST['educ_attain'];
            $emp_status     = $_POST['emp_status'];
            $skill_name     = $_POST['skill_name'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_youth (lname,fname,mi,age,sex,civil_status,contact_number,email_address,educ_attain,emp_status,skill_name) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$lname, $fname, $mi, $age, $sex, $civil_status, $contact_number, $email_address, $educ_attain, $emp_status, $skill_name]);
 
            notif('Application submitted successfully!', 'success');
            header("refresh: 0");
        }
    }
 
    public function view_youth(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_youth");
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function delete_youth(){
        if(isset($_POST['delete_youth'])) {
            $id_youth = $_POST['id_youth'];
            $this->archive_record('tbl_youth', 'id_youth', $id_youth, 'youth');
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_youth WHERE id_youth = ?");
            $stmt->execute([$id_youth]);
            header("Refresh:0");
            exit();
        }
    }
 
    public function create_brgyid() {
        if(isset($_POST['create_brgyid'])) {
            $id_resident  = $_POST['id_resident'];
            $lname        = $_POST['lname'];
            $fname        = $_POST['fname'];
            $mi           = $_POST['mi'];
            $houseno      = $_POST['houseno'];
            $street       = $_POST['street'];
            $brgy         = $_POST['brgy'];
            $municipal    = $_POST['municipal'];
            $bplace       = $_POST['bplace'];
            $bdate        = $_POST['bdate'];
            $contact      = $_POST['contact'];
            $inc_lname    = $_POST['inc_lname'];
            $inc_fname    = $_POST['inc_fname'];
            $inc_mi       = $_POST['inc_mi'];
            $inc_contact  = $_POST['inc_contact'];
            $inc_houseno  = $_POST['inc_houseno'];
            $inc_street   = $_POST['inc_street'];
            $inc_brgy     = $_POST['inc_brgy'];
            $relation     = $_POST['relation'];
            $inc_municipal = $_POST['inc_municipal'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_brgyid (`id_resident`,`lname`,`fname`,`mi`,`houseno`,`street`,`brgy`,`municipal`,`bplace`,`bdate`,`contact`,`relation`,`inc_lname`,`inc_fname`,`inc_mi`,`inc_contact`,`inc_houseno`,`inc_street`,`inc_brgy`,`inc_municipal`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$id_resident, $lname, $fname, $mi, $houseno, $street, $brgy, $municipal, $bplace, $bdate, $contact, $relation, $inc_lname, $inc_fname, $inc_mi, $inc_contact, $inc_houseno, $inc_street, $inc_brgy, $inc_municipal]);
 
            notif('Application submitted! You will receive a text message for further details.', 'success');
            header("refresh: 0");
        }
    }
 
    public function get_single_brgyid($id_brgyid){
        $id_brgyid  = isset($_GET['id_brgyid']) ? $_GET['id_brgyid'] : $id_brgyid;
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_brgyid WHERE id_brgyid = ?");
        $stmt->execute([$id_brgyid]);
        $resident = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resident ?: false;
    }
 
    public function view_brgyid(){
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT * from tbl_brgyid");
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function delete_brgyid(){
        $id_brgyid = $_POST['id_brgyid'];
        if(isset($_POST['delete_brgyid'])) {
            $this->archive_record('tbl_brgyid', 'id_brgyid', $id_brgyid, 'brgyid');
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_brgyid WHERE id_brgyid = ?");
            $stmt->execute([$id_brgyid]);
            header("Refresh:0");
            exit();
        }
    }
 
    public function create_blotter() {
        if(isset($_POST['create_blotter'])) {
            $id_resident = $_POST['id_resident'];
            $lname       = $_POST['lname'];
            $fname       = $_POST['fname'];
            $mi          = $_POST['mi'];
            $houseno     = $_POST['houseno'];
            $street      = $_POST['street'];
            $brgy        = $_POST['brgy'];
            $municipal   = $_POST['municipal'];
            $contact     = $_POST['contact'];
            $narrative   = $_POST['narrative'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("INSERT INTO tbl_blotter (`id_resident`,`lname`,`fname`,`mi`,`houseno`,`street`,`brgy`,`municipal`,`contact`,`narrative`) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$id_resident, $lname, $fname, $mi, $houseno, $street, $brgy, $municipal, $contact, $narrative]);
 
            notif('Blotter/Complaint submitted successfully.', 'success');
            header("refresh: 0");
        }
    }
 
    public function get_single_blotter($id_resident){
        $id_resident = $_GET['id_resident'];
        $connection  = $this->openConn();
        $stmt = $connection->prepare("SELECT * FROM tbl_blotter where id_resident = ?");
        $stmt->execute([$id_resident]);
        $resident = $stmt->fetch();
        return ($stmt->rowCount() > 0) ? $resident : false;
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
            $this->archive_record('tbl_blotter', 'id_blotter', $id_blotter, 'blotter');
            $connection = $this->openConn();
            $stmt = $connection->prepare("DELETE FROM tbl_blotter WHERE id_blotter = ?");
            $stmt->execute([$id_blotter]);
            header("Refresh:0");
            exit();
        }
    }
 
    public function update_blotter() {
        if (isset($_POST['update_blotter'])) {
            $id_blotter = $_GET['id_blotter'];
            $lname      = $_POST['lname'];
            $fname      = $_POST['fname'];
            $mi         = $_POST['mi'];
            $houseno    = $_POST['houseno'];
            $street     = $_POST['street'];
            $brgy       = $_POST['brgy'];
            $municipal  = $_POST['municipal'];
            $contact    = $_POST['contact'];
            $narrative  = $_POST['narrative'];
 
            $connection = $this->openConn();
            $stmt = $connection->prepare("UPDATE tbl_blotter SET lname=?, fname=?, mi=?, houseno=?, street=?, brgy=?, municipal=?, contact=?, narrative=? WHERE id_blotter=?");
            $stmt->execute([$lname, $fname, $mi, $houseno, $street, $brgy, $municipal, $contact, $narrative, $id_blotter]);
 
            notif('Complaint/Blotter record updated successfully.', 'success');
            header("refresh: 0");
        }
    }
 
    public function add_comment($announcement_id, $user_id, $comment_text) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("INSERT INTO tbl_announcement_comments (announcement_id, user_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$announcement_id, $user_id, $comment_text]);
        return $connection->lastInsertId();
    }
 
    public function get_comments($announcement_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT c.*, CONCAT(r.fname, ' ', r.lname) AS full_name FROM tbl_announcement_comments c JOIN tbl_resident r ON c.user_id = r.id_resident WHERE c.announcement_id = ? ORDER BY c.created_at ASC");
        $stmt->execute([$announcement_id]);
        return $stmt->fetchAll();
    }
 
    public function delete_comment($comment_id, $user_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("DELETE FROM tbl_announcement_comments WHERE id_comment = ? AND user_id = ?");
        $stmt->execute([$comment_id, $user_id]);
    }
 
    public function toggle_reaction($announcement_id, $user_id, $reaction_type) {
        $connection = $this->openConn();
        $check = $connection->prepare("SELECT id_reaction FROM tbl_announcement_reactions WHERE announcement_id = ? AND user_id = ? AND reaction_type = ?");
        $check->execute([$announcement_id, $user_id, $reaction_type]);
        $existing = $check->fetch();
 
        if ($existing) {
            $del = $connection->prepare("DELETE FROM tbl_announcement_reactions WHERE id_reaction = ?");
            $del->execute([$existing['id_reaction']]);
            return 'removed';
        } else {
            $delOld = $connection->prepare("DELETE FROM tbl_announcement_reactions WHERE announcement_id = ? AND user_id = ?");
            $delOld->execute([$announcement_id, $user_id]);
            $ins = $connection->prepare("INSERT INTO tbl_announcement_reactions (announcement_id, user_id, reaction_type, created_at) VALUES (?, ?, ?, NOW())");
            $ins->execute([$announcement_id, $user_id, $reaction_type]);
            return 'added';
        }
    }
 
    public function get_reactions($announcement_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT reaction_type, COUNT(*) as count FROM tbl_announcement_reactions WHERE announcement_id = ? GROUP BY reaction_type");
        $stmt->execute([$announcement_id]);
        return $stmt->fetchAll();
    }
 
    public function get_user_reaction($announcement_id, $user_id) {
        $connection = $this->openConn();
        $stmt = $connection->prepare("SELECT reaction_type FROM tbl_announcement_reactions WHERE announcement_id = ? AND user_id = ?");
        $stmt->execute([$announcement_id, $user_id]);
        $row = $stmt->fetch();
        return $row ? $row['reaction_type'] : null;
    }
 
}
 
$bmis = new BMISClass();
?>