<?php namespace Crazymeeks\AccountLocker;

/**
 * The Account locker manager
 *
 * @author Jeff Claud<jeffclaud17@gmail.com>
 * @since 2017
 */

use App\User;
use Illuminate\Http\Request;
class AccountLocker{
	
	protected $user;

	protected $email = null;

	public function __construct(User $user){
		$this->user = $user;
	}


	/**
	 * Lock the account
	 *
	 * @param mixed           This can be an instance of Illuminate\Http\Request or user's email
	 * @return mixed
	 */
	public function lock($request){

		$this->checkRequirements($request);
		return $this->_execute();
	}

	/**
	 * Validate the method's param requirements
	 *
	 * @param mixed $request
	 * @return void
	 */
	protected function checkRequirements($request){
		if(is_null($request) || empty($request)){
			throw new \Crazymeeks\AccountLocker\Exceptions\ParameterCannotBeNullException('lock() method cannot be null or empty');
		}

		if($request instanceof Request){
			// get the email in the request
			$this->getEmailInRequest($request);
		}else{
			$this->email = $request;
		}

		if(!in_array(config('accountlocker.status_field_name'), $this->user->getFillable())){
			throw new \Crazymeeks\AccountLocker\Exceptions\FieldNotFoundException('The ' . config('accountlocker.status_field_name') . ' not found in the \'users\' table');
		}

		if(!in_array(config('accountlocker.login_attempts_field'), $this->user->getFillable())){
			throw new \Crazymeeks\AccountLocker\Exceptions\FieldNotFoundException('The ' . config('accountlocker.login_attempts_field') . ' not found in the \'users\' table');
		}
	}

	/**
	 * Lock the user here
	 *
	 * @param string $action        The action to be taken
	 * @return bool
	 */
	protected function _execute($action = null){
		$user = $this->user->where('email', $this->email)->first();
		if(count($user) > 0){
			// lock the user
			$startTime = date('Y-m-d H:i:s');
			$user->{config('accountlocker.status_field_name')} = is_null($action) ? 2 : 1;
			$user->{config('accountlocker.locktime_fields')[0]} = is_null($action) ? $startTime : 0;
			$user->{(config('accountlocker.locktime_fields')[1])} = is_null($action) ? date('Y-m-d H:i:s',strtotime(config('accountlocker.locked_duration'),strtotime($startTime))) : 0;
			$user->{config('accountlocker.login_attempts_field')} = is_null($action) ? 3 : 0;
			if(!$user->save()){
				throw new \Crazymeeks\AccountLocker\Exceptions\CannotLockUserException('Error locking the user. Please try again');
			}
			$message = is_null($action) ? config('accountlocker.lock_message') : config('accountlocker.unlock_message');
			return response()->json([$message], is_null($action) ? 423 : 200);
		}
		throw new \Crazymeeks\AccountLocker\Exceptions\UserNotFoundException('User not found');
	}

	/**
	 * Increments the value of `login_attempts` field in the database
	 *
	 * @param mixed           This can be an instance of Illuminate\Http\Request or user's email
	 * @return void
	 */
	public function addAttempts($request){

		$this->checkRequirements($request);

		$user = $this->user->where('email', $this->email)->first();
		if(count($user) > 0){
			
			$user->{config('accountlocker.login_attempts_field')} = (int)$user->{config('accountlocker.login_attempts_field')} + 1;
			$user->save();
			
			if($user->{config('accountlocker.login_attempts_field')} >= 3){
				$this->_execute();
			}
		}
	}

	/**
	 * Check the status of the user
	 *
	 * @param string $status
	 * @param mixed           This can be an instance of Illuminate\Http\Request or user's email
	 * @see config.php['values_of_status_text'] for values
	 * @return bool
	 */
	public function user_is($status, $request){
		if(!in_array($status, config('accountlocker.values_of_status_text'))){
			return ['error_code' => 400,
				'message' => 'Invalid parameter value. Please use these values: ' . 'active,inactive,locked and deleted'
			];
		}

		if($request instanceof Request){
			$this->getEmailInRequest($request);
		}else{
			$this->email = $request;
		}
		$user = $this->user->where('email', $this->email)->first();

		if(count($user) > 0){
			if($status == 'locked' && (string)$user->{config('accountlocker.status_field_name')} == '2'){
				return true;
			}elseif($status == 'active' && (string)$user->{config('accountlocker.status_field_name')} == '1'){
				return true;
			}elseif($status == 'inactive' && (string)$user->{config('accountlocker.status_field_name')} == '0'){
				return true;
			}elseif($status == 'deleted' && (string)$user->{config('accountlocker.status_field_name')} == '3'){
				return true;
			}
		}
		return false;
	}

	/**
	 * Lock the account
	 *
	 * @param mixed           This can be an instance of Illuminate\Http\Request or user's email
	 * @return mixed
	 */
	public function unlock($request){
		$this->checkRequirements($request);
		return $this->_execute(__FUNCTION__);
	}


	/**
	 * Get the email in the request
	 *
	 * @return string | throw Exception
	 */
	public function getEmailInRequest($request){
		if(!isset($request->email)){
			throw new \Crazymeeks\AccountLocker\Exceptions\EmailNotFoundInRequestException('email not found in the request');
		}

		return $this->email = $request->email;
	}

}