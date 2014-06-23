<?php

    /**
     * Class that acts as a manager for all user sessions
     * 
     * @author Joshua Kissoon
     * @since 20131210
     * @updated 20140623
     */
    class SessionsManager
    {

        /**
         * Invalidate all sessions for user sessions that have passed the session lifetime of the site 
         */
        public static function updateSessions()
        {
            $session_lifetime = Utility::variableGet("session_lifetime");
            $old_session_ts = time() - $session_lifetime;
            $old_session_dt = date("Y-m-d H:i:s", $old_session_ts);
            $sql = "UPDATE user_session SET status='0' WHERE create_ts < '$old_session_dt'";

            $db = Sweia::getInstance()->getDB();
            return $db->query($sql);
        }

    }
    