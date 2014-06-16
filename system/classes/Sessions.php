<?php

    /**
     * @description Class that acts as a manager for all user sessions
     * @author Joshua Kissoon
     * @date 20131210
     */
    class Sessions
    {

        /**
         * @desc Invalidate all sessions for this user which have passed the session lifetime of the site 
         */
        public static function updateSessions()
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $session_lifetime = Utility::variableGet("session_lifetime");
            $old_session_ts = time() - $session_lifetime;
            $old_session_dt = date("Y-m-d H:i:s", $old_session_ts);
            $sql = "UPDATE user_session SET status='0' WHERE create_ts < '$old_session_dt'";
            return $DB->query($sql);
        }

    }
    