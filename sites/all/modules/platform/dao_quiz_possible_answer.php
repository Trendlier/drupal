<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_quiz_possible_answer_add($answer, $question_id)
{
    $answer_id =
        platform_db_quiz_possible_answer_insert($answer, $question_id);
    drupal_set_message(
        'Added Quiz Possible Answer ' .
        '(ID ' . $answer_id . ')');
    platform_db_quiz_possible_answer_entity(
        $answer_id, $answer->entity_id);
}

function platform_db_quiz_possible_answer_insert($answer, $question_id)
{
    db_set_active('platform');

    $now = new DateTime('now', new DateTimeZone('UTC'));

    $id = db_insert('quiz.possible_answer')
        ->fields(array(
            'question_id' => $question_id,
            'answer_text' => $answer->answer_text,
            'points' => $answer->points,
            'created_utc' => $now->format('c'),
        ))
        ->execute();

    db_set_active();

    return $id;
}

function platform_db_quiz_possible_answer_entity($id, $entity_id)
{
    db_set_active();

    db_insert('platform.quiz_possible_answer_entity')
        ->fields(array(
            'quiz_possible_answer_id' => $id,
            'entity_id' => $entity_id,
        ))
        ->execute();

    db_set_active();
}
