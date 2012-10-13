$(function() {
    ls.vote.$fTmpOnVoteComment = ($.type(ls.vote.onVoteComment) == 'function') ? ls.vote.onVoteComment : function() {
    };
    /** Функция срабатывает в случае успешного окончания голосования за блог */
    ls.vote.onVoteComment = function(idTarget, objVote, value, type, result) {
        /** Вызываем родительский */
        ls.vote.$fTmpOnVoteComment(idTarget, objVote, value, type, result);

        /** Теперь свой собственный */
        ls.vote.showOpinionForm(idTarget, type)
    };
});