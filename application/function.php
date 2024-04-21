<?php
    //si inizializza una sessione per mantenere le informazioni sui byte trasmessi in precedenza
    session_start();
?>

<?php
    
    //resttuisce il numerro di switch, host e link in un array
    function get_summary(){ //http://127.0.0.1:8080/wm/core/controller/summary/json con questo link si riescono ad ottenere il numero di switch, host e link tra switch i quali sono bidirezionali
        
        $url= 'http://127.0.0.1:8080/wm/core/controller/summary/json';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);

        $arr = json_decode($response, true);

        //array contenente il numero di switch, host e link
        $summary=array();

        $summary[0]=0;
        $summary[1]=0;
        $summary[2]=0;
        
        foreach($arr as $key => $value) {
            
            if($key=='# Switches'){

                $summary[0]=$value;

            }else if($key=='# hosts'){

                $summary[1]=$value;

            }else if($key=='# inter-switch links'){

                $summary[2]=$value;

            }           
        }
        return $summary;
    }



    
    //ricava gli id di tutti gli switch e li ordina in ordine crescente
    function get_switch_id(){

        ///wm/core/controller/switches/json restituisce gli identificatori di tutti gli switch collegati al controller
        $url= 'http://127.0.0.1:8080/wm/core/controller/switches/json';
        
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);

        $arr = json_decode($response, true);

        $switch_id_array=array();
        $count=0;

        //il risultato ottenuto è un array costituito da array nei quali c'è il campo switchDPID che contiene l'ID dello switch
        foreach($arr as $key => $value) {
            
            foreach($value as $key1 => $val) {

                //si salvano solo gli ID degli switch in un array ulteriore
                if($key1=='switchDPID'){

                    $switch_id_array[$count]=$val;
                }
            }

            $count++;
        }
        //ordinamento degli id degli switch in ordine crescente, in modo che vengano inseriti in ordine in tabella
        sort($switch_id_array);

        return $switch_id_array;

    }
    
    //ricava i link tra gli switch
    function get_switch_link(){

        ///wm/topology/links/json restituisce i vari link e a quali switch sono collegati
        $url= 'http://127.0.0.1:8080/wm/topology/links/json';
        
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);

        $arr = json_decode($response, true);

        $switch_link_array=array();
        $count=0;

        foreach($arr as $key => $value) {
            
            foreach($value as $key1 => $val) {

                //si concatena l'id dello switch sorgente con l'id dello switch di destiazione per ciascun link, separati da un trattino
                if($key1=='src-switch'){

                    $switch_link_array[$count]=$val;

                }else if($key1=='dst-switch'){

                    $switch_link_array[$count]= $switch_link_array[$count]."-".$val;

                }else if($key1=='src-port'){

                    $switch_link_array[$count]= $switch_link_array[$count]."-".$val;
                }else if($key1=='dst-port'){

                    $switch_link_array[$count]= $switch_link_array[$count]."-".$val;
                }
            }

            $count++;
        }
        
        return $switch_link_array;
    }     

    //ricava i link tra host e switch
    function get_host_switch_link(){ 

        ///wm/device/ restituisce tutti i dispositivi rilevati dal controllore con relativo indirizzo mac, ipv4, ipv6, porta e identificatore dello switch a cui è collegato
        $url= 'http://127.0.0.1:8080/wm/device/';
        
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        
        curl_close($curl);

        $arr = json_decode($response, true);

        //array che viene restituito dalla funzione, contiene identificatore dell'host concatenato all'id dello switch ad esso collegato
        $host_switch_link=array();

        //stesso contenuto dell'array precedente con l'aggiunta di host che non sono collegati a niente e vengono rilevati dal controllore in alcuni casi
        $host_switch_link1=array();

        $count=0;

        foreach($arr as $key => $value) {
            
            foreach($value as $key1 => $val) {

                foreach($val as $key2 => $val2) {

                    //si concatena l'id dello switch con l'id dell'host a cui è collegato, separati da un trattino
                    if($key2=='mac'){
                        //assegna l'id dello switch al vettore
                        $host_switch_link1[$count]=$val2[0];
    
                    }else if($key2=='attachmentPoint'){

                        foreach($val2 as $key3 => $val3) {
                           
                            foreach($val3 as $key4 => $val4) {
                                
                                if($key4=='switch'){
                                    //concatena l'id dell'host aćollegato a quello switch
                                    $host_switch_link1[$count]= $host_switch_link1[$count]."-".$val4;
                                }else if($key4=='port'){
                                    //concatena l'id della porta dello switch a cui l'host è collegato
                                    $host_switch_link1[$count]= $host_switch_link1[$count]."-".$val4;
                                }
                            }
                        }
                    }
                }
                $count++;
            }
        }

        //le stringhe nel vettore con una lunghezza minore di 41 (l'unica altra lunghezza possibile è 17, ovvero l'id dell'host) vengono rimosse
        //fanno riferimento agli host rilevati dal controllore anche se non sono collegati alla rete
        foreach($host_switch_link1 as $key => $value) {
           
            if(strlen($value)<41){
            
                unset($host_switch_link1[$key]);
   
            }
        }

        //si azzera il contatore perchè tramite la funzione unset vengono rimossi gli elementi del vettore e gli indici potrebbero essere non consecutivi
        $count=0;

        //si assegna il risultato ottenuto al vettore da rstituire e assegnando ad esso indici numerici consecutivi
        foreach($host_switch_link1 as $key => $value) {

            $host_switch_link[$count]=$host_switch_link1[$key];
            $count++;    
        }

        return $host_switch_link;
    }  

    //restituisce le righe della tabella dei flussi per un singolo switch, in particolare per lo switch $switch_id_array[$i]
    function get_flow_table_row($switch_id_array, $i){

        //array da riempire con i dati da inserire in una singola riga della tabella
        $raw=array();

        //per ciascuno switch si mostrano le statistiche, si utilizzano le api per ogni singolo switch anzichè l api che include tutti gli switch
        $url= 'http://127.0.0.1:8080/wm/core/switch/'.$switch_id_array[$i].'/flow/json';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);

        $arr = json_decode($response, true);

        //indice da utilizzare per inserire i dati in raw
        $j=1;
           
        foreach($arr as $key => $value) {

            foreach($value as $key1 => $val) {

                //si conta il numero di flussi
                $raw[0]=count($value);

                foreach($val as $key2 => $val2) {

                    //il risultato ottenuto contiene solo informazioni che ci interessano
                    if($key2=='packet_count' || $key2=='byte_count' || $key2=='duration_sec'){

                        $raw[$j]=$val2;
                        $j++;                                
                    }
                }
            }
        }

        return $raw;
    }

    //restituisce lo stato di tutte le porte dello switch $switch_id_array[$i] in un array
    function get_port_state($switch_id_array, $i){

        //per ciascuno switch si mostrano le statistiche relative alle porte, si utilizzano le api per ogni singolo switch anzichè l'api che include tutti gli switch
        //in quetso caso si ricava solo lo stato
        $url= 'http://127.0.0.1:8080/wm/core/switch/'.$switch_id_array[$i].'/port-desc/json';
 
        $curl = curl_init($url);
  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     
        $response = curl_exec($curl);
     
        curl_close($curl);
     
        $arr = json_decode($response, true);

        //array contenente lo stato attivo/non attivo di ogni porta degli switch
        $state=array();
        //indice utilizzato per $state
        $w=0;

        foreach($arr as $key => $value) { 

            foreach($value as $key1 => $val) {
                foreach($val as $key2 => $val2) { 

                    //interessa solo lo stato
                    if( $key2=="state"){

                        $state[$w]=$val2[0];
                        $w++;
                    }
                }       
            }
        }

        return $state;
    }
    
    //restituisce le righe della tabella delle porte dello switch $switch_id_array[$i]
    function get_port_table_raw($switch_id_array, $i){

        //array che contiene le informazioni di un singolo switch
        $single_switch=array();

        //per ciascuno switch si mostrano le statistiche relative alle porte, si utilizzano le api per ogni singolo switch anzichè l'api che include tutti gli switch
        $url= 'http://127.0.0.1:8080/wm/core/switch/'.$switch_id_array[$i].'/port/json';
 
        $curl = curl_init($url);
  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     
        $response = curl_exec($curl);
     
        curl_close($curl);

        $arr = json_decode($response, true);

        //indice utilizzato per $single_switch
        $k=0;
     
        foreach($arr as $key => $value) {
      
            foreach($value as $key1 => $val) {

                foreach($val as $key2 => $val2) {

                    foreach($val2 as $key3 => $val3) {

                        foreach($val3 as $key4 => $val4) {
                    
                            //gli ultimi 2 campi non servono
                            if($key4!='duration_nsec' && $key4!='properties'){

                                $single_switch[$k]=$val4;
                                
                                $k++;            
                            }    
                        }        
                    }        
                }          
            }
        }

        return  $single_switch;
    } 

    function get_bandwidth($switch_id_array){

        //numero di switch nella rete
        $count = count($switch_id_array);

        $single_switch=array();
        
        $bandwidth_array=array();

        //array di supporto per aggiornare il vettore globale

        $j=0;
        $bandwidth=0;
        $bandwidth1=0;
        //contiene il valore massimo di banda per ciascuna interfaccia
        $max_bandwidth=0;
        $max_bandwidth1=0;
        //tempo in cui interfaccia inattiva
        $inactive_time=0;
        $inactive_time1=0;


        //si scorre per ciascuno switch
        for($i=0; $i< $count; $i++){

            //si ricavano le righe della tabella porte dello switch
            $single_switch=get_port_table_raw($switch_id_array, $i);
                    
            //per ciascuna porta si ottengono 10 informazioni, dividendo di un fattore 10 si ottiene il numero di porte di ogni switch
            $single_switch_length=count($single_switch)/10;

            for($l=0; $l<$single_switch_length; $l++){

                $bandwidth_array[$j]=$switch_id_array[$i];
                $bandwidth_array[$j]=$bandwidth_array[$j]. "-" .$single_switch[0];

                //supporto per raccogliere i dati da assegnare al vettore globale
                //id dello switch-porta dello switch
                $support[$j]=$bandwidth_array[$j];
                  
                //si concatena con byte ricevuti, trasmessi e tempo trascorso
                $support[$j]=$support[$j]."-".$single_switch[3]."-".$single_switch[4]."-".$single_switch[9];
                    
                //se il vettore di sessione non è vuoto
                if(isset($_SESSION["bandwidth_session_array"]) && !empty($_SESSION["bandwidth_session_array"])) {
                        
                    //si controlla lo switch e la porta
                    $pos=bandwidth_position($bandwidth_array[$j]);
                        
                    //se lo switch e porta selezionati non erano stati salvati, quindi sono nuovi, si calcola dall'inizio
                    if($pos==-1){
                        //calcolo throughput in trasmissione
                        $bandwidth=($single_switch[4]/$single_switch[9])*8;
                        $max_bandwidth=$bandwidth;

                        //calcolo throughput in ricezione
                        $bandwidth1=($single_switch[3]/$single_switch[9])*8;
                        $max_bandwidth1=$bandwidth1;

                        //se bandwidth == 0, a time si assegna $single_switch[9]
                        if($bandwidth == 0){
                            $inactive_time=$single_switch[9];
                        }else{
                            $inactive_time=0;
                        }    
                        //se bandwidth1 == 0, a time si assegna $single_switch[9]
                        if($bandwidth1 == 0){
                            $inactive_time1=$single_switch[9];
                        }else{
                            $inactive_time1=0;
                        }
                            
                           
                    }else{
                        //si ricava la riga del vettore di sessione corrispondente allo switch corrente
                        $row=$_SESSION["bandwidth_session_array"][$pos];
                        $row1=explode("-",$row);
                      
                        $bandwidth1=(($single_switch[3]-$row1[2])/($single_switch[9]-$row1[4]))*8;

                        $bandwidth=(($single_switch[4]-$row1[3])/($single_switch[9]-$row1[4]))*8;
                            
                        //dato che se viaggiano MB e GB, i numeri sono rappresentati con la virgola (707,127.00) allora si elimina il carattere virgola  
                            
                        $b=explode(",", $row1[6]);
                        $row1[6]="";
                        for($w=0;$w<count($b); $w++){
                            $row1[6]= $row1[6].$b[$w];
                        }

                        $e=explode(",", $bandwidth);
                        $bandwidth="";
                        for($w=0;$w<count($e); $w++){
                            $bandwidth= $bandwidth.$e[$w];
                        }
                            
                        if($row1[6] < $bandwidth){
                            $max_bandwidth=$bandwidth;
                                
                        }else{
                            $max_bandwidth=$row1[6];
                        }
    
                        $b1=explode(",", $row1[5]);
                        $row1[5]="";
                        for($w=0;$w<count($b1); $w++){
                            $row1[5]= $row1[5].$b1[$w];
                        }

                        $e1=explode(",", $bandwidth1);
                        $bandwidth1="";
                        for($w=0;$w<count($e1); $w++){
                            $bandwidth1= $bandwidth1.$e1[$w];
                        }    
                        if($row1[5] < $bandwidth1){
                            $max_bandwidth1=$bandwidth1;
                                
                        }else{
                            $max_bandwidth1=$row1[5];
                        }
                            
                        //se bandwidth == 0, a time si aggiungono 10 secondi
                        if($bandwidth == 0){
                            //se un valore vale ancora 0, si aggiunge il tempo di aggiornamento trascorso
                            $row1[8]+=$single_switch[9]-$row1[4];
                            $inactive_time=$row1[8];
                        }else{
                            $inactive_time=0;
                        }
    
                        //se bandwidth1 == 0, a time si aggiungono 10 secondi
                        if($bandwidth1 == 0){
                            //se un valore vale ancora 0, si aggiunge il tempo di aggiornamento trascorso
                            $row1[7]+=$single_switch[9]-$row1[4];
                            $inactive_time1=$row1[7];
                        }else{
                            $inactive_time1=0;
                        }            
                    }
                        session_write_close();

                }else{

                    //si divide il numero di byte con i secondi trascorsi e si moltiplica per 8 in modo da avere il risultato in bps
                    $bandwidth1=($single_switch[3]/$single_switch[9])*8;

                    //si divide il numero di byte con i secondi trascorsi e si moltiplica per 8 in modo da avere il risultato in bps
                    $bandwidth=($single_switch[4]/$single_switch[9])*8;
                    //si assegna la massima bandwidth, che nel primo caso corrisponde alla bandwidth appena calcolata
                    $max_bandwidth=$bandwidth;

                    //si assegna la massima bandwidth, che nel primo caso corrisponde alla bandwidth appena calcolata
                    $max_bandwidth1=$bandwidth1;

                    //se bandwidth == 0, a time si assegna $single_switch[9]
                    if($bandwidth == 0){
                        $inactive_time=$single_switch[9];
                    }else{
                        $inactive_time=0;
                    }
    
                    //se bandwidth == 0, a time si assegna $single_switch[9]
                    if($bandwidth1 == 0){
                        $inactive_time1=$single_switch[9];
                    }else{
                        $inactive_time1=0;
                    }        
                }
                   
                //si mostrano solo 2 cifre dopo la virgola
                $bandwidth=number_format($bandwidth, 2);
                $max_bandwidth=number_format($max_bandwidth, 2);

                $bandwidth1=number_format($bandwidth1, 2);
                $max_bandwidth1=number_format($max_bandwidth1, 2);

                $support[$j]=$support[$j]. "-" . $max_bandwidth1 . "-" . $max_bandwidth ."-". $inactive_time1 ."-". $inactive_time;

                //vettore risultato finale
                $bandwidth_array[$j]=$bandwidth_array[$j]. "-" . $bandwidth1 ."-" . $bandwidth. "-". $single_switch[9]. "-" . $max_bandwidth1 . "-" . $max_bandwidth ."-". $inactive_time1. "-". $inactive_time;
                        
                //si fanno avanzare i dati di 10 posizioni
                array_splice($single_switch, 0, 10);
                $j++;         
            }
    
        }

        session_start();
        //si azzera l'array di sessione
        $_SESSION["bandwidth_session_array"] = array();

        //si aggiorna il vettore di sessione con i nuovi dati
        for($i=0; $i<count($support); $i++){ 

            $_SESSION["bandwidth_session_array"][$i]=$support[$i];
        }
        session_write_close();
           
        sort($bandwidth_array);

        return $bandwidth_array;
    }

    //ricava la posizione nel vettore globale  di switch porta concatenati con un trattino
    function bandwidth_position($bandwidth_string){

        $pos = -1;

        foreach ($_SESSION["bandwidth_session_array"] as $key => $value) {
        
            if (strpos($value, $bandwidth_string) !== false) {
                $pos = $key;
                break;
            }
        }
        return $pos;
    }

    //si scambiano 10 elementi del vettore con altri 10 elementi se una porta con numero maggiore precede una porta con numero minore    
    function array_sort($single_switch, $single_switch_length){
        $swapped=false;

        do {
            $swapped = false;
            //inizia da 1 dato che i primi 10 elementi riguardano la porta local
            for ($a = 1; $a <  $single_switch_length-1; $a++) {

                if ($single_switch[10*$a] > $single_switch[10*($a+1)]) {
                    $temp = array();

                    for ($j = 0; $j <  10; $j++) { 

                        $temp[$j]=$single_switch[(10*$a)+$j]; 
                    }

                    for ($j = 0; $j <  10; $j++) { 

                        $single_switch[(10*$a)+$j]=$single_switch[(10*($a+1))+$j]; 
                    }

                    for ($j = 0; $j <  10; $j++) { 

                        $single_switch[(10*($a+1))+$j]=$temp[$j]; 
                    }

                    $swapped = true;
                }
            }
        } while ($swapped);

        return $single_switch;
    }
?>