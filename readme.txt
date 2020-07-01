=== lead collector ===
Contributors: joaomluz
Tags: wordpress, plugin, template
Requires at least: 5.4.2
Tested up to: 5.4.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple lead collector wordpress plugin example.

== Description ==

This plugin creates a shortcode to render a lead form. The contacts will be saved in a post type Customer.

== How to use? ==

* Unzip this plugin in your /plugin folder and enable via wp-admin.
* Use the short code [lead_form] in your posts or pages to render the contact form.
* You can also change the labels of the fields like: ex:[lead_form name="new_name_label"] (name, phone, email, budget, message)
* To change the maxlength of any field: ex: [lead_form name_max="30"]  (name, phone, email, budget, message)
* To change the column or row size of message field: ex: [lead_form message_rows="5" message_cols="5"] 

== Comming soon ==
* Sortables columns on Customer edit.php page