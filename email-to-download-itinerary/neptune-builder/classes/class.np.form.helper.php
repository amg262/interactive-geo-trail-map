<?php
/**
 * Form Helper Class
 *
 * @Author: Ashok Kumar Nath (Bappi D Great)
 * @package: Builder
 */

if ( ! defined( 'ABSPATH' ) ) die( NP_HACK_MSG );

if( ! class_exists( 'NP_FORM_HELPER' ) ){
    /**
     * Class NP_FORM_HELPER
     */
    class NP_FORM_HELPER{
        
        /**
         * Defining Input types
         */
        const INPUT_TYPE_TEXT = 'text';
        const INPUT_TYPE_TEXTAREA = 'textarea';
        const INPUT_TYPE_RADIO = 'radio';
        const INPUT_TYPE_CHECKBOX = 'checkbox';
        const INPUT_TYPE_SELECT = 'select';
        const INPUT_TYPE_POST_TYPE = 'post_type';
        
        /**
         * Build a form based on elements
         * @since 1.0.1
         *
         * @param   $form   array   An array of form fields and configuration
         *                          @see self::element method params
         *
         * @return  $html   string  Generated form html
         */
        public function build_form( $form ) {
            $html = '';
            foreach( $form['elements'] as $element ) {
                $html .= self::element(
                                       $form['wrap'],
                                       $element['label'],
                                       $element['type'],
                                       $element['name'],
                                       $element['default'],
                                       $element['value'],
                                       $element['placeholder'],
                                       $element['options'],
                                       $element['attr']
                                    );
            }
            
            return apply_filters(
                                'np_builder/form_helper/form_builder_html',
                                $html,
                                $form
                            );
        }
        
        /**
         * Form element builder
         * @since 1.0.1
         *
         * @param   $wrapped        STRING      Usually a HTML tag
         * @param   $label          STRING      Field Label
         * @param   $type           STRING      Input type
         * @param   $name           STRING      Field Name
         * @param   $default        STRING      Field Default Value
         * @param   $value          STRING      Field Value, if any
         * @param   $placeholder    STRING      Field Placeholder
         *
         * @return  $html           STRING      HTML Output
         */
        public static function element( $wrapped, $label, $type, $name, $default, $value, $placeholder = false, $options = array(), $attr = '' ){
            
            switch( $type ){
                case self::INPUT_TYPE_TEXT:
                    $html = self::render_input_text( $name, $default, $value, $placeholder, $attr );
                    break;
                
                case self::INPUT_TYPE_TEXTAREA:
                    $html = self::render_input_textarea( $name, $default, $value, $placeholder, $attr );
                    break;
                
                case self::INPUT_TYPE_RADIO:
                    $html = self::render_input_radio( $name, $default, $value, $options, $attr );
                    break;
                
                case self::INPUT_TYPE_CHECKBOX:
                    
                    break;
                
                case self::INPUT_TYPE_SELECT:
                    $html = self::render_input_select( $name, $default, $value, $options, $attr );
                    break;
                
                case self::INPUT_TYPE_POST_TYPE:
                    $html = self::render_input_post_type( $name, $default, $value, $options, $attr );
                    break;
            }
            
            $method_name = 'element_' . $wrapped;
            if( method_exists( get_class(), $method_name ) ){
                $output = self::$method_name( $label, $html );
            }else{
                $output = $html;
            }
            
            return apply_filters(
                                'np_builder/form_helper/element',
                                $output,
                                $wrapped,
                                $label,
                                $type,
                                $name,
                                $default,
                                $value,
                                $placeholder
                            );
            
        }
        
        /**
         * Render input type text
         * @since 1.0.1
         *
         * @param   $name           STRING      Field Name
         * @param   $default        STRING      Field Default Value
         * @param   $value          STRING      Field Value, if any
         * @param   $placeholder    STRING      Field Placeholder
         *
         * @return  $html           STRING      Input HTML
         */
        public static function render_input_text( $name, $default, $value, $placeholder, $attr ){
            if( ! $placeholder ){
                $html = "<input type='text' name='{$name}' data-default='{$default}' value='{$value}' {$attr}>";
            }
            $html = "<input type='text' name='{$name}' data-default='{$default}' value='{$value}' placeholder='{$placeholder}' {$attr}>";
            
            return apply_filters(
                                'np_builder/form_helper/render_input_text',
                                $html,
                                $name,
                                $default,
                                $value,
                                $placeholder
                            );
        }
        
        /**
         * Render input type textarea
         * @since 1.0.1
         *
         * @param   $name           STRING      Field Name
         * @param   $default        STRING      Field Default Value
         * @param   $value          STRING      Field Value, if any
         * @param   $placeholder    STRING      Field Placeholder
         *
         * @return  $html           STRING      Input HTML
         */
        public static function render_input_textarea( $name, $default, $value, $placeholder, $attr ){
            
            if( ! $placeholder ){
                $html = "<textarea name='{$name}' data-default='{$default}' {$attr}>{$value}</textarea>";
            }
            $html = "<textarea name='{$name}' data-default='{$default}' {$attr}>{$value}</textarea>";
            
            return apply_filters(
                                'np_builder/form_helper/render_input_textarea',
                                $html,
                                $name,
                                $default,
                                $value,
                                $placeholder
                            );
        }
        
        /**
         * Render input type select
         * @since 1.0.1
         *
         * @param   $name           STRING      Field Name
         * @param   $default        STRING      Field Default Value
         * @param   $value          STRING      Field Value, if any
         * @param   $options        ARRAY       Array of options
         *
         * @return  $html           STRING      Input HTML
         */
        public static function render_input_select( $name, $default, $value, $options, $attr ){
            
            $html = "<select name='{$name}' {$attr}>";
            foreach( $options as $option ){
                $selected = $default == $option ? 'selected' : '';
                $html .= "<option {$selected} value='{$option}'>" . ucfirst( $option ) . "</option>";
            }
            $html .= '</select';
            
            return apply_filters(
                                'np_builder/form_helper/render_input_select',
                                $html,
                                $name,
                                $default,
                                $value,
                                $options
                            );
        }
        
        /**
         * Render input type radio
         * @since 1.0.1
         *
         * @param   $name           STRING      Field Name
         * @param   $default        STRING      Field Default Value
         * @param   $value          STRING      Field Value, if any
         * @param   $options        ARRAY       Array of options
         *
         * @return  $html           STRING      Input HTML
         */
        public static function render_input_radio( $name, $default, $value, $options, $attr ){
            
            $html = '';
            foreach( $options as $option ) {
                $checked = checked( $option, $default, false );
                $html .= "<input {$checked} type='radio' name='{$name}' value='{$option}' {$attr}>" . ucfirst( $option ) . '&nbsp;&nbsp;&nbsp;';
            }
            
            return apply_filters(
                                'np_builder/form_helper/render_input_radio',
                                $html,
                                $name,
                                $default,
                                $value,
                                $options
                            );
        }
        
        /**
         * Render input type post_type
         * @since 1.0.1
         *
         * @param   $name           STRING      Field Name
         * @param   $default        STRING      Field Default Value
         * @param   $value          STRING      Field Value, if any
         * @param   $options        ARRAY       Array of options
         *
         * @return  $html           STRING      Input HTML
         */
        public static function render_input_post_type( $name, $default, $value, $options, $attr ){
            
            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'date',
                'order'            => 'DESC',
                'post_type'        => $options['post_type'],
                'post_status'      => 'publish'
            );
            $posts = get_posts( $args );
            
            $html = "<select name='{$name} {$attr}'>";
            foreach( $posts as $post ){
                $selected = $default == $post->ID ? 'selected' : '';
                $html .= "<option {$selected} value='{$post->ID }'>{$post->post_title}</option>";
            }
            $html .= '</select>';
            
            return apply_filters(
                                'np_builder/form_helper/render_input_post_type',
                                $html,
                                $name,
                                $default,
                                $value,
                                $options
                            );
        }
        
        /**
         * Wrapped html by tr tag
         * @since 1.0.1
         *
         * @param   $label          STRING  Field Label
         * @param   $html           STRING  Form HTML
         *
         * @return  $html           STRING  Input HTML with wrapping tag
         */
        public static function element_tr( $label, $html ) {
            ob_start();
            ?>
            <tr>
                <th><?php echo $label ?></th>
                <td><?php echo $html ?></td>
            </tr>
            <?php
            $output = ob_get_contents();
            ob_end_clean();
            
            return apply_filters(
                                'np_builder/form_helper/element_tr',
                                $output,
                                $label,
                                $html
                            );
        }
        
        /**
         * Wrapped html by table tag
         * @since 1.0.1
         *
         * @param   $label          STRING  Field Label
         * @param   $html           STRING  Form HTML
         *
         * @return  $html           STRING  Input HTML with wrapping tag
         */
        public static function element_table( $label, $html ){
            ob_start();
            ?>
            <table cellpadding="5" cellspacing="5">
                <?php echo self::element_tr( $label, $html ); ?>
            </table>
            <?php
            $output = ob_get_contents();
            ob_end_clean();
            
            return apply_filters(
                                'np_builder/form_helper/element_table',
                                $output,
                                $label,
                                $html
                            );
        }
    }
}