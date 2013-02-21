<?
/**
 * Экшен, отображающий страницу со списком голосований
 *
 * @author  Андрей Г. Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Г. Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 12.10.12 14:31    - Создание файла экшена.<br>
 *
 * @package plugins/opinion
 */
class PluginOpinion_ActionProfile extends PluginOpinion_Inherit_ActionProfile {

    protected function RegisterEvent() {
        parent::RegisterEvent();
        if (Config::Get('plugin.opinion.mode') == 'full' || Config::Get('plugin.opinion.mode') == 'vote-control') {
            $this->AddEventPreg('/^.+$/i', '/^votinginfo$/i', '/^$/i', 'EventVotingInfo');
            $this->AddEventPreg('/^.+$/i', '/^votinginfo$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventVotingInfo');
        }
    }

    public function EventVotingInfo() {
        if (!$this->CheckUserProfile()) {
            return parent::EventNotFound();
        }

        $bDelete = getRequest('form_votes_list_submit_del');
        if ($bDelete) {
            $aVoteIds = getRequest('vote_select');
            $this->User_DeleteVotesByIds($this->oUserProfile->getId(), $aVoteIds);
        }

        /** Передан ли номер страницы */
        $iPage = $this->GetParamEventMatch(1, 2) ? $this->GetParamEventMatch(1, 2) : 1;

        /** Получаем список голосований */
        $aResult = $this->User_GetVotesByUserId($this->oUserProfile->getId(), $iPage, Config::Get('plugin.opinion.votes_per_page'));

        if (!empty($aResult)) {
            /** @var $aVotes Массив голосований */
            $aVotes = $aResult['aVotes'];
            foreach ($aVotes as $key => $aVote) {
                switch ($aVote['target_type']) {
                    case 'comment':
                        $aVotes[$key]['url'] = '/comments/' . $aVote['target_id'];
                        break;
                    case 'topic':
                        $oTopic = $this->Topic_GetTopicById($aVote['target_id']);
                        $aVotes[$key]['url'] = $oTopic->getUrl();
                        break;
                    case 'blog':
                        $oBlog = $this->Blog_GetBlogById($aVote['target_id']);
                        $aVotes[$key]['url'] = '/blog/' . $oBlog->getUrl();
                        break;
                    case 'user':
                        $aVotes[$key]['url'] = $this->oUserProfile->getUserWebPath();
                        break;
                }
            }

            /** Формируем постраничность */
            $aPaging = $this->Viewer_MakePaging($aResult['iCount'], $iPage, Config::Get('plugin.opinion.votes_per_page'), Config::Get('pagination.pages.count'), $this->oUserProfile->getUserWebPath() . 'votinginfo');
        } else {
            $aVotes = $aPaging = array();
        }
        /** Загружаем переменные в шаблон */
        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('aVotes', $aVotes);
        
        /** Получаем список новых мнений */
        $aNewOpinion = $this->PluginOpinion_User_GetNewOpinionId();
        if ($aNewOpinion){
            $this->Viewer_Assign('aNewOpinion', $aNewOpinion);
        	$this->PluginOpinion_User_SetReadOpinion($aNewOpinion);
        }
        
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.opinion.about_user') . ' ' . $this->oUserProfile->getLogin());

        /** Устанавливаем шаблон вывода */
        $this->SetTemplateAction('voting_info');

    }
}
