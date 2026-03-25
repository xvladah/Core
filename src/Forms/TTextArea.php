<?php

class TTextArea extends TFormControlText implements IFormControl
{

    public function __construct($name, $text = '')
    {
        parent::__construct('textarea', ['name'=>$name]);
        $this->nodeValue = $text;
    }

    public function setCols(int $cols) :TFormControl
    {
        $this->attributes->add('cols', $cols);
        return $this;
    }

    public function setRows(int $rows) :TFormControl
    {
        $this->attributes->add('rows', $rows);
        return $this;
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

    public function html() :string
    {
        return '<'.$this->nodeName.$this->attributes->html().'>&#10;'.$this->nodeValue.'</'.$this->nodeName.'>';
    }
}
