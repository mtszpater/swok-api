<?php
class ConsoleReader
{
    private $currentFunctionName;
    private $currentArgs;

    public function _readLine(){
        $this->currentArgs = array();


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

    public function getCurrentFunctionName()
    {
        return $this->currentFunctionName;
    }

    public function getCurrentArgs()
    {
        return $this->currentArgs;
    }
}