<?php
/**
 * 邮件模型 - 数据对象模型
 * @author jason <yangjs17@yeah.net> 
 * @version TS3.0
 */
class MailModel {
	
	// 允许发送邮件的类型
	public static $allowed = array (
			'Register',
			'unAudit',
			'resetPass',
			'resetPassOk',
			'invateOpen',
			'invate',
			'atme',
			'comment',
			'reply' 
	);
	public $message;
	
	/**
	 * 初始化方法，加载phpmailer，初始化默认参数
	 * 
	 * @return void
	 */
	public function __construct() {
		require_once (VENDOR_PATH . '/phpmailer/class.phpmailer.php');
		require_once (VENDOR_PATH . '/phpmailer/class.pop3.php');
		require_once (VENDOR_PATH . '/phpmailer/class.smtp.php');
		
		$this->option = array (
				'email_sendtype' => C ( 'email_sendtype' ),
				'email_host' => C ( 'email_host' ),
				'email_port' => C ( 'email_port' ),
				'email_ssl' => C ( 'email_ssl' ),
				'email_account' => C ( 'email_account' ),
				'email_password' => C ( 'email_password' ),
				'email_sender_name' => C ( 'email_sender_name' ),
				'email_sender_email' => C ( 'email_sender_email' ),
				'email_reply_account' => C ( 'email_sender_email' ) 
		);
	}
	
	/**
	 * 测试发送邮件
	 * 
	 * @param array $data
	 *        	邮件相关内容数据
	 * @return boolean 是否发送成功
	 */
	public function test_email($sendto_email) {
		return $this->send_email ( $sendto_email, '测试邮件', '这是一封测试邮件' );
	}
	
	/**
	 * 发送邮件
	 * 
	 * @param string $sendto_email
	 *        	收件人的Email
	 * @param string $subject
	 *        	主题
	 * @param string $body
	 *        	正文
	 * @param array $senderInfo
	 *        	发件人信息 array('email_sender_name'=>'发件人姓名', 'email_account'=>'发件人Email地址')
	 * @return boolean 是否发送邮件成功
	 */
	public function send_email($sendto_email, $subject, $body, $senderInfo = '') {
		$mail = new PHPMailer ();
		if (empty ( $senderInfo )) {
			$sender_name = $this->option ['email_sender_name'];
			$sender_email = empty ( $this->option ['email_sender_email'] ) ? $this->option ['email_account'] : $this->option ['email_sender_email'];
		} else {
			$sender_name = $senderInfo ['email_sender_name'];
			$sender_email = $senderInfo ['email_sender_email'];
		}
		
		if ($this->option ['email_sendtype'] == 'smtp') {
			$mail->Mailer = "smtp";
			$mail->Host = $this->option ['email_host']; // sets GMAIL as the SMTP server
			$mail->Port = $this->option ['email_port']; // set the SMTP port
			if ($this->option ['email_ssl']) {
				$mail->SMTPSecure = "ssl"; // sets the prefix to the servier tls,ssl
			}
			if (! empty ( $this->option ['email_account'] ) && ! empty ( $this->option ['email_password'] )) {
				$mail->SMTPAuth = true; // turn on SMTP authentication
				$mail->Username = $this->option ['email_account']; // SMTP username
				$mail->Password = $this->option ['email_password']; // SMTP password
			}
		} elseif ($this->option ['email_sendtype'] == 'sendmail') {
			$mail->Mailer = "sendmail";
			$mail->Sendmail = '/usr/sbin/sendmail';
		} else {
			$mail->Mailer = "mail";
		}
		
		$mail->Sender = $this->option ['email_account']; // 真正的发件邮箱
		
		$mail->SetFrom ( $sender_email, $sender_name, 0 ); // 设置发件人信息
		
		$mail->CharSet = "UTF-8"; // 这里指定字符集！
		$mail->Encoding = "base64";
		
		if (is_array ( $sendto_email )) {
			foreach ( $sendto_email as $v ) {
				$mail->AddAddress ( $v );
			}
		} else {
			$mail->AddAddress ( $sendto_email );
		}
		
		// 以HTML方式发送
		$mail->IsHTML ( true ); // send as HTML
		$mail->Subject = $subject; // 邮件主题
		$mail->Body = $body; // 邮件内容
		$mail->AltBody = "text/html";
		$mail->SMTPDebug = false;
		
		$result = $mail->Send ();
		
		$this->setMessage ( $mail->ErrorInfo );
		
		return $result;
	}
	public function setMessage($message) {
		$this->message = $message;
	}
}