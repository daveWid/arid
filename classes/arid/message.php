<?php defined('ARID') or die('No direct script access.');
/**
 * Message is a class that lets you easily view messages in your application.
 *
 * @package		Arid
 * @author		Dave Widmer
 * @see			http://www.davewidmer.net
 * @copyright	2011 © Dave Widmer
 */
class Arid_Message
{
	/**
	 * Constants to use for the types of messages that can be set.
	 */
	const ERROR = 'error';
	const NOTICE = 'notice';
	const SUCCESS = 'success';
	const WARN = 'warn';

	/**
	 * @var	mixed	The message to display.
	 */
	public $message;

	/**
	 * @var	string	The type of message.
	 */
	public $type;

	/**
	 * Creates a new Message instance.
	 *
	 * @param	string	Type of message
	 * @param	mixed	Message to display, either string or array
	 */
	public function __construct($type, $message)
	{
		$this->type = $type;
		$this->message = $message;
	}

	/**
	 * Displays the message.
	 *
	 * @param	{string}	The name of the view that is loaded when displaying
	 * @return	{string}	HTML for message
	 */
	public function render($view = "message")
	{
		return View::factory($view)->set(array(
			'type' => $this->type,
			'message' => $this->message,
		))->render();
	}

	/**
	 * Gets a string representation of the message class.
	 *
	 * @return	{string}
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Sets a message.
	 *
	 * @param	string	Type of message
	 * @param	mixed	Array/String for the message
	 * @return	void
	 */
	public static function set($type, $message)
	{
		return new Message($type, $message);
	}

	/**
	 * Sets an error message.
	 *
	 * @param	mixed	String/Array for the message(s)
	 * @return	void
	 */
	public static function error($message)
	{
		return Message::set(Message::ERROR, $message);
	}

	/**
	 * Sets a notice.
	 *
	 * @param	mixed	String/Array for the message(s)
	 * @return	void
	 */
	public static function notice($message)
	{
		return Message::set(Message::NOTICE, $message);
	}

	/**
	 * Sets a success message.
	 *
	 * @param	mixed	String/Array for the message(s)
	 * @return	void
	 */
	public static function success($message)
	{
		return Message::set(Message::SUCCESS, $message);
	}

	/**
	 * Sets a warning message.
	 *
	 * @param	mixed	String/Array for the message(s)
	 * @return	void
	 */
	public static function warn($message)
	{
		return Message::set(Message::WARN, $message);
	}

}
