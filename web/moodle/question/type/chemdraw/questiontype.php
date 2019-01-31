<?php
/**
 * Question type class for the chemdraw question type.
 *
 * @package    qtype
 * @subpackage chemdraw
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/chemdraw/question.php');


/**
 * The chemdraw question type.
 *
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_chemdraw extends question_type {

    /**
     * Initialise the common question_definition fields.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata){
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
    }

    /**
     * Load the possible response/answers of the current question
     * 
     * @param object $questiondata the question definition data
     * @return array keys: subquestionids / values: arrays of possible responses
     */
    public function get_possible_responses($questiondata){
        $responses = array();

        foreach($questiondata->options->answers as $answer_id => $answer){

            $responses[$answer_id] = new question_possible_response($answer->answer, $answer->fraction);

        }

        $responses[null] = question_possible_response::no_response();

        return array($questiondata->id => $responses);
    }

    /**
     * Saving the question with any extra information
     * 
     * @param object $question The question to be saved.
     */
    public function save_question_options($question) {

        global $DB;
        $result = new stdClass();

        // Perform sanity checks on fractional grades.
        $maxfraction = -1;
        foreach ($question->answer as $key => $answerdata) {
            if ($question->fraction[$key] > $maxfraction) {
                $maxfraction = $question->fraction[$key];
            }
        }

        if ($maxfraction != 1) {
            $result->error = get_string('fractionsnomax', 'question', $maxfraction * 100);
            return $result;
        }

        parent::save_question_options($question);

        $this->save_question_answers($question);

        $this->save_hints($question);
    }

    /**
     * Filling necessary fields for the question_answers table
     * 
     * @param stdClass $answer Object to save the answer data.
     * @param object $questiondata The data from the question editing form
     * @param int $key A key of the answer in question
     * @param object $context This is needed for working with files
     * @return $answer Answer object with filled data
     */
    protected function fill_answer_fields($answer, $questiondata, $key, $context){
        $answer = parent::fill_answer_fields($answer, $questiondata, $key, $context);
        return $answer;
    }





}