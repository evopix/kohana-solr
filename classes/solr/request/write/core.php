<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Solr powered search for the Kohana Framework.
 *
 * Credit where credit is due:
 *     Some inspiration taken from <https://github.com/buraks78/Logic-Solr-API>
 * 
 * @package    Solr
 * @author     Brandon Summers <brandon@brandonsummers.name>
 * @copyright  (c) 2011 Brandon Summers
 * @license    MIT
 */
class Solr_Request_Write_Core extends Solr_Request {

	/**
	 * @var  Solr_Writer  writer instance for format
	 */
	protected $_writer;

	/**
	 * @var  string  uri to the servlet that handles this request
	 */
	protected $_handler = 'update';

	/**
	 * @var  array  array of write data
	 */
	protected $_data = array();

	/**
	 * Overload construct to instantiate a writer for the request.
	 *
	 * @param   string  $host    the Solr host url
	 * @param   string  $reader  the read driver to use
	 * @param   string  $writer  the write driver to use
	 * @return  void
	 */
	public function __construct($host, $reader_type = NULL, $writer_type = NULL)
	{
		parent::__construct($host, $reader_type, $writer_type);
		$this->_writer = Solr_Writer::factory($this->_writer_type);
	}

	/**
	 * Sets and gets data for the request.
	 *
	 * @param   string  $type   the type of data
	 * @param   string  $name   name of the data
	 * @param   mixed   $value  value for the data
	 * @return  mixed
	 */
	protected function _data($type, $name, $value = NULL)
	{
		if ($value === NULL)
			return Arr::path($this->_data, $type.'.'.$name);

		$this->_data[$type][$name] = $value;
		return $this;
	}

	/**
	 * Executes the write request. Returns a write response.
	 *
	 * @return  Solr_Response_Write
	 */
	public function execute()
	{
		$data = $this->_writer->compile($this->_data);

		$request = Request::factory($this->_compile_url());
		$request->method('post');
		$request->post('stream.body', $data);
		$response = $request->execute();

		return $this->_response = new Solr_Response_Write($response);
	}

	/**
	 * Sets and gets the overwrite param for the request.
	 *
	 * @param   bool   $value  if TRUE newer documents will replace previously added documents with the same uniqueKey
	 * @return  mixed
	 */
	public function overwrite($value = NULL)
	{
		if ( ! $value)
			return $this->_data('add', 'overwrite');

		$this->_data('add', 'overwrite', $value);
		return $this;
	}

	/**
	 * Sets and gets the commit param for the request.
	 *
	 * @param   mixed  $value  array of commit attributes or a boolean
	 * @return  mixed
	 */
	public function commit($value = NULL)
	{
		if ( ! $value)
			return $this->_data('update', 'commit');

		if (is_array($value))
		{
			$this->_data('update', 'commit', $value);
		}
		elseif ((bool) $value === TRUE)
		{
			$this->_data('update', 'commit', (bool) $value);
		}

		return $this;
	}

	/**
	 * Sets and gets the commitWithin param for the request.
	 *
	 * @param   integer  $value  number of milliseconds within which to add the documents to the index
	 * @return  mixed
	 */
	public function commit_within($value = NULL)
	{
		if ( ! $value)
			return $this->_data('add', 'commitWithin');

		if ($value > 0)
		{
			$this->_data('add', 'commitWithin', $value);
		}

		return $this;
	}

	/**
	 * Sets and gets the optimize param for the request.
	 *
	 * @param   mixed  $value  array of optimize attributes or a boolean
	 * @return  mixed
	 */
	public function optimize($value = NULL)
	{
		if ( ! $value)
			return $this->_data('update', 'optimize');

		if (is_array($value))
		{
			$this->_data('update', 'optimize', $value);
		}
		elseif ((bool) $value === TRUE)
		{
			$this->_data('update', 'optimize', (bool) $value);
		}

		return $this;
	}

	/**
	 * Sets and gets the documents to add/update for the request.
	 *
	 * @param   array  $documents  array of documents to add/update
	 * @return  mixed
	 */
	public function documents(array $documents = NULL)
	{
		if ( ! $documents)
			return $this->_data('add', 'doc');

		$this->_data('add', 'doc', $documents);
		return $this;
	}

	/**
	 * Sets and gets the rollback param for the request.
	 *
	 * @param   bool   $value  if TRUE rolls back all add/deletes made to the index since the last commit
	 * @return  mixed
	 */
	public function rollback($value = NULL)
	{
		if ( ! $value)
			return $this->_data('rollback', 'rollback');

		if ((bool) $value === TRUE)
		{
			$this->_data('rollback', 'rollback', (bool) $value);
		}

		return $this;
	}

	/**
	 * Sets and gets a delete by id message for the request.
	 *
	 * @param   mixed  $id  array or id of document(s) to delete
	 * @return  mixed
	 */
	public function delete($id = NULL)
	{
		if ( ! $id)
			return $this->_data('delete', 'id');

		$this->_data('delete', 'id', $id);
		return $this;
	}

	/**
	 * Sets and gets a delete by query message for the request.
	 *
	 * @param   mixed  $query  search query for documents to delete
	 * @return  mixed
	 */
	public function delete_by_query($query = NULL)
	{
		if ( ! $query)
			return $this->_data('delete', 'query');

		$this->_data('delete', 'query', $query);
		return $this;
	}

}