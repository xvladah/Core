<?php

namespace Core\Forms;
class TText extends TFormControlText implements IFormControl
{

    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'text','name'=>$name,'autocomplete'=>'off']);
    }

}
