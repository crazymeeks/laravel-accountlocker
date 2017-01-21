<?php namespace Crazymeeks\AccountLocker\Facade;

use Illuminate\Support\Facades\Facade;

class AccountLocker extends Facade{
	
	protected static function getFacadeAccessor(){
		return 'accountlocker';
	}
}