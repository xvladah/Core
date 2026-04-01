<?php

namespace Core\Forms;
class TFormSysInfo extends TFormControl implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('div', ['name'=>$name]);
    }

    public function validate() :bool
    {
        return true;
    }

    public function isDisabled() :bool
    {
        return true;
    }

    public function getValue()
    {
        return null;
    }

    public function setValue($value) :TFormControl
    {
        return $this;
    }
}
