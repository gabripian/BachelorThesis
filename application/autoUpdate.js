//funzione per aggiornare la tabella dei flussi
function updateFlowTable() {

    //si crea un ogetto XMLHttpRequest
    var xhr = new XMLHttpRequest();
    
    //funzione chiamata quando la proprietà readyState cambia, questa proprietø indica lo stato di XMLHttpRequest
    xhr.onreadystatechange = function() {
        
        //controllo se i dati sono pronti o no
        if(xhr.readyState == 4 && xhr.status == 200){
            //si aggiorna la tabella contenuta nel div con i dati ottenuti sottoforma di stringa
            document.querySelector('#flow_table_container').innerHTML = xhr.responseText;
        }     
    };
    
    //richiesta asincrona alla pagina di aggiornamento
    xhr.open('GET', 'update_flow_table.php', true);
    //richiesta viene inviata in rete
    xhr.send();
}

//aggiornamento viene effettuato ogni minuto (espresso in millisecondi)
setInterval(updateFlowTable, 10000); 


//funzione per aggiornare la tabella delle porte
function updatePortTable() {

    var xhr = new XMLHttpRequest();
    
    xhr.onreadystatechange = function() {
        
        if(xhr.readyState == 4 && xhr.status == 200){
            
            document.querySelector('#port_table_container').innerHTML = xhr.responseText;
        }     
    };
    
    xhr.open('GET', 'update_port_table.php', true);
    
    xhr.send();
}

setInterval(updatePortTable, 10000); 


//funzione per aggiornare la tabella dei throughput
function updateBandwidthTable() {

    var xhr = new XMLHttpRequest();
    
    xhr.onreadystatechange = function() {
        
        if(xhr.readyState == 4 && xhr.status == 200){
            
            document.querySelector('#bandwidth_table_container').innerHTML = xhr.responseText;
        }     
    };
    
    xhr.open('GET', 'update_bandwidth_table.php', true);
    
    xhr.send();
}

setInterval(updateBandwidthTable, 10000); 


function updateSummary() {

    var xhr = new XMLHttpRequest();
    
    xhr.onreadystatechange = function() {
        
        if(xhr.readyState == 4 && xhr.status == 200){
           
            document.querySelector('#summary_container').innerHTML = xhr.responseText;
        }     
    };
    
    xhr.open('GET', 'update_summary.php', true);
   
    xhr.send();
}

setInterval(updateSummary, 10000); 


//funzione per aggiornare la topologia della rete
function updateTopology() {

    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
    
        if(xhr.readyState == 4 && xhr.status == 200){

                //responseText contiene i dati aggiornati in formato testuale
                var updatedData = JSON.parse(xhr.responseText);
                //si richiama la funzione che crea la topologia con i nuovi dati
                create_network(updatedData.switch_id_array, updatedData.switch_link_array, updatedData.host_switch_link, updatedData.host_id_array, updatedData.host_switch_link_id, updatedData.switch_statistics, updatedData.switch_link_id, updatedData.switch_flow_array, updatedData.bandwidth_array);
        }
    };
    xhr.open('GET', 'update_topology.php', true);
    
    xhr.send();
}

setInterval(updateTopology, 10000);