<?php 
    error_reporting(E_ALL ^ E_WARNING);
    require('classes/resident.class.php');
    ini_set('display_errors',0);
    $userdetails = $residentbmis->get_userdata();
    $id_resident = $_GET['id_resident'];
    $resident = $residentbmis->get_single_resident($id_resident);
    

    $residentbmis->profile_update();

?>

<!DOCTYPE html> 
<html>

    <head> 
    <title> Barangay Management System </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <style>

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

        /* Navbar Buttons */

        .btn3 {
        border-radius: 20px;
        border: none; /* Remove borders */
        color: white; /* White text */
        font-size: 16px; /* Set a font size */
        cursor: pointer; /* Mouse pointer on hover */
        margin-left: 23%;
        padding: 8px 22px;
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
        margin-left: .1%;
        padding: 8px 22px;
        }

        .btn6 {
        border-radius: 20px;
        border: none; /* Remove borders */
        color: white; /* White text */
        font-size: 16px; /* Set a font size */
        cursor: pointer; /* Mouse pointer on hover */
        padding: 8px 22px;
        margin-left: .1%;
        }


        /* Darker background on mouse-over */

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

        .btn6:hover {
        background-color: RoyalBlue;
        color: black;
        }

        .footerlinks{
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
        .navbar { padding: 0.8rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .dropdown-menu { border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .dropdown-menu .btn { text-align: left; width: 100%; padding: 10px 20px; font-size: 0.9rem; }
        .dropdown-menu .btn:hover { background: #f8f9fa; }
    </style>
    <body> 

        <!-- Back-to-Top and Back Button -->

        <a data-toggle="tooltip" title="Back-To-Top" class="top-link hide" href="" id="js-top">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg>
            <span class="screen-reader-text">Back to top</span>
        </a>

        <!-- Eto yung navbar -->

        <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="resident_homepage.php">Barangay San Pedro Management System</a>
        
        <div class="d-flex align-items-center ms-auto">
            <a href="resident_homepage.php" class="btn btn-primary me-3">
                <i class="fa fa-home fa-lg"></i> Home
            </a>
            
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-1"></i>
                    <?= $userdetails['surname'];?>, <?= $userdetails['firstname'];?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="btn" href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>"><i class="fas fa-user"></i> &nbsp; Profile</a></li>
                    <li><a class="btn" href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>"><i class="fas fa-lock"></i> &nbsp; Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="btn text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp; Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

        <div id="down2"></div>

        <br>

        <div class="container"> 
            <div class="card" style="margin-top: 2em;">  
                    <div class="card-header bg-primary text-white" style="font-size:20px"> Personal Information </div>
                <div class="card-body"> 
                    <form method="post">

                        <h6>
                            <i class="fas fa-user-circle"></i>
                            View Information
                        </h6>

                        <hr>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Last Name:</label>
                                    <input class="form-control" value="<?= $resident['lname'];?>" >
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>First Name:</label>
                                    <input class="form-control" value="<?= $resident['fname'];?>" >
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Middle Name:</label>
                                    <input class="form-control" value="<?= $resident['mi'];?>" >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Email:</label>
                                    <input class="form-control" value="<?= $resident['email'];?>" >
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Sex:</label>
                                    <input class="form-control" value="<?= $resident['sex'];?>" >
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Nationality:</label>
                                    <input  class="form-control" value="<?= $resident['nationality'];?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
    <div class="col">
        <div class="form-group">
            <label>Birth Date:</label>
            <input type="date" class="form-control" name="bdate" value="<?= $resident['bdate'];?>">
        </div>
    </div>
</div>
                            <div class="col">
                                <div class="form-group" id="down1">
                                    <label>Birth Place:</label>
                                    <input class="form-control" value="<?= $resident['bplace'];?>" >
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6>
                            <i class="fas fa-user-edit"></i>
                            Update Information
                        </h6>

                        <hr>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Age:</label>
                                    <input class="form-control" type="number" name="age" value="<?= $resident['age'];?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <input class="form-control" type="text" name="status" value="<?= $resident['status'];?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Contact:</label>
                                    <input class="form-control" type="tel" name="contact" maxlength="11" pattern="[0-9]{11}" value="<?= $resident['contact'];?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>House No:</label>
                                    <input class="form-control" type="text" name="houseno" value="<?= $resident['houseno'];?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Street:</label>
                                    <input class="form-control" type="text" name="street" value="<?= $resident['street'];?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Barangay:</label>
                                    <input class="form-control" type="text" name="brgy" value="<?= $resident['brgy'];?>">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row" style="margin-bottom: 5px;"> 
                            <div class="col-xl-12">
                                <div class="form-inline">
                                    <input class="form-control" name="lname" type="hidden" value="<?= $resident['lname'];?>"/>
                                    <input class="form-control" name="mi" type="hidden" value="<?= $resident['mi'];?>" />
                                    <button type="submit button" class="btn btn-info" style="margin-left: 37%; width:143px;"  name="search_household">View Household</button>
                                    <button class="btn btn-primary" style="margin-left: .2%; width:143px;" type="submit" name="profile_update"> Update </button>
                                    <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>"></a>   
                                    <div>
                                        <br><br>
                                        <?php include'testingsearch.php'?>  
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

        <!-- Footer -->

        <footer id="footer" class="bg-primary text-white d-flex-column text-center">
            <hr class="mt-0">

            <div class="text-center">
                <h1 class="text-white">Services</h1>
                <ul class="list-unstyled list-inline">

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Documents">
                    <i class="fas fa-file fa-2x"></i>
                    </a>
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a href="#!" class="footerlinks sbtn btn-large mx-1" title="Card">
                    <i class="fas fa-id-card fa-2x"></i>
                    </a>
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Friend">
                    <i class="fas fa-user-friends fa-2x"></i>
                    </a>
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Blotter">
                    <i class="fas fa-user-shield fa-2x"></i>
                    </a>
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Contact">
                    <i class="fas fa-phone fa-2x"></i>
                    </a>
                </li>
                </ul>
            </div>

            <hr class="mb-0">

            <!--Footer Links-->

            <div class="container text-left text-md-center">
                <div class="row">

                    <!--First column-->

                    <div class="col-md-3 mx-auto shfooter">
                        <h5 class="my-2 font-weight-bold d-none d-md-block">Documentation</h5>
                        <div class="d-md-none title" data-target="#Documentation" data-toggle="collapse">
                            <div class="mt-3 font-weight-bold">Documentation
                                <div class="float-right navbar-toggler">
                                    <i class="fas fa-angle-down"></i>
                                    <i class="fas fa-angle-up"></i>
                                </div>
                            </div>
                        </div>
                        <ul class="list-unstyled collapse" id="Documentation">
                            <li><a class="footerlinks" href="services_certofres.php">Certificate of Residency</a></li>
                            <li><a class="footerlinks" href="services_brgyclearance.php">Barangay Clearance</a></li>
                            <li><a class="footerlinks" href="services_certofindigency.php">Certificate of Indigency</a></li>
                            <li><a class="footerlinks" href="services_business.php">Business Permit</a></li>
                            <li><a class="footerlinks" href="services_brgyid.php">Barangay ID</a></li>
                        </ul>
                    </div>

                    <!--/.First column-->

                    <hr class="clearfix w-100 d-md-none mb-0">

                    <!--Third column-->

                    <div class="col-md-3 mx-auto shfooter">
                        <h5 class="my-2 font-weight-bold d-none d-md-block">Other Services</h5>
                        <div class="d-md-none title" data-target="#OtherServices" data-toggle="collapse">
                            <div class="mt-3 font-weight-bold">Other Services
                                <div class="float-right navbar-toggler">
                                    <i class="fas fa-angle-down"></i>
                                    <i class="fas fa-angle-up"></i>
                                </div>
                            </div>
                        </div>

                        <ul class="list-unstyled collapse" id="OtherServices">
                            <li><a class="footerlinks" href="services_blotter.php">Peace and Order</a></li>
                        </ul>
                        <ul class="list-unstyled collapse" id="OtherServices">
                            <li><a class="footerlinks" href="services_blotter.php">Youth Profiling</a></li>
                        </ul>
                    </div>

                    <!--/.Third column-->

                    <hr class="clearfix w-100 d-md-none mb-0">
 

                </div>
            </div>

            <!--/.Footer Links-->

            <hr class="mb-0">


        </footer>
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
                <script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>

    </body>
</html>
