<?php 
    define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php'); 
    require('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $id_resident = $_GET['id_resident'];
    $resident = $residentbmis->get_single_resident($id_resident);

    $bmis->create_blotter();



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

            /* Modal */

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

            /* Under Navbar */

            .container1 {
                position: relative;
                font-family: Arial;
                background-color: lightblue;
            }
            .container {
                justify-content: center;
                position: relative;
            }
            .text-block {
                position: absolute;
                bottom: 35%;
                right: 20%;
                background-color: black; 
                opacity: .7;
                color: white;
                padding-left: 20px;
                padding-right: 20px;
                border-radius: 20px;
            }

            /* Slideshow */

            * {
            box-sizing: border-box;
            }

            .picture {
            position: relative;
            left: -15px;
            width: 102.7%;
            }

            .picture1{
                height: 100px;
            }

            /* Position the image container (needed to position the left and right arrows) */
            .container2 {
            position: relative;
            }

            /* Hide the images by default */
            .mySlides {
            display: none;
            }

            /* Add a pointer when hovering over the thumbnail images */
            .cursor {
            cursor: grabbing;
            }

            /* Next & previous buttons */
            .prev,
            .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 30px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            border-radius: 0 3px 3px 0;
            user-select: none;
            -webkit-user-select: none;
            cursor: grab;
            }

            /* Position the "next button" to the right */
            .next {
            right: 15px;
            border-radius: 3px 0 0 3px;
            }

            /* On hover, add a black background color with a little bit see-through */
            .prev:hover,
            .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
            }

            /* Container for image text */
            .caption-container {
            position: relative;
            left: -15px;
            text-align: center;
            background-color: #222;
            padding: 5px;
            color: white;
            width: 102.7%;
            font-size: 25px;
            }

            .row:after {
            content: "";
            display: table;
            clear: both;
            }

            /* Six columns side by side */
            .column {
            width: 16.66%;
            }

            /* Add a transparency effect for thumnbail images */
            .demo {
            opacity: 0.6;
            }

            .active,
            .demo:hover {
            opacity: 1;
            }


            .paa{
                margin-top: 20px;
                position: relative;
                left: -28%;
            }

            /* Card Flip */

            .container3{
                margin-top: 3%;
            }

            .flip-card {
                background-color: transparent;
                width: 300px;
                height: 300px;
                perspective: 1000px;
            }

            .flip-card-inner {
                position: relative;
                width: 100%;
                height: 100%;
                text-align: center;
                transition: transform 0.6s;
                transform-style: preserve-3d;
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            }

            .flip-card:hover .flip-card-inner {
                transform: rotateY(180deg);
            }

            .flip-card-front, .flip-card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
            }

            .flip-card-front {
                color: white;
            }

            .flip-card-back {
                padding: 7px;
                color: white;
                transform: rotateY(180deg);
            }

            /* Footer */

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
            .picture{
                height: 120px;
                width: 120px;
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
    <a data-toggle="tooltip" title="Back to Top" class="top-link hide" href="#top" id="js-top">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg>
        <span class="screen-reader-text">Back to top</span>
    </a>

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


    <section class="container my-5 py-4">
        <h2 class="text-center fw-bold">Blotter</h2>
        <hr class="mx-auto border-dark shadow-sm" style="width: 100px; height: 3px;">
        
        <div class="row g-4 justify-content-center align-items-center mt-4 text-center">
            <div class="col-6 col-md-3"><img class="img-fluid rounded shadow-sm" src="icons/3.jpg" alt="Incident Type 1"></div>
            <div class="col-6 col-md-3"><img class="img-fluid rounded shadow-sm" src="icons/1.jpg" alt="Incident Type 2"></div>
            <div class="col-6 col-md-3"><img class="img-fluid rounded shadow-sm" src="icons/2.jpg" alt="Incident Type 3"></div>
            <div class="col-6 col-md-3"><img class="img-fluid rounded shadow-sm" src="icons/4.jpg" alt="Incident Type 4"></div>
        </div>
    </section>

<section class="container my-5 py-5">
    <h2 class="text-center fw-bold mb-2">Public Information & Guidelines</h2>
    <p class="text-center text-muted mb-4">Please review the procedures and definitions regarding the Barangay Blotter system.</p>
    <hr class="mx-auto mb-5" style="width: 60px; height: 3px; background-color: #007bff; opacity: 1;">

    <div class="row row-cols-1 row-cols-md-3 g-4">
        
        <div class="col">
            <div class="flip-card shadow-sm">
                <div class="flip-card-inner">
                    <div class="flip-card-front bg-white border d-flex flex-column justify-content-center align-items-center p-4">
                        <div class="icon-circle bg-light mb-3">
                            <i class="fas fa-file-signature fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Filing Procedure</h5>
                        <p class="text-muted small">Standard operating steps</p>
                    </div>
                    <div class="flip-card-back bg-primary text-white p-4 d-flex flex-column justify-content-center">
                        <h6 class="fw-bold mb-3"><i class="fas fa-list-ol me-2"></i>How to File</h6>
                        <ul class="list-unstyled small text-start">
                            <li class="mb-2"><strong>Step 1: </strong> Complete the digital incident report form accurately.</li>
                            <li class="mb-2"><strong>Step 2:</strong> Our officers will review the case details for validity.</li>
                            <li class="mb-0"><strong> Step 3:</strong> An appointment will be scheduled for both parties.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="flip-card shadow-sm">
                <div class="flip-card-inner">
                    <div class="flip-card-front bg-white border d-flex flex-column justify-content-center align-items-center p-4">
                        <div class="icon-circle bg-light mb-3">
                            <i class="fas fa-balance-scale fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold text-dark">System Definition</h5>
                        <p class="text-muted small">What is a Blotter?</p>
                    </div>
                    <div class="flip-card-back bg-primary text-white p-4 d-flex flex-column justify-content-center">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Official Record</h6>
                        <p class="small lh-base">The Barangay Blotter is an official daily log of events and complaints reported to local authorities, serving as a primary legal document for community mediation.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="flip-card shadow-sm">
                <div class="flip-card-inner">
                    <div class="flip-card-front bg-white border d-flex flex-column justify-content-center align-items-center p-4">
                        <div class="icon-circle bg-light mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Primary Purpose</h5>
                        <p class="text-muted small">Why we record incidents</p>
                    </div>
                    <div class="flip-card-back bg-primary text-white p-4 d-flex flex-column justify-content-center">
                        <h6 class="fw-bold mb-3"><i class="fas fa-target-camera me-2"></i>Objectives</h6>
                        <p class="small lh-base">To maintain a factual written history of incidents, support law enforcement in documentation, and facilitate fair conflict resolution within the Barangay.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
</body>

        <!-- Button trigger modal -->

<div class="container container4 py-4">
    <h1 class="text-center font-weight-bold">Resident Complain</h1>
    <hr style="border-top: 2px solid #000; opacity: 1;">

<div class="col-12 text-center">   
    <!-- Added 'btn-block' for older Bootstrap or 'w-100' for Bootstrap 4/5 -->
    <!-- Added 'py-3' for extra vertical height and 'mb-4' for spacing -->
    <button type="button" 
            class="btn btn-primary btn-lg w-100 w-md-auto py-3 px-5 shadow-sm" 
            style="font-weight: 600; font-size: 1.2rem;"
            data-toggle="modal" 
            data-target="#exampleModalCenter">
        <i class="fas fa-edit mr-2"></i> File  a Complain
    </button>
</div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="exampleModalCenterTitle">
                        <i class="fas fa-folder-open mr-2"></i> Complain Form Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body px-4 py-4">
                    <form method="post" class="was-validated" enctype="multipart/form-data"> 

                        <!-- Complainant Info Section Header -->
                        <div class="mb-3">
                            <h6 class="text-muted font-weight-bold small text-uppercase">Resident Information</h6>
                            <hr class="mt-1">
                        </div>

                        <div class="row"> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Last name:</label>
                                    <input name="lname" type="text" class="form-control bg-light" value="<?= $resident['lname']?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small">First name:</label>
                                    <input name="fname" type="text" class="form-control bg-light" value="<?= $resident['fname']?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Middle name:</label>
                                    <input name="mi" type="text" class="form-control bg-light" value="<?= $resident['mi']?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Age</label>
                                    <input name="age" type="number" class="form-control bg-light" value="<?= $resident['age']?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">            
                                    <label class="font-weight-bold small">Contact Number:</label>
                                    <input name="contact" type="text" maxlength="11" class="form-control bg-light" value="<?= $resident['contact']?>" required>
                                </div>
                            </div>
                        </div>            

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold small">House No:</label>
                                    <input type="text" class="form-control bg-light" name="houseno" value="<?= $resident['houseno']?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Street:</label>
                                    <input type="text" class="form-control bg-light" name="street" value="<?= $resident['street']?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Barangay:</label>
                                    <input type="text" class="form-control bg-light" name="brgy" value="<?= $resident['brgy']?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Municipality:</label>
                                    <input type="text" class="form-control bg-light" name="municipal" value="<?= $resident['municipal']?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Incident Details Section Header -->
                        <div class="mt-4 mb-3">
                            <h6 class="text-muted font-weight-bold small text-uppercase text-primary">Incident Report Details</h6>
                            <hr class="mt-1 border-primary">
                        </div>

                        <!-- Styled Guidelines Box -->
                        <div class="card bg-light mb-3 border-left-info shadow-sm">
                            <div class="card-body py-2 px-3">
                                <h6 class="font-weight-bold mb-1 small text-info"><i class="fas fa-info-circle mr-1"></i> Submission Guidelines:</h6>
                                <ul class="mb-0 text-muted" style="font-size: 13px; padding-left: 20px;">
                                    <li>Use simple words; avoid complex terminology.</li>
                                    <li>Be specific; avoid profanity and bad language.</li>
                                    <li>Ensure the report is clear and easy to read.</li>
                                    <li><strong>Do not</strong> use Emojis or special Symbols.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="report" class="font-weight-bold">Narrative Report:</label>
                            <textarea class="form-control border-primary" rows="6" id="report" name="narrative" 
                                      placeholder="Describe the incident (Who, What, Where, When, Why)..." 
                                      required style="border-width: 2px;"></textarea>
                            <div class="invalid-feedback font-italic">Narrative report is required for submission.</div>
                        </div>

                        <div class="modal-footer bg-light px-0 pb-0 pt-3">
                            <input name="id_resident" type="hidden" value="<?= $resident['id_resident']?>">
                            <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="create_blotter" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Submit Blotter
                            </button>
                        </div> 
                    
                    </form>
                </div>
            </div>
        </div>
    </div>  
</div>
        <br>
    <br>
    <br>
    
        <script>
// This makes the filename appear inside the input box after you select it
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>
        <script>
            var slideIndex = 1;
            showSlides(slideIndex);

            function plusSlides(n) {
            showSlides(slideIndex += n);
            }

            function currentSlide(n) {
            showSlides(slideIndex = n);
            }

            function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("demo");
            var captionText = document.getElementById("caption");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            captionText.innerHTML = dots[slideIndex-1].alt;
            }
        </script>

        <script>
           // This makes the file name appear inside the label after selection
$('.custom-file-input').on('change', function() {
   let fileName = $(this).val().split('\\').pop();
   $(this).next('.custom-file-label').addClass("selected").html(fileName);
});
        </script>

        <script>
            function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            // Assuming you have an <img> tag with id="preview"
            $('#preview').attr('src', e.target.result);
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