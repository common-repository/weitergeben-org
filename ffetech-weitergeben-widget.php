<?php

// https://www.wpexplorer.com/create-widget-plugin-wordpress/

add_action( 'widgets_init', 'weitergeben_widget_init' );

require_once(plugin_dir_path( __FILE__ ) . 'ffetech-weitergeben-builder.php');

function weitergeben_widget_init() {
    register_widget( 'Weitergeben_Widget' );
}

class Weitergeben_Widget extends WP_Widget {
 
    public $builder;

    function __construct() {
 
        parent::__construct(
            'weitergeben-org',
            'WeiterGeben.org'
        );

        $this->builder = new Weitergeben_Builder();
    }
 
    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {
 
        $title        = isset( $instance['title'       ] ) ? $instance['title'       ] : 'Möbel einfach abholen';
        $describe     = isset( $instance['describe'    ] ) ? $instance['describe'    ] : 'Die meisten dieser Gebrauchtmöbel sind nur kurz verfügbar und falls sich kein Interessent/Abnehmer findet, werden diese zerstört.';
        $country      = isset( $instance['country'     ] ) ? $instance['country'     ] : 'DE';
        $zip          = isset( $instance['zip'         ] ) ? $instance['zip'         ] : '10178';
        $radius       = isset( $instance['radius'      ] ) ? $instance['radius'      ] : '50';
        $category     = isset( $instance['category'    ] ) ? $instance['category'    ] : 'Alle';
        $limit        = isset( $instance['limit'       ] ) ? $instance['limit'       ] : '1';
        $maximgwidth  = isset( $instance['maximgwidth' ] ) ? $instance['maximgwidth' ] : 250;
        $imgheight    = isset( $instance['imgheight'   ] ) ? $instance['imgheight'   ] : 250;
        $extsrcs      = isset( $instance['extsrcs'     ] ) ? $instance['extsrcs'     ] : false;

        $this->builder->enqueue_imports();

        echo $args['before_widget'];
 
        if (!empty($title))
            echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];

        echo '<div class="textwidget">';
        if (!empty($describe))
            echo '<p>' . $describe . '</p>';
        echo $this->builder->get_css($maximgwidth, $imgheight);
        echo $this->builder->get_scripts($extsrcs);
        echo $this->builder->get_result();
        echo $this->builder->get_form($country, $zip, $radius, $category, $limit);
        echo $this->builder->get_footer();
        echo '</div>';
 
        echo $args['after_widget'];
        
    }
 
    public function form( $instance ) {
 
        $title        = !empty( $instance['title'       ] ) ? $instance['title'       ] : 'Möbel einfach abholen';
        $describe     = !empty( $instance['describe'    ] ) ? $instance['describe'    ] : 'Die meisten dieser Gebrauchtmöbel sind nur kurz verfügbar und falls sich kein Interessent/Abnehmer findet, werden diese zerstört.';
        $country      = !empty( $instance['country'     ] ) ? $instance['country'     ] : 'DE';
        $zip          = !empty( $instance['zip'         ] ) ? $instance['zip'         ] : '10178';
        $radius       = !empty( $instance['radius'      ] ) ? $instance['radius'      ] : '50';
        $category     = !empty( $instance['category'    ] ) ? $instance['category'    ] : 'Alle';
        $limit        = !empty( $instance['limit'       ] ) ? $instance['limit'       ] : '1';
        $maximgwidth  = !empty( $instance['maximgwidth' ] ) ? $instance['maximgwidth' ] : 250;
        $imgheight    = !empty( $instance['imgheight'   ] ) ? $instance['imgheight'   ] : 250;
        $extsrcs      = !empty( $instance['extsrcs'     ] ) ? $instance['extsrcs'     ] : false;

        ?>

        <?php // title ?>
        <p>
            <label>Titel:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="title" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <?php // desribe ?>
        <p>
            <label>Beschreibung:</label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'describe' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'describe' ) ); ?>"><?php echo wp_kses_post( $describe ); ?></textarea>
        </p>

        <?php // country ?>
        <p>
        <label>Land:</label>
		<select name="<?php echo $this->get_field_name( 'country' ); ?>" id="<?php echo $this->get_field_id( 'country' ); ?>" class="widefat">
		<?php
            $options = array(
                'DE' => 'Deutschland',
                'AT' => 'Österreich',
                'CH' => 'Schweiz',
            );
            foreach ( $options as $key => $name ) {
                echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $country, $key, false ) . '>'. $name . '</option>';

            } ?>
            </select>
        </p>

        <?php // zip ?>
        <p>
            <label>PLZ:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'zip' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'zip' ) ); ?>" type="text" value="<?php echo esc_attr( $zip ); ?>" />
        </p>

        <?php // radius ?>
        <p>
        <label>Radius:</label>
		<select name="<?php echo $this->get_field_name( 'radius' ); ?>" id="<?php echo $this->get_field_id( 'radius' ); ?>" class="widefat">
		<?php
            $options = array(
                '10' => '10 km',
                '25' => '25 km',
                '50' => '50 km',
                '150' => '150 km',
                '0' => 'überall',
            );
            foreach ( $options as $key => $name ) {
                echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $radius, $key, false ) . '>'. $name . '</option>';

            } ?>
            </select>
        </p>

        <?php // category ?>
        <p>
        <label>Kategorie:</label>
		<select name="<?php echo $this->get_field_name( 'category' ); ?>" id="<?php echo $this->get_field_id( 'category' ); ?>" class="widefat">
		<?php
            $options = array(
                'Alle' => 'Alle',
                'Büro' => 'Büro',
                'Garten' => 'Garten',
                'Gastronomie' => 'Gastronomie',
                'Gesundheit' => 'Gesundheit',
                'Kinder' => 'Kinder',
                'Laden' => 'Laden',
                'Lager' => 'Lager',
                'Schule' => 'Schule',
                'Sport' => 'Sport',
                'Wohnung' => 'Wohnung',
                'Werkstatt' => 'Werkstatt',
            );
            foreach ( $options as $key => $name ) {
                echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $category, $key, false ) . '>'. $name . '</option>';

            } ?>
            </select>
        </p>

        <?php // Anzahl ?>
        <p>
        <label>Anzahl:</label>
		<select name="<?php echo $this->get_field_name( 'limit' ); ?>" id="<?php echo $this->get_field_id( 'limit' ); ?>" class="widefat">
		<?php
            $options = array(
                '1' => '1',
                '2' => '2',
                '5' => '5',
                '10' => '10',
            );
            foreach ( $options as $key => $name ) {
                echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $limit, $key, false ) . '>'. $name . '</option>';

            } ?>
            </select>
        </p>

        <?php // max image width ?>
        <p>
            <label>Bildhöhe in Pixel:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'maximgwidth' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'maximgwidth' ) ); ?>" type="number" value="<?php echo esc_attr( $maximgwidth ); ?>" />
        </p>

        <?php // image height ?>
        <p>
            <label>Bildhöhe in Pixel:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'imgheight' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'imgheight' ) ); ?>" type="number" value="<?php echo esc_attr( $imgheight ); ?>" />
        </p>

        <?php // external source ?>
        <p>
            <label>Alle verfügbaren Quellen mit Gebrauchtmöbeln anzeigen:</label>
            <input id="<?php echo esc_attr( $this->get_field_id( 'extsrcs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'extsrcs' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $extsrcs ); ?> />
        </p>

        <?php
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
        $instance['title'      ] = isset( $new_instance['title'      ] ) ? wp_strip_all_tags( $new_instance['title'      ] ) : '';
        $instance['describe'   ] = isset( $new_instance['describe'   ] ) ? wp_strip_all_tags( $new_instance['describe'   ] ) : '';
        $instance['country'    ] = isset( $new_instance['country'    ] ) ? wp_strip_all_tags( $new_instance['country'    ] ) : '';
        $instance['zip'        ] = isset( $new_instance['zip'        ] ) ? wp_strip_all_tags( $new_instance['zip'        ] ) : '';
        $instance['radius'     ] = isset( $new_instance['radius'     ] ) ? wp_strip_all_tags( $new_instance['radius'     ] ) : '';
        $instance['category'   ] = isset( $new_instance['category'   ] ) ? wp_strip_all_tags( $new_instance['category'   ] ) : '';
        $instance['limit'      ] = isset( $new_instance['limit'      ] ) ? wp_strip_all_tags( $new_instance['limit'      ] ) : '';
        $instance['imgheight'  ] = isset( $new_instance['imgheight'  ] ) ? wp_strip_all_tags( $new_instance['imgheight'  ] ) : '';
        $instance['maximgwidth'] = isset( $new_instance['maximgwidth'] ) ? wp_strip_all_tags( $new_instance['maximgwidth'] ) : '';
        $instance['extsrcs'    ] = isset( $new_instance['extsrcs'    ] ) ? wp_strip_all_tags( $new_instance['extsrcs'    ] ) : '';
        return $instance;
    }
 
}

?>