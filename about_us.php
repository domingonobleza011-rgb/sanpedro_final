<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Overview | Brgy. San Pedro Iriga City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/dist/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js" integrity="sha512-/HL24m2nmyI2+ccX+dSHphAHqLw60Oj5sK8jf59VWtFWZi9vx7jzoxbZmcBeeTeCUc7z1mTs3LfyXGuBU32t+w==" crossorigin="anonymous"></script>
      <!-- responsive tags for screen compatibility -->
      <meta name="viewport" content="width=device-width, initial-scale=1"><!-- bootstrap css --> 
      <link href="../BarangaySystem/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
      <!-- fontawesome icons --> 
      <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 60px 0 80px 0;
            border-bottom: 5px solid #ffc107; /* Added a gold accent line */
        }
        .feature-icon {
            font-size: 3.5rem;
            color: #1e3c72;
            margin-bottom: 1rem;
            display: block;
        }
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
            
    </style>
</head>
<body class="text-center"> <header class="hero-section">
        <div class="container">
            <a href="resident_homepage.php" class="btn-home-outline">
                <i class="bi bi-house-door-fill me-2"></i> RETURN TO HOME
            </a>
<br><br><br> <hr style="
    border: 2px solid #000; 
    opacity: 1; 
    width: 100vw; 
    position: relative; 
    left: 50%; 
    right: 50%; 
    margin-left: -50vw; 
    margin-right: -50vw;">
            <h1 class="display-3 fw-bold">System Overview</h1>
            <p class="lead text-uppercase tracking-wider opacity-75">Smart Integrated Barangay Records & Youth Engagement System</p>
            <p class="h5 fw-light">Barangay San Pedro, Iriga City</p>
        </div>
    </header>
    <main class="container my-5">
        
        <section class="row justify-content-center mb-10">
            <div class="col-lg-10">
                <h2 class="fw-bold mb-4">About the System</h2>
                <p class="text-secondary fs-5">
                    This platform modernizes the administrative operations of Barangay San Pedro. 
                    By transitioning to a digital framework, we ensure faster document processing, 
                    accurate resident data management, and a robust tracking system for community-specific records.
                </p>
            </div>
        </section>

        <hr class="my-5 w-50 mx-auto">

        <h2 class="fw-bold mb-5">Core System Functions</h2>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm p-4">
                    <div class="card-body">
                        <i class="bi bi-people-fill feature-icon"></i>
                        <h4 class="fw-bold">Resident Profiling</h4>
                        <p class="text-muted">A centralized database for demographic info, family backgrounds, and residency verification.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm p-4">
                    <div class="card-body">
                        <i class="bi bi-person-arms-up feature-icon"></i>
                        <h4 class="fw-bold">Youth Profiling</h4>
                        <p class="text-muted">Specialized tracking for youth demographics and skills to support local SK programs and scholarships.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm p-4">
                    <div class="card-body">
                        <i class="bi bi-shield-exclamation feature-icon"></i>
                        <h4 class="fw-bold">Blotter Records</h4>
                        <p class="text-muted">Secure digital recording of incidents, mediation schedules, and legal peace-and-order documentation.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm p-4">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-pdf-fill feature-icon"></i>
                        <h4 class="fw-bold">Document Issuance</h4>
                        <p class="text-muted">Automated generation of Barangay Clearances, Residency, and Indigency Certificates.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm p-4">
                    <div class="card-body">
                        <i class="bi bi-shop feature-icon"></i>
                        <h4 class="fw-bold">Business Permits</h4>
                        <p class="text-muted">Efficient tracking and processing of local business permits and commercial jurisdiction records.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm p-4">
                    <div class="card-body">
                        <i class="bi bi-info-circle-fill feature-icon"></i>
                        <h4 class="fw-bold">Resident Messaging</h4>
                        <p class="text-muted">A direct communication line for residents to inquire about services and receive official updates.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-primary text-white py-5 mt-5">
        <div class="container">
            <p class="mb-1 fw-bold">&copy; 2026 Barangay San Pedro, Iriga City.</p>
            <p class="text-dark small mb-0">Developed for Smart Integrated Barangay Management</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>