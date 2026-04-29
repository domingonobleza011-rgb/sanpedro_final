<?php
    error_reporting(E_ALL ^ E_WARNING);
    require('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_admin();

    $type       = $_GET['type'] ?? 'registrations';
    $id_program = (int)($_GET['id_program'] ?? 0);

    if (!$id_program) { die('Program ID required.'); }

    $prog = $bmis->get_single_program($id_program);
    if (!$prog) { die('Program not found.'); }

    $safeName = preg_replace('/[^a-z0-9_]/i', '_', $prog['title']);

    header('Content-Type: text/csv; charset=utf-8');

    if ($type === 'attendance') {
        $data = $bmis->get_attendance_report($id_program);
        header('Content-Disposition: attachment; filename="attendance_' . $safeName . '_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['#', 'Full Name', 'Email', 'Phone', 'Registration Code', 'Registration Date', 'Attended', 'Attendance Date']);
        foreach ($data as $i => $row) {
            fputcsv($out, [
                $i + 1,
                $row['firstname'] . ' ' . $row['surname'],
                $row['email'],
                $row['phone_number'],
                $row['registration_code'],
                $row['registration_date'],
                $row['attended'] ? 'Yes' : 'No',
                $row['attendance_date'] ?? 'N/A'
            ]);
        }
    } else {
        $data = $bmis->get_program_registrations($id_program);
        header('Content-Disposition: attachment; filename="registrations_' . $safeName . '_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['#', 'Full Name', 'Email', 'Phone', 'Age', 'Registration Code', 'Status', 'Date Registered']);
        foreach ($data as $i => $row) {
            fputcsv($out, [
                $i + 1,
                $row['firstname'] . ' ' . $row['surname'],
                $row['email'],
                $row['phone_number'],
                $row['age'] ?? 'N/A',
                $row['registration_code'],
                $row['status'],
                $row['registration_date']
            ]);
        }
    }

    fclose($out);
    exit;