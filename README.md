# Jegarn PHP SDK

php implementation of jegarn client.




## Install

    composer require jegarn/jegarn-php




## Example


connect

    $client = new SwooleClient("jegarn.com", 9501, 5000);
    $client->setConfig([
        'open_length_check'     => 1,
        'package_length_type'   => 'N',
        'package_length_offset' => 0,
        'package_body_offset'   => 4,
        'package_max_length'    => 2048,
        'socket_buffer_size'    => 1024 * 1024 * 2,
        'daemonize'             => 1,
        'ssl_cert_file'         => __DIR__ . '/ssl.crt',
        'ssl_key_file'          => __DIR__ . '/ssl.key'
    ]);
    $client->setUser("account", "password");
    $client->setConnectListener(function($client){
        echo "connect\n";
    });
    $client->setDisconnectListener(function($cilent){
        echo "disconnect\n";
    });
    $client->setErrorListener(function(ErrorObject $errorObject, $client){
        echo 'error code:',$errorObject->code, "\n";
    });
    $client->setSendListener(function(Base $packet, $client){
        echo "send:\n"; print_r($packet); echo "\n";
    });
    // new message listener
    $client->setPacketListener(function(Base $pkt, $client){
        echo "recv:\n"; print_r($pkt); echo "\n";
    });

send new message

    $packet = new TextChat();
    $packet->from = "my_uid";
    $packet->to = "friend_uid";
    $packet->setText("hello");
    $client->sendPacket($packet);




## License

[Apache License Version 2.0](./LICENSE)