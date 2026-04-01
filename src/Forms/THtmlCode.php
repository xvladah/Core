<?php

namespace Core\Forms;
class THtmlCode extends TFormControl implements IFormControl
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

    public function setValue($text) :TFormControl
    {
        $this->nodeValue = $text;
        return $this;
    }

    public function getValue()
    {
        return $this->nodeValue;
    }
}
