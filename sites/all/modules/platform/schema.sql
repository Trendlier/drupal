CREATE SCHEMA platform;

CREATE TABLE platform.product_node(
    product_id INTEGER NOT NULL UNIQUE,
    node_id INTEGER NOT NULL UNIQUE REFERENCES node(nid)
);

CREATE TABLE platform.review_node(
    review_id INTEGER NOT NULL UNIQUE,
    node_id INTEGER NOT NULL UNIQUE REFERENCES node(nid)
);

CREATE TABLE platform.news_post_node(
    news_post_id INTEGER NOT NULL UNIQUE,
    node_id INTEGER NOT NULL UNIQUE REFERENCES node(nid)
);

CREATE TABLE platform.quiz_node(
    quiz_id INTEGER NOT NULL UNIQUE,
    node_id INTEGER NOT NULL UNIQUE REFERENCES node(nid)
);

CREATE TABLE platform.quiz_question_entity(
    quiz_question_id INTEGER NOT NULL UNIQUE,
    entity_id INTEGER NOT NULL UNIQUE
);

CREATE TABLE platform.quiz_possible_answer_entity(
    quiz_possible_answer_id INTEGER NOT NULL UNIQUE,
    entity_id INTEGER NOT NULL UNIQUE
);
