<?php defined('ARID') or die('No direct script access.');
/**
 * A specialized Page class that holds extra information when processing a
 * Wordpress page.
 *
 * @package		Arid
 * @author		Dave Widmer
 * @copyright	2011 © Dave Widmer
 */
class Arid_Page
{
	/** The default page type. */
	public static $default = "blog";

	/**
	 * A filter to search for pages
	 * If you want to filter by more pages, this is where you want to add them
	 */
	public static $page_types = array(
		"blog" => array('view' => "posts", 'title' => "Blog"),
		"page" => array('view' => "page", 'title' => ":title"),
		"single" => array('view' => "single", 'title' => ":title"),
		"category" => array('view' => "archive", 'title' => "Category » :query"),
		"author" => array('view' => "archive", 'title' => "Author » :query"),
		"date" => array('view' => "archive", 'title' => ":date"),
		"archive" => array('view' => "archive", 'title' => "Archive » :query"),
		"error404" => array('view' => "404", 'title' => "Page Not Found"),
		"search-no-results" => array('view' => "search-no-results", 'title' => "No Results For :search"),
		"search" => array('view' => "search", 'title' => "Search Results » :search"),
	);

	/**
	 * A list of tokens and the internal functions they call.
	 */
	public static $tokens = array(
		':title' => 'get_title',
		':query' => 'get_query',
		':date'	=> 'get_date',
		':search' => 'get_search'
	);

	/** The type of page. */
	public $type;

	/** The name of the view. */
	public $view;

	/** The title of the page. */
	public $title;

	/** Assoc array of the filters that were applied. */
	public $filters = array();

	/**
	 * Constructor
	 *
	 * @param	array	A list of classes to search for when creating this page
	 */
	public function __construct(array $classes)
	{
		$type = Page::$default;

		// We have to reverse the classes because Wordpress puts them in a 
		// reverse order
		$classes = array_reverse($classes);

		foreach (array_keys(Page::$page_types) as $tag)
		{
			if (in_array($tag, $classes) !== FALSE)
			{
				$type = $tag;
				break;
			}
		}

		$this->type = $type;
		$this->view = Page::$page_types[$type]['view'];
		$this->title = $this->format_title(Page::$page_types[$type]['title']);
	}

	/**
	 * Runs the title through some keywords to do some filtering.
	 *
	 * @param	string	Page title
	 * @return	string	Formatted title
	 */
	private function format_title($title)
	{
		foreach (Page::$tokens as $token => $func)
		{
			if (strpos($title, $token) !== false)
			{
				$title = str_replace($token, $this->{$func}(), $title);
			}
		}

		return $title;
	}

	/**
	 * Gets the title of the current page or post
	 *
	 * @return	string	The title
	 */
	private function get_title()
	{
		$title = get_the_title();
		$this->filters['title'] = $title;
		return $title;
	}

	/**
	 * Gets the queried data that WP_Query used.
	 *
	 * @return	string	The query string
	 */
	private function get_query()
	{
		$query = $this->query_params();
		$this->filters = array_merge($this->filters, $query);

		return implode(" » ", $query);
	}

	/**
	 * Gets a human readible date from WP_Query
	 *
	 * @return	string	Human readible date
	 */
	private function get_date()
	{
		$query = $this->query_params();
		$this->filters = array_merge($this->filters, $query);

		$year = Arr::get($query, 'year', null);
		$month = Arr::get($query, 'monthnum', null);

		$format = ($month !== null) ? "F Y" : "Y";
		return date($format, mktime(null, null, null, $month, 1, $year));
	}

	/**
	 * Gets the query parameters that WP_Query used.
	 *
	 * @return	array	List of key/values for the query
	 */
	private function query_params()
	{
		$query = array();

		global $wp_query;
		foreach ($wp_query->query as $key => $value)
		{
			$query[$key] = $value;
		}

		return $query;
	}

	/**
	 * Gets the searched phrase
	 *
	 * @return	string	The searched phrase
	 */
	private function get_search()
	{
		$s = Arr::get($_GET, "s", "");
		$this->filters['search'] = $s;
		return $s;
	}

}
