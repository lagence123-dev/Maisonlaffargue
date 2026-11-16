<?php

namespace MoneticoPaiement\Sdk\Response\PaymentService;

class Data
{
    private ?string $MD = null;
    private ?string $PaReq = null;
    private ?string $creq = null;
    private ?string $threeDSSessionData = null;

    private ?string $threeDSMethodData = null;

    public function __construct(array $data){
        if(isset($data['MD'])){
            $this->setMD($data['MD']);
        }

        if(isset($data['PaReq'])){
            $this->setPaReq($data['PaReq']);
        }

        if(isset($data['creq'])){
            $this->setCreq($data['creq']);
        }

        if(isset($data['threeDSSessionData'])){
            $this->setThreeDSSessionData($data['threeDSSessionData']);
        }

        if(isset($data['threeDSMethodData'])){
            $this->setThreeDSMethodData($data['threeDSMethodData']);
        }
    }


    public function toArray(): array
    {
        return array_filter([
            'MD' => $this->getMD(),
            'PaReq' => $this->getPaReq(),
            'creq' => $this->getCreq(),
            'threeDSSessionData' => $this->getThreeDSSessionData(),
            'threeDSMethodData' => $this->getThreeDSMethodData(),
        ], function ($value) {
            return !is_null($value);
        });
    }
    public function getMD(): ?string
    {
        return $this->MD;
    }

    public function setMD(string $MD): void
    {
        $this->MD = $MD;
    }

    public function getPaReq(): ?string
    {
        return $this->PaReq;
    }

    public function setPaReq(string $PaReq): void
    {
        $this->PaReq = $PaReq;
    }

    public function getCreq(): ?string
    {
        return $this->creq;
    }

    public function setCreq(string $creq): void
    {
        $this->creq = $creq;
    }

    public function getThreeDSSessionData(): ?string
    {
        return $this->threeDSSessionData;
    }

    public function setThreeDSSessionData(string $threeDSSessionData): void
    {
        $this->threeDSSessionData = $threeDSSessionData;
    }

    public function getThreeDSMethodData(): ?string
    {
        return $this->threeDSMethodData;
    }

    public function setThreeDSMethodData(?string $threeDSMethodData): void
    {
        $this->threeDSMethodData = $threeDSMethodData;
    }


}