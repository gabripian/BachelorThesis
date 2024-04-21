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
        ?>
        
    </body>
</html>