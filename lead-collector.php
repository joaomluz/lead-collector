<?php
/**
 * Plugin Name: lead collector
 * Version: 1.0.0
 * Plugin URI: http://www.example.com/
 * Description: Simple lead collector wordpress plugin example.
 * Author: João Luz
 * Author URI: http://www.example.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: lead-collector
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author João Luz
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin model files.
require_once 'includes/model/model-lead-collector-main.php';
require_once 'includes/model/model-lead-collector-settings.php';
require_once 'includes/model/model-lead-collector-post-type.php';
require_once 'includes/model/model-lead-collector-admin-api.php';
require_once 'includes/model/class-lead-collector-taxonomy.php';

// Load main contoller
require_once 'includes/controller/controller-lead-collector.php';
