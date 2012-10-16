<?php
/**
 * Конфигурация плагина
 *
 * @author  Андрей Г. Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Г. Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 11.10.12 14:00    - Создание файла конфигурации.<br>
 *
 * @package plugins/opinion
 */

Config::Set('db.table.opinion_rating', '___db.table.prefix___opinion_rating');
Config::Set('db.table.opinion', '___db.table.prefix___opinion');
Config::Set('router.page.opinion', 'PluginOpinion_ActionOpinion');

$config = array(

    /** Количество сообщений на странице */
    'votes_per_page' => 6,

    /** Режим:
     *      - full: Полный режим, работает и распределение мест и учет голосов
     *      - rang-control: Работает только система контроля ранга
     *      - vote-control: Работает только система учета голосов*/
    'mode' => 'full',

    /** ГОЛОСОВАНИЕ ЗА ПОЛЬЗОВАТЕЛЯ НЕ ОТКЛЮЧАЕТСЯ */
    'opinion_for_user' => true,

    /** Отображать ли диалоговое окно мнения при голосовании за блог */
    'opinion_for_blog' => true,

    /** Отображать ли диалоговое окно мнения при голосовании за топик */
    'opinion_for_topic' => true,

    /** Отображать ли диалоговое окно мнения при голосовании за комментарий */
    'opinion_for_comment' => true,
);

return $config;