<?php
session_start();
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$result = "";
$post = $_REQUEST;

function call($params, $type, $data = null){
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
        CURLOPT_POSTFIELDS => $data
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return json_encode($response);
    }
}

switch ($post['method']) {
    case "inserisciProdotti":
        $result = json_decode(call('prodotti', 'POST', json_encode($post['data'])));
        break;
    case "inserisciOrdine":
        $result = json_decode(call('ordine', 'POST', json_encode($post['data'])));
        break;
    case "eliminoprodotto":
        $result = json_decode(call('prodotti/'.$post['data'], 'DELETE'));
        break;
    case "modificaprodotto":
        $result = json_decode(call('prodotti/'.$post['id'], 'PUT', json_encode($post['data'])));
        break;
    case "modificaordine":
        $result = json_decode(call('ordine/'.$post['id'], 'PUT', json_encode($post['data'])));
        break;
    case "modificaordinenost":
        $result = json_decode(call('ordine/'.$post['id'], 'PUT', json_encode($post['data'])));
        break;
    case "deleteordine":
        $result = json_decode(call('ordine/'.$post['id'], 'DELETE'));
        break;
    case "getordinialimento":
        $result = json_decode(call('getordinialimento/'.$post['id'], 'GET'));
        break;
    case "generotabellastorici":
        $dati = json_decode($post['dati']);
        
        $tipoquantita = json_decode(json_decode(call('tipoquantita', 'GET')));
        $a = '<div class="table-responsive"><table class="table table-bordered">
        <thead>
        <tr>
        <th scope="col">Cliente</th>
        <th scope="col">Quantità</th>
        <th scope="col">Tipo</th>
        <th scope="col">Data</th>
        <th scope="col">Azioni</th>
        </tr>
        </thead>
        <tbody>';

        foreach($dati as $singolo){
            
            $a .= '<tr>';
            $a .= '<td>';
            $a .= '<input type="text" class="form-control modCliente" data-target="'.$singolo->id.'" value="'.$singolo->cliente.'">';
            $a .= '</td>';
            $a .= '<td>';
            $a .= '<input class="form-control modPeso" data-target="'.$singolo->id.'" type="number" min="0" value="'.$singolo->quantita.'" step="0.1">';
            $a .= '</td>';
            $a .= '<td>';
            // $a .= '<select data-live-search="true" class="selectpicker form-control">';
            
            $selectedID = $singolo->id_tipo;

            foreach($tipoquantita as $tipoSingolo){
                if($selectedID == $tipoSingolo->id){
                    $a .= $tipoSingolo->tipo;
                    $selected = 'selected';
                }else{
                    $selected = '';
                }
                // $a .= '<option '.$selected.' value="'.$tipoSingolo->id.'">'.$tipoSingolo->tipo.'</option>';
            }
            // $a .= '</select>';
            $a .= '</td>';
            $a .= '<td>';
            $a .= $singolo->datetime;
            $a .= '</td>';
            $a .= '<td>';
            $a .= '<div class="btn-group" role="group" aria-label="Basic example">';
            $a .= '<button data-target="'.$singolo->id.'" class="btn btn-sm btn-success salvomodProdotto">';
            $a .= '<i class="fas fa-check"></i>';
            $a .= '</button>';
            $a .= '<button data-target="'.$singolo->id.'" class="btn btn-sm btn-danger eliminoProdotto">';
            $a .= '<i class="far fa-trash-alt"></i>';
            $a .= '</button>';
            $a .= '</button>';
            $a .= '</td>';
            $a .= '</tr>';
        }
        $a .= '</tbody>
            </table></div>';
        $result = json_encode($a);
        break;
    default:
        $result = "Non hai specificato un metodo che me piace..";
        break;

}

print $result;
