<?php

namespace Core\Forms;
class TFormGPS extends TFormControlText implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::GPS, __('err.bad_gps_format', 'Chybný formát GPS souřadnic'));
    }

    public function setValue($text) :TFormControl
    {
        $text = TRule::formatGPS($text);
        parent::setValue($text);
        return $this;
    }
}
