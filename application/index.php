<?php
    //si inizializza una sessione per mantenere le informazioni sui byte trasmessi in precedenza
    session_start();
?>

<!DOCTYPE html>
<html>

    <head>
       
        <link rel="stylesheet" href="style.css">
        <!--si include il file javascript contenente la funzione di creazione della topologia della rete -->
        <script type="text/javascript" src="script.js"></script>
        <!--libreria per creare la topologia della rete -->
        <script type="text/javascript" src="https://unpkg.com/vis-network@9.0.3/dist/vis-network.min.js"></script>
        <!--si include il file javascript per l'aggiornamento dei dati-->
        <script type="text/javascript" src="autoUpdate.js"></script>

        <?php
       
            //si include il file contenente funzioni per ottenere i dati dal controllore
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
   
       
        <?php
        
            //array contenente il numero di switch, host e link
            $summary=array();
            //si assegna il numero di switch, host e link
            $summary=get_summary();

            $num_switch=$summary[0];
            $num_host=$summary[1];
            $num_link=$summary[2];
            
        ?>
    
        <div id="summary_container">
            <div id="switch"><img src="images/switch.png" alt="Switch Icon">Switch<div class="numberswitch"><?php echo $num_switch; ?></div></div>
            <div id="host"><img src="images/computer.png" alt="Host Icon">Host<div class="number"><?php echo $num_host; ?></div></div>
            <div id="link"><img src="images/networking.png" alt="Link Icon">Link<div class="number"><?php echo $num_link; ?></div></div>
        </div>
    
        
        <form id="myForm">
            <label id="insert" for="bandwidth">Choose throughput range:</label><br><br>
            <div id="insert1">
            <label for="bandwidth"> min:</label>  
            <input type="text" id="bandwidth" name="bandwidth" value="90">
            <label for="bandwidth1"> max:</label>
            <input type="text" id="bandwidth1" name="bandwidth1" value="110">
            <button id="button">Submit</button>
            </div>   
        </form>

        <br>
        <div id="check"></div>
        <br>
        
        <script>
            
            //carica i valori della form preceentemente salvati se esistono
            document.getElementById("bandwidth").value = localStorage.getItem("minValue");
            document.getElementById("bandwidth1").value = localStorage.getItem("maxValue");

            //evento quando si inviano i valori inseriti
            document.getElementById("myForm").addEventListener("submit", function(event) {
                //si ricavano i valori di throughput inseriti 
                var min=document.getElementById("bandwidth").value;
                var max=document.getElementById("bandwidth1").value;
                var minValue = parseFloat(min);
                var maxValue = parseFloat(max);

                //verifica correttezza valori
                if(min == "" || max == ""){
                    
                    document.getElementById("check").innerHTML="Enter a number";
                    event.preventDefault();

                }else if(isNaN(min) || isNaN(max)){

                    document.getElementById("check").innerHTML="Values must be numbers";
                    event.preventDefault();

                } else if (minValue < 0 || maxValue < 0) {

                   
                    document.getElementById("check").innerHTML="Values must be positive";
                    event.preventDefault(); //non invia il modulo, quindi non ricarica 

                }else if (minValue  >= maxValue) {
                    
                    document.getElementById("check").innerHTML ="min must be lower than max";
                    event.preventDefault(); 
                }

                //salvataggio valori
                localStorage.setItem("minValue", minValue);
                localStorage.setItem("maxValue", maxValue);
                
            });

        </script>
        
        <?php

            //array che contiene gli id degli switch
            $switch_id_array=array();
            //array che contiene i link tra i vari switch
            $switch_link_array=array();
            //array che contiene gli id degli host
            $host_id_array=array();
            //array contenente le coppie indirizzo dell'host e identificatore dello switch a cui è collegato l'host, separati da un trattino -
            $host_switch_link=array();
            //array contenente i link tra switch con i numeri di porta a cui gli switch sono collegati
            $switch_link_id=array();
            //array contenente indirizzo dell'host e identificatore dello switch a cui è collegato l'host, separati da un trattino e separati da un trattino dal numero di porta dello switch
            $host_switch_link_id=array();
            //array che contiene le statistiche di tutte le porte di tutti gli switch
            $switch_statistics=array();
            //array contenente la tabella dei flussi per ciascuno switch
            $switch_flow_array=array();
            //array contenente la bandwidth di ciascuna porta di ogni switch
            $bandwidth_array=array();

            //si ottengono gli id degli switch ordinati in ordine crescente
            $switch_id_array=get_switch_id();

            //si ottengono i link tra switch
            $switch_link_id=get_switch_link();

            //si ottengono i link tra host e switch
            $host_switch_link_id=get_host_switch_link();

            //si ottengono i link tra switch come gli id degli switch separati da un trattino
            foreach($switch_link_id as $key => $value) {
                
                $switch_link_array[$key]=substr($switch_link_id[$key], 0, 23);
                $switch_link_array[$key]=$switch_link_array[$key].substr($switch_link_id[$key], 25, 24);
            }

            //si ottengono le coppie id host-id switch
            foreach($host_switch_link_id as $key => $value) {
                //si rimuove il carattere -numero di porta
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

                //si assegna anche l'id dello switch che non viene restituito da get_port_table_row
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
            
            $bandwidth_array=get_bandwidth($switch_id_array);
        ?>
        
        <div id="topology">Network Topology</div>      
        <div id="container"></div>

        <div id="popup" class="popup">
            <div class="popup-content">
                
                <div id="popup-content"></div>
            </div>
        </div>

        <script>
           
            //assegnazione del contenuto degli array in PHP agli array in js
            var switch_id_array = <?php echo json_encode($switch_id_array); ?>;
            var switch_link_array = <?php echo json_encode($switch_link_array); ?>;
            var host_switch_link = <?php echo json_encode($host_switch_link); ?>;
            var host_id_array = <?php echo json_encode($host_id_array); ?>;
            var host_switch_link_id = <?php echo json_encode($host_switch_link_id); ?>;
            var switch_statistics = <?php echo json_encode($switch_statistics); ?>;
            var switch_link_id = <?php echo json_encode($switch_link_id); ?>;
            var switch_flow_array = <?php echo json_encode($switch_flow_array); ?>;
            var bandwidth_array = <?php echo json_encode($bandwidth_array); ?>;
            //si crea la topologia della rete
            create_network(switch_id_array, switch_link_array, host_switch_link, host_id_array, host_switch_link_id, switch_statistics, switch_link_id, switch_flow_array, bandwidth_array);
    
        </script>
        
        <br>
    </body>

</html>