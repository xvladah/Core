<?php

class TFile extends TFormControl implements IFormControl
{
    public function __construct($name, $options = [])
    {
        parent::__construct('input', ['type'=>'file','name'=>$name]);
    }

    public function setSize($size)
    {
        return $this->attributes->add('size', $size);
    }

    public function getSize()
    {
        return $this->attributes->items['size'];
    }

    public function setMultiple($str = '') :TFormControl
    {
        $this->attributes->add('multiple', $str);
        return $this;
    }
}
