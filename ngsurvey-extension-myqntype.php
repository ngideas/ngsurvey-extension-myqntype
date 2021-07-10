<?php
/**
 * NgSurvey extension to add My Question Type functionality.
 *
 * @link              https://ngideas.com
 * @since             1.0.0
 * @package           NgSurvey
 *
 * @wordpress-plugin
 * Plugin Name:       NgSurvey My Question Type
 * Plugin URI:        https://ngideas.com/
 * Description:       This is NgSurvey extension to add My Question Type to NgSurvey plugin. 
 * Version:           1.0.0
 * Author:            NgIdeas
 * Author URI:        https://ngideas.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       myqntype
 * Domain Path:       /languages
 * NgSurvey Type:     Extension
 * NgSurvey ID:       ngsurvey-extension-myqntype
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NgSurvey_Extension_MyQnType' ) ) :

class NgSurvey_Extension_MyQnType {
    
    /**
     * Construct the plugin.
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }
    
    /**
     * Initialize the plugin.
     */
    public function init() {
        if( ! defined( 'NGSURVEY_PATH' ) ) {
            add_action( 'admin_notices', array ( $this, 'need_ngsurvey' ) );
            return;
        }
        
        // Now load all classes
        include_once 'includes/class-myqntype-question.php';
        
        // Add plugin hooks
        $this->add_hooks();
    }
    
    /**
     * A notice to show when the plugin is loaded without NgSurvey loaded.
     *
     * @return string Fallack notice.
     */
    
    public function need_ngsurvey() {
        $error = sprintf( __( 'NgSurvey My Question requires %sNgSurvey%s to be installed & activated!' , 'ngsurvey-extension-myqntype' ), '<a href="http://wordpress.org/extend/plugins/ngsurvey/">', '</a>' );
        $message = '<div class="error"><p>' . $error . '</p></div>';
        
        echo $message;
    }
    
    /**
     * Adds the required hooks to NgSurvey to register the question type with NgSurvey.
     */
    public function add_hooks() {
        
    	$plugin = new MyQnType_Question();
        
        // Add filter to enqueue plugin JavaScript files
        add_filter( 'ngsurvey_enqueue_admin_scripts', array( $plugin, 'enqueue_admin_scripts' ), 10, 2 );
        
        // Add filter to inject the question type to list of available question types
        add_filter( 'ngsurvey_fetch_question_types', array( $plugin, 'get_type' ) );
        
        // Add filter to inject the question form for adding new question/edit question
        add_filter( 'ngsurvey_fetch_question_form', array( $plugin, 'get_form' ) );
        
        // Add filter to inject conditional rules of the question type
        add_filter( 'ngsurvey_conditional_rules', array( $plugin, 'get_rules' ) );
        
        // Add action to save the question form
        add_action( 'ngsurvey_save_question_form', array( $plugin, 'save_form' ) );
        
        // Add filter to show response details of a user response
        add_filter( 'ngsurvey_survey_results', array( $plugin, 'get_results' ) );
        
        // Add filter to get the consolidated report of this question type
        add_filter( 'ngsurvey_consolidated_report', array( $plugin, 'get_reports' ) );
        
        // Add filter to render the response form when showing single survey
        add_filter( 'ngsurvey_response_form', array( $plugin, 'get_display' ) );
        
        // Add filter to validate the user response
        add_filter( 'ngsurvey_validate_response', array( $plugin, 'validate'), 10, 3 );
        
        // Add filter to return the user response data that should be saved to data
        add_filter( 'ngsurvey_filter_user_responses', array( $plugin, 'filter_response_data'), 10, 3 );
    }
}

$NgSurvey_Extension_MyQnType = new NgSurvey_Extension_MyQnType( __FILE__ );

endif;
