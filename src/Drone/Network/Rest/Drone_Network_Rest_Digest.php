<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

/**
 * Digest class
 *
 * Class for Digest access authetication
 */
class Drone_Network_Rest_Digest
{
    /**
     * Requests client authentication
     *
     * @return null
     */
    public function request()
    {
        if (empty($_SERVER['PHP_AUTH_DIGEST']))
        {
            $status = $this->http::HTTP_UNAUTHORIZED;

            $this->http->writeStatus($status);
            header('WWW-Authenticate: Digest realm="'.$this->realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($this->realm).'"');
            die('Error ' . $status .' (' . $this->http->getStatusText($status) . ')!!');
        }
    }

    /**
     * Checks credentials
     *
     * @return boolean
     */
    public function authenticate()
    {
        if (!($data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($this->whiteList[$data['username']]))
        {
            $this->http->writeStatus($this->http::HTTP_UNAUTHORIZED);
            return false;
        }

        $A1 = md5($data['username'] . ':' . $this->realm . ':' . $this->whiteList[$data['username']]);
        $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
        $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

        if ($data['response'] != $valid_response)
        {
            $this->http->writeStatus($this->http::HTTP_UNAUTHORIZED);
            return false;
        }

        $this->username = $data['username'];

        return true;
    }

    private function http_digest_parse($txt)
    {
        // protect against missing data
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $keys = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

        foreach ($matches as $m)
        {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }

    /**
     * Shows the server response
     *
     * @return null
     */
    public function response()
    {
        $status = http_response_code();
        echo 'Error ' . $status .' (' . $this->http->getStatusText($status) . ')!!';
    }
}