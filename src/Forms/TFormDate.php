<?php

namespace Core\Forms;
class TFormDate extends TFormControlText implements IFormControl
{
    private $format = null;

    public function __construct($name, $format = 'd.m.Y')
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->setFormat($format);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::DATE, __('err.bad_date_format', 'Chybný formát datumu'));
    }

    public function setValue($text) :TFormControl
    {
        $tm = strtotime(str_replace('/', '.', $text));
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
