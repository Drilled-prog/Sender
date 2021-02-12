### Example: send.php

`redirect('/spasibo-za-zakaz.html')` перенаправление на страницу после отправки формы - это не обязательный параметр, если не указан будет возвращен json на счёт этого ниже

`spamFilter(2)` ограничение кол-ва отправок с 1 IP в день, для отключение проверки установить 0

`subject('Текст')` тема письма

`to('my-mail@gmail.com')` куда слать письмо

`addField('Ключ', 'post_key')` добавляет в сообщение строку с переносом, Ключ: Значение ↲ 
по желанию можно добавить 3-й параметр addField('Ключ', 'post_key', true) для проверки на заполнение, 
если поле будет пустым, письмо отправлено не будет. post_key под капотом $_POST['post_key']

`addText('Любой текст.')` добавляет в сообщение строку с переносом, Любой текст. ↲

`send()` отправить письмо

##### Eсли не задан redirect() получаем такой ответ
Удачно отправлено - `{"success": 1}`

Ошибка - `{"success": 0, "errorReason": null|string, "errorPayload": null|object }`

###### Ошибки
spamFilter - `{"success": 0, "errorReason": "ban", "errorPayload": {"unbanAfter": {"hours": 1, "minutes": 59, "seconds": 33} } }`

badRequest - `{"success": 0, "errorReason": "badRequest", "errorPayload": null }` (Срабатывает если запрос не с методом POST)

validation - `{"success": 0, "errorReason": "validation", "errorPayload": {"field": "user_name", "reason": "required"} }`
```

#### Example:
```php
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
```
