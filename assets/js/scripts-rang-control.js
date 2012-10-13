/**
 * Скрипты, используемые в плагине
 *
 * @author  Андрей Г. Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Г. Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 11.10.12 08:18 - Создание файла скриптов.<br>
 *
 * @package plugins/opinion
 */

/************************************ГОЛОСОВАНИЕ ЗА ПОЛЬЗОВАТЕЛЯ*******************************************************/
/** Переопределяем метод родного ls.vote */
var $fTmpOnVoteUser = ls.vote.onVoteUser;
ls.vote.onVoteUser = function(idTarget, objVote, value, type, result) {
    /** Вызываем родительский */
    $fTmpOnVoteUser(idTarget, objVote, value, type, result);

    /** Теперь свой собственный */
    ls.ajax('/opinion/refresh_rating', {iUserId: idTarget}, function(result) {
        /** Если получили хороший результат - отобразим его пользователю*/
        if (!result.bStateError) {
            var $oNewElements = $(result.sHtmlCode);
            $('.user_position_' + idTarget + ' .count')
                .attr('class', $oNewElements.find('.count').attr('class'))
                .html($oNewElements.find('.count').html());
        }
    }.bind(this));

};
