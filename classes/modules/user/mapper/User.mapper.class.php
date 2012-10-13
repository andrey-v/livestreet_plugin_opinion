<?php
/**
 * Переопределенный маппер модуля. В нем добавлены методы работы с базой данных для новых таблиц.
 *
 * @author  Андрей Г. Воронов <andreyv@gladcode.ru>
 * @copyrights  Copyright &copy; 2012, Андрей Г. Воронов<br>
 *              Является частью плагина Opinion<br>
 * @version 1.0 от 11.10.12 10:15    - Создание основного класса плагина.<br>
 *
 * @package plugins/opinion
 */
class PluginOpinion_ModuleUser_MapperUser extends PluginOpinion_Inherit_ModuleUser_MapperUser {

    /**
     * Возвращает список голосований пользователя
     *
     * @param  $iUserId int     ID пользователя
     * @param  $iCurrPage int   Номер страницы
     * @param  $iPerPage int    Количество элементов на страницу
     * @return array
     */
    public function GetVotesByUserId($iUserId, $iCurrPage, $iPerPage) {
        //$sql = "SELECT * FROM " . Config::Get('db.table.opinion') . " WHERE `user_id` = ? limit ?d, ?d";
        $sql = "SELECT
                  o.*,
                  v.vote_value
                FROM
                    " . Config::Get('db.table.opinion') . " as o,
                    " . Config::Get('db.table.vote') . " as v
                WHERE
                    (o.target_id = v.target_id and o.target_type = v.target_type and o.voter_id = v.user_voter_id) and o.user_id = ?
                LIMIT
                     ?d, ?d";
        $iCount = 0;
        if ($aVotes = $this->oDb->selectPage($iCount, $sql, $iUserId, ($iCurrPage - 1) * $iPerPage, $iPerPage)) {
            return array(
                'aVotes' => $aVotes,
                'iCount' => $iCount,
            );
        }
        return array();
    }

    /**
     * Устанавливает запись о текущем рейтинге пользователя
     *
     * @param $user_id int Идентификатор пользователя
     * @param $user_rating float Текущий рейтинг пользователя
     * @param $user_position int Позиция пользователя в общем списке
     * @return null|int
     */
    public function SetRating($user_id, $user_rating, $user_position) {
        $sql = "REPLACE INTO " . Config::Get('db.table.opinion_rating') . "
			SET
				user_id = ? ,
				user_rating = ? ,
				user_position = ?
		";
        return $this->oDb->query($sql, $user_id, $user_rating, $user_position);
    }

    public function isNewUserForRatingStat($user_id) {
        $sql = "SELECT count(*) as c FROM " . Config::Get('db.table.opinion_rating') . " WHERE `user_id` = ?";
        $aRow = $this->oDb->selectRow($sql, $user_id);
        return ($aRow['c'] == 1) ? FALSE : TRUE;
    }

    /**
     * Получает массив с последними данными рейтинга пользователя
     *
     * @param $user_id int Идентификатор пользователя
     * @return array|null
     */
    public function GetLastRating($user_id) {
        /** Получаем последний рейтинг пользователя */
        $sql = "SELECT
					s.user_rating,
					s.user_position
				FROM
					" . Config::Get('db.table.opinion_rating') . " as s
				WHERE
					s.user_id = ?
				";
        if ($aRow = $this->oDb->selectRow($sql, $user_id)) {
            return array(
                'user_rating' => $aRow['user_rating'],
                'user_position' => $aRow['user_position']
            );
        }
        return null;
    }

    /**
     * Получает массив с текущими данными рейтинга пользователя
     *
     * @param $user_id int Идентификатор пользователя
     * @return array|null
     */
    public function GetCurrentRating($user_id) {
        /** Получаем текущий рейтинг пользователя */
        $sql = "SELECT
                  u1.user_rating as user_rating,
                  (select count(*) from " . Config::Get('db.table.user') . " u3) as user_count,
                  (SELECT COUNT(*) FROM " . Config::Get('db.table.user') . " u2 WHERE u1.user_rating > u2.user_rating) AS user_position
                FROM
                  " . Config::Get('db.table.user') . " u1
                WHERE
                  user_id = ?
                ORDER BY
                    user_rating DESC";
        if ($aRow = $this->oDb->selectRow($sql, $user_id)) {
            return array(
                'user_rating' => $aRow['user_rating'],
                'user_position' => $aRow['user_count'] - $aRow['user_position']
            );
        }
        return null;
    }

    /**
     * Записывает сообщение о голосовании
     *
     * @param $voter_id int Идентификатор пользователя КОТОРЫЙ голосует
     * @param $user_id int Идентификатор пользователя ЗА которого голосуют
     * @param $target_id int Идентификатор ресурса, за который голосуют
     * @param $target_type [topic|user|blog|comment] Тип ресурса
     * @param $comment string Текст сообщения
     * @return mixed
     */
    public function SetVotingComment($voter_id, $user_id, $target_id, $target_type, $comment) {
        $sql = "INSERT INTO `" . Config::Get('db.table.opinion') . "` (
                `voter_id`,
			    `user_id`,
				`target_id`,
				`target_type`,
				`comment`) VALUES (?, ?,   ?,  ?,  ?)";
        return $this->oDb->query($sql, $voter_id, $user_id, $target_id, $target_type, $comment);
    }

    /**
     * Удаляет сообщения по переданным идентификаторам и возвращает успешность выполнения операции
     *
     * @param $iUserId int Идентификатор пользователя //deprecated
     * @param $aVoteIds array Массив идентификаторов голосов для удаления
     * @return bool
     */
    public function DeleteVotesByIds($iUserId, $aVoteIds) {
        print_r($aVoteIds);
        $sql = "DELETE FROM " . Config::Get('db.table.opinion') . " WHERE user_id=? and id IN(?a)";
        if ($this->oDb->query($sql, $iUserId, $aVoteIds)) {
            return true;
        }
        return false;
    }

}