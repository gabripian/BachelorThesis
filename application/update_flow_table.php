<?php
    //file delle funzioni per interagire con il controller
    require __DIR__ . '/function.php';

    //si ricavano gli id degli switch presenti nella rete
    $switch_id_array = get_switch_id();
    //si conta il numero di switch
    $count = count($switch_id_array);
    //oggetto html contenente la tabella aggiornata
    $flow_Table_HTML = '';

    //nuova tabella dei flussi
    $flow_Table_HTML .= '<table>';
    $flow_Table_HTML .= '<tr>';
    $flow_Table_HTML .= '<th>switch id</th>';
    $flow_Table_HTML .= '<th>flow count</th>';
    $flow_Table_HTML .= '<th>packet count</th>';
    $flow_Table_HTML .= '<th>byte count</th>';
    $flow_Table_HTML .= '<th>duration (sec)</th>';
    $flow_Table_HTML .= '</tr>';

    for ($i = 0; $i < $count; $i++) {

        $raw = get_flow_table_row($switch_id_array, $i);

        $flow_Table_HTML .= '<tr>';
        $flow_Table_HTML .= '<td>' . $switch_id_array[$i] . '</td>';
        $flow_Table_HTML .= '<td>' . $raw[0] . '</td>';
        $flow_Table_HTML .= '<td>' . $raw[1] . '</td>';
        $flow_Table_HTML .= '<td>' . $raw[2] . '</td>';
        $flow_Table_HTML .= '<td>' . $raw[3] . '</td>';
        $flow_Table_HTML .= '</tr>';
    }

    $flow_Table_HTML .= '</table>';

    //restituisce la tabella dei flussi aggiornata
    echo $flow_Table_HTML;
?>