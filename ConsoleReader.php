<?php

/**
 * Created by PhpStorm.
 * User: pater
 * Date: 28.05.2017
 * Time: 17:05
 */
class ConsoleReader
{
    private $currentFunctionName;
    private $currentArgs;

    public function _readLine(){
        $stdin = fopen('php://stdin', 'r');
        $fg = fgets($stdin, 1024);
        $jsonIterator = new RecursiveIteratorIterator( new RecursiveArrayIterator(json_decode($fg, TRUE)), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($jsonIterator as $key => $val) {
            if (is_array($val)) {
                $this->currentFunctionName = $key;
            } else {
                $this->currentArgs[$key] = $val;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentFunctionName()
    {
        return $this->currentFunctionName;
    }

    /**
     * @return mixed
     */
    public function getCurrentArgs()
    {
        return $this->currentArgs;
    }
}