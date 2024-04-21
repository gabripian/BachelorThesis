<?php

            //metodi che ottengono il numero di host, il numero di switch e di link, i link sono i link tra gli switch e tra switch e host senza tener conto della bidirezionalità
            //nel file index.php si indicano solo i link tra switch tenendo conto della bidirezionalità

            ///wm/core/controller/switches/json restituisce gli identificatori di tutti gli switch collegati al controller
            $url= 'http://127.0.0.1:8080/wm/core/controller/switches/json';
            
            //echo "<br>";

            /*// Collection object
            $data = [
                'collection' => 'restAPI'
            ];*/

             /*// Set the request data as JSON using json_encode function
            curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));*/

            $curl = curl_init($url);


            // Set the CURLOPT_RETURNTRANSFER option to true
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

           
            // Execute cURL request with all previous settings
            $response = curl_exec($curl);

            // Close cURL session
            curl_close($curl);

            
            //echo "<br>";
            //echo $response;
            $arr = json_decode($response, true);
            
            $num_switch=0;
            //il risultato ottenuto è un array costituito da array nei quali c'è il campo switchDPID che contiene l'ID dello switch
            foreach($arr as $key => $value) {
                
                $num_switch++;

                foreach($value as $key1 => $val) {

                    //si stampano solo gli ID degli switch
                    if($key1=='switchDPID'){
                        //echo "switch" . $num_switch . " => " . $val . "<br>";
                    }
                }
            }

        ?>


        <?php

            ///wm/device/ indica il numero di host connessi alla rete
            $url= 'http://127.0.0.1:8080/wm/device/';

            //echo "<br>";

            $curl = curl_init($url);


            // Set the CURLOPT_RETURNTRANSFER option to true
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


            // Execute cURL request with all previous settings
            $response = curl_exec($curl);

            // Close cURL session
            curl_close($curl);

            //echo $response;
            $arr = json_decode($response, true);
            
            $num_host=0;

            //il risultato ottenuto è un array costituito da array, servono due for se si vuole contare il numero di host per come sono restituiti i dati
            foreach($arr as $key => $value) {
                
                foreach($value as $key1 => $val) {

                    $num_host++;

                }
            }

        ?>

        <?php


            ///wm/topology/links/json restituisce il numero di link di collegamento tra switch
            $url= 'http://127.0.0.1:8080/wm/topology/links/json';

            //echo "<br>";

            $curl = curl_init($url);


            // Set the CURLOPT_RETURNTRANSFER option to true
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


            // Execute cURL request with all previous settings
            $response = curl_exec($curl);

            // Close cURL session
            curl_close($curl);

            $arr = json_decode($response, true);

            $num_switch_link=0;
            //il risultato ottenuto è un array costituito da array nei quali c'è il campo switchDPID che contiene l'ID dello switch
            foreach($arr as $key => $value) {
                
                $num_switch_link++;

            }

            //il numero di link totale si ottiene sommando il numero di link tra switch ed il numero di host dati che c'è un link per ciascun host
            $num_link=$num_switch_link+$num_host;
        ?>



<?php
            /*
            $url = 'https://kvstore.p.rapidapi.com/collections';

            $collection_name = 'RapidAPI';

            $request_url = $url . '/' . $collection_name;

            $curl = curl_init($request_url);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            //In case we need to set multiple options, instead of repeatedly call curl_setopt, we can represent the options we want to change and 
            //their values as key – value pairs into an array, and use the curl_setopt_array function to pass them at once


            $options = [
                CURLOPT_URL => "https://api.nasa.gov/planetary/apod?api_key=DEMO_KEY",
                CURLOPT_RETURNTRANSFER => true
            ]​

            curl_setopt_array($handler, $options);

            
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'X-RapidAPI-Host: kvstore.p.rapidapi.com',
                'X-RapidAPI-Key: 7xxxxxxxxxxxxxxxxxxxxxxx',
                'Content-Type: application/json'
            ]);

            $response = curl_exec($curl);
            curl_close($curl);
            echo $response.PHP_EOL;
            */

        ?>























<!DOCTYPE html>
<html>

    <head>
        <link rel="stylesheet" href="style.css">
        <script src="script.js"></script>

        <!-- librerie grafiche per la rappresentazione della topologia 
        <script src="https://cdn.anychart.com/releases/8.8.0/js/anychart-core.min.js"></script>
        <script src="https://cdn.anychart.com/releases/8.8.0/js/anychart-graph.min.js"></script>
        <script src="https://cdn.anychart.com/releases/8.8.0/js/anychart-data-adapter.min.js"></script>
        <script src="d3.min.js"></script>
        -->

        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/networkgraph.js"></script>

        

        <script type="text/javascript" src="https://unpkg.com/vis-network@9.0.3/dist/vis-network.min.js"></script>

        <?php
              //si include il file contenente la funzione di ordinamento del vettore e altre funzioni
              require __DIR__ . '/function.php';

        ?>

        <div id="head1">
            
            <h1 id="head2">Stato Della Rete</h1>
        
        </div>
        
    </head>

    <body>
   
       
        <?php
            //array contenente il numero di switch, host e link
            $summary=array();

            //si assegna il numero di switch, host e link
            $summary=get_summary();

            $num_switch=$summary[0];
            $num_host=$summary[1];
            $num_link=$summary[2];
            
        ?>

        <div id="switch"><img src="images/switch.png" alt="Switch Icon">Switch<div class="numberswitch"><?php echo $num_switch; ?></div></div>
        <div id="host"><img src="images/computer.png" alt="Host Icon">Host<div class="number"><?php echo $num_host; ?></div></div>
        <div id="link"><img src="images/networking.png" alt="Link Icon">Link<div class="number"><?php echo $num_link; ?></div></div>




        <?php


            //array che contiene gli id degli switch
            $switch_id_array=array();
            //array che contiene i link tra i vari switch
            $switch_link_array=array();
            //array che contiene gli id degli host
            $host_id_array=array();
            //array contenente le coppie indirizzo dell'host e identificatore dello switch a cui è collegato l'host, separati da un trattino -
            $host_switch_link=array();

            //si ottengono gli id degli switch ordinati in ordine crescente
            $switch_id_array=get_switch_id();

            //si ottengono i link tra switch
            $switch_link_array=get_switch_link();

            //si ottengono i link tra host e switch
            $host_switch_link=get_host_switch_link();

            //si assegnano al relativo vettore, gli identificatori di ciascun host già ricavati e salvati in $host_switch_link concatenati con gli id degli switch collegati
            foreach($host_switch_link as $key => $value) {
                //gli identificatori sono lunghi 17 caratteri
                $host_id_array[$key]=substr($host_switch_link[$key], 0, 17);
               

            }

            //si ordinano gli id degli host
            sort($host_id_array);
            
        ?>
        <div id="topology">Network Topology</div>      
        <div id="container"></div>
        

        <script>
           
            //assegnazione del contenuto degli array in PHP agli array in js
            var switch_id_array = <?php echo json_encode($switch_id_array); ?>;
            var switch_link_array = <?php echo json_encode($switch_link_array); ?>;
            var host_switch_link = <?php echo json_encode($host_switch_link); ?>;
            var host_id_array = <?php echo json_encode($host_id_array); ?>;

            //vettore dei nodi del grafo
            var nodes = [];

            //creazione dei nodi, si assegna a ciascun nodo switch una label con l'id dello switch e ciascun nodo è rappresentato con l'immagine dello switch
            switch_id_array.forEach(function(switchId) {

                nodes.push({ id: switchId, label: switchId, image: 'images/switch.png'});
            });

            //stessa cosa nel caso degli host, cambia solo l'immagine associata
            host_id_array.forEach(function(switchId) {

                nodes.push({ id: switchId, label: switchId, image: 'images/computer.png'});
            });
            
            //vettore dei link del grafo
            var edges = [];

            //creazione degli archi, si dividono gli elementi del vettore separati da un tattito -, questi elementi indicano il nodo sorgente ed il nodo destinazione
            switch_link_array.forEach(function(link) {
                
                var links = link.split('-');
            
                edges.push({ from: links[0], to: links[1] });
            });
           
            //archi tra host e swicth
            host_switch_link.forEach(function(link) {
                
                var links = link.split('-');
                //i link tra host e switch si colorano diversamente, anche quando si selezionano col mouse
                edges.push({ from: links[0], to: links[1], color: { color: "lime", highlight: "#3fff00", hover: "#3fff00"}});
            });

            //creazione dell'oggetto dati per il grafo
            var data = {
                nodes: nodes,
                edges: edges
            };

            //opzioni per la visualizzazione del grafo
            var options = {
                layout: {
                    
                    //false altrimenti la rete viene rappresentata come albero in cui i nodi non si possono trascinare dalla loro posizione nell'albero
                    hierarchical: false
                },
                interaction: {
                    //quando si passa col mouse sopra un elemento, viene evidenziato
                    hover: true
                },
                nodes: {
                    //i nodi si rappresentano come immagine
                    shape: 'image'
                    
                },
                edges: {
                    //spessore dei link
                    width: 3
                    
                }
            };

            //creazione del grafo
            var container = document.getElementById('container');
            var network = new vis.Network(container, data, options);
    
               
        </script>
      
        <br>
 
        <br>

        <?php

           //numero totale di switch
            //$count = count($switch_id_array);

            /*
            //tabella dei flussi
            echo '<div class="flow_table">Flow Table</div>';
            //div contenente la tabella dei flussi
            echo '<div id="flow_table_container">';
   
                echo '<table>
                <tr>
                    <th>switch id</th>
                    <th>flow count</th>
                    <th>packet count</th>
                    <th>byte count</th>
                    <th>duration (sec)</th>
                </tr>';
                
                //array da riempire con i dati da inserire in una singola riga della tabella
                $raw=array();

                for($i=0; $i< $count; $i++){

                    //si ricava ogni riga da inserire nella tabella riguardo la tabella di flusso per ogni switch
                    $raw=get_flow_table_row($switch_id_array, $i);

                    echo '<tr>
                    <td>'.$switch_id_array[$i].'</td>
                    <td>' .$raw[0].'</td>
                    <td>' .$raw[1]. '</td>
                    <td>' .$raw[2]. '</td>
                    <td>' .$raw[3]. '</td>
                    </tr>';
                }

                echo '</table>';

            echo '</div>';

            echo '<br>';
            echo '<br>';

            */
            
            /*
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

            */

            //metodo per abilitare le statistiche, ma non funziona

            /*

            $url ='http://127.0.0.1:8080/wm/statistics/config/enable/json';

            $data ='';

            $curl = curl_init($url);

            // 1. Set the CURLOPT_RETURNTRANSFER option to true
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');

            // 2. Set the CURLOPT_POST option to true for POST request
            //curl_setopt($curl, CURLOPT_POST, true);

            // 3. Set the request data as JSON using json_encode function
            curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));

            $response = curl_exec($curl);

            curl_close($curl);

            echo $response;

            */




        ?>
        

    </body>

</html>

