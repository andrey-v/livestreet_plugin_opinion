$(function() {
    ls.vote.$fTmpOnVoteBlog = ($.type(ls.vote.onVoteBlog) == 'function') ? ls.vote.onVoteBlog : function() {
    };
    /** Функция срабатывает в случае успешного окончания голосования за блог */
    ls.vote.onVoteBlog = function(idTarget, objVote, value, type, result) {
        /** Вызываем родительский */
        ls.vote.$fTmpOnVoteBlog(idTarget, objVote, value, type, result);

        /** Теперь свой собственный */
        ls.vote.showOpinionForm(idTarget, type)
    };
});