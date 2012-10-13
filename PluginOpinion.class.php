<?php
/**
 * Основной класс плагина
 *
 * @author  Андрей Г. Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Г. Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 11.10.12 08:06    - Создание основного класса плагина.<br>
 *
 * @package plugins/opinion
 */

/**
 *  Запрещаем прямой доступ. В архитектуре LS, работа с плагинами реализуется через класс Plugin (они от него наследуются),
 * и поэтому здесь осуществляем логично сделать проверку на наличие родительского класса, если он доступен, то вероятно,
 * что загрузка ядра LS прошла успешно, иначе чего-то не того происходит и оно все равно правильно работать не будет. */
if (!class_exists('Plugin')) {
    die('You are bad hacker, try again, baby!');
}

/**
 * Сам класс плагина
 *
 * @see Plugin - там много public-методов, которые могут быть использованы или переопределены
 * @link http://docs.livestreetcms.com/api/1.0/Plugin - официальное описание этого класса
 *
 * @method Viewer_AppendScript - Добавляет файл скрипта в очередь подключаемых скриптов.
 * @method Viewer_AppendStyle -  Добавляет файл стилей в очередь на подключение.
 */
class PluginOpinion extends Plugin {

    /** @var array Переопределяемые объекты */
    protected $aInherits = array(
        'module' => array(
            'ModuleUser' => '_ModuleUser',
        ),
        'action' => array(
            'ActionProfile' => '_ActionProfile',
        ),
        'mapper' => array(
            'ModuleUser_MapperUser' => '_ModuleUser_MapperUser',
        ),
    );

    /** Инициализация плагина */
    public function Init() {
        /** Вызов родительского метода инициализации плагина */
        parent::Init();

        /** Подключение скрипта плагина */
        $this->Viewer_AppendScript(dirname(__FILE__) . "/assets/js/scripts-" . Config::Get('plugin.opinion.mode') . ".js");

        if (Config::Get('plugin.opinion.opinion_for_blog') == true)
            $this->Viewer_AppendScript(dirname(__FILE__) . "/assets/js/scripts-vote-for-blog.js");

        if (Config::Get('plugin.opinion.opinion_for_topic') == true)
            $this->Viewer_AppendScript(dirname(__FILE__) . "/assets/js/scripts-vote-for-topic.js");

        if (Config::Get('plugin.opinion.opinion_for_comment') == true)
            $this->Viewer_AppendScript(dirname(__FILE__) . "/assets/js/scripts-vote-for-comment.js");

        /** Подключение стилей плагина */
        $this->Viewer_AppendStyle(dirname(__FILE__) . "/assets/css/styles-" . Config::Get('plugin.opinion.mode') . ".css");
    }


    /** Активация плагина. При активации создаем таблицы в БД */
    public function Activate() {
        /** Вызов родительского метода активации */
        parent::Activate();
        /** Создаем таблицы в БД */
        $response = $this->ExportSQL(dirname(__FILE__) . '/sql.sql');
        /** Результат активации */
        return $response['result'];
    }

}