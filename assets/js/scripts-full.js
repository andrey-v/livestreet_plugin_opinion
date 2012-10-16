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

    ls.vote.showOpinionForm(idTarget, type)
};

/************************************ГОЛОСОВАНИЕ ЗА ТОПИК**************************************************************/
ls.user.voting_feedback = function(form) {
    ls.ajaxSubmit('/opinion/feedback', form, function(result) {
        if (result.sMsg) {
            ls.msg.notice(null, result.sMsg);
            if (result.bClose)
                $('#window_voting_feedback_form').jqmHide();
        }
    }.bind(ls.user));
};

ls.vote.showOpinionForm = function(idTarget, type) {
    $('#window_voting_feedback_form')
        .find('textarea').val('')
        .end()
        .find('#voting-id').attr('value', idTarget)
        .end()
        .find('#voting-type').attr('value', type)
        .end()
        .jqm()
        .jqmShow();
}

/**
 * Удаление списка сообщений
 */
ls.vote.removeVotes = function () {
    if ($('.form_votes_checkbox:checked').length == 0) {
        return false;
    }
    $('#form_votes_list_submit_del').val(1);
    $('#form_votes_list').submit();
    return false;
};