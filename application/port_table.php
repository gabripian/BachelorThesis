<!DOCTYPE html>
<html>

    <head>
       
        <link rel="stylesheet" href="style.css">
        <!--si include il file javascript per l'aggiornamento dei dati-->
        <script type="text/javascript" src="autoUpdate.js"></script>

        <?php
            //si include il file contenente la funzione di ordinamento del vettore e altre funzioni
            require __DIR__ . '/function.php';
        ?>

        <div class="head1">
            
            <h1>Network Status</h1>
        
        </div>
        
    </head>

    <body>

        <div class="navbar">
            <a href="index.php"><img id="a" src="images/home.png" alt="Home Icon"> Topology <div id="topology1">(Home)</div></a>
            <a href="flow_table.php"><img src="images/table.png" alt="Table Icon"> Flow Table</a>
            <a href="port_table.php"><img src="images/table.png" alt="Table Icon"> Port Table</a>
            <a href="bandwidth_table.php"><img src="images/table.png" alt="Table Icon"> Througput <div id="monitoring">Monitoring</div></a>
        </div>

        <br>
        <br>
        
        <?php

            //array che contiene gli id degli switch
            $switch_id_array=array();
            
            //si ottengono gli id degli switch ordinati in ordine crescente
            $switch_id_array=get_switch_id();
            
            //numero totale di switch
            $count = count($switch_id_array);

            //tabella delle porte
            echo '<div class="flow_table">Port Table</div>';
            
            //div contenente la tabella delle porte
            echo '<div id="port_table_container">';
    
                echo '<table>
                <tr>
                    <th>switch id</th>
                    <th>port number</th>
                    <th>state</th>
                    <th>receive packets</th>
                    <th>transmit packets</th>
                    <th>receive bytes</th>
                    <th>transmit bytes</th>
                    <th>receive dropped</th>
                    <th>transmit dropped</th>
                    <th>receive errors</th>
                    <th>transmit errors</th>
                    <th>duration (sec)</th>
                </tr>';

                //array che contiene le informazioni di un singolo switch
                $single_switch=array();

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

                        echo '<tr>
                            <td>'.$switch_id_array[$i].'</td>
                            <td>' .$single_switch[0]. '</td>
                            <td>' .$state[$l].'</td>
                            <td>' .$single_switch[1]. '</td>
                            <td>' .$single_switch[2]. '</td>
                            <td>' .$single_switch[3].'</td>
                            <td>' .$single_switch[4]. '</td>
                            <td>' .$single_switch[5]. '</td>
                            <td>' .$single_switch[6]. '</td>
                            <td>' .$single_switch[7]. '</td>
                            <td>' .$single_switch[8]. '</td>
                            <td>' .$single_switch[9]. '</td>
                        </tr>';

                        //si fanno avanzare i dati di 10 posizioni
                        array_splice($single_switch, 0, 10);   
                    }
                }
                
                echo '</table>';

            echo '</div>';

            echo '</br>';
            echo '</br>';
        ?>
        
    </body>
</html>