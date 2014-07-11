<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_quiz_possible_answers_add_edit($answers, $question_id)
{
    // Insert and Update
    foreach ($answers as $answer)
    {
        platform_db_quiz_possible_answer_add_edit($answer, $question_id);
    }

    // Delete
    $ids = platform_db_quiz_possible_answer_ids_get($answers);
    platform_db_quiz_possible_answer_not_in_question_delete(
        $ids, $question_id);
}

function platform_db_quiz_possible_answer_add_edit($answer, $question_id)
{
    $answer_id = platform_db_quiz_possible_answer_id_get($answer->entity_id);

    if (is_null($answer_id))
    {
        $answer_id =
            platform_db_quiz_possible_answer_insert($answer, $question_id);
        drupal_set_message(
            'Added Quiz Possible Answer ' .
            '(ID ' . $answer_id . ')');
        platform_db_quiz_possible_answer_entity(
            $answer_id, $answer->entity_id);
    }
    else
    {
        platform_db_quiz_possible_answer_update(
            $answer_id, $answer, $question_id);
        drupal_set_message(
            'Updated Quiz Possible Answer ' .
            '(ID ' . $answer_id . ')');
    }
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

function platform_db_quiz_possible_answer_id_get($entity_id)
{
    db_set_active();

    $record = db_select('platform.quiz_possible_answer_entity', 'qe')
        ->fields('qe')
        ->condition('qe.entity_id', $entity_id)
        ->execute()
        ->fetchObject();

    db_set_active();

    if (is_object($record))
    {
        return $record->quiz_possible_answer_id;
    }
    else
    {
        return null;
    }
}

function platform_db_quiz_possible_answer_update($id, $answer, $question_id)
{
    db_set_active('platform');

    $now = new DateTime('now', new DateTimeZone('UTC'));

    db_update('quiz.possible_answer')
        ->fields(array(
            'question_id' => $question_id,
            'answer_text' => $answer->answer_text,
            'points' => $answer->points,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();

    return $id;
}

function platform_db_quiz_possible_answer_ids_get($answers)
{
    $return_entity_id =
        function($answer)
        {
            return $answer->entity_id;
        };
    $entity_ids = array_map($return_entity_id, $answers);

    db_set_active();

    $records = db_select('platform.quiz_possible_answer_entity', 'qe')
        ->fields('qe')
        ->condition('qe.entity_id', $entity_ids, 'IN')
        ->execute()
        ->fetchAll();

    db_set_active();

    $return_answer_id =
        function($record)
        {
            return $record->quiz_possible_answer_id;
        };
    return array_map($return_answer_id, $records);
}

function platform_db_quiz_possible_answer_not_in_question_delete(
    $ids, $question_id)
{
    db_set_active('platform');

    db_update('quiz.possible_answer')
        ->fields(array(
            'is_deleted' => true,
        ))
        ->condition('id', $ids, 'NOT IN')
        ->condition('question_id', $question_id)
        ->execute();

    db_set_active();
}
