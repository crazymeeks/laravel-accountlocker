<?php

/**
 * Package's config file
 *
 * @author Jeff Claud<jeffclaud17@gmail.com>
 * @since 2017
 */

return [
	

	/**
	 * The package version
	 */
	'version' => '1.0',


	/**
	 * The table name
	 */
	'table' => 'users',

	/**
	 * The name of field in our database table
	 */
	'status_field_name' => 'status',

	/**
	 * The field data type
	 */
	'status_type' => 'integer',

	/**
	 * The acceptable values of the status
	 *
	 * 0=inactive; 1=active; 2=locked; 3=deleted
	 */
	'values_of_status' => [0, 1, 2, 3],

	/**
	 * The text representation of the the key 'values_of_status'
	 */
	'values_of_status_text' => ['active', 'inactive', 'locked', 'deleted'],

	/**
	 * The name of fields in the table where
	 * we save the started and end time of lock
	 */
	'locktime_fields' => ['lock_time_started', 'end_lock_time'],

	/**
	 * The login attempt field name in the database
	 */
	'login_attempts_field' => 'login_attempts',

	/**
	 * The data type of locktime_fields
	 */
	'locktime_fields_type' => 'string|varchar',

	/**
	 * The lock duration. 1hr
	 */
	'locked_duration' => '+1 hour', // +30 minutes

	'lock_message' => 'User has been locked',

	'unlock_message' => 'User has been unlocked',
];