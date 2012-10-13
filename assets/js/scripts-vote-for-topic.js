$(function() {
    ls.vote.$fTmpOnVoteTopic = ($.type(ls.vote.onVoteTopic) == 'function') ? ls.vote.onVoteTopic : function() {
    };

    /** Функция срабатывает в случае успешного окончания голосования за топик */
    ls.vote.onVoteTopic = function(idTarget, objVote, value, type, result) {
        /** Вызываем родительский */
        ls.vote.$fTmpOnVoteTopic(idTarget, objVote, value, type, result);

        /** Теперь свой собственный */
        ls.vote.showOpinionForm(idTarget, type)
    };
});