<?php

namespace Core\Forms;
class TFormTimeEx extends TFormControlText implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'time','name'=>$name]);
    }
}
