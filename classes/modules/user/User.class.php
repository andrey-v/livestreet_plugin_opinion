<?php
/**
 * Переопределенный объект. В нем реализуется перекрытие метода обновления сессии с целью дополнить его
 * функционалом плагина.
 *
 * @author  Андрей Г. Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Г. Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 11.10.12 09:40    - Создание основного класса плагина.<br>
 *
 * @package plugins/opinion
 */
/** @noinspection PhpUndefinedClassInspection */
class PluginOpinion_ModuleUser extends PluginOpinion_Inherit_ModuleUser {

    /**
     * Инициализация модуля
     */
    public function Init() {
        parent::Init();
        $this->oMapper = Engine::GetMapper(__CLASS__);
    }

    public function SetVotingComment($sTargetType, $iTargetId, $sVotingComment) {
        switch ($sTargetType) {
            case 'topic':
                if (Config::Get('plugin.opinion.opinion_for_topic') == TRUE)
                    if ($oTopic = $this->Topic_GetTopicById($iTargetId)) {
                        $iUserId = $oTopic->getUserId();
                        $this->oMapper->SetVotingComment($this->oUserCurrent->getId(), $iUserId, $iTargetId, $sTargetType, $sVotingComment);
                        return TRUE;
                    }
                break;
            case 'blog':
                if (Config::Get('plugin.opinion.opinion_for_blog') == TRUE)
                    if ($oBlog = $this->Blog_GetBlogById($iTargetId)) {
                        $iUserId = $oBlog->getOwnerId();
                        $this->oMapper->SetVotingComment($this->oUserCurrent->getId(), $iUserId, $iTargetId, $sTargetType, $sVotingComment);
                        return TRUE;
                    }
                break;
            case 'comment':
                if (Config::Get('plugin.opinion.opinion_for_comment') == TRUE)
                    if ($oComment = $this->Comment_GetCommentById($iTargetId)) {
                        $iUserId = $oComment->getUserId();
                        $this->oMapper->SetVotingComment($this->oUserCurrent->getId(), $iUserId, $iTargetId, $sTargetType, $sVotingComment);
                        return TRUE;
                    }
                break;
            case 'user':
                $this->oMapper->SetVotingComment($this->oUserCurrent->getId(), $iTargetId, $iTargetId, $sTargetType, $sVotingComment);
                return TRUE;
                break;
        }

        return FALSE;
    }

    public function getRatingState($user_id) {
        /** @var $result array Результат по рейтингу пользователя */
        $result = array(
            'delta_position' => 0,
            'new_position'   => 0,
            'delta_rating'   => 0,
            'new_rating'     => 0,
        );

        /** @var $currentRating array|null Текущий рейтинг пользователя */
        $currentRating = $this->oMapper->GetCurrentRating($user_id);

        /** Если НЕ произошла ошибка в запросе */
        if ($currentRating != NULL) {

            $lastRating = $currentRating;

            /** Проверяем есть ли пользователь в таблице рейтинга */
            if ($this->oMapper->isNewUserForRatingStat($user_id) == TRUE) {
                /** И если нет его, то добавляем запись о нем */
                $this->oMapper->SetRating($user_id, $currentRating['user_rating'], $currentRating['user_position']);
            } else {
                /** @var $lastRating array|null Последний рейтинг пользователя */
                $lastRating = $this->oMapper->GetLastRating($user_id);
            }
            /** Если НЕ произошла ошибка в запросе */
            if ($lastRating != NULL) {
                $result['delta_position'] = $lastRating['user_position'] - $currentRating['user_position'];
                $result['new_position'] = $currentRating['user_position'];
                $result['delta_rating'] = $lastRating['user_rating'] - $currentRating['user_rating'];
                $result['new_rating'] = $currentRating['user_rating'];

                if ($this->oUserCurrent && $user_id == $this->oUserCurrent->getId())
                    /** Установим новые значения */
                    $this->oMapper->SetRating($user_id, $currentRating['user_rating'], $currentRating['user_position']);

                /** Если все хорошо, вернем результат */
                return $result;
            } else return array('new_position' => 'hello2');
        } else return array('new_position' => 'hello');

        return $result;
    }

    /**
     * Получает список голосований за пользователя
     *
     * @param  $iUserId int     ID пользователя
     * @param  $iCurrPage int   Номер страницы
     * @param  $iPerPage int    Количество элементов на страницу
     * @return array
     */
    public function GetVotesByUserId($iUserId, $iCurrPage, $iPerPage) {
        /** Получаем массив голосований соответствующей страницы*/
        if ($this->oUserCurrent && $iUserId == $this->oUserCurrent->getId()) {
            return $this->oMapper->GetVotesByUserId($iUserId, $iCurrPage, $iPerPage);
        }

        return array();
    }

    /**
     * Удаление набора голосов пользователя из списка
     *
     * @param $iUserId
     * @param $aVoteIds
     * @return bool
     */
    public function DeleteVotesByIds($iUserId, $aVoteIds) {
        if ($this->oUserCurrent && $iUserId == $this->oUserCurrent->getId()) {
            $aResult = array();
            foreach ($aVoteIds as $key => $val)
                $aResult[] = $key;

            return $this->oMapper->DeleteVotesByIds($iUserId, $aResult);
        }

        return FALSE;
    }

    public function GetVoteForUser($oUser, $iValue) {
        /**
         * Начисляем силу и рейтинг юзеру, используя логарифмическое распределение
         */
        $skill = $oUser->getSkill();
        $iMinSize = 0.42;
        $iMaxSize = 3.2;
        $iSizeRange = $iMaxSize - $iMinSize;
        $iMinCount = log(0 + 1);
        $iMaxCount = log(500 + 1);
        $iCountRange = $iMaxCount - $iMinCount;
        if ($iCountRange == 0) {
            $iCountRange = 1;
        }
        if ($skill > 50 and $skill < 200) {
            $skill_new = $skill / 40;
        } elseif ($skill >= 200) {
            $skill_new = $skill / 2;
        } else {
            $skill_new = $skill / 70;
        }
        $iDelta = $iMinSize + (log($skill_new + 1) - $iMinCount) * ($iSizeRange / $iCountRange);
        /**
         * Определяем новый рейтинг
         */
        return $iValue * $iDelta;
    }

    public function GetVoteForBlog($oUser, $iValue) {
        /**
         * Устанавливаем рейтинг блога, используя логарифмическое распределение
         */
        $skill = $oUser->getSkill();
        $iMinSize = 1.13;
        $iMaxSize = 15;
        $iSizeRange = $iMaxSize - $iMinSize;
        $iMinCount = log(0 + 1);
        $iMaxCount = log(500 + 1);
        $iCountRange = $iMaxCount - $iMinCount;
        if ($iCountRange == 0) {
            $iCountRange = 1;
        }
        if ($skill > 50 and $skill < 200) {
            $skill_new = $skill / 20;
        } elseif ($skill >= 200) {
            $skill_new = $skill / 10;
        } else {
            $skill_new = $skill / 50;
        }
        $iDelta = $iMinSize + (log($skill_new + 1) - $iMinCount) * ($iSizeRange / $iCountRange);
        /**
         * Сохраняем рейтинг
         */
        return $iValue * $iDelta;
    }

    public function GetVoteForTopic($oUser, $iValue) {
        $skill = $oUser->getSkill();
        /**
         * Начисляем силу и рейтинг автору топика, используя логарифмическое распределение
         */
        $iMinSize = 0.1;
        $iMaxSize = 8;
        $iSizeRange = $iMaxSize - $iMinSize;
        $iMinCount = log(0 + 1);
        $iMaxCount = log(500 + 1);
        $iCountRange = $iMaxCount - $iMinCount;
        if ($iCountRange == 0) {
            $iCountRange = 1;
        }
        if ($skill > 50 and $skill < 200) {
            $skill_new = $skill / 70;
        } elseif ($skill >= 200) {
            $skill_new = $skill / 10;
        } else {
            $skill_new = $skill / 100;
        }
        $iDelta = $iMinSize + (log($skill_new + 1) - $iMinCount) * ($iSizeRange / $iCountRange);
        return $iValue * $iDelta / 2.73;
    }

    public function GetVoteForComment($oUser, $iValue) {
        /**
         * Начисляем силу автору коммента, используя логарифмическое распределение
         */
        $skill = $oUser->getSkill();
        $iMinSize = 0.004;
        $iMaxSize = 0.5;
        $iSizeRange = $iMaxSize - $iMinSize;
        $iMinCount = log(0 + 1);
        $iMaxCount = log(500 + 1);
        $iCountRange = $iMaxCount - $iMinCount;
        if ($iCountRange == 0) {
            $iCountRange = 1;
        }
        if ($skill > 50 and $skill < 200) {
            $skill_new = $skill / 70;
        } elseif ($skill >= 200) {
            $skill_new = $skill / 10;
        } else {
            $skill_new = $skill / 130;
        }
        $iDelta = $iMinSize + (log($skill_new + 1) - $iMinCount) * ($iSizeRange / $iCountRange);
        /**
         * Сохраняем силу
         */
        return $iValue * $iDelta;
    }
    
    /** Получаем список новых мнений */    
    public function GetNewOpinionId(){
        return $this->oMapper->GetNewOpinionId($this->oUserCurrent->getId());
    }
    
    /** Изменяем новые мнения в старые */    
    public function SetReadOpinion($aOpinionId){
        return $this->oMapper->SetReadOpinion($aOpinionId);
    }
}
