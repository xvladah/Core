<?php

namespace Core\Forms;
class TFormDateEx extends TFormControlText implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'date','name'=>$name]);
    }
}
