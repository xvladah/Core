<?php

class TButton extends TFormControl implements IFormControl
{

    public function __construct($name)
    {
        parent::__construct('button', ['type'=>'button','name'=>$name]);
    }

    public function setValue($value) :TFormControl
    {
        $this->nodeValue = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->nodeValue;
    }

    public function doGet(string $location) :TFormControl
    {
        $this->attributes->add('onclick', 'javascript:window.location.href=\''.$location.'\'');
        return $this;
    }
}
