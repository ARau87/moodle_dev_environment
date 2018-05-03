<?php

/**
 * Class defining how to render the editing form.
 * 
 * @package     qtype
 * @subpackage  chemdraw
 * @copyright   2018 Andreas Rau
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

 /**
  * ChemDraw question editing form definition.
  * 
  * @copyright  2018 Andreas Rau
  * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */
class qtype_chemdraw_edit_form extends question_edit_form {


    /**
     * Add any question-type specific form fields.
     *
     * @param object $mform the form being built.
     */ 
    protected function definition_inner($mform){

        $this->setup_jsme_editor();

        $this->add_answers_section($mform);

        $this->add_interactive_settings();

    }

    /**
     * Setup the jsme editor by importing the required javascript code
     */
    private function setup_jsme_editor(){

        global $PAGE;

        $PAGE->requires->js('/question/type/chemdraw/lib/js/setup-jsme.js');
        $PAGE->requires->js('/question/type/chemdraw/lib/vendor/jsme/jsme.nocache.js');
    }

    /**
     * Creates the 'Answers' section in the edit form
     * 
     * @param object Reference to the mform object
     */
    private function add_answers_section(&$mform){

        // Adding the header of answers section
        $mform->addElement('header', 'answerhdr',
        get_string('answers', 'question'), '');

        $mform->setExpanded('answerhdr', 1);
        $answersoption = '';

        $this->add_JSME_editor($mform);
    }

    /**
     * Add the JSME editor and some other needed
     * fields to the form
     * 
     * @param object Reference to the mform object
     */
    private function add_JSME_editor(&$mform){

        // Add the JSME editor to the answers section 
        // and add all needed scripts.
        $mform->addElement('html', html_writer::tag('div', '',
                array('class' => 'jme_applet', 
                      'id' => 'jsme_container', 
                      'name' => 'JME1', 
                      )));
        
        $mform->addElement('text', 'answer', 
                'SMILES', '');
        $mform->setType('answer', PARAM_RAW);

        $mform->addElement('editor', 'feedback', 
                'Feedback', 'Feedback to the question');
        //$mform->setType('feedback', PARAM_RAW);

        $mform->addElement('hidden', 'fraction', 1.0);
        $mform->setType('fraction', PARAM_INTEGER);

    }  
    
    /**
     * Preprocessing the data
     * 
     * @param object Reference to the question instance
     * 
     */
    protected function data_preprocessing($question){

        $question = parent::data_preprocessing($question);

        var_dump($question);

        // Only try to render the answers to the form if the question is edited and NOT 
        // if a new question is created
        if(isset($question->options) && sizeof($question->options->answers) > 0){

            // Render the question answer to the form
            foreach($question->options->answers as $answer){

                $question->answer = $answer->answer;

            }
        }


        return $question;

    }


    /** 
     *  Returns the name of the question type
     *
     * @return string name of the question type
     */
    public function qtype() {
        return 'chemdraw';
    }

}