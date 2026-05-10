<?php
    define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php'); 
    require('classes/main.class.php');
    require('classes/resident.class.php');
    
    $userdetails = $bmis->get_userdata();
    $bmis->create_youth();


?>

<!DOCTYPE html>

<html>
  <head> 
    <title> Barangay Management System </title>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- responsive tags for screen compatibility -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- custom css --> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
        <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>

            /* Navbar Buttons */

            .btn1 {
            border-radius: 20px;
            border: none; /* Remove borders */
            color: white; /* White text */
            font-size: 16px; /* Set a font size */
            cursor: pointer; /* Mouse pointer on hover */
            margin-left: 23%;
            padding: 8px 22px;
            }

            .btn2 {
            border-radius: 20px;
            border: none; /* Remove borders */
            color: white; /* White text */
            font-size: 16px; /* Set a font size */
            cursor: pointer; /* Mouse pointer on hover */
            padding: 8px 22px;
            margin-left: .1%;
            }

            .btn3 {
            border-radius: 20px;
            border: none; /* Remove borders */
            color: white; /* White text */
            font-size: 16px; /* Set a font size */
            cursor: pointer; /* Mouse pointer on hover */
            padding: 8px 22px;
            margin-left: .1%;
            }

            .btn4 {
            border-radius: 20px;
            border: none; /* Remove borders */
            color: white; /* White text */
            font-size: 16px; /* Set a font size */
            cursor: pointer; /* Mouse pointer on hover */
            padding: 8px 22px;
            margin-left: .1%;
            }

            .btn5 {
            border-radius: 20px;
            border: none; /* Remove borders */
            color: white; /* White text */
            font-size: 16px; /* Set a font size */
            cursor: pointer; /* Mouse pointer on hover */
            padding: 8px 22px;
            margin-left: .1%;
            }

            /* Darker background on mouse-over */
            .btn1:hover {
            background-color: RoyalBlue;
            color: black;
            }

            .btn2:hover {
            background-color: RoyalBlue;
            color: black;
            }

            .btn3:hover {
            background-color: RoyalBlue;
            color: black;
            }

            .btn4:hover {
            background-color: RoyalBlue;
            color: black;
            }

            .btn5:hover {
            background-color: RoyalBlue;
            color: black;
            }

            /* Back-to-Top */

            .top-link {
            transition: all 0.25s ease-in-out;
            position: fixed;
            bottom: 0;
            right: 0;
            display: inline-flex;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            margin: 0 3em 3em 0;
            border-radius: 50%;
            padding: 0.25em;
            width: 80px;
            height: 80px;
            background-color: #3661D5;
            }
            .top-link.show {
            visibility: visible;
            opacity: 1;
            }
            .top-link.hide {
            visibility: hidden;
            opacity: 0;
            }
            .top-link svg {
            fill: white;
            width: 24px;
            height: 12px;
            }
            .top-link:hover {
            background-color: #3498DB;
            }
            .top-link:hover svg {
            fill: #000000;
            }

            .screen-reader-text {
            position: absolute;
            clip-path: inset(50%);
            margin: -1px;
            border: 0;
            padding: 0;
            width: 1px;
            height: 1px;
            overflow: hidden;
            word-wrap: normal !important;
            clip: rect(1px, 1px, 1px, 1px);
            }
            .screen-reader-text:focus {
            display: block;
            top: 5px;
            left: 5px;
            z-index: 100000;
            clip-path: none;
            background-color: #eee;
            padding: 15px 23px 14px;
            width: auto;
            height: auto;
            text-decoration: none;
            line-height: normal;
            color: #444;
            font-size: 1em;
            clip: auto !important;
            }

            .container1
            {
                background-color: #3498DB;
                height: 342px;
                color: black;
                font-family: Arial, Helvetica, sans-serif;
                text-align: center;
            }

            .idpicture{
                border: 1px solid black;
            }

            .applybutton
            {
                width: 100% !important;
                height: 50px !important;
                border-radius: 20px;
                margin-top: 5%;
                margin-bottom: 8%;
                font-size: 25px;
                letter-spacing: 2px;
            }

            .paa
            {
                margin-top: 10px;
                position: relative;
                left: -28%;
            }

            .text1{
                margin-top: 30px;
                font-size: 50px;
            }

            .picture{
                height: 120px;
                width: 120px;
            }

            /* width */
            ::-webkit-scrollbar {
            width: 5px;
            }

            /* Track */
            ::-webkit-scrollbar-track {
            background: #f1f1f1; 
            }
            
            /* Handle */
            ::-webkit-scrollbar-thumb {
            background: #888; 
            }

            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
            background: #555; 
            }

            .card4 {
                width: 195px;
                height: 210px;
                overflow: auto;
                margin: auto;
                color: white;
            }

            .card3 {
                width: 195px;
                height: 210px;
                overflow: hidden;
                margin: auto;
                color: white;
            }

            .card2 {
                width: 195px;
                height: 210px;
                overflow: auto;
                margin: auto;
                color: white;
            }

            .card1 {
                width: 195px;
                height: 210px;
                overflow: auto;
                margin: auto;
                color: white;
            }

            a{
                color:white;
                }
            .shfooter .collapse {
                display: inherit;
            }
                @media (max-width:767px) {
            .shfooter ul {
                    margin-bottom: 0;
            }

            .shfooter .collapse {
                    display: none;
            }

            .shfooter .collapse.show {
                    display: block;
            }

            .shfooter .title .fa-angle-up,
            .shfooter .title[aria-expanded=true] .fa-angle-down {
                    display: none;
            }

            .shfooter .title[aria-expanded=true] .fa-angle-up {
                    display: block;
            }

            .shfooter .navbar-toggler {
                    display: inline-block;
                    padding: 0;
            }

            }

            .resize {
                text-align: center;
            }
            .resize {
                margin-top: 3rem;
                font-size: 1.25rem;
            }
            /*RESIZESCREEN ANIMATION*/
            .fa-angle-double-right {
                animation: rightanime 1s linear infinite;
            }

            .fa-angle-double-left {
                animation: leftanime 1s linear infinite;
            }
            @keyframes rightanime {
                50% {
                    transform: translateX(10px);
                    opacity: 0.5;
            }
                100% {
                    transform: translateX(10px);
                    opacity: 0;
            }
            }
            @keyframes leftanime {
                50% {
                    transform: translateX(-10px);
                    opacity: 0.5;
            }
                100% {
                    transform: translateX(-10px);
                    opacity: 0;
            }
            }

            /* Contact Chip */

            .chip {
            display: inline-block;
            padding: 0 25px;
            height: 50px;
            line-height: 50px;
            border-radius: 25px;
            background-color: #2C54C1;
            margin-top: 5px;
            }

            .chip img {
            float: left;
            margin: 0 10px 0 -25px;
            height: 50px;
            width: 50px;
            border-radius: 50%;
            }

            .zoom {
            transition: transform .3s;
            }

            .zoom:hover {
            -ms-transform: scale(1.4); /* IE 9 */
            -webkit-transform: scale(1.4); /* Safari 3-8 */
            transform: scale(1.4); 
            }
            .container1 img {
    /* Prevents icons from getting too large on desktop */
    max-height: 120px; 
    object-fit: contain;
}

@media (max-width: 576px) {
    .text1 {
        font-size: 1.8rem; /* Shrinks the title slightly on phones */
    }
}
                .mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 65px;
    background-color: #ffffff;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1050;
    border-top: 1px solid #dee2e6;
}

.mobile-bottom-nav .nav-item {
    text-decoration: none;
    color: #6c757d;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 0.7rem; /* Small text for mobile */
    font-weight: 500;
}

.mobile-bottom-nav .nav-item i {
    font-size: 1.4rem; /* Larger icons for easy tapping */
    margin-bottom: 2px;
}

.mobile-bottom-nav .nav-item:active {
    color: #0d6efd;
}

/* Add padding to the bottom of the body so content isn't hidden by the nav */
@media (max-width: 767px) {
    body {
        padding-bottom: 80px;
    }
}

        </style>
  </head>

    <body>

        <!-- Back-to-Top and Back Button -->

        <a data-toggle="tooltip" title="Back-To-Top" class="top-link hide" href="" id="js-top">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg>
            <span class="screen-reader-text">Back to top</span>
        </a>

        <!-- Eto yung navbar -->

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top d-none d-md-block shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="resident_homepage.php">
            <i class="bi bi-building-fill me-2"></i> Barangay San Pedro
        </a>
        <div class="d-flex ms-auto">
            <a href="resident_homepage.php" class="btn btn-primary me-1"><i class="bi bi-house-door-fill me-1"></i> Home</a>
            <a href="resident_announcement.php" class="btn btn-primary me-1"><i class="bi bi-megaphone-fill me-1"></i> Announcements</a>
            <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-person-badge me-1"></i> Profile</a>
            <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-shield-lock me-1"></i> Password</a>
            <a href="logout.php" class="btn btn-danger ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- MOBILE BOTTOM NAV (Hidden on Desktop) -->
<div class="mobile-bottom-nav d-md-none">
    <a href="resident_homepage.php" class="nav-item">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </a>
    <a href="resident_announcement.php" class="nav-item">
        <i class="bi bi-megaphone-fill"></i>
        <span>News</span>
    </a>
    <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-person-badge"></i>
        <span>Profile</span>
    </a>
    <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-shield-lock"></i>
        <span>Pass</span>
    </a>
    <a href="logout.php" class="nav-item text-danger">
        <i class="bi bi-box-arrow-right"></i>
        <span>Exit</span>
    </a>
</div>


        <div class="container-fluid container1 py-4"> 
    <div class="row justify-content-center text-center"> 
        <div class="col-12 col-md-10 col-lg-8"> 
            
            <div class="header mb-4">
                <h1 class="text1 display-4 fw-bold">Youth Profiling</h1>
       
            </div>

            <div class="row g-3 justify-content-center align-items-center">
                <div class="col-4 col-sm-3">
                    <img class="img-fluid" src="icons/Documents/docu1.png" alt="Document 1">
                </div>
                <div class="col-4 col-sm-3">
                    <img class="img-fluid" src="icons/Documents/docu3.png" alt="Document 3">
                </div>
                <div class="col-4 col-sm-3">
                    <img class="img-fluid" src="icons/Documents/docu2.png" alt="Document 2">
                </div>
            </div>

        </div>
    </div>
</div>

        <div id="down3"></div>

        <br>
       
        <div class="container text-center">
            <div class="row">
                <div class="col">
                    <h1>Procedure</h1>
                    <hr style="background-color: black;">
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col">
                    <i class="fas fa-laptop fa-7x"></i>

                    <br>
                    <br>

                    <h3>Step 1: Fill-Up</h3>
                    <p>First step is to Fill-Up the entire form in our system.</p>
                </div>

                <div class="col">
                    <i class="fas fa-user-check fa-7x"></i>

                    <br>
                    <br>

                    <h3>Step 2: Assessment</h3>
                    <p>Second step is to verify all of the information you've been given
                    in our system </p>
                </div>

                <div class="col">
                    <i class="fas fa-file fa-7x"></i>

                    <br>
                    <br>

                    <h3>Step 3: Release</h3>
                    <p>Verified</p>
                </div>
            </div>

            <div id="down2"></div>

            <br>
           
        <div id="down1"></div>

        <br>
        <br>
        <br>

        <!-- Button trigger modal -->

       <div class="container py-4">
    <h1 class="text-center font-weight-bold">Registration</h1>
    <hr class="mb-4" style="border-top: 2px solid black; opacity: 1;">

    <div class="col-12 text-center">   
    <!-- Added 'btn-block' for older Bootstrap or 'w-100' for Bootstrap 4/5 -->
    <!-- Added 'py-3' for extra vertical height and 'mb-4' for spacing -->
    <button type="button" 
            class="btn btn-primary btn-lg w-100 w-md-auto py-3 px-5 shadow-sm" 
            style="font-weight: 600; font-size: 1.2rem;"
            data-toggle="modal" 
            data-target="#exampleModalCenter">
        <i class="fas fa-edit mr-2"></i> Youth Profiling
    </button>
</div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 15px; overflow: hidden; border: none;">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="exampleModalCenterTitle">
                        <i class="fas fa-id-card mr-2"></i> Youth Profile Registration
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="post" class="was-validated" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        
                        <!-- Name Section -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Last Name</label>
                                    <input name="lname" type="text" class="form-control" placeholder="Required" value="<?= isset($userdetails['lname']) ? htmlspecialchars($userdetails['lname']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">First Name</label>
                                    <input name="fname" type="text" class="form-control" placeholder="Required" value="<?= isset($userdetails['fname']) ? htmlspecialchars($userdetails['fname']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">M.I.</label>
                                    <input name="mi" type="text" class="form-control" placeholder="Optional" value="<?= isset($userdetails['mi']) ? htmlspecialchars($userdetails['mi']) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Demographics Section -->
                        <div class="row mt-2">
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Age</label>
                                    <input name="age" type="number" class="form-control" value="<?= isset($userdetails['age']) ? htmlspecialchars($userdetails['age']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Sex</label>
                                    <select name="sex" class="form-control custom-select" required>
                                        <option value="" disabled>Select</option>
                                        <option value="Male" <?= (isset($userdetails['sex']) && $userdetails['sex'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= (isset($userdetails['sex']) && $userdetails['sex'] == 'Female') ? 'selected' : '' ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Civil Status</label>
                                    <select name="civil_status" class="form-control custom-select" required>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Solo Parent">Solo Parent</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Header -->
                        <div class="mt-4 mb-2">
                            <h6 class="text-primary font-weight-bold"><i class="fas fa-at mr-1"></i> Contact & Education</h6>
                            <hr class="mt-1">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Contact Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text small">+63</span></div>
                                        <input type="text" class="form-control" name="contact_number" placeholder="9XXXXXXXXX" required pattern="\d{10}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Email Address</label>
                                    <input type="email" class="form-control" name="email_address" placeholder="name@example.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Educational Attainment</label>
                                    <input type="text" class="form-control" name="educ_attain" placeholder="e.g. College Undergraduate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-uppercase">Employment Status</label>
                                    <select name="emp_status" class="form-control custom-select" required>
                                        <option value="Employed">Employed</option>
                                        <option value="Unemployed">Unemployed</option>
                                        <option value="Self-Employed">Self-Employed</option>
                                        <option value="Student">Student</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold small text-uppercase">Special Skills / Interests</label>
                                    <textarea class="form-control" name="skill_name" rows="2" placeholder="e.g. Graphic Design, Public Speaking, Sports" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3">
                        <input name="id_youth" type="hidden" value="<?= isset($userdetails['id_resident']) ? $userdetails['id_resident'] : ''; ?>">
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                        <button name="create_youth" type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
            </form>
        </div>
    </div>
</div>
        

        <br>
        <br>
        <br>

       
        <script>
            // Add the following code if you want the name of the file appear on select
            $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });
        </script>

        <script>
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#blah')
                            .attr('src', e.target.result)
                            .width(200)
                            .height(200);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>

        <script>
            $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
            });
        </script>

        <script>
            $(document).ready(function(){
            // Add smooth scrolling to all links
            $("a").on('click', function(event) {

                // Make sure this.hash has a value before overriding default behavior
                if (this.hash !== "") {
                // Prevent default anchor click behavior
                event.preventDefault();

                // Store hash
                var hash = this.hash;

                // Using jQuery's animate() method to add smooth page scroll
                // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                $('html, body').animate({
                    scrollTop: $(hash).offset().top
                }, 800, function(){

                    // Add hash (#) to URL when done scrolling (default click behavior)
                    window.location.hash = hash;
                });
                } // End if
            });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>

    </body>
</html>
