$(document).ready(async function(){
    init()
})

function init(){
    try {
        $('.selectpicker').selectpicker();
        // $('.table').dataTable( {
        //     "autoWidth": false
        // });
    } catch (error) {
        console.log(error)        
    }
}

var prodotti = false
var ordini = false

async function faccioChiamate(){
    await getprodotti()
    await getordini()
}

async function getordini(){
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "listaordini"},
            beforeSend: function () {
            }
        }).done(function (msg) {
            var prodotti = msg;
            resolve(prodotti)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
        });
    });
}

async function getprodotti(){
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "listaProdotti"},
            beforeSend: function () {
            }
        }).done(function (msg) {
            var ordini = msg;
            resolve(prodotti)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
        });
    });
}


$("#addProduct").submit(function(event){
    event.preventDefault();
    var data = {
        "tipo": $("#nameProduct").val(),
        "prezzo_ingrosso": $("#prezzoIngrosso").val(),
        "prezzo_vendita": $("#prezzoVendita").val()
    }
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "inserisciProdotti", "data": data},
            beforeSend: function () {
            }
        }).done(function (msg) {
            if(msg == 1){
                $.notify({
                    message: "Prodotto aggiunto",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile aggiungere il prodotto",
                    type: 'danger'
                },{
                    type: 'danger'
                });
            }
            location.reload(); 

            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
        });
    });
})

$("#addOrdine").submit(function(event){
    event.preventDefault();
    var data = {
        "cliente": $("#clientName").val(),
        "quantita": $("#quantitaOrdine").val(),
        "id_alimento": $("#selectProdotto").val(),
        "stato": "1",
        "id_tipo": $("#selectTipoQuant").val()
    }
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "inserisciOrdine", "data": data},
            beforeSend: function () {
            }
        }).done(function (msg) {
            if(msg == 1){
                $.notify({
                    message: "Prodotto aggiunto",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile aggiungere il prodotto",
                    type: 'danger'
                },{
                    type: 'danger'
                });
            }
            location.reload(); 

            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
        });
    });
})

$("#editProdotti").click(function(event){
    $('.modal').modal('show')
})

$(".eliminoProdotto").click(function(event){
    var target = $(this).data('target')
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "eliminoprodotto", "data": target},
            beforeSend: function () {
            }
        }).done(function (msg) {
            if(msg == 1){
                $.notify({
                    message: "Prodotto Eliminato",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile eliminare il prodotto",
                    type: 'danger'
                },{
                    type: 'danger'
                });
            }
            location.reload(); 

            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
        });
    });
})

$(".salvomodProdotto").click(function(event){
    var target = $(this).data('target')

    var data = {
        "tipo": $(".modnameProduct[data-target='"+target+"']").val(),
        "prezzo_ingrosso": $(".modprezzoIngrosso[data-target='"+target+"']").val(),
        "prezzo_vendita": $(".modprezvendita[data-target='"+target+"']").val()
    }
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "modificaprodotto", "data": data, "id": target},
            beforeSend: function () {
            }
        }).done(function (msg) {

            if(msg == 1){
                location.reload(); 
                $.notify({
                    message: "Prodotto modificato",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile modificare il prodotto",
                },{
                    type: 'danger'
                });
            }
            location.reload(); 

            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
        });
    });
})

$(".finalize").click(function(){
    var target = $(this).data('target')
    var data = {
        "stato": "2"
    }
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "modificaordine", "data": data, "id": target},
            beforeSend: function () {
            }
        }).done(function (msg) {
            if(msg == null){
                location.reload(); 
                $.notify({
                    message: "Ordine modificato",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile modificare il ordine",
                },{
                    type: 'danger'
                });
            }


            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
            location.reload(); 

        });
    });

})

$(".delete").click(function(){
    var target = $(this).data('target')
    var data = {
        "stato": "0"
    }
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "modificaordine", "data": data, "id": target},
            beforeSend: function () {
            }
        }).done(function (msg) {

            if(msg == 1){
                location.reload(); 
                $.notify({
                    message: "Ordine modificato",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile modificare il ordine",
                },{
                    type: 'danger'
                });
            }

            
            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
            location.reload(); 
        });
    });
})

$(".edit").click(function(){
    var target = $(this).data('target')
    
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "getordinialimento", "id": target},
            beforeSend: function () {
            }
        }).done(async function (msg) {

            await generoModalstorici(msg)
            
            // $('.modal .modal-body').html(storico)
            // $('.modal').modal('show')
            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
        });
    });
})

async function generoModalstorici(dati){
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "generotabellastorici", "dati": JSON.stringify(dati)},
            beforeSend: function () {
            }
        }).done(function (msg) {
            $('.modal .modal-body').html(msg)
            $('.modal').modal('show')
            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
            init()
        });
    });
}

$('.modal').on('click', '.salvomodProdotto', function(){
    var target = $(this).data('target')

    var data = {
        "quantita": $(".modPeso[data-target='"+target+"']").val(),
        "cliente": $(".modCliente[data-target='"+target+"']").val()
    }

    console.log(data)

    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "modificaordinenost", "data": data, "id": target},
            beforeSend: function () {
            }
        }).done(function (msg) {

            if(msg == 1){
                location.reload(); 
                $.notify({
                    message: "Ordine modificato",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile modificare il ordine",
                },{
                    type: 'danger'
                });
            }

            
            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
            location.reload(); 
        });
    });
})

$('.modal').on('click', '.eliminoProdotto', function(){
    var target = $(this).data('target')
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "deleteordine", "id": target},
            beforeSend: function () {
            }
        }).done(function (msg) {

            if(msg == 1){
                location.reload(); 
                $.notify({
                    message: "Ordine modificato",
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: "Non è stato possibile modificare il ordine",
                },{
                    type: 'danger'
                });
            }

            
            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
            location.reload(); 
        });
    });
})

function confermatutto(){
    return new Promise(resolve => {
        $.ajax({
            url: "ajax.php",
            method: "POST",
            data: {"method": "confermatutto"},
            beforeSend: function () {
            }
        }).done(function (msg) {
            resolve(msg)
        }).fail(function (err) {
            resolve("FAIL 404")
        }).always(function () {
            location.reload(); 
            init()
        });
    });
}