<?php

class TSubmit extends TFormControl implements IFormControl
{

    public function __construct($name)
    {
        parent::__construct('button', ['type'=>'submit','name'=>$name]);
    }

    public function setValue(mixed $value) :TFormControl
    {
        $this->nodeValue = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->nodeValue;
    }
}
