<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_quiz_add($quiz)
{
    $quiz_id = platform_db_quiz_insert($quiz);

    drupal_set_message(
            'Added Quiz ' . $quiz->title . ' ' .
            '(ID ' . $quiz_id . ')');

    platform_db_quiz_node($quiz_id, $quiz->nid);

    foreach ($quiz->questions as $question)
    {
        platform_db_quiz_question_add($question, $quiz_id);
    }
}

function platform_db_quiz_edit($quiz)
{
    $id = platform_db_quiz_id_get($quiz->nid);

    platform_db_quiz_update($id, $quiz);

    drupal_set_message('Updated ' . $quiz->title . ' (ID ' . $id . ')');
}

function platform_db_quiz_remove($node)
{
    $id = platform_db_quiz_id_get($node->nid);

    platform_db_quiz_node_delete($id, $node->nid);
    platform_db_quiz_delete($id);

    drupal_set_message('Deleted the quiz (ID ' . $id . ')');
}

function platform_db_quiz_insert($quiz)
{
    db_set_active('platform');

    $now = new DateTime('now', new DateTimeZone('UTC'));

    $id = db_insert('quiz.quiz')
        ->fields(array(
            'type_id' => $quiz->type_id,
            'category_id' => $quiz->category_id,
            'name' => $quiz->title,
            'total_points' => $quiz->total_points,
            'is_hidden' => $quiz->is_hidden,
            'created_utc' => $now->format('c'),
        ))
        ->execute();

    db_set_active();

    return $id;
}

function platform_db_quiz_node($id, $nid)
{
    db_set_active();

    db_insert('platform.quiz_node')
        ->fields(array(
            'quiz_id' => $id,
            'node_id' => $nid
        ))
        ->execute();

    db_set_active();
}

function platform_db_quiz_id_get($nid)
{
    db_set_active();

    $record = db_select('platform.quiz_node', 'pn')
        ->fields('pn')
        ->condition('pn.node_id', $nid)
        ->execute()
        ->fetchObject();

    db_set_active();

    if (is_object($record))
    {
        return $record->quiz_id;
    }
    else
    {
        throw new Exception('Quiz for node ' . $nid . ' not in platform DB!');
    }
}

function platform_db_quiz_update($id, $quiz)
{
    db_set_active('platform');

    db_update('quiz.quiz')
        ->fields(array(
            'type_id' => $quiz->type_id,
            'category_id' => $quiz->category_id,
            'name' => $quiz->title,
            'total_points' => $quiz->total_points,
            'is_hidden' => $quiz->is_hidden,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();
}

function platform_db_quiz_node_delete($id, $nid)
{
    db_set_active();

    db_delete('platform.quiz_node')
        ->condition('quiz_id', $id)
        ->condition('node_id', $nid)
        ->execute();

    db_set_active();
}

function platform_db_quiz_delete($id)
{
    db_set_active('platform');

    db_update('quiz.quiz')
        ->fields(array(
            'is_deleted' => true,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();
}
