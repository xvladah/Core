<?php

/**
 * Třída pro evidenci oprávnění uživatelů
 *
 * @name		TRights
 * @version		1.0
 * @author		vladimir.horky
 * @copyright	Vladimir Horky, 2018.
 */

declare(strict_types=1);

namespace Core\User;
abstract class TRights
{
	const int GUEST 		= -1;
	const string GUEST_STR	= 'G';

	const int USER 			= 1;
	const string USER_STR	= 'U';

	const int ADMIN  		= 4294967296; // max integer + 1
	const string ADMIN_STR	= 'A';

	protected array $items;

	public function __construct()
	{
		$this->items = [];
	}

	public function add(int $pravo_id, null|string|int $subject) :TRights
	{
		if(!key_exists($pravo_id, $this->items))
			$this->items[$pravo_id] = [];

		$this->items[$pravo_id][] = $subject;
		return $this;
	}

	public function count() :int
	{
		return count($this->items);
	}

	public function clear() :TRights
	{
		$this->items = [];
		return $this;
	}

	public function getItem(?int $ArrayIndex)
	{
		return $this->items[$ArrayIndex];
	}

	public function getItemValue(?int $ArrayIndex, ?int $RightIndex)
	{
		$item = $this->items[$ArrayIndex];
		return $item[$RightIndex];
	}

	public function hasRight(?int $right) :bool
	{
		foreach($this->items as $key => $values)
        {
			if(($right & $key) == $key)
				return true;
        }

		return false;
	}

	public function hasRightItem(int $right, ?int $subject) :bool
	{
		/*if(key_exists($right, $this->items))
		{
			foreach($this->items[$right] as $item)
			{
				if(($item & $subject) == $subject)
					return true;
			}
		}*/

        foreach($this->items as $key => $values)
        {
            if(($right & $key) == $key)
            {
                foreach($values as $value)
                    if(($value & $subject) == $subject)
                        return true;
            }
        }

		return false;
	}

	public function load(int $user_id): self
	{
		$this->add(self::USER, self::USER);
		
		if(in_array($user_id, TConfig::ADMINS))
			$this->add(self::ADMIN, self::ADMIN);

		return $this;
	}

	public function getRightsList(int $right) :string
	{
		$result = '';

		if(key_exists($right, $this->items))
		{
			foreach($this->items[$right] as $item)
			{
				if($result != '')
					$result .= ',';

				$result .= $item;
			}
		}

		return $result;
	}

	public function getRightsArray(int $right) :array
	{
		$result = [];

		if(key_exists($right, $this->items))
		{
			foreach($this->items[$right] as $item)
				$result[] = $item;
		}

		return $result;
	}

	public function getMainRightsArray() :array
	{
		return array_keys($this->items);
	}

}
