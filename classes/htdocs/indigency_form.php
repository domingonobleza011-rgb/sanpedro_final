<?php
require('classes/resident.class.php');
$userdetails = $residentbmis->get_userdata();
$id_resident = $_GET['id_resident'];
$resident = $residentbmis->get_single_certofindigency($id_resident);
  ?>
<!DOCTYPE html>
<html id="clearance">
<style>
   /* General Layout */
body {
    background-color: #f0f0f0;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
}

.certificate {
    width: 210mm; /* A4 Width */
    min-height: 297mm; /* A4 Height */
    padding: 20mm;
    margin: 20px auto;
    background: white;
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative;
    box-sizing: border-box;
}

/* Header Styling */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: center;
}

.seal1 {
    width: 100px;
    height: 100px;
}
.seal2 {
    width: 100px;
    height: 100px;
}

.header-text p {
    margin: 2px 0;
    font-size: 14px;
}

.office-title {
    text-align: center;
    font-family: 'Times New Roman', serif;
    font-size: 22px;
    font-weight: bold;
    color: #003366;
    margin-top: 10px !important;
    border-bottom: 2px solid #003366;
    padding-bottom: 5px;
}

h1 {
    text-align: center;
    font-size: 24px;
    margin-top: 15px !important;
    text-transform: capitalize;
}

/* Body Text */
.content {
    margin-top: 30px !important;
    line-height: 1.5;
    text-align: justify;
    font-size: 16px;
}

.salutation {
    margin-bottom: 30px;
    font-weight: bold;
}

/* Footer & Signature */
.footer {
    margin-top: 70px !important;
    display: flex;
    justify-content: flex-end;
}

.signature-block {
    text-align: center;
    width: 300px;
}

.signature-block .name {
    text-decoration: underline;
    margin-bottom: 0;
}

.watermark-note {
    position: absolute;
    bottom: 40px;
    left: 40px;
    font-size: 12px;
    font-style: italic;
    color: #888;
}

/* Print Settings */
@media print {
    @page {
        size: A4 portrait;
        margin: 0; /* This removes the URL/Date at the top and bottom */
    }

    body {
        margin: 0;
        padding: 0;
    }

    .certificate {
        margin: 0 !important;
        padding: 15mm !important; /* Slightly reduce padding to fit content */
        width: 210mm;
        height: 297mm; /* Force exact A4 height */
        border: none !important;
        box-shadow: none !important;
        overflow: hidden; /* Prevents a stray line from creating Page 2 */
    }
    .header {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
        /* Ensure the header has enough space at the top */
        margin-top: 0 !important; 
    }

    .noprint {
        display: none !important;
    }
}
</style>

 <head>
    <meta charset="UTF-8">
    <title><?= $resident['lname'];?>,     <?= $resident['fname'];?>    <?= $resident['mi'];?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../BarangaySystem/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- font Awesome -->
    <link href="../BarangaySystem/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="../BarangaySystem/bootstrap/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="../BarangaySystem/bootstrap/css/morris-0.4.3.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../BarangaySystem/bootstrap/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link href="./BarangaySystem/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="../BarangaySystem/bootstrap/css/select2.css" rel="stylesheet" type="text/css" />
    <script src="../BarangaySystem/bootstrap/css/jquery-1.12.3.js" type="text/javascript"></script>  
    
</head>
 <body class="skin-black" >
     <!-- header logo: style can be found in header.less -->
    
    
     <?php 
     
     include "classes/conn.php"; 

     ?>         
                
               <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certification of Indigency</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="certificate">
       <div class="header" style="width: 100%; margin-bottom: 20px;">
    <div style="float: left; width: 100px;">
        <img src="icons/logo.png" class="seal1" alt="Barangay Seal" style="display: block;">
    </div>
    
    <div class="header-text" style="display: inline-block; text-align: center; width: calc(100% - 220px);">
        <p>Republic of the Philippines</p>
        <p>Province of Camarines Sur</p>
        <p>City of Iriga</p>
        <p><strong>Barangay San Pedro</strong></p>
    </div>

    <div style="float: right; width: 100px;">
        <img src="icons/Documents/seal.png" class="seal2" alt="City Seal" style="display: block;">
    </div>
    <div style="clear: both;"></div> <!-- Clears the floats -->
</div>

        <div class="office-title">
            OFFICE OF THE PUNONG BARANGAY
        </div>

        <h1>Certification of Indigency</h1>

        <div class="content">
            <p class="salutation">TO WHOM IT MAY CONCERN:</p>
            
            <p>This is to certify that <strong><?= $resident['lname'];?>, <?= $resident['fname'];?> <?= $resident['mi'];?></strong>, legal age, Married, and a bonafide resident of this barangay with postal address at <?= $resident['houseno'];?> <?= $resident['street'];?> <?= $resident['brgy'];?> <?= $resident['municipal'];?>.</p>
            
            <p>This said person is a good moral character and an active member of the community. She is one of those who belong to <strong>INDIGENT family</strong>.</p>
            
            <p>This certification is being issued upon the request of the interested party for <strong><?= $resident['purpose'];?></strong> and whatever legal purpose/s this may serve.</p>
            
            <p>Issued this <strong><?= $resident['date'];?></strong> at Barangay San Pedro, Iriga City, Philippines.</p>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p>Certified by:</p>
                <br><br>
                <p class="name"><strong>JOSEPH B. BEBONIA</strong></p>
                <p>PUNONG BARANGAY</p>
            </div>
        </div>
        
        <div class="watermark-note">
            Not valid w/o official seal
        </div>
    </div>
</body>
</html>
    <div class="text-center">
        <button type="button" class="btn btn-success noprint" onclick="window.print()">
            Print Certificate
        </button>
    </div>
    </body>
    <?php
    
    ?>


    <script>
        window.onload = function() {
        window.print();
    };
    
    // Optional: Close the window/tab automatically after printing or canceling
    window.onafterprint = function() {
        window.close();
    };
function PrintElem(elem) {
    // This ensures any elements with the 'noprint' class are hidden
    // and the window focus is on the current document for the printer.
    window.focus();
    window.print();
}

    function Popup(data) 
    {
        var mywindow = window.open('', 'my div', 'height=400,width=1000');
        //mywindow.document.write('<html><head><title>my div</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        //mywindow.document.write('</head><body class="skin-black" >');
         var printButton = document.getElementById("printpagebutton");
        //Set the print button visibility to 'hidden' 
        printButton.style.visibility = 'hidden';
        mywindow.document.write(data);
        //mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();

        printButton.style.visibility = 'visible';
        mywindow.close();

        return true;
    }
    </script>
</html>