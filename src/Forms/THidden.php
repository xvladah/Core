<?php

class THidden extends TFormControl implements IFormControl
{
    public function __construct($name, $value)
    {
        parent::__construct('input', ['type'=>'hidden','name'=>$name, 'value'=>$value]);
    }
}
