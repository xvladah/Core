<?php

namespace Core\Forms;
class TMultiComboBox extends TComboBox implements IFormControl
{
    /** pokud je multicombobox pouzit ve formulari, musi se automaticky vybirat polozka, pokud je jenom jedna **/
    private bool $autoSelectFirst = false;

    public function __construct($name, $options = [])
    {
        if(!str_contains('[', $name))
            $name = $name.'[]';

        parent::__construct($name, $options);
        $this->setMultiple();
    }

    public function setMultiple($multiselect = true): static
    {
        if($multiselect)
            $this->attributes->add('multiple','multiple');
        else
            $this->attributes->delete('multiple');

        return $this;
    }

    public function setValue($value) :TFormControl
    {
        $this->value = (array)$value;
        return $this;
    }

    public function getValue()
    {
        return (array)$this->value;
    }

    /**
     * funkce je potreba pro automaticky vyber, pokud je multibox pouzit ve formulari
     * @param true $selectFirst
     * @return TFormControl
     */
    public function setAutoSelectFirst(bool $autoSelectFirst = true) :TFormControl
    {
        $this->autoSelectFirst = $autoSelectFirst;
        return $this;
    }

    public function validate() :bool
    {
   	 //   $result = parent::validate();
        $result = true;
        if($result)
        {
            if($this->isRequired())
            {
                $valid = false;

                if($this->childNodes->count() > 0)
                {
                    $values = $this->getValue();
                    $options = $this->getOptions();

                    if(is_array($values))
                    {
                        foreach($values as $value)
                        {
                            if(!in_array($value, $options))
                            {
                                $valid = false;
                                break;
                            } else
                                $valid = true;
                        }
                    } else {
                        $valid = in_array($values, $options);
                    }

                    if(!$valid)
                        $this->errors[':idselect'] = __('err.bad_value', 'Neplatná hodnota');
                } else
                    $this->errors[':idselect'] = __('err.no_values', 'Nenalezeny hodnoty');

                return $valid;
            } else
                return true;
        } else
            return false;
    }

    public function html() :string
    {
        foreach($this->childNodes->items as $node)
            $node->attributes->delete('selected');

        $first = false;

        $values = $this->getValue();
        if(is_array($values))
        {
            if(count($values) > 0)
            {
                foreach($this->childNodes->items as $node)
                {
                    if(in_array($node->attributes->items['value'], $values))
                        $node->attributes->add('selected', 'selected');
                }
            } else
                $first = true;
        } else {
            if($values != '')
            {
                foreach($this->childNodes->items as $node)
                    if($node->attributes->items['value'] == $values)
                    {
                        $node->attributes->add('selected', 'selected');
                        break;
                    }
            } else
                $first = true;
        }

        if($first && $this->autoSelectFirst)
        {
            if($this->childNodes->count() === 1)
            {
                foreach($this->childNodes->items as $node)
                {
                    $this->setValue($node->attributes->items['value']);
                    $node->attributes->add('selected', 'selected');
                    break;
                }
            }
        }

        return TElement::html();
    }

}
