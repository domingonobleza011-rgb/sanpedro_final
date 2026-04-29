<?php
require('classes/resident.class.php');
$userdetails = $residentbmis->get_userdata();
$id_resident = $_GET['id_resident'];
$resident = $residentbmis->get_single_brgyid($id_resident);

include "classes/conn.php"; 

date_default_timezone_set('Asia/Manila');
$date_issued = date('F j, Y'); 
$date_expires = date('F j, Y', strtotime('+1 year')); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ID Print - <?= $resident['lname'];?></title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; background-color: #f0f0f0; margin: 0; padding: 10px; }
        
        /* Layout for Single Page Vertical Stack */
        .print-container {
            display: flex;
            flex-direction: column; /* This stacks them top and bottom */
            align-items: center;
            gap: 15px; /* Space between front and back */
            background: white;
            padding: 20px;
            width: fit-content;
            margin: auto;
            border-radius: 8px;
        }

        @media print {
            body { background: white; padding: 0; }
            .noprint { display: none; }
            .print-container { padding: 0; width: 100%; border: none; }
            .id-card { page-break-inside: avoid; } /* Prevents splitting a card across pages */
        }

        /* ID Card Dimensions */
        .id-card {
            width: 3.25in;
            height: 4.75in;
            border: 1.5px solid #000;
            background-color: #fff;
            position: relative;
            box-sizing: border-box;
            padding: 10px;
            text-align: center;
        }

        /* Front Content Styling */
        .header-text { font-size: 9px; line-height: 1.2; text-transform: uppercase; }
        .header-title { font-weight: bold; font-size: 13px; margin: 4px 0; }
        .office-title { color: #2c5e8c; font-weight: bold; font-size: 10px; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }

        .photo-box {
            width: 130px;
            height: 130px;
            border: 1px solid #000;
            margin: 10px auto;
            background: #f9f9f9;
        }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .id-number { font-size: 16px; font-weight: bold; color: #000; margin-top: 5px; }
        .data-label { font-size: 9px; text-transform: uppercase; border-top: 1px solid #000; width: 85%; margin: 2px auto 12px auto; padding-top: 2px; display: block; font-weight: normal; }
        
        .name-text { font-weight: bold; font-size: 14px; text-transform: uppercase; padding: 0 5px; }
        .address-text { font-size: 12px; height: 35px; line-height: 1.2; }

        .sig-name { font-weight: bold; font-size: 11px; text-transform: uppercase; margin-top: 25px; }
        .sig-title { font-size: 9px; border-top: 1px solid #000; width: 60%; margin: 2px auto; padding-top: 2px; }

        /* Back Content Styling */
        .back-content { text-align: left; font-size: 11px; padding: 10px; margin-top: 5px; }
        .back-row { margin-bottom: 20px; border-bottom: 1px solid #000; position: relative; padding-bottom: 2px; min-height: 15px; }
        .back-row span { font-weight: bold; position: absolute; left: 80px; bottom: 2px; }
        
        .footer-note { 
            font-size: 9px; 
            font-style: italic; 
            position: absolute; 
            bottom: 15px; 
            left: 15px; 
            text-align: left; 
            width: 90%;
        }
    </style>
</head>
<body>

    <div style="text-align:center; margin-bottom: 20px;">
        <button type="button" class="noprint" onclick="window.print()" style="padding: 12px 40px; cursor:pointer; font-weight:bold; background:#28a745; color:white; border:none; border-radius:5px; font-size: 16px;">
            PRINT BARANGAY ID
        </button>
    </div>

    <div class="print-container">
        
        <div class="id-card">
            <div class="header-text">Republic of the Philippines</div>
            <div class="header-text">Province of Camarines Sur</div>
            <div class="header-text">City of Iriga</div>
            <div class="header-title">Barangay San Pedro</div>
            <div class="office-title">OFFICE OF THE PUNONG BARANGAY</div>

            <div class="photo-box">
                
            </div>

            <div class="id-number"><?= $resident['bdate'];?>-<?= $resident['id_resident'];?></div>
            <span class="data-label">ID NO.</span>

            <div class="name-text"><?= $resident['fname'];?> <?= $resident['mi'];?> <?= $resident['lname'];?></div>
            <span class="data-label">NAME</span>

            <div class="address-text">Zone <?= $resident['houseno'];?>, San Pedro, Iriga City</div>
            <span class="data-label">ADDRESS</span>

            <div class="sig-name">JOSEPH B. BEBONIA</div>
            <div class="sig-title">Punong Barangay</div>
        </div>

        <div class="id-card">
            <div class="back-content">
              <br><br>
                <div class="back-row">Contact No.: <span><?= $resident['contact'];?></span></div>
                
                <div style="font-weight:bold; margin-bottom:15px; text-decoration: underline;">In case of Emergency:</div>
                <div class="back-row">Name: <span><?= $resident['inc_fname'];?> <?= $resident['inc_lname'];?></span></div>
                <div class="back-row">Relation: <span><?= $resident['relation'];?></span></div>
                <div class="back-row">Contact No: <span><?= $resident['inc_contact'];?></span></div>

                <div style="margin-top: 60px; text-align:center; font-size: 12px;">
                    Issued this <strong><?= $date_issued; ?></strong>
                </div>
            </div>

            <div class="footer-note">
                <strong>Note:</strong><br>
                This ID is valid for 1 year only upon the issuance.
            </div>
        </div>

    </div>

</body>
</html>