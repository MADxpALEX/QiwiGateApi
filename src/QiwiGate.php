<?php

class QiwiGate
{
    private $qiwiKey;
    private $accountKey;

    public $error = false;
    public $response;
    public $history;

    private $method;
    private $options;
    private $assoc;
    private $type;
    private $link;
    private $result;

    public function __construct($purseAPIKey, $accountAPIKey)
    {
        $this->qiwiKey    = $purseAPIKey;
        $this->accountKey = $accountAPIKey;
    }

    public function sendRequest($method, $options = [], $assoc = true)
    {
        $this->setProperties($method, $options, $assoc);
        $this->initializeTheQuery();
        $this->checkResult();

        return $this;
    }

    private function checkResult()
    {
        $result = json_decode($this->result);

        if ($result->status === 'error') {
            $this->error = true;
        }
    }

    private function initializeTheQuery()
    {
        $this->link = $this->getLink();

        $this->result = file_get_contents($this->link);

        $this->response = json_decode($this->result, $this->assoc);
    }

    private function setProperties($method, $options, $assoc)
    {
        $this->method  = $method
             ->options = $options
             ->type    = explode('.', $method)[0]
             ->assoc   = $assoc;
    }

    private function getLink()
    {
        $link = 'https://qiwigate.ru/api?key=' . $this->getKey() . '&method=' . $this->method;

        if (!empty($this->options)) {
            $link = $link . '&' . http_build_query($this->options);
        }

        return $link;
    }

    private function getKey()
    {
        if ($this->type === 'qiwi') {
            return $this->qiwiKey;
        }

        return $this->accountKey;
    }
}