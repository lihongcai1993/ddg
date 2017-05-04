<?
namespace Home\Model;

use Think\Model;

/**
 * Class UserAddressModel
 * @package Home\Model
 */
class UsersModel extends Model
{
    protected $_validate = array(
        array('email', 'require', '邮箱必填'), //默认情况下用正则进行验证
        array('email', 'email', '邮箱格式不正确'), 
        array('email', '', '邮箱已注册',0,'unique',), 
        array('username', 'require', '用户名必填'), 
        array('pwd', 'require', '密码不能为空'), 
        array('pwd', '6', '密码不能小于六位',2,'length'), 
        array('repwd', 'require', '确认密码不能为空'),
        array('pwd', 'repwd', '两次密码不一致',2,'confirm'), 
        array('yzm', 'require', '验证码不能为空'), 
        array('yzm', 'passyzm', '验证码错误',2,'callback'), 
        array('xy', '1', '请勾选协议',1,'equal',3),
        array('username', 'require', '用户名必填',1,'',4),
        array('mobile', ' /^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0-9])\d{8}$/i', '手机号码格式有误',2,'regex',4),
        array('sex', 'require', '性别必选',1,'',4), 
   
    );
    protected function passyzm($yzm)
    {
    	$verify = new \Think\Verify();//实例化
		if ($verify->check($yzm)) 
		{
			return true;
		}
		else
		{
			return false;
		}
    }
   
}