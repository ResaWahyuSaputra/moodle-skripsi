<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Combined question embedded sub-question renderer class.
 *
 * @package   qtype_combined
 * @copyright 2019 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/multichoice/renderer.php');


class qtype_combined_multichoice_embedded_renderer extends qtype_renderer
    implements qtype_combined_subquestion_renderer_interface {

    public function subquestion(question_attempt $qa,
                                question_display_options $options,
                                qtype_combined_combinable_base $subq,
                                $placeno) {
        $fullresponse = new qtype_combined_response_array_param($qa->get_last_qt_data());
        $response = $fullresponse->for_subq($subq);
        if (isset($response['answer'])) {
            $response = $response['answer'];
        } else {
            $response = -1;
        }
        $commonattributes = array(
            'type' => 'radio'
        );
        if ($options->readonly) {
            $commonattributes['disabled'] = 'disabled';
        }
        $rbuttons = array();
        $feedbackimg = array();
        $classes = array();

        $question = $subq->question;
        foreach ($question->get_order($qa) as $value => $ansid) {
            $inputname = $qa->get_qt_field_name($subq->step_data_name('answer'));
            $ans = $question->answers[$ansid];
            $inputattributes = array();
            $inputattributes['name'] = $inputname;
            $inputattributes['value'] = $value;
            $inputattributes['id'] = $ansid;
            $isselected = $question->is_choice_selected($response, $value);
            if ($isselected) {
                $inputattributes['checked'] = 'checked';
            } else {
                unset($inputattributes['checked']);
            }

            $rbuttons[] = html_writer::empty_tag('input', $inputattributes + $commonattributes) .
                html_writer::tag('label',
                    html_writer::span(\qtype_combined\utils::number_in_style($value, $question->answernumbering), 'answernumber') .
                    $question->make_html_inline($question->format_text(
                        $ans->answer, $ans->answerformat, $qa, 'question', 'answer', $ansid)),
                    ['for' => $inputattributes['id']]);

            if ($options->feedback && $isselected && trim($ans->feedback)) {
                $feedback[] = html_writer::tag('span',
                    $question->make_html_inline($question->format_text(
                        $ans->feedback, $ans->feedbackformat,
                        $qa, 'question', 'answerfeedback', $ansid)),
                    array('class' => ' subqspecificfeedback '));
            } else {
                $feedback[] = '';
            }

            $class = 'r' . ($value % 2);
            if ($options->correctness && $isselected) {
                $feedbackimg[] = $this->feedback_image($ans->fraction);
                $class .= ' ' . $this->feedback_class($ans->fraction);
            } else {
                $feedbackimg[] = '';
            }
            $classes[] = $class;
        }

        if ('h' === $subq->get_layout()) {
            $inputwraptag = 'span';
        } else {
            $inputwraptag = 'div';
        }

        $rbhtml = '';
        foreach ($rbuttons as $key => $rb) {
            $rbhtml .= html_writer::tag($inputwraptag, $rb . ' ' . $feedbackimg[$key] . $feedback[$key],
                    array('class' => $classes[$key])) . "\n";
        }

        $result = html_writer::tag($inputwraptag, $rbhtml, array('class' => 'answer'));
        return $result;
    }
}
