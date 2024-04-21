<?php
    // Include il file contenente le funzioni per ottenere i dati aggiornati
    require __DIR__ . '/function.php';

    //array contenente le sttistiche di ciascuno switch
    $switch_statistics=array();
    //array contenente la tabella dei flussi per ciascuno switch
    $switch_flow_array=array();
    //array che contiene la abnda di ciascuna porta
    $bandwidth_array= array();

    //si aggiornano i dati utilizzando le funzioni definite nel file function.php
    $switch_id_array = get_switch_id();
    $switch_link_id = get_switch_link();
    $host_switch_link_id = get_host_switch_link();
    $bandwidth_array=get_bandwidth($switch_id_array);

    //si ottengono i link tra switch come gli id degli switch separati da un trattino
    foreach($switch_link_id as $key => $value) {
                
        $switch_link_array[$key]=substr($switch_link_id[$key], 0, 23);
        $switch_link_array[$key]=$switch_link_array[$key].substr($switch_link_id[$key], 25, 24);
        
    }

    //si ottengono le coppie id host-id switch
    foreach($host_switch_link_id as $key => $value) {
        //gli identificatori sono lunghi 17 caratteri
        $host_switch_link[$key]=substr($host_switch_link_id[$key], 0, 41);
    }
    
    //si assegnano al relativo vettore, gli identificatori di ciascun host già ricavati e salvati in $host_switch_link concatenati con gli id degli switch collegati
    foreach($host_switch_link as $key => $value) {

        //gli identificatori sono lunghi 17 caratteri
        $host_id_array[$key]=substr($host_switch_link[$key], 0, 17);
       
    }

    //si ordinano gli id degli host
    sort($host_id_array);

    //numero totale di switch
    $count = count($switch_id_array);
            
    for($i=0; $i< $count; $i++){

        //si assegna anche l+id dello switch che non viene restituito da get_port_table_row
        array_push($switch_statistics, $switch_id_array[$i]);
       
        //si ricavano le righe della tabella porte dello switch
        $switch_statistics1=get_port_table_raw($switch_id_array, $i);

        for($j=0; $j< count($switch_statistics1); $j++){
            array_push($switch_statistics, $switch_statistics1[$j]);
        }
    }

    for($i=0; $i< $count; $i++){

        //si ricavano le statistiche dei flussi per ciascuno switch
        array_push($switch_flow_array, $switch_id_array[$i]);
        //si ricavano le righe della tabella dei flussi dello switch
        $switch_flow_array1=get_flow_table_row($switch_id_array, $i);

        for($j=0; $j< count($switch_flow_array1); $j++){

            array_push($switch_flow_array, $switch_flow_array1[$j]);
        }                             
    }

    //si crea un array con i nuovi dati
    $updated_data = array(
        'switch_id_array' => $switch_id_array, 'switch_link_array' =>  $switch_link_array, 'host_switch_link' => $host_switch_link, 'host_id_array' => $host_id_array, 'host_switch_link_id' => $host_switch_link_id, 'switch_statistics' => $switch_statistics, 'switch_link_id' => $switch_link_id, 'switch_flow_array' => $switch_flow_array, 'bandwidth_array' => $bandwidth_array
    );

    //si restituiscono i dati aggiornati in formato JSON
    header('Content-Type: application/json');
    echo json_encode($updated_data);
?>