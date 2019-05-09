<?
class User{
    const ITTETTUNK_USER_ROOT = 'user';
    final static public function getIttettunkUrl($array){
        $ret = '';
        $uid = false;
            if($array[user_id]){
                $uid = $array[user_id];
            }else return $ret;
            $ret = ITTETTUNK_DOMAIN.self::ITTETTUNK_USER_ROOT.'/'.$uid;
        return $ret;
    }
}
?>