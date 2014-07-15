<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_quiz_questions_add_edit($questions, $quiz_id)
{
    // Insert and Update
    foreach ($questions as $question)
    {
        platform_db_quiz_question_add_edit($question, $quiz_id);
    }

    // Delete
    $ids = platform_db_quiz_question_ids_get($questions);
    platform_db_quiz_question_not_in_quiz_delete($ids, $quiz_id);
}

function platform_db_quiz_question_add_edit($question, $quiz_id)
{
    $question_id = platform_db_quiz_question_id_get($question->entity_id);

    if (is_null($question_id))
    {
        $question_id = platform_db_quiz_question_insert($question, $quiz_id);
        drupal_set_message('Added Quiz Question (ID ' . $question_id . ')');
        platform_db_quiz_question_entity($question_id, $question->entity_id);
    }
    else
    {
        platform_db_quiz_question_update($question_id, $question, $quiz_id);
        drupal_set_message('Updated Quiz Question (ID ' . $question_id . ')');
    }

    platform_db_quiz_possible_answers_add_edit(
        $question->possible_answers, $question_id);
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

function platform_db_quiz_question_id_get($entity_id)
{
    db_set_active();

    $record = db_select('platform.quiz_question_entity', 'qe')
        ->fields('qe')
        ->condition('qe.entity_id', $entity_id)
        ->execute()
        ->fetchObject();

    db_set_active();

    if (is_object($record))
    {
        return $record->quiz_question_id;
    }
    else
    {
        return null;
    }
}

function platform_db_quiz_question_update($id, $question, $quiz_id)
{
    db_set_active('platform');

    db_update('quiz.question')
        ->fields(array(
            'quiz_id' => $quiz_id,
            'question_text' => $question->text,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();

    return $id;
}

function platform_db_quiz_question_ids_get($questions)
{
    $return_entity_id =
        function($question)
        {
            return $question->entity_id;
        };
    $entity_ids = array_map($return_entity_id, $questions);

    db_set_active();

    $records = db_select('platform.quiz_question_entity', 'qe')
        ->fields('qe')
        ->condition('qe.entity_id', $entity_ids, 'IN')
        ->execute()
        ->fetchAll();

    db_set_active();

    $return_question_id =
        function($record)
        {
            return $record->quiz_question_id;
        };
    return array_map($return_question_id, $records);
}

function platform_db_quiz_question_not_in_quiz_delete($ids, $quiz_id)
{
    db_set_active('platform');

    db_update('quiz.question')
        ->fields(array(
            'is_deleted' => true,
        ))
        ->condition('id', $ids, 'NOT IN')
        ->condition('quiz_id', $quiz_id)
        ->execute();

    db_set_active();
}
