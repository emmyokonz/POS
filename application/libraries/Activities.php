<?php

class Activities
{

	public $tables = array();

	function __construct()
	{

		// initialize db tables data
		$this->tables = $this->config->item('tables');

	}


	/**
	* __call
	*
	* Acts as a simple way to call model methods without loads of stupid alias'
	*
	* @param string $method
	* @param array  $arguments
	*
	* @return mixed
	* @throws Exception
	*/
	public function __call($method, $arguments)
	{
		if (method_exists( $this->ion_auth_model, $method) ) {
			return call_user_func_array( array($this->ion_auth_model,$method), $arguments);
		}
		if (method_exists( $this->ion_auth, $method) ) {
			return call_user_func_array( array($this->ion_auth,$method), $arguments);
		}
		throw new Exception('Undefined method Permission::' . $method . '() called');
	}

	/**
	* __get
	*
	* Enables the use of CI super-global without having to define an extra variable.
	*
	* I can't remember where I first saw this, so thank you if you are the original author. -Militis
	*
	* @param    string $var
	*
	* @return    mixed
	*/
	public function __get($var)
	{
		return get_instance()->$var;
	}
	 
	/**
     * Generates the SELECT portion of the query
     */
    public function select($select = '*', $escape = null)
    {
    	$this->db->select($select, $escape);
    	return $this;
    }
    
    // ------------------------------------------------------------------------

	/**
	 * Return an array of activities table fields.
	 * 
	 * @access 	public
	 * @param 	none
	 * @return 	array
	 */
	public function fields()
	{
		if (isset($this->fields))
		{
			return $this->fields;
		}

		$this->fields = $this->db->list_fields($this->tables['activities']);
		return $this->fields;
	}
	
	/**
	 * Create a new activity log.
	 *
	 * @access 	public
	 * @param 	array 	$data 	Array of data to insert.
	 * @return 	int 	The new activity ID if created, else false.
	 */
	public function create(array $data = array())
	{
		// Without $data, nothing to do.
		if (empty($data))
		{
			return false;
		}

		// Multiple activities?
		if (isset($data[0]) && is_array($data[0]))
		{
			$ids = array();
			foreach ($data as $_data)
			{
				$ids[] = $this->create($_data);
			}

			return $ids;
		}

		(isset($data['controller'])) OR $data['controller'] = $this->router->fetch_class();
		(isset($data['method']))     OR $data['method']     = $this->router->fetch_method();
		(isset($data['action_time'])) OR $data['action_time'] = time();
		(isset($data['ip_address'])) OR $data['ip_address'] = $this->input->ip_address();
		
		// Proceed to creation and return the ID.
		$this->db->insert($this->tables['activities'], $data);
		return $this->db->insert_id();
	}

	// ------------------------------------------------------------------------

	/**
	 * Retrieve a single activity by its ID.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten to use "get_by" method.
	 * 
	 * @access 	public
	 * @param 	int 	$id 	The activity's ID.
	 * @return 	object if found, else null.
	 */
	public function get($id)
	{
		// Getting by id?
		if (is_numeric($id))
		{
			return $this->get_by('id', $id);
		}

		// Otherwise, let "get_by" method handle the rest.
		return $this->get_by($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Retrieve a single activity by arbitrary WHERE clause.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten for better code readability and performance.
	 * 
	 * @access 	public
	 * @param 	mixed 	$field 	Column name or associative array.
	 * @param 	mixed 	$match 	Comparison value, array or null.
	 * @return 	object if found, else null.
	 */
	public function get_by($field, $match = null)
	{
		// We start with an emoty $activity.
		$activity = false;

		// Attempt to get the entity from database.
		$db_activity = $this->db
			->where($field, $match, 1, 0)
			->order_by('id', 'DESC')
			->get($this->tables['activities'])
			->row();

		// If found, we create its object.
		if ($db_activity)
		{
			$activity = new KB_Activity($db_activity);
		}

		// Return the final result.
		return $activity;
	}

	// ------------------------------------------------------------------------

	/**
	 * Retrieve multiple activities by arbitrary WHERE clause.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten for better code readability and performance
	 *         			and of let the parent handle the WHERE clause.
	 *
	 * @access 	public
	 * @param 	mixed 	$field 	Column name or associative array.
	 * @param 	mixed 	$match 	Comparison value, array or null.
	 * @param 	int 	$limit 	Limit to use for getting records.
	 * @param 	int 	$offset Database offset.
	 * @return 	array of objects if found, else null.
	 */
	public function get_many($field = null, $match = null, $limit = 0, $offset = 0)
	{
		// We start with an empty $activities.
		$activities = false;

		// Attempt to get activities from database.
		$db_activities = $this->db
			->where($field, $match)
			->limit($limit)
			->offset($offset)
			->order_by('id', 'DESC')
			->get($this->tables['activities'])
			->result();

		// If we found any, create their objects.
		if ($db_activities)
		{
			foreach ($db_activities as $db_activity)
			{
				$activities[] = new KB_Activity($db_activity);
			}
		}

		// Return the final result
		return $activities;
	}

	// ------------------------------------------------------------------------

	/**
	 * Retrieve all activities.
	 * @access 	public
	 * @param 	int 	$limit 	Limit to use for getting records.
	 * @param 	int 	$offset Database offset.
	 * @return 	array o objects if found, else null.
	 */
	public function get_all($limit = 0, $offset = 0)
	{
		// We start with an empty $activities.
		$activities = false;

		// Attempt to get activities from database.
		$db_activities = $this->db
			->limit($limit)
			->offset($offset)
			->order_by('id', 'DESC')
			->get($this->tables['activities'])
			->result();

		// If we found any, create their objects.
		if ($db_activities)
		{
			foreach ($db_activities as $db_activity)
			{
				$activities[] = new KB_Activity($db_activity);
			}
		}

		// Return the final result
		return $activities;
	}

	// ------------------------------------------------------------------------

	/**
	 * This method is used in order to search activities table.
	 *
	 * @since 	1.3.2
	 *
	 * @access 	public
	 * @param 	mixed 	$field
	 * @param 	mixed 	$match
	 * @param 	int 	$limit
	 * @param 	int 	$offset
	 * @return 	mixed 	array of objects if found any, else false.
	 */
	public function find($field, $match = null, $limit = 0, $offset = 0)
	{
		// We start with empty activities
		$activities = false;

		// Attempt to find activities.
		$db_activities = $this->db
			->find($field, $match, $limit, $offset)
			->order_by('id', 'DESC')
			->get('activities')
			->result();

		// If we found any, we create their objects.
		if ($db_activities)
		{
			foreach ($db_activities as $db_activity)
			{
				$activities[] = new KB_Activity($db_activity);
			}
		}

		// Return the final result.
		return $activities;
	}

	// ------------------------------------------------------------------------

	/**
	 * Update a single entity by it's ID.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten for better usage.
	 * 
	 * @access 	public
	 * @param 	mixed 	$id 	The activity's ID or array of WHERE clause.
	 * @param 	array 	$data 	Array of data to update.
	 * @return 	bool
	 */
	public function update($id, array $data = array())
	{
		// Updating by ID?
		if (is_numeric($id))
		{
			return $this->update_by(array('id' => $id), $data);
		}

		// Otherwise, let "update_by" handle the rest.
		return $this->update_by($id, $data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update a single or multiple activities by arbitrary WHERE clause.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten the let the parent handle WHERE clause.
	 * 
	 * @access 	public
	 * @return 	bool
	 */
	public function update_by()
	{
		// Collect arguments first and make sure there are some.
		$args = func_get_args();
		if (empty($args))
		{
			return false;
		}

		// Data to set is always the last argument.
		$data = array_pop($args);
		if ( ! is_array($data) OR empty($data))
		{
			return false;
		}

		// Start updating/
		$this->db->update($data);

		// If there are arguments left, use the as WHERE clause.
		if ( ! empty($args))
		{
			// Get rid of nasty deep array.
			(is_array($args[0])) && $args = $args[0];

			// Let the parent generate the WHERE clause.
			$this->db->where($args);
		}

		// Proceed to update.
		$this->db->update('activities');
		return ($this->db->affected_rows() > 0);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete a single activity by its ID.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten to use "delete_by" method.
	 * 
	 * @access 	public
	 * @param 	mixed 	$id 	The activity's ID or array of WHERE clause.
	 * @return 	bool
	 */
	public function delete($id)
	{
		// Deleting by ID?
		if (is_numeric($id))
		{
			return $this->delete_by('id', $id, 1, 0);
		}

		// Otherwise, let "delete_by" handle the rest.
		return $this->delete_by($id, null, 1, 0);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete multiple activities by arbitrary WHERE clause.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten for better code readability and performance,
	 *         			add optional limit and offset and let the parent handle
	 *         			generating the WHERE clause.
	 *
	 * @access 	public
	 * @param 	mixed 	$field 	Column name or associative array.
	 * @param 	mixed 	$match 	Comparison value, array or null.
	 * @param 	int 	$limit
	 * @param 	int 	$offset
	 * @return 	bool 	true if any records deleted, else false.
	 */
	public function delete_by($field = null, $match = null, $limit = 0, $offset = 0)
	{
		// Let's delete.
		$this->db
			->where($field, $match, $limit, $offset)
			->delete('activities');

		// See if there are affected rows.
		return ($this->db->affected_rows() > 0);
	}

	// --------------------------------------------------------------------

	/**
	 * Quick access to log activity.
	 * @access 	public
	 * @param 	int 	$user_id
	 * @param 	string 	$activity
	 * @param 	string 	$controller 	the controller details
	 * @return 	int 	the activity id.
	 */
	public function log_activity($user_id, $activity)
	{
		// Both user's ID and activity are required.
		if (empty($user_id) OR empty($activity))
		{
			return false;
		}

		return $this->create(array(
			'user_id'  => $user_id,
			'action' => $activity,
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * Count activities by arbitrary WHERE clause.
	 *
	 * @since 	1.3.0
	 *
	 * @access 	public
	 * @param 	mixed 	$field
	 * @param 	mixed 	$match
	 * @param 	int 	$limit
	 * @param 	int 	$offset
	 * @return 	int
	 */
	public function count($field = null, $match = null, $limit = 0, $offset = 0)
	{
		// Let's build the query first.
		$query = $this->db
			->where($field, $match, $limit, $offset)
			->get('activities');

		// We return the count.
		return $query->num_rows();
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete all activities of which the entity no longer exist.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten for better code readability and performance,
	 *         			and add optional limit and offset.
	 *
	 * @access 	public
	 * @param 	int 	$limit
	 * @param 	int 	$offset
	 * @return 	bool
	 */
	public function purge($limit = 0, $offset = 0)
	{
		// Get only users IDS.
		$ids = $this->db->entities->get_ids('type', 'user');

		// Let's delete.
		$this->db
			->where('!user_id', $ids, $limit, $offset)
			->delete('activities');

		return ($this->db->affected_rows() > 0);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('log_activity'))
{
	/**
	 * Log user's activity.
	 * @param 	int 	$user_id
	 * @param 	string 	$activity
	 * @return 	int 	the activity id.
	 */
	function log_activity($user_id, $activity)
	{
		return get_instance()->activities->log_activity($user_id, $activity);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_activity'))
{
	/**
	 * Retrieve a single activity by its ID or arbitrary WHERE clause.
	 * @access 	public
	 * @param 	mixed 	$field 	ID, column name or associative array.
	 * @param 	mixed 	$match 	Comparison value, array or null.
	 * @return 	object if found, else null.
	 */
	function get_activity($field, $match = null)
	{
		// In case of using the ID.
		if (is_numeric($field))
		{
			return get_instance()->activities->get($field);
		}

		return get_instance()->activities->get_by($field, $match);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_activities'))
{
	/**
	 * Retrieve multiple activities by arbitrary WHERE clause or 
	 * retrieve all activities if no arguments passed.
	 * @access 	public
	 * @param 	mixed 	$field 	Column name or associative array.
	 * @param 	mixed 	$match 	Comparison value, array or null.
	 * @return 	array of objects if found, else null.
	 */
	function get_activities($field = null, $match = null, $limit = 0, $offset = 0)
	{
		return get_instance()->activities->get_many($field, $match, $limit, $offset);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('count_activities'))
{
	/**
	 * Count activities by arbitrary WHERE clause.
	 *
	 * @since 	1.3.0
	 *
	 * @param 	mixed 	$field
	 * @param 	mixed 	$match
	 * @param 	int 	$limit
	 * @param 	int 	$offset
	 * @return 	int
	 */
	function count_activities($field = null, $match = null, $limit = 0, $offset = 0)
	{
		return get_instance()->activities->count($field, $match, $limit, $offset);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('purge_activities'))
{
	/**
	 * Delete all activities of which the entity no longer exist.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.0 	Rewritten to follow method structuer.
	 *
	 * @param 	int 	$limit
	 * @param 	int 	$offset
	 * @return 	bool
	 */
	function purge_activities($limit = 0, $offset = 0)
	{
		return get_instance()->activities->purge($limit, $offset);
	}
}
// ------------------------------------------------------------------------

/**
 * KB_Activity
 *
 * @package 	CodeIgniter
 * @subpackage 	Skeleton
 * @author 		Kader Bouyakoub <bkader[at]mail[dot]com>
 * @link 		https://goo.gl/wGXHO9
 * @copyright 	Copyright (c) 2018, Kader Bouyakoub (https://goo.gl/wGXHO9)
 * @since 		1.3.0
 */
class KB_Activity
{
	/**
	 * Activity data container.
	 * @var 	object
	 */
	public $data;

	/**
	 * The activity's ID.
	 * @var 	integer
	 */
	public $id = 0;

	/**
	 * Array of data awaiting to be updated.
	 * @var 	array
	 */
	protected $queue = array();
	
	/**
	 * Constructor.
	 *
	 * Retrieves the activity data and passes it to KB_Activity::init().
	 *
	 * @access 	public
	 * @param 	mixed	 $id 	Activity's ID, activityname, object or WHERE clause.
	 * @return 	void
	 */
	public function __construct($id = 0) {
		// In case we passed an instance of this object.
		if ($id instanceof KB_Activity) {
			$this->init($id->data);
			return;
		}

		// In case we passed the entity's object.
		elseif (is_object($id)) {
			$this->init($id);
			return;
		}

		if ($id) {
			$activity = get_activity($id);
			if ($activity) {
				$this->init($activity->data);
			} else {
				$this->data = new stdClass();
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Sets up object properties.
	 * @access 	public
	 * @param 	object
	 */
	public function init($activity) {
		$this->data = $activity;
		$this->id   = (int) $activity->id;

		// We add user details to the $data object.
		//$this->data->user = get_user($activity->user_id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Magic method for checking the existence of a property.
	 * @access 	public
	 * @param 	string 	$key 	The property key.
	 * @return 	bool 	true if the property exists, else false.
	 */
	public function __isset($key) {
		// Just make it possible to use ID.
		if ('ID' == $key) {
			$key = 'id';
		}

		// Return true only if found in $data or this object.
		return (isset($this->data->{$key}) OR isset($this->{$key}));
	}

	// ------------------------------------------------------------------------

	/**
	 * Magic method for getting a property value.
	 * @access 	public
	 * @param 	string 	$key 	The property key to retrieve.
	 * @return 	mixed 	Depends on the property value.
	 */
	public function __get($key) {
		// We start with an empty value.
		$value = false;

		// Is if found in $data object?
		if (isset($this->data->{$key})) {
			$value = $this->data->{$key};
		}

		// Then we return the final result.
		return $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Magic method for setting a property value.
	 * @access 	public
	 * @param 	string 	$key 	The property key.
	 * @param 	mixed 	$value 	The property value.
	 */
	public function __set($key, $value) {
		// Just make it possible to use ID.
		if ('ID' == $key) {
			$key = 'id';
		}

		// If found, we make sure to set it.
		$this->data->{$key} = $value;

		// We enqueue it for later use.
		$this->queue[$key]  = $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Magic method for unsetting a property.
	 * @access 	public
	 * @param 	string 	$key 	The property key.
	 */
	public function __unset($key) {
		// Remove it from $data object.
		if (isset($this->data->{$key})) {
			unset($this->data->{$key});
		}

		// We remove it if queued.
		if (isset($this->queue[$key])) {
			unset($this->queue[$key]);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Method for checking the existence of an activity in database.
	 * @access 	public
	 * @param 	none
	 * @return 	bool 	true if the activity exists, else false.
	 */
	public function exists() {
		return ( ! empty($this->id));
	}

	// ------------------------------------------------------------------------

	/**
	 * Method for checking the existence of a property.
	 * @access 	public
	 * @param 	string 	$key 	The property key.
	 * @return 	bool 	true if the property exists, else false.
	 */
	public function has($key) {
		return $this->__isset($key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Returns an array representation of this object data.
	 *
	 * @since 	1.3.3
	 *
	 * @access 	public
	 * @return 	array
	 */
	public function to_array() {
		return get_object_vars($this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Method for setting a property value.
	 * @access 	public
	 * @param 	string 	$key 	The property key.
	 * @param 	string 	$value 	The property value.
	 * @return 	object 	we return the object to make it chainable.
	 */
	public function set($key, $value) {
		$this->__set($key, $value);
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Method for getting a property value.
	 * @access 	public
	 * @param 	string 	$key 	The property key.
	 * @return 	mixed 	Depends on the property's value.
	 */
	public function get($key) {
		return $this->__get($key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Method for updating the activity in database.
	 *
	 * @since 	1.3.0
	 * @since 	1.4.0 	$value can be null if $key is an array
	 * 
	 * @access 	public
	 * @param 	string 	$key 	The field name.
	 * @param 	mixed 	$value 	The field value.
	 * @return 	bool 	true if updated, else false.
	 */
	public function update($key, $value = null) {
		// We make sure things are an array.
		$data = (is_array($key)) ? $key : array($key => $value);

		// Keep the status in order to dequeue the key.
		$status = update_activity($this->id, $data);

		if ($status === true) {
			foreach ($data as $k => $v) {
				if (isset($this->queue[$k])) {
					unset($this->queue[$k]);
				}
			}
		}

		return $status;
	}

	// ------------------------------------------------------------------------

	/**
	 * Method for saving anything changes.
	 * @access 	public
	 * @param 	void
	 * @return 	bool 	true if updated, else false.
	 */
	public function save() {
		// We start if FALSE status.
		$status = false;

		// If there are enqueued changes, apply them.
		if ( ! empty($this->queue)) {
			$status = update_activity($this->id, $this->queue);

			// If the update was successful, we reset $queue array.
			if ($status === true) {
				$this->queue = array();
			}
		}

		// We return the final status.
		return $status;
	}

	// ------------------------------------------------------------------------

	/**
	 * Method for retrieving the array of data waiting to be saved.
	 * @access 	public
	 * @return 	array
	 */
	public function dirty() {
		return $this->queue;
	}

}
