<?php

namespace Core\Forms;
class TReset extends TFormControl implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('button', ['type'=>'reset','name'=>$name]);
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
}
