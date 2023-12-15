<?php
include_once "$root/chat/services/contacts.php";
?>

<link href="/chat/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/chat/css/chat.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
<link href="js/bootstrap.bundle.min.js" rel="stylesheet" />


<div class="container">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card chat-app">
                <div id="plist" class="people-list">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div>

                        <input type="text" class="form-control" placeholder="Search...">
                    </div>

                    <ul class="list-unstyled chat-list mt-2 mb-0">
                        <?php
                        
                    
                        foreach ($contacts as $contact) {
                            echo <<<HTML
                                    <li class="clearfix" id="contact_$contact[chat]" onclick="selectContact($('#contact_'+$contact[chat]).attr('id'), $('#name_'+$contact[chat]).html(), $('#image_'+$contact[chat]).attr('src'), $_SESSION[id]);">
                                        <img src="$contact[image]" alt="avatar" id="image_$contact[chat]">
                                        <div class="about">
                                            <div class="name" id="name_$contact[chat]">$contact[name]</div>
                                            <div class="status"><i class="bi bi-person-add"></i> left 7 mins ago </div>
                                        </div>
                                    </li>
                                HTML;

                        }
                        ?>

                    </ul>
                </div>

                <div class="chat" id="chat">

                </div>
            </div>
        </div>
    </div>
</div>

<script src="/chat/js/bootstrap.bundle.min.js"></script>
<script src="/chat/js/mqttws31.min.js" type="text/javascript"></script>

<script>
// Configuración del cliente MQTT
const broker = "localhost";
const port = 8083 //8081; // Puerto por defecto para WebSockets    
const qos = 1; // Calidad de servicio (QoS)
var name = "";
var contacts = [];
var clients = [];
var messages = [];
var currentUser = 0;

$(document).ready(function() {
    url = "/chat/services/getContacts.php";

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json'
    }).done(loadContacts).fail(function(response) {
        console.log("Response: ", response);
    });
});

function loadContacts(response) {
    console.log("Response: ", response);
    contacts = response.data;
    console.log("Contacts: ", contacts);
    console.log("Contacts: ", contacts[0][1]);
    console.log("Longitud de contactos: ", contacts.length);

    connectContacts();
}

// Función de conexión
onConnect = (client) => {
    console.log("Connect MQTT");

    // Suscribirse al topic
    topic = "udenar/chat/" + client.clientId;
    client.subscribe(topic, {
        qos: qos
    });
    console.log('Subscribe to: ' + topic);
};

// Función de recepción de mensajes
onMessageArrived = (message) => {
    console.log("Message arrive: " + message.destinationName + ": " + message.payloadString);
    client_name = message.destinationName.split('/')[3];
    console.log(client_name);

    // $('#' + client_name + ' p').html(message.payloadString);

    // if (name != '' && client_name == name) {
    //     html = $('#chat_msj').html();
    //     html += "<p>" + message.payloadString + "<p>";
    //     $('#chat_msj').html(html);
    // } else {
    //     $('#chat_msj').html("");
    // }
};

// Set many clients
function connectContacts() {
    contacts.forEach(function(contact) {
        var contactId = 'contact_' + contact[0] + "/#";

        var client = new Paho.MQTT.Client(broker, port, contactId);
        client.onMessageArrived = onMessageArrived;
        clients.push(client);
        console.log(clients);

        // Conectar al broker MQTT
        client.connect({
            onSuccess: function() {
                onConnect(client);
            }, //callback
            useSSL: false, // Usar conexión segura (SSL)
        });
    });
};

function messageSent(image, message) {
    messageContainer = document.getElementById('message-container');

    messageContainer.innerHTML += `
            <li class="clearfix">
                <div class="message-data text-right">
                    <span class="message-data-time">10:10 AM, Hoy</span>
                    <img src="${image}" alt="avatar">
                </div>
                <div class="message other-message float-right">${message}</div>
            </li>
        `;
}

function messageReceived(message) {
    messageContainer = document.getElementById('message-container');

    messageContainer.innerHTML += `
            <li class="clearfix">
                <div class="message-data">
                    <span class="message-data-time">10:12 AM, Hoy</span>
                </div>
                <div class="message my-message">${message}</div>
            </li>
        `;
}

function selectContact(id, name, image, user_id) {
    currentUser = user_id;
    console.log('Contacto ' + name + ' seleccionado');
    console.log('image: ' + image);
    console.log('id: ' + id);
    loadMessage(id);
    loadChat(name, image);

    $('.clearfix').removeClass('active');
    $('#' + id).addClass('active');
}

// function loadChat(name, image) {
//     const chat = document.getElementById('chat');

//     chat.innerHTML = `<div class="chat-header clearfix">
//                     <div class="row">
//                         <div class="col-lg-6">
//                             <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
//                                 <img src="${image}" alt="avatar" id='contact_image'>
//                             </a>

//                             <div class="chat-about">
//                                 <h6 class="m-b-0" id='contact_name'>${name}</h6>
//                                 <small>Last seen: 2 hours ago</small>
//                             </div>
//                         </div>

//                         <div class="col-lg-6 hidden-sm text-right">
//                             <a href="javascript:void(0);" class="btn btn-outline-secondary"><i class="fa fa-camera"></i></a>
//                             <a href="javascript:void(0);" class="btn btn-outline-primary"><i class="fa fa-image"></i></a>
//                             <a href="javascript:void(0);" class="btn btn-outline-info"><i class="fa fa-cogs"></i></a>
//                             <a href="javascript:void(0);" class="btn btn-outline-warning"><i class="fa fa-question"></i></a>
//                         </div>
//                     </div>
//                 </div>
//                 <div class="chat-history">
//                         <ul class="m-b-0" >`;
//     messages.forEach(function(message) {
//         //if ((getCookie("user_id"))) {
//         console.log("user_id : " + currentUser);

//         //}
//         console.log(message);
//         if (message[2] == currentUser) {
//             chat.innerHTML += `
//                                 <li class="clearfix">
//                                     <div class="message-data text-right">
//                                         <span class="message-data-time">${message[1]}</span>
//                                     </div>
//                                     <div class="message other-message float-right"> ${message[0]}</div>
//                                 </li>
//                         `;
//         } else {
//             chat.innerHTML += `<li class="clearfix">
//                                     <div class="message-data">
//                                         <span class="message-data-time">${message[1]}</span>
//                                     </div>
//                                     <div class="message my-message">${message[0]}</div>
//                                 </li>`;
//         }
//     });

//     chat.innerHTML += `</ul>
//                 </div>
//                 <div class="chat-message clearfix">
//                     <div class="input-group mb-0">
//                         <div class="input-group-prepend">
//                             <span class="input-group-text"><i class="fa fa-send"></i></span>
//                         </div>

//                         <input type="text" class="form-control" placeholder="Enter text here...">
//                     </div>
//                 </div>
//             `;
// }

function loadChat(name, image) {
    const chat = document.getElementById('chat');
    let chat2 = "";

    messages.forEach(function(message) {
        console.log("user_id : " + currentUser);

        //}
        console.log(message);
        if (message[2] == currentUser) {
            chat2 += `
                    <li class="clearfix">
                        <div class="message-data text-right">
                            <span class="message-data-time">${message[1]}</span>
                            <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="avatar">
                        </div>
                        <div class="message other-message float-right"> ${message[0]}</div>
                    </li>
            `;
        } else {
            chat2 += `
                <li class="clearfix">
                    <div class="message-data">
                        <span class="message-data-time">${message[1]}</span>
                    </div>
                    <div class="message my-message">${message[0]}</div>
                </li>`;
        }
    });

    chat.innerHTML = `
                <div class="chat-header clearfix">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                <img src="${image}" alt="avatar" id='contact_image'>
                            </a>

                            <div class="chat-about">
                                <h6 class="m-b-0" id='contact_name'>${name}</h6>
                                <small>Last seen: 2 hours ago</small>
                            </div>
                        </div>

                        <div class="col-lg-6 hidden-sm text-right">
                            <a href="javascript:void(0);" class="btn btn-outline-secondary"><i class="fa fa-camera"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-primary"><i class="fa fa-image"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-info"><i class="fa fa-cogs"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-warning"><i class="fa fa-question"></i></a>
                        </div>
                    </div>
                </div>

                <div class="chat-history">
                    <ul class="m-b-0" id="message-container">
                    ${chat2}
                    </ul>
                </div>

                <div class="chat-message clearfix">
                    <div class="input-group mb-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-send"></i></span>
                        </div>

                        <input type="text" class="form-control" placeholder="Enter text here...">
                    </div>
                </div>
            `;
}

function loadMessage(chat_id) {

    id = chat_id.split('_')[1];
    url = "/chat/services/getMessages.php";

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function(response) {
        messages = response.data;
    }).fail(function(response) {
        console.log("Response: ", response);
    });
}
</script>