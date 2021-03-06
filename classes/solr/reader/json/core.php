<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Solr powered search for the Kohana Framework.
 *
 * @package    Solr
 * @author     Brandon Summers <brandon@brandonsummers.name>
 * @copyright  (c) 2011 Brandon Summers
 * @license    MIT
 */
class Solr_Reader_JSON_Core extends Solr_Reader {

	/**
	 * Parses the json response into an array.
	 *
	 * @return  array
	 */
	public function parse()
	{
		$body = $this->_response->body();
		return json_decode($body, TRUE);
	}

}