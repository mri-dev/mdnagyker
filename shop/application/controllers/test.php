<?
use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Template;

class test extends Controller
{
		function __construct(){
			parent::__construct();


			if ( isset($_GET['mailtest']))
			{

				$this->settings = $this->view->settings;
				$mail = new Mailer(
					$this->settings['page_title'],
					SMTP_USER,
					"smtp"
				);
				$mail->setReplyTo( $this->settings['page_title'], $this->settings['email_noreply_address'] );
				$mail->add( 'molnar.istvan@web-pro.hu' );

				$arg = array(
					'settings' 		=> $this->settings,
					'user_nev' 		=> $_GET['who'],
					'user_email' 	=> $_GET['send'],
					'user_jelszo' 	=> 'XXXXXX'
				);

				$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get($_GET['template'], $arg);

				$mail->setSubject( 'Minta email' );
				$msg = (new Template( VIEW . 'templates/mail/' ))->get( $_GET['mailtest'], $arg );
				$mail->setMsg( $msg );

				if (isset($_GET['send'])) {
					$re = $mail->sendMail();

					print_r($re);
				}
			}


		}

		function mailtemplate()
		{
			$arg = array(
				'settings' => $this->view->settings
			);
			$template = (new Template( VIEW . 'templates/mail/' ))->get( 'admin_requesttermprice', $arg );

			echo $template;
		}

		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>
