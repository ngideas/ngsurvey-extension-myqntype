<?php
/**
 * The template for displaying Choice question type on front-end.
 *
 * @link       https://ngideas.com
 * @since      1.0.0
 *
 * @package    NgSurvey
 * @subpackage NgSurvey/public/views
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$question = $data;
$custom   = '';

foreach ( $question->responses as $response ) {
    if( !empty( $response[ 'answer_data' ] ) ) {
        $custom = $response[ 'answer_data' ];
    }
}

switch ( $question->params->get('choice_type') ) {
    case 'radio':
        foreach ( $question->answers as $answer ) {
            ?>
            <div class="mb-1 form-check custom-radio<?php echo $question->params->get('show_answers_inline') ? ' custom-control-inline' : '';?>">
            	<input 
            		type="radio" 
            		id="answer-<?php echo $question->id;?>-<?php echo $answer->id;?>" 
            		name="ngform[answers][<?php echo $question->id?>][response][]"
            		value="<?php echo $answer->id;?>"
            		class="form-check-input"
                    <?php echo $question->params->get('required') ? 'required="required"' : '';?>
                    <?php echo in_array( $answer->id, array_column( $question->responses, 'answer_id' ) ) ? 'checked="checked"' : '';?>>

            	<label class="form-check-label" for="answer-<?php echo $question->id;?>-<?php echo $answer->id;?>">
            		<?php echo $answer->title;?>
            	</label>
            </div>
            <?php
        }
        break;
        
    case 'checkbox':
        foreach ( $question->answers as $answer ) {
            ?>
            <div class="mb-1 form-check custom-checkbox<?php echo $question->params->get('show_answers_inline') ? ' custom-control-inline' : '';?>">
            	<input 
            		type="checkbox" 
            		id="answer-<?php echo $question->id;?>-<?php echo $answer->id;?>" 
            		name="ngform[answers][<?php echo $question->id?>][response][]"
            		value="<?php echo $answer->id;?>"
            		class="form-check-input"
            		<?php echo $question->params->get('minimum_selections') ? 'minlength="'.$question->params->get('minimum_selections').'"' : '';?>
            		<?php echo $question->params->get('maximum_selections') ? 'maxlength="'.$question->params->get('maximum_selections').'"' : '';?>
            		<?php echo $question->params->get('required') ? 'required="required"' : '';?>
            		<?php echo in_array( $answer->id, array_column( $question->responses, 'answer_id' ) ) ? 'checked="checked"' : '';?>>
            	<label class="form-check-label" for="answer-<?php echo $question->id;?>-<?php echo $answer->id;?>"><?php echo $answer->title;?></label>
            </div>
            <?php
        }
        break;
        
    case 'select':
        ?>
    	<select class="form-select" name="ngform[answers][<?php echo $question->id?>][response][]" <?php echo $question->params->get('required') ? 'required="required"' : '';?>>
    		<option value=""><?php echo __( '- Select an option -', NGSURVEY_TEXTDOMAIN );?></option>
    		<?php 
    		foreach ( $question->answers as $answer ) {
    		    ?>
    		    <option 
    		    	value="<?php echo $answer->id;?>"
    		    	<?php echo in_array( $answer->id, array_column( $question->responses, 'answer_id' ) ) ? 'selected="selected"' : '';?>>
    		    	<?php echo $answer->title;?>
    		    </option>
    		    <?php
    		}
    		?>
    	</select>
        <?php
        break;
}

if( $question->params->get('show_custom_answer') ) {
    ?>
    <div class="form-inline">
        <div class="form-group">
        	<input type="text" 
        		class="form-control" 
        		name="ngform[answers][<?php echo $question->id?>][custom]"
        		id="custom-answer-<?php echo $question->id;?>" 
        		<?php echo $question->params->get('custom_answer_maxlength') > 0 ? ' maxlength="'.$question->params->get('custom_answer_maxlength').'"' : '';?>
        		placeholder="<?php echo __( $question->params->get('custom_answer_placeholder', 'Other'), NGSURVEY_TEXTDOMAIN );?>"
        		value="<?php echo esc_attr( $custom );?>">
        </div>
    </div>
    <?php
}
