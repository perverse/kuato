<?php

namespace Kuato\Traits;

use Crypt;

trait EncryptableTrait 
{
	public function getAttribute($key)
	{
		$value = parent::getAttribute($key);

		if(in_array($key, $this->encryptable)):

			if( ! empty($value) ):
				$value = Crypt::decrypt($value);
			endif;  

		endif;

		return $value;
	}

	public function setAttribute($key, $value)
	{
		if (in_array($key, $this->encryptable)):
			$value = Crypt::encrypt($value);
		endif;
		
		return parent::setAttribute($key, $value);
	}
}
