<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://demoshop.entercheck.eu/
 * @since      1.0.0
 *
 * @package    Enterpay_Company_Search
 * @subpackage Enterpay_Company_Search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enterpay_Company_Search
 * @subpackage Enterpay_Company_Search/admin
 * @author     Ha Nguyen <nd.dungha@gmail.com>
 */
class Enterpay_Company_Search_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		include(plugin_dir_path(__FILE__) . 'partials/enterpay-company-search-admin-fields-settings.php');
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Enterpay_Company_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Enterpay_Company_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/enterpay-company-search-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Enterpay_Company_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Enterpay_Company_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/enterpay-company-search-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Register the menu for the admin area.
	 *
	 * @since    1.0.0
	 */




	public function add_menu()
	{
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page("Entercheck", "Entercheck", 'manage_options', $this->plugin_name . '-entercheck', array($this, 'page_enterpay_admin'));
		add_submenu_page($this->plugin_name . '-entercheck', 'Credentials', 'Credentials', 'manage_options', $this->plugin_name . '-credentials', array($this, 'page_enterpay_admin'));
		//add_submenu_page($this->plugin_name . '-entercheck', 'Field settings', 'Field settings', 'manage_options', $this->plugin_name . '-enterpay-fields', array($this, 'page_enterpay_fields_admin'));
		remove_submenu_page($this->plugin_name . '-entercheck', $this->plugin_name . '-entercheck');
	}

	public function page_enterpay_admin()
	{
		include(plugin_dir_path(__FILE__) . 'partials/enterpay-company-search-admin-display.php');
	}	
		
	public function page_enterpay_fields_admin()
	{
		include(plugin_dir_path(__FILE__) . 'partials/enterpay-company-search-admin-fields-settings.php');
	}

	public function admin_register_settings()
	{

		register_setting('enterpay_plugin_options', 'enterpay_plugin_options', 'enterpay_plugin_options_validate');

		add_settings_section('api_settings', 'API Credentials', 'enterpay_plugin_section_text', 'dbi_example_plugin');

		add_settings_field('enterpay_plugin_setting_username', 'Username', 'enterpay_plugin_setting_username', 'dbi_example_plugin', 'api_settings');
		add_settings_field('enterpay_plugin_setting_password', 'Password', 'enterpay_plugin_setting_password', 'dbi_example_plugin', 'api_settings');
		add_settings_field('enterpay_plugin_setting_environment', 'Type of environment', 'enterpay_plugin_setting_environment', 'dbi_example_plugin', 'api_settings');

		add_settings_field('enterpay_plugin_setting_enterpaytoken', 'Token', 'enterpay_plugin_setting_enterpaytoken', 'dbi_example_plugin', 'api_settings');

		// add meta box to user profile page
		add_action('show_user_profile', array($this, 'enterpay_user_profile_fields'));
		add_action('edit_user_profile', array($this, 'enterpay_user_profile_fields'));
		add_action('personal_options_update', array($this, 'enterpay_user_profile_fields_save'));
		add_action('edit_user_profile_update', array($this, 'enterpay_user_profile_fields_save'));
	}

	// add meta box to user profile page table Customer Organization  VAT ID, Bsuiness ID
	public function enterpay_user_profile_fields($user)
	{
		$user_id = $user->ID;
		$company_base = get_user_meta($user_id, 'company_base', true);
		$invoice_address = get_user_meta($user_id, 'invoice_address', true);
		if (!empty($company_base)) {
			$company_base = json_decode($company_base, true);
		}
		$company_info = get_user_meta($user_id, 'company_info', true);

		if (!empty($company_info)) {
			$company_info = json_decode($company_info, true);
		}
		if (!empty($company_info)) {
			//$uuid = $company_base['uuid'];
			$companyId = $company_base['companyId'];			

			// get Bsuiness ID and VAT ID from company_info
			$business_id = $company_info['ids'][0]['idValue'];
			$vat_id = $company_info['ids'][1]['idValue'];
			$status = $company_info['status'];
			
			$invoiceAddresses = [];			
			/*
			$company_name = "";
			$address = "";
			$operatorCode = "";
			$operator = "";
			$ovt = "";
			*/
			foreach($company_info['receivingFinvoiceAddress'] as $invoice){
				if (empty($invoice_address) || $invoice_address == $invoice['address'].' / '.$invoice['operatorCode']){
					$invoiceAddresses[] = [
						'company_name' => $invoice['name'],
						'address' => $invoice['address'],
						'operatorCode' => $invoice['operatorCode'],
						'operator' => $invoice['operator'],
						'ovt' => $invoice['ovt']	
					];
				}
			}
			$status = $company_info['status'];
			
			$options = get_option('enterpay_plugin_options');
			$api_domain = "portal.entercheck.eu"; 
			if (!isset($options['environment']) || empty($options['environment']) || $options['environment'] == 'test') { 
				$api_domain = "portal.test.entercheck.eu"; 
			}	

?>
			<h3><?php _e("Customer Organization", 'enterpay-company-search'); ?></h3>
			<table class="form-table">
				<tr>
					<th><label for="enterpay_vat_id"><?php _e("Link to Entercheck", 'enterpay-company-search'); ?></label></th>
					<td>
						<a target="_blank" href="https://<?php echo $api_domain; ?>/companies/<?php echo $companyId; ?>">Link to Entercheck</a>
					</td>
				</tr>
				<tr>
					<th><label for="enterpay_bsuiness_id"><?php _e("Bsuiness ID", 'enterpay-company-search'); ?></label></th>
					<td>
						<input type="text" name="enterpay_bsuiness_id" id="enterpay_bsuiness_id" readonly value="<?php echo esc_attr($business_id); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th><label for="enterpay_vat_id"><?php _e("VAT ID", 'enterpay-company-search'); ?></label></th>
					<td>
						<input type="text" name="enterpay_vat_id" id="enterpay_vat_id" readonly value="<?php echo esc_attr($vat_id); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th><label for="enterpay_vat_id"><?php _e("Status", 'enterpay-company-search'); ?></label></th>
					<td>
						<input type="text" name="enterpay_vat_id" id="enterpay_vat_id" readonly value="<?php echo esc_attr($status); ?>" class="regular-text" /><br />
					</td>
				</tr>
			</table>
			
			<?php 
			$ind = 1;
			foreach ($invoiceAddresses as $invoiceAddress) { ?>			
				<h3><?php _e("Invoice Address", 'enterpay-company-search'); ?> <?php if (count($invoiceAddresses) > 1) echo $ind; ?></h3>
				<table class="form-table">
					<tr>
						<th><label for="enterpay_bsuiness_id"><?php _e("Company name", 'enterpay-company-search'); ?></label></th>
						<td>
							<input type="text" readonly value="<?php echo esc_attr($invoiceAddress['company_name']); ?>" class="regular-text" /><br />
						</td>
					</tr>
					<tr>
						<th><label for="enterpay_bsuiness_id"><?php _e("Address", 'enterpay-company-search'); ?></label></th>
						<td>
							<input type="text"  readonly value="<?php echo esc_attr($invoiceAddress['address']); ?>" class="regular-text" /><br />
						</td>
					</tr>
					<tr>
						<th><label for="enterpay_vat_id"><?php _e("Operator code", 'enterpay-company-search'); ?></label></th>
						<td>
							<input type="text"  readonly value="<?php echo esc_attr($invoiceAddress['operatorCode']); ?>" class="regular-text" /><br />
						</td>
					</tr>
					<tr>
						<th><label for="enterpay_vat_id"><?php _e("Operator", 'enterpay-company-search'); ?></label></th>
						<td>
							<input type="text"  readonly value="<?php echo esc_attr($invoiceAddress['operator']); ?>" class="regular-text" /><br />
						</td>
					</tr>	
					<tr>
					<th><label for="enterpay_vat_id"><?php _e("OVT", 'enterpay-company-search'); ?></label></th>
						<td>
							<input type="text"  readonly value="<?php echo esc_attr($invoiceAddress['ovt']); ?>" class="regular-text" /><br />
						</td>				
					</tr>
				</table>
			<?php 
				$ind++;
			} 
			?> 			
<?php
		}
	}

	public function enterpay_user_profile_fields_save($user_id)
	{
		if (!current_user_can('edit_user', $user_id)) {
			return false;
		}
		// update_user_meta($user_id, 'enterpay_token', $_POST['enterpay_token']);

		if (
			!empty($_POST['enterpay_vat_id']) && !empty($_POST['enterpay_bsuiness_id'])
			&& !empty($_POST['enterpay_vat_id']) && !empty($_POST['enterpay_bsuiness_id'])
		) {
			// update_user_meta($user_id, 'enterpay_token', $_POST['enterpay_token']);
			update_user_meta($user_id, 'enterpay_vat_id', $_POST['enterpay_vat_id']);
			update_user_meta($user_id, 'enterpay_bsuiness_id', $_POST['enterpay_bsuiness_id']);
		}

		// update_user_meta($user_id, 'enterpay_vat_id', $_POST['enterpay_vat_id']);
		// update_user_meta($user_id, 'enterpay_bsuiness_id', $_POST['enterpay_bsuiness_id']);

	}
	// save checkout fields  Bsuiness ID and VAT ID in user meta
	public function enterpay_checkout_fields_save($order_id)
	{
		$order = wc_get_order($order_id);
		$user_id = $order->get_user_id();
		// update_user_meta($user_id, 'enterpay_token', $_POST['enterpay_token']);

		if (
			!empty($_POST['enterpay_vat_id']) && !empty($_POST['enterpay_bsuiness_id'])
			&& !empty($_POST['enterpay_vat_id']) && !empty($_POST['enterpay_bsuiness_id'])
		) {
			// update_user_meta($user_id, 'enterpay_token', $_POST['enterpay_token']);
			update_user_meta($user_id, 'enterpay_vat_id', $_POST['enterpay_vat_id']);
			update_user_meta($user_id, 'enterpay_bsuiness_id', $_POST['enterpay_bsuiness_id']);
		}

		// update_user_meta($user_id, 'enterpay_vat_id', $_POST['enterpay_vat_id']);
		// update_user_meta($user_id, 'enterpay_bsuiness_id', $_POST['enterpay_bsuiness_id']);

	}
}
