<?php

namespace Core\Forms;
class TLabel extends TFormControl implements IFormControl
{
    public function __construct()
    {
        parent::__construct('div', []);
    }

    public function validate() :bool
    {
        return true;
    }

    public function isDisabled() :bool
    {
        return true;
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
