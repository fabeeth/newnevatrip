База данных: newnevatrip
Подключение изменять в app-config.php
Функционал: 
index.php:
1) Просмотр списка заказов;
2) Просмотр штрихкодов по нажатию на кнопку ( а так же скрыть их );
3) Удаление заказа;
4) Оформление нового заказа:
  а) Выбор фильма,
  б) Выбор даты ,
  в) Выбор времени,
  г) Выбор типов билетов,
После оформления заказа идет запрос к АПИ, которая выдает рандомный ответ, присутствует 5 попыток запроса к апи, что бы получить положительный ответ.
5) Кнопка фильмы ( переход на films.php ).
films.php:
1) Просмотр информации о имеющихся фильмах;
2) Возможность удаление фильма ( а так же удаляются все связаные с ним данные );
3) Возможность изменения данных о фильме;
4) Добавление нового фильма:
  а) Ввод названия фильма,
  б) Описание фильма,
  в) Стоимость фильма,
  г) Дата и время фильма ( есть возможность добавление несколько дат и времени ).
5) Кнопка назад ( переход на index.php ).
Есть возможность просмотра билетов, присутствует кнопка "Билеты" на index.php:
tickets.php:
  а) Просмотр имеющихся типов билетов,
  б) Возможность удалять тип билета ( а так же удаляются все связаные с ним данные ),
  в) Возможность изменения типа билета ( после изменения, связанные с ним данные остаются, тк уже оформлен заказ ),
  г) Возможность просмотра билетов с индивидуальными штрихкодами для каждого билета,
  д) Возможность удаления билета ( с последующими изменениями в остальных таблицах ).