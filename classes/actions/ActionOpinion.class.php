<?php
/**
 * Основной экшен плагина
 *
 * @author  Андрей Г. Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Г. Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 11.10.12 16:55    - Создание основного класса плагина.<br>
 *
 * @package plugins/opinion
 */
class PluginOpinion_ActionOpinion extends ActionPlugin {

    /** Регистрация экшена */
    public function Init() {
    }

    /** Регистрируем евенты */
    protected function RegisterEvent() {
        if (Config::Get('plugin.opinion.mode') == 'full' || Config::Get('plugin.opinion.mode') == 'rang-control')
            $this->AddEvent('refresh_rating', 'EventRefreshRating');

        if (Config::Get('plugin.opinion.mode') == 'full' || Config::Get('plugin.opinion.mode') == 'vote-control')
            $this->AddEvent('feedback', 'EventFeedBack');
    }

    protected function EventRefreshRating() {
        /** Обрабатываем как ajax запрос (json) */
        $this->Viewer_SetResponseAjax('json');

        /** Получим id пользователя */
        $iUserId = getRequest('iUserId');

        /** @var $aRatingInfo Информация о рейтинге пользователя */
        $aRatingInfo = $this->User_getRatingState($iUserId);

        $this->Viewer_Assign('aRatingInfo', $aRatingInfo);
        $this->Viewer_Assign('iUserId', $iUserId);

        $sHtmlCode = $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'rating_state.tpl');

        /** Устанавливаем ответ */
        $this->Viewer_AssignAjax('sHtmlCode', $sHtmlCode);

    }

    protected function EventFeedBack() {
        if ($oUserCurrent = $this->User_GetUserCurrent()) {
            /** Обрабатываем как ajax запрос (json) */
            $this->Viewer_SetResponseAjax('json');
            $this->Viewer_AssignAjax('bClose', false);

            /** Получим текст сообщения */
            $sVotingComment = getRequest('voting-feedback-text');
            if (trim($sVotingComment) == '') {
                $this->Message_AddNoticeSingle(
                    $this->Lang_Get('plugin.opinion.empty_text'),
                    $this->Lang_Get('attention')
                );
            } else {
                /** Запишем в БД данные о голосовании */
                $iTopicId = getRequest('voting-id');
                $sVotingType = getRequest('voting-type');
                if ((bool)$iTopicId) {
                    $this->Viewer_AssignAjax('bClose', true);
                    $bResult = $this->User_SetVotingComment($sVotingType, $iTopicId, $sVotingComment);
                    if (!$bResult)
                        $this->Message_AddNoticeSingle(
                            $this->Lang_Get('plugin.opinion.error_write_comment'),
                            $this->Lang_Get('error')
                        );
                    else
                        $this->Message_AddNoticeSingle($this->Lang_Get('plugin.opinion.good_write_comment'));
                } else
                    $this->Message_AddNoticeSingle(
                        $this->Lang_Get('plugin.opinion.empty_topic'),
                        $this->Lang_Get('attention')
                    );
            }
        }
    }
}