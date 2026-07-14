<?php
/**
 * Creates the ACTIONS-FILTERS.md markdown file, comprising of all
 * action and filter hooks parsed from the Plugin code.
 *
 * @package Postiz Auto Poster
 * @author WP Zinc
 */

// Setup Read Actions and Filters class.
require_once 'class-read-actions-filters.php';
$read_actions_filters = new Read_Actions_Filters();

// Read Plugin filters.
$filter_docs = $read_actions_filters->run(
	// Define Plugin folders to include in Docs. 
    array(
    	'../admin',
    	'../includes',
    	'../views',
    ),
    true, // Extract filters.
    false, // Extract actions.
    'markdown', // Return as HTML/markdown compatible with GitHub.
    '\'postiz_auto_poster_', // Only build Docs for actions starting with postiz_auto_poster_.
    false, // Change prefix.
    true // Return by file.
);
$action_docs = $read_actions_filters->run( 
	// Define Plugin folders to include in Docs.
    array(
    	'../admin',
    	'../includes',
    	'../views',
    ),
    false, // Extract filters.
    true, // Extract actions.
    'markdown', // Return as HTML/markdown compatible with GitHub.
    '\'postiz_auto_poster_', // Only build Docs for actions starting with postiz_auto_poster_.
    false, // Change prefix.
    true // Return by file.
);

// Build HTML.
$html = '<h1>Filters</h1>' . $filter_docs;
$html .= '<h1>Actions</h1>' . $action_docs;

// Write to file.
file_put_contents( '../ACTIONS-FILTERS.md', $html );