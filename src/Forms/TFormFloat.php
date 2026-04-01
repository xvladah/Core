<?php

namespace Core\Forms;
class TFormFloat extends TFormNumericControl implements IFormControl
{
    private ?int $decimals  = null;
    private ?string $separator = null;

    public function __construct($name, int $decimals)
    {
        parent::__construct($name);
        $this->setDecimals($decimals);
        $this->setSeparator('.');

        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::FLOAT, __('err.bad_float_format', 'Chybný formát reálného čísla'));
    }

    public function setValue($text) :TFormControl
    {
        $zal = str_replace(' ', '', $text);
        $zal = str_replace(',', '.', $zal);

        if($this->getSeparator() != '.' && $this->getSeparator() != ',')
            $zal = str_replace($this->getSeparator(), '.', $zal);

        if(is_float($zal) || is_numeric($zal))
            parent::setValue(number_format($zal, $this->getDecimals(), $this->getSeparator(), $this->getThousandSep()));
        else
            parent::setValue($text);

        return $this;
    }

    public function getValue()
    {
        $value = str_replace($this->getThousandSep(), '', parent::getValue());
        if($this->separator != '.')
            return str_replace($this->getSeparator(), '.', $value);
        else
            return $value;
    }

    public function setDecimals(int $decimals) :TFormNumericControl
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function getDecimals() :int
    {
        return $this->decimals;
    }

    public function setSeparator(string $separator) :TFormNumericControl
    {
        $this->separator = $separator;
        return $this;
    }

    public function getSeparator() :string
    {
        return $this->separator;
    }
}
