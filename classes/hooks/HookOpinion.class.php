<?php
/**
 * Класс хука, добовляющего информацию о рейтинге пользователя в его профиль.
 *
 * @author  Андрей Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 11.10.12 12:49    - Создание файла.<br>
 *
 * @package plugins/opinion
 */
class PluginOpinion_HookOpinion extends Hook {

    public function RegisterHook() {
        if (Config::Get('plugin.opinion.mode') == 'full' || Config::Get('plugin.opinion.mode') == 'rang-control')
            if (Router::GetAction() == "profile") {
                $this->AddHook('template_profile_top_begin', 'RatingInfo');
            }
        if (Config::Get('plugin.opinion.mode') == 'full' || Config::Get('plugin.opinion.mode') == 'vote-control')
            if ($oUserCurrent = $this->User_GetUserCurrent()) {
                $this->AddHook('template_body_end', 'VotingForm');
                $this->AddHook('template_profile_sidebar_menu_item_last', 'VotingInfo');
                $this->AddHook('template_profile_top_end', 'HeaderInfo');
            }
    }

    public function RatingInfo($oUserProfile) {
        /** @var $iUserId int Идентификатор пользователя */
        $iUserId = $oUserProfile['oUserProfile']->getId();

        /** @var $aRatingInfo Информация о рейтинге пользователя */
        $aRatingInfo = $this->User_getRatingState($iUserId);

        /** Обозначим переменные для шаблона */
        $this->Viewer_Assign('aRatingInfo', $aRatingInfo);
        $this->Viewer_Assign('iUserId', $iUserId);

        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'rating_state.tpl');
    }

    public function VotingForm() {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'voting_feedback_window.tpl');
    }

    public function VotingInfo($oUserProfile) {
        $oUserCurrent = $this->User_GetUserCurrent();
        if ($oUserCurrent && $oUserProfile['oUserProfile']->getId() == $oUserCurrent->getId()) {
            return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'voting_info_menu.tpl');
        }
    }

    public function HeaderInfo($oUserProfile) {
        $oUserCurrent = $this->User_GetUserCurrent();
        if ($oUserCurrent && $oUserProfile['oUserProfile']->getId() == $oUserCurrent->getId() && (Router::GetActionEventName() == 'EventVotingInfo')) {
            return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'header-info.tpl');
        }
    }
}