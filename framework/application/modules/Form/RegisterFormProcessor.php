<?php /* Requires PHP5+ */

# Make sure the script is not accessed directly.
if(!defined('BASE_PATH')) exit('No direct script access allowed');


# Get the FormValidator Class.
require_once Utility::locateFile(MODULES.'Form'.DS.'FormValidator.php');

# Get the FormProcessor Class.
require_once Utility::locateFile(MODULES.'Form'.DS.'FormProcessor.php');

# Get the RegisterFormPopulator Class.
require_once Utility::locateFile(MODULES.'Form'.DS.'RegisterFormPopulator.php');


/**
 * RegisterFormProcessor
 *
 * The PostFormProcessor Class is used to create and process post forms.
 *
 */
class RegisterFormProcessor extends FormProcessor
{
	/*** public methods ***/

	/**
	 * processRegistration
	 *
	 * Processes a submitted Post.
	 *
	 * @param	$data					An array of values tp populate the form with.
	 * @access	public
	 * @return	string
	 */
	public function processRegistration($data=array())
	{
		try
		{
			# Bring the alert-title variable into scope.
			global $alert_title;
			# Bring the login object into scope.
			global $login;
			# Set the Database instance to a variable.
			$db=DB::get_instance();
			# Set the Document instance to a variable.
			$doc=Document::getInstance();
			# Bring the content instance into scope.
			$main_content=Content::getInstance();
			# Set the Validator instance to a variable.
			$validator=Validator::getInstance();
			# Get the PostFormPopulator Class.
			require_once Utility::locateFile(MODULES.'Form'.DS.'RegisterFormPopulator.php');

			# Instantiate a new instance of RegisterFormPopulator.
			$populator=new RegisterFormPopulator();
			# Populate the form and set the SubContent data members for this post.
			$populator->populateRegisterForm($data);
			# Set the Populator object to the data member.
			$this->setPopulator($populator);

			# Set the email to a variable.
			$email=$login->getEmail();
			# Set the email_conf to a variable.
			$email_conf=$login->getEmail();
			# Set the password to a variable.
			$password=$login->getPassword();
			# Set the password_conf to a variable.
			$password_conf=$login->getPasswordConf();
			# Set the username to a variable.
			$username=$login->getUsername();

			# Check if the form has been submitted and the submit button was the "Post" button.
			if(array_key_exists('_submit_check', $_POST) && (isset($_POST['register']) && ($_POST['register']==='Register')))
			{
				# Create a session that holds all the POST data (it will be destroyed if it is not needed.)
				$this->setSession();

				# If the form has been submitted, we don't need some previous data. Unset the post login session data.
				unset($_SESSION['_post_login']);

				# Get the FormValidator Class.
				require_once Utility::locateFile(MODULES.'Form'.DS.'FormValidator.php');
				# Instantiate a FormValidator object
				$fv=new FormValidator();

				$empty_username=$fv->validateEmpty('username','Please enter a username that is at least 5 characters long.', 5, 64);
				$empty_email=$fv->validateEmpty('email','Please enter your email address.', 4, 100);
				$empty_email_conf=$fv->validateEmpty('email_conf','Please confirm your email address.', 4, 100);
				$empty_password=$fv->validateEmpty('password','Please enter a password that is at least 6 characters and contains at least one number as well as letters. It is good practice to use a mix of CAPITAL and lowercase letters with at least 1 number and/or special characters (ie. !,@,#,$,%,^,&, etc.). For assistance creating a password you may go to: <a href="http://strongpasswordgenerator.com/" target="_blank">StrongPasswordGenerator.com</a>', 6, 64);
				$empty_password_conf=$fv->validateEmpty('password_conf','Please confirm your password.', 6, 64);

				# If username is not empty.
				if($empty_username===FALSE)
				{
					# Check if the username is unique.
					$unique=$login->checkUnique('username', $username);
					# Username is not unique.
					if($unique===FALSE)
					{
						# Set an error.
						$fv->setErrors('The username '.$username.' is already in use, please choose another.');
					}
				}

				# If email is not empty.
				if($empty_email===FALSE)
				{
					# Does the email match the default?
					if($email=='youremail@somewhere.com')
					{
						# Set an error.
						$fv->setErrors('Please enter your email address.');
						# Unset the email data.
						unset($_POST['email']);
					}
					else
					{
						$real=$fv->validateEmail('email', 'Please enter a valid email address.', TRUE);
						if($real===TRUE)
						{
							$unique=$login->checkUnique('email', $email);
							if($unique===FALSE)
							{
								$fv->setErrors('An account using the email address "'.$email.'" already exists in the system, please choose another. Or you may use the "<a href="'.REDIRECT_TO_LOGIN.'login/LostPassword/" title="Lost passowrd">lost password</a>" feature to recover your account information.');
							}
							else
							{
								if($empty_email_conf===FALSE)
								{
									if($email!=$email_conf)
									{
										$fv->setErrors('The email addresses you entered did not match.');
									}
								}
							}
						}
						else
						{
							unset($_POST['email']);
						}
					}
				}

				if($empty_password===FALSE)
				{
					$alphanumeric=$fv->validateAlphanum('password', 'Your password must be made up of letters and at least 1 number.');
					if($alphanumeric===TRUE)
					{
						if($empty_email_conf===FALSE)
						{
							if($password!=$password_conf)
							{
								$fv->setErrors('The passwords you entered did not match.');
							}
						}
					}
				}
				if(CAPTCHA_PUBLICKEY!='' && CAPTCHA_PRIVATEKEY!='')
				{
					if(isset($_POST["recaptcha_challenge_field"]))
					{
						$valid_recaptcha=$fv->reCaptchaCheckAnswer(CAPTCHA_PRIVATEKEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"], '', 'You must correctly enter the squiggly characters in the box to complete your registration. Make sure they are correct. There is a "help" button in the little red box.');
						if($valid_recaptcha===FALSE)
						{
							$login->setReCaptchaError($fv->getReCaptchaError());
						}
					}
					else
					{
						$fv->setErrors('There is a reCaptcha on this page that needs to be filled out. It is generated by a different website. Please give it a moment to appear before you click the "register" button.');
					}
				}

				# Check for errors to display so that the script won't go further.
				if($fv->checkErrors()===TRUE)
				{
					# Create a variable to the error heading.
					$alert_title='Resubmit the form after correcting the following errors:';
					# Set the FormValidator class errors to a variable.
					$error=$fv->displayErrors();
					# Set the error message to the Document object data member so that it me be displayed on the page.
					$doc->setError($error);
				}
				# The post is considered "unique" and may be added to the database.
				else
				{
					# Insert a new account into the database.
					$login->createAccount();

					try
					{
						$row=$db->get_row('SELECT `ID`, `random` FROM '.DBPREFIX.'users WHERE `username` = '.$db->quote($db->escape($username)).' LIMIT 1');
						if($row!==NULL)
						{
							# Make sure the post login info is sent to the login page. Set it in a session.
							$_SESSION['_post_login']=$login->getPostLogin();
							# Set email subject to a variable.
							$subject="Activation email from ".DOMAIN_NAME;
							$to_address=trim($email);
							# Set email body to a variable.
							$message=$username.','."<br />\n<br />\n".
							'This email has been sent from <a href="'.APPLICATION_URL.'">'.DOMAIN_NAME.'</a>.'."<br />\n<br />\n".
							'You have received this email because this email address was used during registration for our site.'."<br />\n".
							'If you did not register at '.DOMAIN_NAME.', please disregard this email. You do not need to unsubscribe or take any further action.'."<br />\n<br />\n".
							'------------------------------------------------'."<br />\n".
							' Activation Instructions'."<br />\n".
							'------------------------------------------------'."<br />\n<br />\n".
							'Thank you for registering.'."<br />\n".
							'We require that you "validate" your registration to ensure that the email address you entered was correct. This protects against unwanted spam and malicious abuse.'."<br />\n<br />\n".
							'To activate your account, simply click on the following link:'."<br />\n<br />\n".
							'<a href="'.REDIRECT_TO_LOGIN.'login/confirm.php?ID='.$row->ID.'&key='.$row->random.'">'.REDIRECT_TO_LOGIN.'login/confirm.php?ID='.$row->ID.'&key='.$row->random.'</a>'."<br />\n<br />\n".
							'(You may need to copy and paste the link into your web browser).'."<br />\n<br />\n".
							'Once you confirm your status, you may login at <a href="'.REDIRECT_TO_LOGIN.'">'.REDIRECT_TO_LOGIN.'</a>.';
							try
							{
								$doc->sendEmail($subject, $to_address, $message);
								$_SESSION['message']='Account created. Please check your email for details on how to activate it. The email may not arrive instantly in your email inbox. Please give it some time. Please make sure to check your "junk mail" folder in case the email gets routed there. After your account is activated, you may sign in to the '.DOMAIN_NAME.'. Once signed in, you will be able to access special features and download content.';
							}
							catch(Exception $e)
							{
								$_SESSION['message']='I managed to create your profile but failed to send the validation email. Please contact the admin at: <a href="mailto:'.ADMIN_EMAIL.'">'.ADMIN_EMAIL.'</a>';
								$doc->redirect(REDIRECT_TO_LOGIN);
							}
							$doc->redirect(REDIRECT_TO_LOGIN);
						}
					}
					catch(ezDB_Error $ez)
					{
						throw new Exception('There was an error retrieving the new user info for "'.$username.'" from the Database: '.$ez->error.', code: '.$ez->errno.'<br />Last query: '.$ez->last_query, E_RECOVERABLE_ERROR);
					}
				}
			}
			return NULL;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- processRegistration

	/*** End public methods ***/



	/*** private methods ***/

	/**
	 * processRegisterBack
	 *
	 * Processes a submitted form indicating that the User should be sent back to the form that sent them to fetch a file.
	 *
	 * @access	private
	 */
	private function processRegisterBack()
	{
		try
		{
			# Create an array of possible indexes. These are forms that can send the user to get an institution.
			$indexes=array(
				'register'
			);
			# Set the resource value.
			$resource='register';
			$this->processBack($resource, $indexes);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- processRegisterBack

	/**
	 * setSession
	 *
	 * Creates a session that holds all the POST data (it will be destroyed if it is not needed.)
	 *
	 * @access	private
	 */
	private function setSession()
	{
		global $login;

		try
		{
			# Get the Populator object and set it to a local variable.
			$populator=$this->getPopulator();

			# Set the form URL's to a variable.
			$form_url=$populator->getFormURL();
			# Set the current URL to a variable.
			$current_url=FormPopulator::getCurrentURL();
			# Check if the current URL is already in the form_url array. If not, add the current URL to the form_url array.
			if(!in_array($current_url, $form_url)) $form_url[]=$current_url;

			# Create a session that holds all the POST data (it will be destroyed if it is not needed.)
			$_SESSION['form']['register']=
				array(
					'FormURL'=>$form_url,
					'Username'=>$login->getUsername(),
					'Email'=>$login->getEmail(),
					'EmailConf'=>$login->getEmailConf(),
					'Password'=>$login->getPassword(),
				);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- setSession

	/*** End private methods ***/

} # End RegisterFormProcessor class.