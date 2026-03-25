<?php

class TSearch extends TFormControlText implements IFormControl
{

    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'search','name'=>$name,'autocomplete'=>'off']);
    }
}
