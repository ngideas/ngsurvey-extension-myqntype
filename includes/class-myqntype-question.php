<?php
/**
 * The file that defines the my question type class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ngideas.com
 * @since      1.0.0
 *
 * @package    NgSurvey
 * @subpackage NgSurvey/extensions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once NGSURVEY_PATH . 'includes/abstracts/class-ngsurvey-question.php';

/**
 * The survey my question type class.
 *
 * This is used to define my question type class.
 *
 * @package    NgSurvey
 * @author     NgIdeas <support@ngideas.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       https://ngideas.com
 * @since      1.0.0
 */
class MyQnType_Question extends NgSurvey_Question {
    
    /**
     * Define the base question type functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct( $config = array() ) {
        
        $config = array_merge( $config, array(
            'name'      => 'myqntype',
            'group'     => 'choice',
            'icon'      => 'dashicons dashicons-editor-help',
            'title'     => __( 'My Question Type', NGSURVEY_TEXTDOMAIN ),
            'template'  => new NgSurvey_Template_Loader(array(
                'plugin_directory' => plugin_dir_path( dirname( __FILE__ ) ),
                'filter_prefix' => 'ngsurvey-extension-myqntype'
            )),
            'options' => array(
                (object) [
                    'title'    => 'Choice Type',
                    'type'     => 'select',
                    'name'     => 'choice_type',
                    'help'     => 'Select the type of HTML elements should be displayed to the users.',
                    'options'  => [ 'radio' => 'Radio Buttons', 'checkbox' => 'Checkboxes', 'select' => 'Select Dropdown' ],
                    'default'  => 'radio',
                    'filter'   => 'key',
                ],
                (object) [
                    'title'    => 'Minimum Selections',
                    'type'     => 'text',
                    'name'     => 'minimum_selections',
                    'help'     => 'Enter the minimum number of answers user must select. Applicable for checkboxes.',
                    'options'  => null,
                    'default'  => 0,
                    'filter'   => 'uint',
                ],
                (object) [
                    'title'    => 'Maximum Selections',
                    'type'     => 'text',
                    'name'     => 'maximum_selections',
                    'help'     => 'Enter the maximum number of answers the user can select. Applicable for checkboxes.',
                    'options'  => null,
                    'default'  => 0,
                    'filter'   => 'uint',
                ],
                (object) [
                    'title'    => 'Show Answers Inline',
                    'type'     => 'select',
                    'name'     => 'show_answers_inline',
                    'help'     => 'Shows the answers inline instead of one answer per row.',
                    'options'  => [ 1 => 'Yes', 0 => 'No' ],
                    'default'  => 0,
                    'filter'   => 'uint',
                ],
                (object) [
                    'title'    => 'Show Custom Answer',
                    'type'     => 'select',
                    'name'     => 'show_custom_answer',
                    'help'     => 'Shows a text box to the user to enter their own answer.',
                    'options'  => [ 1 => 'Show', 0 => 'Hide' ],
                    'default'  => 0,
                    'filter'   => 'uint',
                ],
                (object) [
                    'title'    => 'Custom Answer Placeholder',
                    'type'     => 'text',
                    'name'     => 'custom_answer_placeholder',
                    'help'     => 'Enter the text that is shown as placeholder in the custom answer textbox.',
                    'options'  => null,
                    'default'  => 'Other',
                ],
                (object) [
                    'title'    => 'Custom Answer Max Length',
                    'type'     => 'text',
                    'name'     => 'custom_answer_maxlength',
                    'help'     => 'Enter the maximum number of characters allowed in the custom answer textbox.',
                    'options'  => null,
                    'default'  => 256,
                    'filter'   => 'uint',
                ],
            ),
        ) );
        
        parent::__construct( $config );
    }
    
    /**
     * The function to save the data submitted through the edit questions form.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array      $ng_form   The array containing all unsanitized form data
     * @var      boolean    $status    True on success, false otherwise
     */
    public function save_form ( $question ) {
        if( $question->qtype != $this->name ) {
            return true;
        }
        
        global $wpdb;
        $status = parent::save_form( $question );

        if( $status !== false ) {
            
            // Delete the answers which are not in the request
            if( empty( $question->answers ) ) {
                foreach ( $question->answers as $answer ) {
                    
                    if( !in_array( $answer->id, $_POST['ngform']['answer_id'] ) ) {
                        
                        $wpdb->delete(
                            "{$wpdb->prefix}ngs_answers",
                            array( 'id' => $answer->id ),
                            array( '%d' )
                        );
                    }
                }
            } else {
                $wpdb->delete(
                    "{$wpdb->prefix}ngs_answers",
                    array( 'question_id' => $question->id ),
                    array( '%d' )
                );
            }
            
            // Now insert or merge answers
            if( !empty( $_POST['ngform'][ 'answer_id' ] ) ) {
                foreach ( $_POST['ngform'][ 'answer_id' ] as $i => $answer_id ) {
                    
                    $wpdb->replace(
                        "{$wpdb->prefix}ngs_answers",
                        array(
                            'id'            => $answer_id,
                            'answer_type'   => 'x',
                            'question_id'   => $question->id,
                            'title'         => wp_filter_post_kses( $_POST['ngform'][ 'answer_title' ][ $i ] ),
                            'sort_order'    => $i + 1,
                        ),
                        array(
                            '%d',
                            '%s',
                            '%d',
                            '%s',
                            '%d'
                        )
                    );
                }
            }
        }
        
        return $status;
    }

    /**
     * Returns the rules templates to support conditional rules of this question.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $rules The conditional rule template of this question
     */
    public function get_rules ( $question ) {
        if( $question->qtype != $this->name ) {
            return $question;
        }
        
        $options = array();
        foreach ( $question->answers as $answer ) {
            $options[] = (object) array( 'label' => $answer->title, 'value' => $answer->id );
        }
        
        $rule = (object) array(
            'id'            => $question->id,
            'field'         => $this->name,
            'label'         => $question->title,
            'icon'          => $this->icon,
            'type'          => 'integer',
            'input'         => 'select',
            'values'        => $options,
            'multiple'      => 'true',
            'plugin'        => 'select2',
            'plugin_config' => (object) array(
                'width'     => 'auto',
                'theme'     => 'bootstrap4'
            ),
            'operators'     => array( "in", "not_in", "is_empty", "is_not_empty"  ),
        );
        array_push( $question->rules, json_encode( $rule ) );

        return $question;
    }
    
    /**
     * The function to filter the response data and return the array of rows to save into database.
     *
     * @since    1.0.0
     * @access   public
	 * @var      array $filtered_data the filtered data returned to caller
	 * @var      stdClass $question the question object
     * 
     * @return   array $filtered_data the filtered response data
     */
    public function filter_response_data ( $filtered_data, $question ) {
        if( $question->qtype != $this->name ) {
            return $filtered_data;
        }
        
        if( !empty( $_POST[ 'ngform' ][ 'answers' ][ $question->id ][ 'response' ] ) ) {
            foreach ( $question->answers as $answer ) {
                foreach ( $_POST[ 'ngform' ][ 'answers' ][ $question->id ][ 'response' ] as $response ) {
                    if( $answer->id == $response ) {
                        $filtered_data[] = array( 'answer_id' => (int) $response, 'column_id' => 0, 'answer_data' => null );
                        break;
                    }
                }
            }
        }
        
        if( !empty( $_POST[ 'ngform' ][ 'answers' ][ $question->id ]['custom'] ) ) {
            $custom_answer = wp_kses_post( wp_unslash( $_POST[ 'ngform' ][ 'answers' ][ $question->id ]['custom'] ) );
            $filtered_data[] = array( 'answer_id' => 1, 'column_id' => 0, 'answer_data' => $custom_answer );
        }
        
        return $filtered_data;
    }
    
}
