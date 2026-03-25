<?php

class TFormTime extends TFormControlText implements IFormControl
{
    private $format = null;

    public function __construct($name, $format = 'H:i')
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->setFormat($format);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::TIME, __('err.bad_time_format', 'Chybný formát času'));
    }

    public function setValue($text) :TFormControl
    {
        $tm = strtotime($text);
        if($tm !== false)
            return parent::setValue(Date($this->format, $tm));
        else
            return parent::setValue($text);
    }

    public function setFormat(string $format) :TFormControl
    {
        $this->format = $format;
        return $this;
    }
}
