<?php

class TOption extends TElement
{

    public function __construct($value, $nodeValue)
    {
        parent::__construct('option', ['value'=>$value]);

        if($nodeValue == '')
            $nodeValue = '&nbsp;';

        $this->nodeValue = $nodeValue;
    }
}
