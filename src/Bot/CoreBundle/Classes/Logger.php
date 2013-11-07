<?php
namespace Bot\CoreBundle\Classes;


class Logger
{
    private $_logDir    = NULL;

    public function __construct($logDir)
    {
        $this->_logDir = $logDir;
    }

    public function write($lStr, $logName = 'general')
    {
        $now = time();
        $logName = $logName.'.'.date( 'Y-m-d', $now ).'.log';
        $str = '';
        if( is_array($lStr) ){
            foreach( $lStr as $k => $v ) $str .= '['.$k.'='.(is_array($v) ? serialize($v) : $v).']';
        } else {
            $str = $lStr;
        }
        $line = '['.date( 'Y-m-d H:i:s', $now ).']['.$now.'] '.$str;
        $line .= '[ip '.@$_SERVER['REMOTE_ADDR'].']';
        $line .= '[forwarded_for '.@$_SERVER['HTTP_X_FORWARDED_FOR'].']';
        $line .= '[host '.@$_SERVER['HTTP_HOST'].']';
        $line .= '[ruri '.@$_SERVER['REQUEST_URI'].']';
        $line .= '[ua '.@$_SERVER['HTTP_USER_AGENT'].']';
        $line .= '[p '.@$_SERVER['REMOTE_PORT'].']';
        $line .= '[called '.$this->_called_from().']';
        umask(0);
        $fp = fopen($this->_logDir.'/'. $logName, 'a+');
        fwrite( $fp, $line."\n" );
        fclose( $fp );
        return true;
    }

    private function _called_from()
    {
        $bTrace = debug_backtrace();
        if( count($bTrace) < 2 ) return '';
        return $bTrace[1]['file'].' (line '.$bTrace[1]['line'].')';
    }

}

?>