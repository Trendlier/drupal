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
        $question_id = platform_db_quiz_question_insert($question, $quiz_id);
        drupal_set_message('Added Quiz Question (ID ' . $question_id . ')');
        platform_db_quiz_question_entity($question_id, $question->entity_id);

        foreach ($question->possible_answers as $answer)
        {
            $answer_id =
                platform_db_quiz_possible_answer_insert($answer, $question_id);
            drupal_set_message(
                'Added Quiz Possible Answer ' .
                '(ID ' . $answer_id . ')');
            platform_db_quiz_possible_answer_entity(
                $answer_id, $answer->entity_id);
        }
    }
}

function platform_db_quiz_edit($quiz)
{
}

function platform_db_quiz_remove($node)
{
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
