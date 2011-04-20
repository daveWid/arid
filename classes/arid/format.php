<?php defined('ARID') or die('No direct script access.');
/**
 * A general formatting helper library
 *
 * @package		Arid
 * @author		Dave Widmer
 * @copyright	2011 © Dave Widmer
 */
class Arid_Format
{
	/**
	 * Formats a tweet with links.
	 *
	 * @param	string	$tweet	Unformatted Tweet
	 * @param	string			Formatted Tweet
	 */
	public static function tweet($tweet)
	{
		$search = array(
			'/http[s]?:\/\/\S+/', // URLs
			'/@[A-Za-z0-9-_]+/', // @names
			'/#[A-Za-z0-9-_]+/', // #hashtag
		);

		$replace = array(
			'<a href="${0}">${0}</a>',
			'<a href="http://twitter.com/${0}">${0}</a>',
			'<a href="http://search.twitter.com/search?q=${0}">${0}</a>',
		);

		list( $name, $tweet ) = explode(': ', $tweet, 2); // Remove the name at the beginning

		$tweet = preg_replace($search, $replace, $tweet);
		return str_replace( array('/@', '=#') , array('/', '=%23') , $tweet);
	}

}
