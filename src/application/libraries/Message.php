<?php
if (! defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Message Library
 *
 * @package Message
 * @subpackage Library
 */
class Message
{
	
	private $_ci;
	private $levels = array (
		'info' => '1',
		'success' => '2',
		'warning' => '3',
		'error' => '4',
		'validation' => '5',
		'question' => '6' 
	);

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_ci = & get_instance();
		$this->_ci->load->helper('message');
		$this->_ci->load->library('session');
	}

	/**
	 * Crear Mensaje
	 *
	 * @param string $message 
	 * @param string $level 
	 * @return void
	 */
	public function set($message = '', $level = 'info')
	{
		if (array_key_exists($level, $this->levels))
		{
			$this->_ci->session->set('notice_' . $this->levels [$level], $message);
			
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Retornar Mensaje
	 *
	 * @param string $level 
	 * @param string $keep 
	 * @return string
	 */
	public function get($level = 'info', $keep = FALSE)
	{
		if (! array_key_exists($level, $this->levels))
		{
			$level = 'info';
		}
		
		$message = $this->_ci->session->get('notice_' . $this->levels [$level]);
		
		if ($keep !== TRUE)
		{
			$this->_ci->session->delete('notice_' . $this->levels [$level]);
		}
		
		return $message;
	}

	/**
	 * Mantener Mensaje al proximo request
	 *
	 * @param string $level 
	 * @return bool
	 */
	
	public function keep($level)
	{
		
		$this->_ci->session->keep('notice_' . $this->levels [$level]);
		
		return TRUE;
	}
}
