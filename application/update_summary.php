<?php
    //file delle funzioni per interagire con il controller
    require __DIR__ . '/function.php';

     //array contenente il numero di switch, host e link
     $summary=array();

     //si assegna il numero di switch, host e link
     $summary=get_summary();

     $num_switch=$summary[0];
     $num_host=$summary[1];
     $num_link=$summary[2];

    //oggetto html contenente la tabella aggiornata
    $summary_HTML = '';

    //nuova tabella dei flussi
    $summary_HTML .= '<div id="switch"><img src="images/switch.png" alt="Switch Icon">Switch<div class="numberswitch">'.$num_switch.'</div></div>';
    $summary_HTML .= '<div id="host"><img src="images/computer.png" alt="Host Icon">Host<div class="number">'.$num_host.'</div></div>';
    $summary_HTML .= '<div id="link"><img src="images/networking.png" alt="Link Icon">Link<div class="number">'.$num_link.'</div></div>';
    

    //restituisce la tabella dei flussi aggiornata
    echo  $summary_HTML;
?>