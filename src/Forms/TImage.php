<?php

namespace Core\Forms;
class TImage extends TFormControl implements IFormControl
{
    public function __construct($name, $options = [])
    {
        parent::__construct('input', ['type'=>'image','name'=>$name]);
    }
}
