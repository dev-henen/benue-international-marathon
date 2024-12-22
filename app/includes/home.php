<?php

$data = [
    'title' => 'My Website',
];

echo $GLOBALS['twig']->render('home.twig', $data);
