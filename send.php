<?php

require_once('./sender/Sender.php');

$sender = new Sender();

$sender->redirect('/');
$sender->spamFilter(3);
$sender->to('mymail@gmail.com');
$sender->subject('Тема письма');
$sender->addText('Привет, дружок! Прилетел новый заказ:');
$sender->addField('Имя', 'user_name');
$sender->addField('Телефон', 'user_phone');
$sender->addText('Обрабатывай пока горячий, Удачи!');
$sender->send();