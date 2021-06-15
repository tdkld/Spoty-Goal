<?php
header('Content-Type: application/json');

if (isset($_GET['method'])) {
    if ($_GET['method'] === 'search') {
        if (isset($_GET['song'])) {
            // todo: make real spotify request
            $songs = array(
                array(
                    'name' => 'First song name',
                    'author' => 'First author',
                    'thumb' => '/img/spotify-song-pic.jpg'
                ),
                array(
                    'name' => 'Second song name',
                    'author' => 'Second author',
                    'thumb' => '/img/spotify-song-pic.jpg'
                )
            );
            echo json_encode(array('data' => $songs));
        } else {
            echo json_encode(array('error' => 'Missing parameters.'));
        }
    } else {
        echo json_encode(array('error' => 'Unsupported API method.'));
    }
} else {
    echo json_encode(array('error' => 'Unsupported HTTP request type.'));
}
?>