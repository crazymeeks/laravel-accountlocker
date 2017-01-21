<?php namespace Crazymeeks\AccountLocker\Exceptions;

/**
 * The Exceptions
 *
 * @author Jeff Claud<jeffclaud17@gmail.com>
 * @since 2017
 */

class CannotLockUserException extends AccountLockerException{
	
	/**
	 * Status code
	 * @var int
	 */
	 protected $statusCode = 400;
}