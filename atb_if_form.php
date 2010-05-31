<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ('abc' is just an example).
// Uncomment and edit this line to override:
# $plugin['name'] = 'abc_plugin';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 0;

$plugin['version'] = '0.9';
$plugin['author'] = 'Adam Bradley';
$plugin['author_uri'] = 'http://www.adamtbradley.com/';
$plugin['description'] = 'Conditional tags atb_if_form and atb_output_form_if_exists';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
# $plugin['order'] = 5;

// Plugin 'type' defines where the plugin is loaded
// 0 = public       : only on the public side of the website (default)
// 1 = public+admin : on both the public and admin side
// 2 = library      : only when include_plugin() or require_plugin() is called
// 3 = admin        : only on the admin side
# $plugin['type'] = 0;

// Plugin 'flags' signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use. 
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

# $plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;

if (!defined('txpinterface'))
	@include_once('../zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---

h1. @atb_if_form@ Help

Lately, I've been writing Textpattern pages for relatively complex sites that do things like this:

bc. <txp:if_section name="widgets,sprockets,thingies">
    <txp:hide>The widgets, sprockets, and thingies sections have their own special layouts.</txp:hide>
    <txp:output_form form='content-<txp:section />' />
<txp:else />
    <txp:hide>All the other sections look pretty much the same.</txp:hide>
    <txp:output_form form='content-default' />
</txp:if_section>

This plugin is designed to simplify this somewhat, and eliminate that list of section names (which is just one more thing for me to forget to update when I change the site).

h2. @atb_if_form@

A simple, hopefully self-explanatory conditional form:

bc. <txp:atb_if_form name="someform">
	<p>someform exists.</p>
<txp:else />
	<p>someform doesn't exist</p>
</txp:atb_if_form>

h2. @atb_output_form_if_exists@

Can be used exactly the same way as the built-in @output_form@ tag, except it doesn't raise an error if the form provided doesn't exist:

bc. <txp:atb_output_form_if_exists form='foo-<txp:section />'>
    This text will be available via the txp:yield tag in foo-[section].
</txp:atb_output_form_if_exists>

It can also be used with a @txp:else@ tag:

bc. <txp:atb_output_form_if_exists form='foo-<txp:section />'>
    This text will be available via the txp:yield tag in foo-[section].
<txp:else />
    <txp:output_form form="foo-default" />
</txp:atb_output_form_if_exists>


# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

// Plugin code goes here.  No need to escape quotes.

function atb_if_form($atts, $thing) {
	extract(lAtts(array(
		'name'	=> '',
	), $atts));
    
	return parse(EvalElse($thing, atb_form_exists($name)));
}

function atb_output_form_if_exists($atts, $thing) {
	global $yield;

	extract(lAtts(array(
		'name'	=> '',
	), $atts));

	if ( atb_form_exists($name) ) {
		$yield[] = $thing !== null ? parse(EvalElse($thing, true)) : null;
		$outp = parse_form($name);
		array_pop($yield);
	} else {
		$outp = parse(EvalElse($thing, false));
	}

	return $outp;
}

function atb_form_exists($name) {
	return getCount('txp_form', "name = '$name'") > 0 ? true : false;
}

# --- END PLUGIN CODE ---

?>
