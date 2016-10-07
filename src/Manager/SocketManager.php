<?php

namespace Jegarn\Manager;

use swoole_client;

class SocketManager extends BaseManager {
    protected $clients;
    public static function getInstance($class = __CLASS__){
        return parent::getInstance($class);
    }
    public function closeClient($id){
        if(isset($this->clients[$id])){
            $this->clients[$id] = null;
            unset($this->clients[$id]);
        }
    }

    public function sendClientMessage($host, $port, $message, $options){
        $id = $this->getClientId($host, $port);
        if(empty($message)){
            return false;
        }
        if(!isset($this->clients[$id])){
            $ssl = isset($options['ssl_cert_file']) && $options['ssl_cert_file'];
            if($client = new swoole_client($ssl ? SWOOLE_SOCK_TCP | SWOOLE_SSL : SWOOLE_SOCK_TCP)){
                $this->clients[$id] = $client;
            }else{
                return false;
            }
            if(!$client->connect($host, $port, isset($options['timeout']) ? $options['timeout'] : 0.5)){
                $this->closeClient($id);
                return false;
            }
        }
        $client = $this->clients[$id];
        $retryCount = 3;
        send_data:
        --$retryCount;
        $messageLen = strlen($message);
        $ret = $client->send($message);
        if($ret === $messageLen){
            return true;
        }else if($retryCount <= 0){
            $this->closeClient($id);
            return false;
        }else if(false === $ret){
            if(!$client->connect($host, $port, isset($options['timeout']) ? $options['timeout'] : 0.1)){
                $this->closeClient($id);
                return false;
            }
            goto send_data;
        }else/*if($ret !== $messageLen)*/{
            $message = substr($message, $ret);
            goto send_data;
        }
    }

    protected function getClientId($host, $port){
        return $host . ':' . $port;
    }
}

