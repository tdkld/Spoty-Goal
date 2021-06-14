<?php
require_once('utils.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        render_template('templates/quiz/run.php', array(), true);
    } else {
        render_template('templates/quiz/create.php', array(), true);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    create_quiz();
} else {
    render_template('templates/error.php', array('error_text' => 'Usupported request type.'), true);
}

function create_quiz()
{
    
}
?>