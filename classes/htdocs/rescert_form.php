<?php
ini_set('display_errors',0);
require('classes/resident.class.php');
$userdetails = $residentbmis->get_userdata();
$id_resident = $_GET['id_resident'];
$resident = $residentbmis->get_single_certofres($id_resident);
  ?>
<!DOCTYPE html>
<html id="clearance">
<style>
    @media print {
        .noprint {
        visibility: hidden;
         }
    }
    @page { size: auto;  margin: 3mm; }
    
    .card {
  transition: 0.3s;
  width: 55%;
  display: flex;
  
  
}

.card:hover {
  box-shadow:none;
}
.login-logo {
    width: 300px;               /* Adjust size as needed */
    height: auto;
    margin-right: 5px;        /* Adds space between logo and the word */
}

.text {
  font-size: 20px;
  padding-left: 20px;
}
 /* Container holding the image and the text */
 .container {
  justify-content: center;
  align-items: center;
  text-align: left;
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
      <body class="skin-black">
    <?php include "classes/conn.php"; ?>

    <body class="skin-black">
    <?php include "classes/conn.php"; ?>

    <style>
        @media print {
            .noprint { display: none !important; }
            @page { size: landscape; margin: 0; }
            body { margin: 0; padding: 0; }
            .main-wrapper { border: none !important; width: 100% !important; height: 100vh; }
        }

        .main-wrapper { display: flex; background: white; font-family: 'Arial', sans-serif; }
        .sidebar { width: 280px; border-right: 3px double #000; padding: 30px 15px; text-align: center; }
        .content-area { flex-grow: 1; padding: 40px 60px; }
        .official-p { margin-bottom: 30px; line-height: 1.1; }
        .official-name { margin-bottom: 10px; font-weight: bold; display: block; font-size: 14px; text-transform: uppercase; }
        .official-title { margin-bottom: 30px; font-size: 10px; display: block; font-style: italic; color: #444; }
    </style>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certification of Residency - <?php echo $resident['lname']; ?></title>
    <style>
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
    margin-top: 20px;
    border-bottom: 2px solid #003366;
    padding-bottom: 5px;
}

h1 {
    text-align: center;
    font-size: 28px;
    margin-top: 30px;
    text-transform: capitalize;
}

/* Body Text */
.content {
    margin-top: 50px;
    line-height: 1.8;
    text-align: justify;
    font-size: 16px;
}

.salutation {
    margin-bottom: 30px;
    font-weight: bold;
}

/* Footer & Signature */
.footer {
    margin-top: 80px;
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
    body { background: none; }
    .certificate {
        margin: 0;
        border: none;
        box-shadow: none;
    }
    @page {
        size: A4;
        margin: 0;
    }
}
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <img src="icons/logo.png" class="seal1 left" alt="Barangay Seal">
            <div class="header-text">
                <p>Republic of the Philippines</p>
                <p>Province of Camarines Sur</p>
                <p>City of Iriga</p>
                <p><strong>Barangay San Pedro</strong></p>
            </div>
            <img src="icons/Documents/seal.png" class="seal2 right" alt="City Seal">
        </div>

        <div class="office-title">
            OFFICE OF THE PUNONG BARANGAY
        </div>

        <h1>CERTIFICATION OF RESIDENCY</h1>

        <div class="content">
            <p class="salutation">TO WHOM IT MAY CONCERN:</p>
            
            <p>This is to certify that <strong><?= $resident['lname'];?>, <?= $resident['fname'];?> <?= $resident['mi'];?></strong>, <strong><?= $resident['age'];?></strong> years of age, born on</p>
            
            <p><strong><?= $resident['date'];?></strong> Filipino citizen, whose specimen signature appears below, is a.</p>
            <p>resident of <strong><?= $resident['houseno'];?> <?= $resident['street'];?> <?= $resident['brgy'];?> <?= $resident['municipal'];?>.</strong></p>
            
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
</html>

    <button 
        type="button" 
        class="btn btn-success noprint" 
        id="printpagebutton" 
        style="padding: 12px 40px; font-size: 18px; font-weight: bold; border-radius: 5px;" 
        onclick="PrintElem('#clearance')">
        Print 
    </button>
</body>
    <?php
    
    ?>


    <script>
         function PrintElem(elem)
    {
        window.print();
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
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