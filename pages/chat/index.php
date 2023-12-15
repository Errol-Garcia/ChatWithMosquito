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
                                    <li class="clearfix" id="contact_$contact[chat]" onclick="selectContact($('#contact_'+$contact[chat]).attr('id'), $('#name_'+$contact[chat]).html(), $('#image_'+$contact[chat]).attr('src'), $_SESSION[id], $('#id_'+$contact[chat]).html());">
                                        <img src="$contact[image]" alt="avatar" id="image_$contact[chat]">
                                        <div class="about">
                                            <div hidden class="name" id="id_$contact[chat]">$contact[id]</div>
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
    const broker = "10.40.40.25";
    // const broker = "localhost";
    const port = 8083 //8081; // Puerto por defecto para WebSockets  
    // const broker = "test.mosquitto.org";
    // const port = 8083 //8081; // Puerto por defecto para WebSockets  
    const qos = 1; // Calidad de servicio (QoS)
    var name = "";
    var contacts = [];
    var clients = [];
    // var messages = [];
    var currentUser = 0;
    var currentContact = 0;
    var chat = 0;

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
        client_id = message.destinationName.split('/')[2];
        // client_name = message.destinationName.split('/')[3];
        // console.log(client_name);
        console.log(client_id.split('_')[1]);

        id = client_id.split('_')[1];
        console.log(chat);

        // if (message.payloadString == 'enviado') {
        //     // generar_numeros();
        //     console.log(message.payloadString);
        // }

        // if (client_name == 'enviado') {
        //     messageContainer = document.getElementById('message-container');
        //     messageContainer.innerHTML = "";
        //     // messageSent("https://bootdey.com/img/Content/avatar/avatar1.png", message.payloadString, "123");
        //     loadMessage(id);

        //     // } else {
        //     // messageReceived(message.payloadString, "123");
        // }

        if (id == chat) {
            // generar_numeros();
            console.log(message.payloadString);

            if (message.payloadString == 'insertado') {
                messageContainer = document.getElementById('message-container');
                messageContainer.innerHTML = "";
                loadMessage(client_id);
            }
        }
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

    function selectContact(id, name, image, user_id, user_selected) {
        currentUser = user_id;
        console.log('Contacto ' + name + ' seleccionado');
        console.log('image: ' + image);
        console.log('id: ' + id);
        currentContact = user_selected;

        loadChat(name, image);
        loadMessage(id);

        $('.clearfix').removeClass('active');
        $('#' + id).addClass('active');
    }

    function loadChat(name, image) {
        const chat = document.getElementById('chat');

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
                    </ul>
                </div>

                <div class="chat-message clearfix">
                        <form method="post" class="input-group mb-0" id="form_chat">
                            <div class="input-group-prepend">
                                <button type="submit" class="input-group-text"><i class="fa fa-send"></i></button>
                            </div>

                            <input type="text" id="txt_message" class="form-control" placeholder="Enter text here...">
                        </form>
                </div>
            `;
    }

    function messageSent(image, message, date) {
        messageContainer = document.getElementById('message-container');

        messageContainer.innerHTML += `
            <li class="clearfix">
                <div class="message-data text-right">
                    <span class="message-data-time">${date}</span>
                    <img src="${image}" alt="avatar">
                </div>
                <div class="message other-message float-right">${message}</div>
            </li>
        `;
    }

    function messageReceived(message, date) {
        messageContainer = document.getElementById('message-container');

        messageContainer.innerHTML += `
            <li class="clearfix">
                <div class="message-data">
                    <span class="message-data-time">${date}</span>
                </div>
                <div class="message my-message">${message}</div>
            </li>
        `;
    }

    function loadMessage(chat_id) {
        chat = chat_id.split('_')[1];;
        id = chat_id.split('_')[1];
        messages = [];
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

            messages.forEach(function(message) {
                if (message[2] == currentUser) {
                    messageSent("https://bootdey.com/img/Content/avatar/avatar1.png", message[0], message[1]);
                } else {
                    messageReceived(message[0], message[1])
                }
            });
        }).fail(function(response) {
            console.log("Response: ", response);
        });
    }

    $(document).on('submit', '#form_chat', function(event) {
        event.preventDefault();
        var message = $('#txt_message').val();
        // var name = $('#name').val();

        url = "/chat/services/add.php";

        console.log("message " + message);
        console.log("user " + currentUser);
        console.log("contact " + currentContact);
        console.log("chat " + chat);

        // $.ajax({
        //     url: url,
        //     method: "POST",
        //     // data: new FormData(this),
        //     data: {
        //         message: id,
        //         send_date: "123",
        //         pub_id: currentUser,
        //         sub_id: currentContact,
        //         chat_id: chat
        //     },
        //     dataType: 'json',
        //     success: function(data) {
        //         console.log(data);
        //         // const message = new Paho.MQTT.Message("insertado");
        //         // message.destinationName = topicPublish;
        //         // message.qos = qos;
        //         // client.send(message);
        //         // console.log("Mensaje publicado en " + topicPublish);
        //     }
        // });

        $.ajax({
            url: url,
            method: "POST",
            // data: new FormData(this),
            data: {
                message: message,
                pub_id: currentUser,
                sub_id: currentContact,
                chat_id: chat
            },
            dataType: 'json',
        }).done(function(response) {
            // console.log(response);


        }).fail(function(response) {
            console.log("Response 1: ", response);
        });

        topicPublish = "udenar/chat/contact_" + chat + "/";

        console.log(topicPublish);

        const message2 = new Paho.MQTT.Message("insertado");
        message2.destinationName = topicPublish;
        message2.qos = qos;
        client = clients[id];
        client.send(message2);
        console.log("Mensaje publicado en " + topicPublish);

        $('#txt_message').val("");
    });
</script>