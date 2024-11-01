<?php
/*
Plugin Name:       WeiterGeben.org
Plugin URI:        https://www.weitergeben.org
Description:       Möbelspenden-Initiative WeiterGeben.org
Version:           1.1.1
Author:            FFE-Tech e. U. Inh. Florian Feilmeier
Author URI:        https://www.ffe-tech.com
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:       ffetech-weitergeben
Domain Path:       /languages

WeiterGeben.org is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
WeiterGeben.org is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with WeiterGeben.org. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// Eingabeformular für Offline-Softwareaktivierung
require_once(plugin_dir_path( __FILE__ ) . 'ffetech-weitergeben-widget.php');
require_once(plugin_dir_path( __FILE__ ) . 'ffetech-weitergeben-builder.php');

function weitergeben($atts)
{
	extract(shortcode_atts(array(
		'maxwidth' => '400',
		'imgheight' => '400',
        'country' => 'DE',
        'zip' => '10178',
        'radius' => '50',
        'category' => 'Alle',
        'limit' => '1',
        'extsrcs' => '0'
    ), $atts));

    $builder = new Weitergeben_Builder();
    $builder->enqueue_imports();

	return '<div>' .
        $builder->get_css($maxwidth, $imgheight) .
        $builder->get_scripts($extsrcs) .
        $builder->get_result() .
        $builder->get_form($country, $zip, $radius, $category, $limit) .
        $builder->get_footer() .
        '</div>';
}

add_shortcode('weitergeben', 'weitergeben');