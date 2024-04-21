<?php
    //file delle funzioni per interagire con il controller
    require __DIR__ . '/function.php';

    //si ricavano gli id degli switch presenti nella rete
    $switch_id_array = get_switch_id();
    //si conta il numero di switch
    $count = count($switch_id_array);

    //array che contiene le informazioni di un singolo switch
    $single_switch=array();

    //oggetto html contenente la tabella aggiornata
    $port_Table_HTML = '';

    //nuova tabella dei flussi
    $port_Table_HTML .= '<table>';
    $port_Table_HTML .= '<tr>';
    $port_Table_HTML .= '<th>switch id</th>';
    $port_Table_HTML .= '<th>port number</th>';
    $port_Table_HTML .= '<th>state</th>';
    $port_Table_HTML .= '<th>receive packets</th>';
    $port_Table_HTML .= '<th>transmit packets</th>';
    $port_Table_HTML .= '<th>receive bytes</th>';
    $port_Table_HTML .= '<th>transmit bytes</th>';
    $port_Table_HTML .= '<th>receive dropped</th>';
    $port_Table_HTML .= '<th>receive dropped</th>';
    $port_Table_HTML .= '<th>receive errors</th>';
    $port_Table_HTML .= '<th>transmit errors</th>';
    $port_Table_HTML .= '<th>duration (sec)</th>';
    $port_Table_HTML .= '</tr>';

    //si scorre per ciascuno switch
    for($i=0; $i< $count; $i++){

        //array contenente lo stato attivo/non attivo di ogni porta degli switch
        $state=array();

        //si ricava lo stato di tutte le interfacce dello switch $switch_id_array[$i]
        $state=get_port_state($switch_id_array, $i);

        //si ricavano le righe della tabella porte dello switch
        $single_switch=get_port_table_raw($switch_id_array, $i);

        //per ciascuna porta si ottengono 10 informazioni, dividendo di un fattore 10 si ottiene il numero di porte di ogni switch
        $single_switch_length=count($single_switch)/10;

        //utilizzando l'algoritmo bubble sort si ordina il vettore in base al numero di porta crescente, si scambiano 10 elementi consecutivi con altri 10 elementi
        //consecutivi dato che ogni 10 elementi dell'array sono riferiti ad un'unica porta
        $single_switch=array_sort($single_switch, $single_switch_length);

        //se il numero supera una certa cifra si rappresenta in notazione scientifica
        for($j=1; $j<count($single_switch); $j++){
                        
            if($single_switch[$j] > 10000000){
                
                $single_switch[$j] = sprintf('%.2e', $single_switch[$j]);
                  
            }
        }

        for($l=0; $l<$single_switch_length; $l++){

            $port_Table_HTML .= '<tr>';
            $port_Table_HTML .= '<td>' .$switch_id_array[$i]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[0]. '</td>';
            $port_Table_HTML .= '<td>' .$state[$l]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[1]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[2]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[3]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[4]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[5]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[6]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[7]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[8]. '</td>';
            $port_Table_HTML .= '<td>' .$single_switch[9]. '</td>';
            $port_Table_HTML .= '</tr>';

            //si fanno avanzare i dati di 10 posizioni
            array_splice($single_switch, 0, 10);        
        }
    }

    $port_Table_HTML .= '</table>';

    //restituisce la tabella dei flussi aggiornata
    echo $port_Table_HTML;
?>