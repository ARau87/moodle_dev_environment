<?php
/**
 * ChemDraw question definition class.
 *
 * @package    qtype
 * @subpackage chemdraw
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');

/**
 * Represents a chemdraw question.
 *
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_chemdraw_question extends question_graded_by_strategy implements question_response_answer_comparer {

    /** @var array of question answers */
    public $answer = array();

    /**
     * Constructor
     * 
     * @param object Grading strategy
     */
    public function __construct() {
        parent::__construct(new question_first_matching_answer_grading_strategy($this));
    }

    /**
     * Defines the expected data from the client
     * 
     * @return array defining the expected data
     */
    public function get_expected_data(){

        return array('answer' => PARAM_RAW_TRIMMED);

    }

    /**
     * Defines what has to be submitted to get the question correct
     * 
     * @return array Answer to this question
     */
    public function get_correct_response(){

        $response = parent::get_correct_response();
        
        return $response;

    }

    /**
     * Checks if the response is complete
     * 
     * @param array The response
     * @return boolean If response is complete or not
     */
    public function is_complete_response(array $response){
        return array_key_exists('answer', $response);
    }

    /**
     * Summarises the response
     * 
     * @param array The response to summarise
     * @return string The summary of the response
     */
    public function summarise_response(array $response){
        if(!array_key_exists('answer', $response)){
            return null;
        }
        else {
            return $response['answer'];
        }
    }

    /**
     * Returns an error if an incorrect or empty answer is provided
     * 
     * @param array The response to the question
     * @return string The validation error to show
     */
    public function get_validation_error(array $response){
        if($this->is_gradable_response($response)){
            return '';
        }
        return 'ERROR';
    }

    /**
     * Checks if the provided response is the same as an old response
     *
     * @param array The previous response to the question
     * @param array The new response
     * @return boolean True if the old and new reponse are the same
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    /**
     * Get the answers to the question
     * 
     * @return array Answers to the question
     */
    public function get_answers() {
        return $this->answers;
    }

    /**
     * Compares the response with the correct answer to the question
     * 
     * @param array Response to the answer
     * @param question_answer Correct answer to the question
     */
    public function compare_response_with_answer(array $response, question_answer $answer){
        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }

        //TODO
        return true;
    }





}