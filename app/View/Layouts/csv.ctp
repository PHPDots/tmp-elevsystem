<?php 
   header('Content-type: application/csv');
   header('Content-Disposition: attachment; filename="'.$filename.'"');

    $csv_file = fopen('php://output', 'w');

    foreach($details as $fields){      
        fputcsv($csv_file,$fields,',','"');
    }

    fclose($csv_file);