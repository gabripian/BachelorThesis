<?php
    //file delle funzioni per interagire con il controller
    require __DIR__ . '/function.php';

    //si ricavano gli id degli switch presenti nella rete
    $switch_id_array = get_switch_id();
    //si conta il numero di switch
    $count = count($switch_id_array);

    //array che contiene la abnda di ciascuna porta
    $bandwidth_array= array();
    
    //si ricava la banda di ciascuna porta
    $bandwidth_array=get_bandwidth($switch_id_array);

    //oggetto html contenente la tabella aggiornata
    $bandwidth_Table_HTML = '';

    //nuova tabella dei flussi
    $bandwidth_Table_HTML .= '<table>';
    $bandwidth_Table_HTML .= '<tr>';
    $bandwidth_Table_HTML .= '<th>switch id</th>';
    $bandwidth_Table_HTML .= '<th>port number</th>';
    $bandwidth_Table_HTML .= '<th>receive throughput (bps)</th>';
    $bandwidth_Table_HTML .= '<th>transmit throughput (bps)</th>';
    $bandwidth_Table_HTML .= '<th>max throughput recv (bps)</th>';
    $bandwidth_Table_HTML .= '<th>max throughput tran (bps)</th>';
    $bandwidth_Table_HTML .= '<th>inactive time recv (sec)</th>';
    $bandwidth_Table_HTML .= '<th>inactive time tran (sec)</th>';
    $bandwidth_Table_HTML .= '<th>duration (sec)</th>';
    $bandwidth_Table_HTML .= '</tr>';

    //si scorre per ciascuno switch
    for($i=0; $i< count($bandwidth_array); $i++){

        $row=$bandwidth_array[$i];
        $row1=explode("-",$row);
                    
        $bandwidth_Table_HTML .= '<tr>';
        $bandwidth_Table_HTML .= '<td>' .$row1[0]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[1]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[2]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[3]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[5]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[6]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[7]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[8]. '</td>';
        $bandwidth_Table_HTML .= '<td>' .$row1[4]. '</td>';
        $bandwidth_Table_HTML .= '</tr>';
  
    }
    
    $bandwidth_Table_HTML .= '</table>';

    //restituisce la tabella aggiornata
    echo $bandwidth_Table_HTML;
?>