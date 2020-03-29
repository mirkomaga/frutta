<?php

session_start();
ob_start();

function call($params, $type){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_PORT => "8000",
        CURLOPT_URL => "http://127.0.0.1:8000/".$params,
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


$tipoquantita = json_decode(call('tipoquantita', 'GET'));
$tipocleaned = (object) [];
foreach($tipoquantita as $tipo){
    $nome = $tipo->tipo;
    $id = $tipo->id;
    $tipocleaned->$id = $nome;
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
        $tmp->somma = (object) []; 
        $tmp->id = $ordine->id; 
        $tmp->sommaIngrosso = 0; 
        $tmp->sommaGuadagno = 0; 
        $tmp->alimentoID = $idAlimento; 
        $tmp->stato = $ordine->stato; 

        $tabella->$idAlimento = $tmp;
    }

    $idTipo = $ordine->id_tipo;
    $nome = $tipocleaned->$idTipo;

    if(!property_exists($tmp->somma, $nome)){
        $tmp->somma->$nome->quantita = $ordine->quantita;
        $tmp->somma->$nome->nome = $nome;
    }else{
        $tmp->somma->$nome->quantita += $ordine->quantita;
    }

    
    $tabella->$idAlimento->sommaIngrosso += round($tabella->$idAlimento->somma * $infoProdotti->$idAlimento->prezzoIngrosso);
    $tabella->$idAlimento->sommaGuadagno += round($tabella->$idAlimento->somma * $infoProdotti->$idAlimento->prezzoVendita);
}


// print '<pre>';
// print_r($tabella);
// print '</pre>';


?>
<pre>
<?php //print_r($tabella);?>
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css"/>
    <link href="fa/css/all.css" rel="stylesheet">
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
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="clientName">Nome Cliente</label>
                                        <input type="text" class="form-control" id="clientName" aria-describedby="Nome Cliente" placeholder="Inserisci nome cliente">
                                        <!-- <small id="IdnameProduct" class="form-text text-muted">Nome prodotto.</small> -->
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="quantitaOrdine">Quantita</label>
                                        <input class="form-control" type="number"min="0"  value="0" step="0.1" id="quantitaOrdine">
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="selectTipoQuant">Tipo</label>
                                        <select data-live-search="true" class="selectpicker form-control" id="selectTipoQuant">
                                            <?php
                                            foreach($tipocleaned as $index=>$singolo){
                                                print '<option value="'.$index.'">'.$singolo.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-3">
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
                                        <th colspan="2">Quantita Totale</th>
                                        <th scope="col">Stato</th>
                                        <th scope="col">Azioni</th>
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
                                        print '<td rowspan="3">'.$ordine->alimento.'</td>';
                                        print '<td class="py-0 my-0"><b>Numero</b></td>';
                                        print '<td class="py-0 my-0">'.$ordine->somma->Numero->quantita.'</td>';
                                        print '<td rowspan="3">';
                                        if($ordine->stato == '1'){
                                            print 'In attesa';
                                        }elseif($ordine->stato == '2'){
                                            print 'Finalizzato';
                                        }else{
                                            print 'Annullato';
                                        }
                                        print '</td>';
                                        print '<td rowspan="3">';
                                        print '<div class="btn-group btn-block" role="group" aria-label="Basic example">
                                            <button type="button" data-target="'.$ordine->id.'" class="btn btn-success finalize"><i class="fas fa-check"></i></button>
                                            <button type="button" data-target="'.$ordine->id.'" class="btn btn-danger delete"><i class="far fa-trash-alt"></i></button>
                                            <button type="button" data-target="'.$ordine->alimentoID.'" class="btn btn-warning edit"><i class="far fa-edit"></i></button>
                                        </div>';
                                        print '</td>';
                                        print '</tr>';
                                        print '<tr class="'.$class.'">';
                                        print '<td class="py-0 my-0"><b>Peso</b></td>';
                                        print '<td class="py-0 my-0">'.$ordine->somma->Peso->quantita.'</td>';
                                        print '</tr>';
                                        print '<tr class="'.$class.'">';
                                        print '<td class="py-0 my-0"><b>Casse</b></td>';
                                        print '<td class="py-0 my-0">'.$ordine->somma->Casse->quantita.'</td>';
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
                                echo '<i class="fas fa-check"></i>';
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
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
    <script src="notify.js" crossorigin="anonymous"></script>
    <script src="script.js" crossorigin="anonymous"></script>
  </body>
</html>