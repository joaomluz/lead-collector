<?php
/**
 * Main plugin class file.
 *
 * @package WordPress Plugin Template/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class lead_collector {

	/**
	 * The single instance of lead_collector.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Local instance of lead_collector_Admin_API
	 *
	 * @var lead_collector_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version; //phpcs:ignore

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token; //phpcs:ignore

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for JavaScripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'lead_collector';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname(dirname( $this->file ));
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', dirname(dirname($this->file )) ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new lead_collector_Admin_API();
		}

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()


	/**
	 * Create customer post type.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	function create_customer_post_type(){
		lead_collector()->register_post_type( 
			'customer', 
			__( 'Customers', 'lead-collector' ),
			__( 'Customer', 'lead-collector' ),
			array(
				'public' => false,
				'capabilities' => array(
					'edit_post'          => 'update_core',
					'read_post'          => 'update_core',
					'delete_post'        => 'update_core',
					'edit_posts'         => 'update_core',
					'edit_others_posts'  => 'update_core',
					'delete_posts'       => 'update_core',
					'publish_posts'      => 'update_core',
					'read_private_posts' => 'update_core'
				),
				'supports' => array( 
					'title', 
					'editor', 
					'excerpt', 
					'thumbnail', 
					'custom-fields', 
					'revisions' 
				)
			)
		);
	}

	/**
	 * Create custom shortcode.
	 *
	 * @access  public
	 * @return  String
	 * @since   1.0.0
	 */

	function lead_form_shortcode( $atts ) {

		$a = shortcode_atts( array(
		   'name' => 'Name',
		   'phone' => 'Phone Number',
		   'email' => 'Email Address',
		   'budget' => 'Desired Budget',
		   'message' => 'Message',
		   'name_max' => '50',
		   'phone_max' => '20',
		   'email_max' => '50',
		   'budget_max' => '10',
		   'message_max' => '500',
		   'message_rows' => '10',
		   'message_cols' => '0',

		), $atts, 'lead_form');

		// 3rd party api to get date and time
		$date_time = '';
		$request_time = wp_remote_get( 'http://worldtimeapi.org/api/timezone/America/Sao_Paulo' );
		if( !is_wp_error( $request_time ) ) {
			$body_time = json_decode(wp_remote_retrieve_body( $request_time ));
			$date_time = $body_time->datetime;
		}

		ob_start();
		include dirname(plugin_dir_path( __FILE__ )) . '/view/view-lead-collector-shortcode.php';
	
		return ob_get_clean();
	}

	/**
	 * Create custom columns to post type customer.
	 *
	 * @access  public
	 * @return  Objeto
	 * @since   1.0.0
	 */

	function set_custom_customer_edit_post_columns($columns) {
		
		unset( $columns['author'] );
		unset( $columns['date'] );
		unset( $columns['comments'] );
		unset( $columns['title'] );
		
		$columns['sc_name'] = __( 'Name', 'lead_collector' );
		$columns['sc_phone'] = __( 'Phone', 'lead_collector' );
		$columns['sc_email'] = __( 'Email', 'lead_collector' );
		$columns['sc_budget'] = __( 'Budget', 'lead_collector' );
		$columns['sc_message'] = __( 'Message', 'lead_collector' );
		$columns['sc_datetime'] = __( 'Date', 'lead_collector' );
	
		return $columns;
	}

	/**
	 * Show custom post metas in columns.
	 *
	 * @access  public
	 * @return  Void
	 * @since   1.0.0
	 */

	function custom_customer_admin_column( $column, $post_id ) {
        switch( $column ) {
            case 'sc_name' :
                echo get_post_meta( $post_id , '_sc_name' , true ); 
                break;
            case 'sc_phone' :
                echo get_post_meta( $post_id , '_sc_phone' , true ); 
                break;
            case 'sc_email' :
                echo get_post_meta( $post_id , '_sc_email' , true ); 
                break;
            case 'sc_budget' :
                echo get_post_meta( $post_id , '_sc_budget' , true ); 
                break;
            case 'sc_message' :
                echo get_post_meta( $post_id , '_sc_message' , true ); 
                break;   
            case 'sc_datetime' :
                echo get_post_meta( $post_id , '_sc_datetime' , true ); 
            break;   
                default :
            break;        
        }
	}
	
	/**
	 * Manage $_POST of shorcode post informations.
	 *
	 * @access  public
	 * @return  Void
	 * @since   1.0.0
	 */

	function lead_form_custom_action() {

		if ( ! isset( $_POST['lead_form_nonce'] ) || ! wp_verify_nonce( $_POST['lead_form_nonce'], 'lead_form_custom_action') ) { 
			exit('The form is not valid');
		}
		$response = array(
			'error' => false,
		);

		//TODO: Validate fields here. 
	
		$this->create_customer_form_post_submit($_POST);
		
		exit(json_encode($_POST));
	}

	/**
	 * Manage $_POST of shorcode post informations.
	 *
	 * @access  public
	 * @return  Void
	 * @since   1.0.0
	 */

	function create_customer_form_post_submit($data) {
    
		if (!$data) {
			return false;
		}
	
		$args = array(
			'post_title' => sanitize_text_field($_POST['sc_name']),
			'post_content' => sanitize_text_field($_POST['sc_message']),
			'post_type' => 'customer',
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
	
		$pid = wp_insert_post($args);
		add_post_meta($pid, "_sc_name", sanitize_text_field($_POST['sc_name'])); 
		add_post_meta($pid, "_sc_phone", sanitize_text_field($_POST['sc_phone']));
		add_post_meta($pid, "_sc_email", sanitize_text_field($_POST['sc_email']));
		add_post_meta($pid, "_sc_budget", sanitize_text_field($_POST['sc_budget']));
		add_post_meta($pid, "_sc_message", sanitize_text_field($_POST['sc_message']));
		add_post_meta($pid, "_sc_datetime", sanitize_text_field($_POST['datetime']));
	
	}

	/**
	 * Register post type function.
	 *
	 * @param string $post_type Post Type.
	 * @param string $plural Plural Label.
	 * @param string $single Single Label.
	 * @param string $description Description.
	 * @param array  $options Options array.
	 *
	 * @return bool|string|lead_collector_Post_Type
	 */
	public function register_post_type( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) {
			return false;
		}

		$post_type = new lead_collector_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $plural Plural Label.
	 * @param string $single Single Label.
	 * @param array  $post_types Post types to register this taxonomy for.
	 * @param array  $taxonomy_args Taxonomy arguments.
	 *
	 * @return bool|string|lead_collector_Taxonomy
	 */
	public function register_taxonomy( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) {
			return false;
		}

		$taxonomy = new lead_collector_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @return void
	 * @since   1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
		wp_register_style( 'bootstrap', esc_url( $this->assets_url ) . 'css/bootstrap.min.css', array(), $this->_version );
		wp_enqueue_style( 'bootstrap' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-frontend' );
		wp_register_script( 'jquery-inputmask', esc_url( $this->assets_url ) . 'js/jquery.inputmask.min.js', array( 'jquery' ));
		wp_enqueue_script( 'jquery-inputmask', );
	} // End enqueue_scripts ()


	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'lead-collector', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = 'lead-collector';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main lead_collector Instance
	 *
	 * Ensures only one instance of lead_collector is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object lead_collector instance
	 * @see lead_collector()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of lead_collector is forbidden' ) ), esc_attr( $this->_version ) );

	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of lead_collector is forbidden' ) ), esc_attr( $this->_version ) );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function install() {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	private function _log_version_number() { //phpcs:ignore
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	

}
