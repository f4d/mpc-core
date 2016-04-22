<?php

class Mpc_Admin {

/** the class constructor   */
	public function __construct() {
		if ( is_admin() ){ 
			add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
			add_action( 'admin_init',  array( $this, 'plugin_admin_init' ) );
		}
	}
	public function plugin_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		return '';
	}	
	public function plugin_admin_init(){

	add_settings_section('plugin_main', 'Main Settings', array($this,'plugin_section_text'), 'plugin');
	//print_r($v);
	register_setting( 'plugin_options', 'plugin_options_savings_plan', array($this,'savings_plan_validate') );

	add_settings_field('savings_plan', "Savings Plan Type - Label", array($fv,'plugin_setting_string_savings_plan'), 'plugin', 'plugin_main');  

	for ($i=1;$i<6;$i++) {
	    $v = new gf2OptionFieldView($i);
	  register_setting( 'plugin_options', 'plugin_options_label'."_$i", array($this,'label_validate') );
	  register_setting( 'plugin_options', 'plugin_options_interval'."_$i", array($this,'interval_validate') );
	  register_setting( 'plugin_options', 'plugin_options_iterations'."_$i", array($this,'iterations_validate') );
	  register_setting( 'plugin_options', 'plugin_options_hours_per_yr'."_$i", array($this,'hours_per_yr_validate') );
	  register_setting( 'plugin_options', 'plugin_options_dues_rate'."_$i", array($this,'dues_rate_validate') );
	  register_setting( 'plugin_options', 'plugin_options_cap_type'."_$i", array($this,'cap_type_validate') );
	  register_setting( 'plugin_options', 'plugin_options_cap'."_$i", array($this,'cap_validate') );
	    register_setting( 'plugin_options', 'plugin_options_contribution_rate'."_$i", array($this,'contribution_rate_validate') );
	  register_setting( 'plugin_options', 'plugin_options_return_rate'."_$i", array($this,'return_rate_validate') );

	  add_settings_field('label'."_$i", "#$i: Label", array($v,'plugin_setting_string_label'), 'plugin', 'plugin_main');
	  add_settings_field('interval'."_$i", "#$i: Interval in Years<br>(Years Per Graph Bar)", array($v,'plugin_setting_string_intvl'), 'plugin', 'plugin_main');
	  add_settings_field('iter'."_$i", "#$i: Iterations<br>(# of Bars on Graph)", array($v,'plugin_setting_string_iter'), 'plugin', 'plugin_main');
	  add_settings_field('hrs_per_yr'."_$i", "#$i: Hours Worked Per Year", array($v,'plugin_setting_string_hours_per_yr'), 'plugin', 'plugin_main');
	  add_settings_field('dues_rate'."_$i", "#$i: Dues Rate <br>(Fraction, 2% = .02)", array($v,'plugin_setting_string_dues_rate'), 'plugin', 'plugin_main');
	  add_settings_field('cap_type'."_$i", "#$i: Dues Rate & Cap Type", array($v,'plugin_setting_string_cap_type'), 'plugin', 'plugin_main');
	  add_settings_field('cap'."_$i", "#$i: Dues Cap in $<br>(Per Year, or Per NNU Monthly)", array($v,'plugin_setting_string_cap'), 'plugin', 'plugin_main');
	    add_settings_field('contrib_rate'."_$i", "#$i: Employer Matching Contribution<br>(Fraction, 2% = .02)", array($v,'plugin_setting_string_contribution_rate'), 'plugin', 'plugin_main');  
	  add_settings_field('return_rate'."_$i", "#$i: Rate of Return<br>(Fraction, 2% = .02)", array($v,'plugin_setting_string_return_rate'), 'plugin', 'plugin_main');  

		}
	}
	public function plugin_section_text() {
		$str = '<p>GF2 visualizer settings</p>';
		$str .= "<p><b>Setting The Gravity Forms Redirect</b><br>\n";
		$str .= "In the redirect URL, set graph=N, where N is a number 1-5 ";
		$str .= "that matches the desired plugin settings you wish to use ";
		$str .= "(#1 - #5 below).</p>";
		$str .= "<p><b>Setting the visualizer shortcode</b><br>\n";
		$str .= "[visualizer id=\"N\" data=\"filter_dues_data\" \n";
		$str .= "series=\"filter_403b_series\"]<br>Where N=ID of graph from the ";
		$str .= "visualizer plugin, and 'filter_dues_data' / 'filter_403b_series' ";
		$str .= "refer to filters referenced by the code.</p>";
		$str .= "<p><b>Setting up the visualizer .CSV file</b><br>";
		$str .= "Row #1: <i>Time,[Ascending],[Descending]</i><br>";
		$str .= "Where [Ascending] and [Descending] are the text labels for ascending and descending lines.<br>";
		$str .= "Row #2: <i>string,number,number</i><br>";
		$str .= "Row #3 - #N: <i>5 Years,0,0</i><br>";
		$str .= "It doesn't matter what these rows have as content, but you need a number of ";
		$str .= "rows equal to the number of points on your graph. So if you have 6 points on your graph, ";
		$str .= "your CSV should have 8 rows in total.</p>";
		echo $str;
	}
}
