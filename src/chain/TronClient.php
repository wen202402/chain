<?php

namespace wen202402\chain\chain;


use IEXBase\TronAPI\Tron;

//$tron->createAccount()
//#var_dump($tron->changeAccountName('address', 'NewName'));
class TronClient{
    private Tron $tron;
    public  $usdtContractAddress = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';
    public  $fullNode = 'https://api.trongrid.io';
    public  $solidityNode = 'https://api.trongrid.io';
    public  $eventServer = 'https://api.trongrid.io';
    public  $torProxy = 'socks5h://127.0.0.1:9050';
    public  $timeout = 3000;


    public function init($timeout=3000,?string $torProxy = 'socks5h://127.0.0.1:9050') {
        $full = new TorHttpProvider($this->fullNode, $this->timeout, false, false, [], '/', $this->torProxy);
        $solidity = new TorHttpProvider($this->solidityNode, $this->timeout, false, false, [], '/', $this->torProxy);
        $event = new TorHttpProvider($this->eventServer, $this->timeout, false, false, [], '/', $this->torProxy);
        $this->tron = new Tron($full, $solidity, $event);
    }









    //* 设置发送方地址和私钥
    public function setCredentials(string $fromAddress, string $privateKey): void{
        $this->tron->setAddress($fromAddress);
        $this->tron->setPrivateKey($privateKey);
    }





    public function getTrxBalance(string $address = null, bool $fromTron = true): mixed{

        return $this->tron->getBalance($address, $fromTron);
    }

                                                                                                              //发送 TRX
    public function sendTrx(string $toAddress, float|int $amount): mixed{
        return $this->tron->send($toAddress, $amount);
    }

                                                                                                                 //获取最新区块
    public function getLatestBlocks(int $count = 2): mixed{
        return $this->tron->getLatestBlocks($count);
    }

                                                                                                             //取USDT余额
    public function getUsdtRawBalance($fromAddress, $usdtContractAddress=''): mixed{
        return  $this->getContract($usdtContractAddress)->balanceOf($fromAddress);
    }




    public function getUsdtContractAddress(): string{
        return $this->usdtContractAddress;
    }

    public function setUsdtContractAddress(string $usdtContractAddress): void{
        $this->usdtContractAddress = $usdtContractAddress;
    }



                                                                                                                     //转帐usdt
    public function transfer(  string $to, string $amount): array{
        return $this->getContract()->transfer($to, $amount);
    }




    public function getContract($usdtContractAddress=''){
        if (empty($usdtContractAddress))$usdtContractAddress=$this->usdtContractAddress;

        return  $this->tron->contract($usdtContractAddress);
    }


                                                                                          //如果你还要支持 createAccount/changeAccountName，可在这里继续封装
    public function getTronInstance(): Tron{
        return $this->tron;
    }
}
