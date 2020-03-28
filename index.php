<?php

session_start();
ob_start();

function call($params, $type){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_PORT => "1001",
        CURLOPT_URL => "http://127.0.0.1:1001/".$params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $type,
        CURLOPT_POSTFIELDS => ""
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}


$tabella = (object) [];

$infoProdotti = (object) [];

$prodotti = json_decode(call('prodotti', 'GET'));

foreach ($prodotti as $prodotto){
    $id = $prodotto->id;

    $tmp = (object) [];
    $tmp->name = $prodotto->tipo;
    $tmp->prezzoIngrosso = $prodotto->prezzo_ingrosso;
    $tmp->prezzoVendita = $prodotto->prezzo_vendita;
    $tmp->id = $prodotto->id;

    $infoProdotti->$id = $tmp;
}

$ordini = json_decode(call('ordine', 'GET'));

foreach($ordini as $ordine){
    $idAlimento = $ordine->id_alimento;
    if(!property_exists($tabella, $idAlimento)){
        
        $tmp = (object) [];
        $tmp->alimento = $infoProdotti->$idAlimento->name; 
        $tmp->somma = 0; 
        $tmp->id = $ordine->id; 
        $tmp->sommaIngrosso = 0; 
        $tmp->sommaGuadagno = 0; 
        $tmp->alimentoID = $idAlimento; 
        $tmp->stato = $ordine->stato; 

        $tabella->$idAlimento = $tmp;
    }

    $tabella->$idAlimento->somma += $ordine->quantita;
    $tabella->$idAlimento->sommaIngrosso += round($tabella->$idAlimento->somma * $infoProdotti->$idAlimento->prezzoIngrosso);
    $tabella->$idAlimento->sommaGuadagno += round($tabella->$idAlimento->somma * $infoProdotti->$idAlimento->prezzoVendita);
}
?>
<pre>
<?php //print_r($prodotti); ;exit();?>
</pre>

<!doctype html>
<html lang="it">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">

    <title>Magalotti Ortofrutta</title>
  </head>
  <body>
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Ordine
                    </div>
                    <div class="card-body">
                        <form id="addOrdine">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="clientName">Nome Cliente</label>
                                        <input type="text" class="form-control" id="clientName" aria-describedby="Nome Cliente" placeholder="Inserisci nome cliente">
                                        <!-- <small id="IdnameProduct" class="form-text text-muted">Nome prodotto.</small> -->
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="quantitaOrdine">Quantita</label>
                                        <input class="form-control" type="number"min="0"  value="0" step="0.1" id="quantitaOrdine">
                                    </div>
                                </div>
                                
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="selectProdotto">Seleziona prodotto</label>
                                        <select data-live-search="true" data-actions-box="true"  class="selectpicker form-control" id="selectProdotto">
                                        <?php
                                        foreach($prodotti as $prodotto){
                                            print '<option value="'.$prodotto->id.'">'.$prodotto->tipo;
                                            print'</option>'; 
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success">Inserisci</button>
                        </form>
                    </div>
                </div>
                <div class="card my-5">
                    <div class="card-header">
                        Storico
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Quantita Totale</th>
                                        <th scope="col">Stato</th>
                                        <th scope="col">Azioni</th>
                                        <th scope="col">Totale Ingrosso</th>
                                        <th scope="col">Totale Guadagno</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($tabella as $index=>$ordine){
                                        switch($ordine->stato){
                                            case "1":
                                                $class = 'table-warning';
                                                break;
                                            case "2":
                                                $class = 'table-success';
                                                break;
                                            case "0":
                                                $class = 'table-danger';
                                                break;
                                        }
                                        print '<tr class="'.$class.'">';
                                        print '<td>'.$ordine->alimento.'</td>';
                                        print '<td>'.$ordine->somma.'</td>';
                                        print '<td>';
                                        if($ordine->stato == '1'){
                                            print 'In attesa';
                                        }elseif($ordine->stato == '2'){
                                            print 'Finalizzato';
                                        }else{
                                            print 'Annullato';
                                        }
                                        print '</td>';
                                        print '<td>
                                                <button data-target="'.$ordine->id.'" class="btn btn-sm btn-success finalize">
                                                    OK
                                                </button>
                                                <button data-target="'.$ordine->id.'" class="btn btn-sm btn-danger delete">
                                                    DEL
                                                </button>
                                                <button data-target="'.$ordine->alimentoID.'"  class="btn btn-sm btn-warning edit">
                                                    MOD
                                                </button>
                                            </td>';
                                        print '<td>'.$ordine->sommaIngrosso.' €</td>';
                                        print '<td>'.$ordine->sommaGuadagno.' €</td>';
                                        print '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card my-5">
                    <div class="card-header">
                        Inserisci Nuovo prodotto
                    </div>
                    <div class="card-body">
                        <form id="addProduct">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="nameProduct">Nome prodotto</label>
                                        <input type="text" class="form-control" id="nameProduct" aria-describedby="Nome prodotto" placeholder="Inserisci nome prodotto">
                                        <!-- <small id="IdnameProduct" class="form-text text-muted">Nome prodotto.</small> -->
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="prezzoIngrosso">Prezzo ingrosso</label>
                                        <input class="form-control" type="number"min="0"  value="0" step="0.1" id="prezzoIngrosso">
                                    </div>
                                </div>
                                
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="prezzoVendita">Prezzo vendita</label>
                                        <input class="form-control" type="number"min="0"  value="0" step="0.1" id="prezzoVendita">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success">Inserisci</button>
                            <button type="button" id="editProdotti" class="btn btn-sm btn-info">Modifica</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal bd-example-modal-lg" id="editPro" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifica</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Prezzo ingrosso</th>
                                <th scope="col">Prezzo Vendita</th>
                                <th scope="col">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($infoProdotti as $index=>$prod){
                                echo '<tr>';
                                echo '<td> ';
                                echo '<input type="text" class="form-control modnameProduct" data-target="'.$index.'" value="'.$prod->name.'">';
                                echo '</td>';
                                echo '<td>';
                                echo '<input class="form-control modprezzoIngrosso" data-target="'.$index.'" type="number" min="0" value="'.$prod->prezzoIngrosso.'" step="0.1">';
                                echo '</td>';
                                echo '<td>';
                                echo '<input class="form-control modprezvendita" data-target="'.$index.'" type="number" min="0" value="'.$prod->prezzoVendita.'" step="0.1">';
                                echo '</td>';
                                echo '<td>';
                                echo '<button data-target="'.$index.'" class="btn btn-sm btn-success salvomodProdotto">';
                                echo 'save';
                                echo '</button>';
                                // echo '<button data-target="'.$index.'" class="btn btn-sm btn-danger eliminoProdotto">';
                                // echo 'del';
                                // echo '</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script src="notify.js" crossorigin="anonymous"></script>
    <script src="script.js" crossorigin="anonymous"></script>
  </body>
</html>