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

        $this->include_jsme_libs();

        //TODO: getstring!!!
        $this->add_per_answer_fields($mform, 'Anwer {no}' /*get_string('answerno', 'qtype_chemdraw', '{no}')*/,
                question_bank::fraction_options());

        $this->add_interactive_settings();

    }

    /**
     * Setup the jsme editor by importing the required javascript code
     */
    private function include_jsme_libs(){

        global $PAGE;
        global $CFG;

        $PAGE->requires->js('/question/type/chemdraw/lib/js/qtype-chemdraw-jsme-editor.js', false);
        $PAGE->requires->js('/question/type/chemdraw/lib/js/qtype-chemdraw-setup-jsme-editform.js', false);
        $PAGE->requires->js('/question/type/chemdraw/lib/vendor/jsme/jsme/jsme.nocache.js', false);
    }
    
    /**
     * Overwriting the get_per_anser_fields to add jsme editor to each question option.
     * 
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $answersoption reference to return the name of $question->options
     *      field holding an array of answers
     * @return array of form fields. 
     */
    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $answeroptions = array();


        // SETTING THE ANSWEROPTIONS
        // The answer field will contain the smiles string of the chemical structure drawn in the editor
        //TODO: getstring!!!
        $answeroptions[] = $mform->createElement('text', 'answer',
                'Smiles', array('size' => 60));

        // Creates a select to choose the grad fraction
        $answeroptions[] = $mform->createElement('select', 'fraction',
                get_string('grade'), $gradeoptions);
      
        //SETTING THE VISIBLE FORM ELEMENTS

        $repeated[] = $mform->createElement('group', 'answeroptions',
                 $label, $answeroptions, null, false);

        //TODO: getstring!!!
        // Creates the jsme container for each question option
        $repeated[] = $mform->createElement('group', 'qtype_chemdraw_jsme_editor', 'Editor', null, null, false);
  

        //TODO: getstring!!!
        $fileimportoptions = $this->create_file_import_section($mform);
        $repeated[] = $mform->createElement('group', 'qtype_chemdraw_file_import', 'File', $fileimportoptions, null, false);

                
        $repeated[] = $mform->createElement('editor', 'feedback',
                get_string('feedback', 'question'), array('rows' => 5), $this->editoroptions);
        
        
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['qtype_chemdraw_mol_import']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        return $repeated;
    }

    /**
     * Creates the file import section
     * 
     * @param object $mform A reference to the mform object
     * @return string The html for the file import section
     */
    private function create_file_import_section(&$mform){

        $fileimportoptions = array();

        //TODO: getstring

        $fileimportoptions[] = $mform->createElement('html', 
        '<div class="qtype_chemdraw_import">
            <div class="qtype_chemdraw_import_row">
                <label class="qtype_chemdraw_import_label">File:</label>
                <input class="qtype_chemdraw_file_picker" type="file" accept=".mol,.smiles,.smi,.rxn" />
            </div>
            <div class="qtype_chemdraw_import_row">
                <div class="qtype_chemdraw_dropzone">
                    <div class="qtype_chemdraw_dropzone_text">Drop file here!</div>
                </div>
            </div>
            <div class="qtype_chemdraw_import_row">
                <label class="qtype_chemdraw_import_label">Mol:</label>
                <textarea class="qtype_chemdraw_mol_import" placeholder="Paste molstring here!"></textarea>
            </div>
        </div>
        '
        );

        return $fileimportoptions;

    }

    /**
     * Preprocess the answers
     * 
     * @param question The question that should be preprocessed
     */
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

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