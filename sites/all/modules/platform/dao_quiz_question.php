<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_quiz_question_add($question, $quiz_id)
{
    $question_id = platform_db_quiz_question_insert($question, $quiz_id);
    drupal_set_message('Added Quiz Question (ID ' . $question_id . ')');
    platform_db_quiz_question_entity($question_id, $question->entity_id);

    foreach ($question->possible_answers as $answer)
    {
        platform_db_quiz_possible_answer_add($answer, $question_id);
    }
}

function platform_db_quiz_question_insert($question, $quiz_id)
{
    db_set_active('platform');

    $now = new DateTime('now', new DateTimeZone('UTC'));

    $id = db_insert('quiz.question')
        ->fields(array(
            'quiz_id' => $quiz_id,
            'question_text' => $question->text,
            'created_utc' => $now->format('c'),
        ))
        ->execute();

    db_set_active();

    return $id;
}

function platform_db_quiz_question_entity($id, $entity_id)
{
    db_set_active();

    db_insert('platform.quiz_question_entity')
        ->fields(array(
            'quiz_question_id' => $id,
            'entity_id' => $entity_id,
        ))
        ->execute();

    db_set_active();
}
