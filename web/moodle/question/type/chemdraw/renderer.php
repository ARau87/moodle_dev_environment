<?php
/**
 * Chemdraw question renderer class.
 *
 * @package    qtype
 * @subpackage chemdraw
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for chemdraw questions.
 *
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_chemdraw_renderer extends qtype_renderer {


    /**
     * Defines how a question of this type should look like.
     * 
     * @param question_attempt $qa A reference to the current question attempt
     * @param question_display_options $options This object controls what should be displayed
     * @return string HTML fragment defining the question's look 
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options){

        // Add the required scripts to the page
        $this->add_scripts();

        // Get a reference to the current question
        $question = $qa->get_question();

        // Add the question text to the page
        $output = html_writer::tag('div', $question->questiontext);

        // Create the input section and add it to the output
        $output .= $this->create_input_section($qa);


        return $output;

    }

    /**
     * This shows a specific feedback in the review page
     * 
     * @param question_attempt $qa A reference to the question attempt
     * @return string The feedback corresponding to the answer given.
     */
    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer(array('answer' => $qa->get_last_qt_var('answer')));
        if (!$answer /*|| !$answer->feedback*/) {

            //TODO: getstring!!!
            return 'Incorrect answer';
        }

        return $question->format_text($answer->feedback, $answer->feedbackformat,
                $qa, 'question', 'answerfeedback', $answer->id);
    }

    /**
     * Returns the HTML fragment for the input fields
     * 
     * @param question_attempt $qa Reference to the question attempt
     * @return string HTML fragment for the input field
     */
    private function create_input_section(question_attempt $qa){

        // Get the name and the current answer to the question
        $inputname = $qa->get_qt_field_name('answer');
        $currentanswer = $qa->get_last_qt_var('answer');

        // Create the container for the JSME editor
        $jsme_editor = html_writer::tag('div', '', array(
            'class' => 'qtype_chemdraw_jsme_container'
        ));

        // Create the hidden smiles answer field
        $smiles_input = html_writer::empty_tag('input', array(
            'type' => 'text',
            'id' => $inputname,
            'name' => $inputname,
            'value' => $currentanswer,
            'class' => 'qtype_chemdraw-jsme-input',
            /*'hidden' => 'false'*/
        ));

        // Create a container for the input field and add the elements created before
        $input_section =  html_writer::start_tag('div', array(
            'class' => 'qtype_chemdraw_answer_section'
        ));
        $input_section .= $jsme_editor;
        $input_section .= $smiles_input;
        
        $input_section .= html_writer::end_tag('div');



        return $input_section;

    }

    /**
     * Adds the scripts needed in the page rendering the question
     */
    private function add_scripts(){

        global $PAGE;

        $PAGE->requires->js('/question/type/chemdraw/lib/js/qtype-chemdraw-jsme-editor.js', false);
        $PAGE->requires->js('/question/type/chemdraw/lib/js/qtype-chemdraw-setup-jsme-renderer.js', false);
        $PAGE->requires->js('/question/type/chemdraw/lib/vendor/jsme/jsme/jsme.nocache.js', false);
    }

}