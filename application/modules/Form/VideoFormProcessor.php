<?php /* Requires PHP5+ */

# Make sure the script is not accessed directly.
if(!defined('BASE_PATH')) exit('No direct script access allowed');


# Get the FormValidator Class.
require_once MODULES.'Form'.DS.'FormValidator.php';

# Get the FormProcessor Class.
require_once MODULES.'Form'.DS.'FormProcessor.php';


/**
 * VideoFormProcessor
 *
 * The VideoFormProcessor Class is used to create and process video forms.
 *
 */
class VideoFormProcessor extends FormProcessor
{
	/*** public methods ***/

	/**
	 * processVideo
	 *
	 * Processes a submitted video for upload.
	 *
	 * @param	$data					An array of values to populate the form with.
	 * @param	$max_size				The maximum allowed size of uploaded videos.
	 * @access	public
	 * @return	string
	 */
	public function processVideo($data, $max_size=314572800)
	{
		try
		{
			# Bring the alert-title variable into scope.
			global $alert_title;
			# Bring the content object into scope.
			global $main_content;
			# Set the Database instance to a variable.
			$db=DB::get_instance();
			# Set the Document instance to a variable.
			$doc=Document::getInstance();
			# Set the Validator instance to a variable.
			$validator=Validator::getInstance();
			# Get the VideoFormPopulator Class.
			require_once MODULES.'Form'.DS.'VideoFormPopulator.php';
			# Get CommandLine class.
			require_once MODULES.'CommandLine'.DS.'CommandLine.php';

			# Remove any un-needed CMS session data.
			# This needs to happen before populateVideoForm is called but AFTER the Populator has been included so that the getCurrentURL method will be available.
			$this->loseSessionData('video');

			# Reset the form if the "reset" button was submitted.
			$this->processReset('video');

			# Instantiate a new instance of the VideoFormPopulator class.
			$populator=new VideoFormPopulator();
			# Populate the form and set the Video data members for this post.
			$populator->populateVideoForm($data);
			# Set the Populator object to the data member.
			$this->setPopulator($populator);

			$display_delete_form=$this->processVideoDelete();
			if($display_delete_form!==FALSE)
			{
				return $display_delete_form;
			}
			$this->processVideoBack();
			//$this->processVideoSelect();

			# Get the Video object from the VideoFormPopulator object and set it to a variable for use in this method.
			$video_obj=$populator->getVideoObject();

			# Set the video's id to a variable.
			$id=$video_obj->getID();

			# Load the YouTube Object video is being edited and/or the video is using an embed code.
			if((!empty($id) || (isset($_POST['video-type']) && $_POST['video-type']=='embed')))
			{
				# Get the YouTube instance. Starts the YouTubeService if it's not already started.
				$yt=$video_obj->getYouTubeObject();
			}

			# Get the video type (file or embed code)
			$video_type=$video_obj->getVideoType();
			# Get the current video's name and set it to a variable.
			if($video_type=='file')
			{
				# Get the current video's name and set it to a variable.
				$current_video=$video_obj->getFileName();
			}
			elseif($video_type=='embed')
			{
				# Set the video embed code to a variable.
				$embed_code=$video_obj->getEmbedCode();
				# Get the current video's YouTube from the embed code and set it to a variable.
				$current_video=$video_obj->getEmbed();
			}
			# Set a variable to FALSE indicating that a video has not been uploaded.
			$uploaded_document=FALSE;
			# Set a variable to FALSE indicating that a thumbnail has not been uploaded.
			$uploaded_thumbnail=FALSE;
			# Set the video API to a variable.
			$api=$video_obj->getAPI();
			# Set the video's author to a variable.
			$author=$video_obj->getAuthor();
			# Set the video's availability to a variable.
			$availability=$video_obj->getAvailability();
			# Set the video's category to a variable.
			$category=$video_obj->getCategory();
			# Set the confirmation email template to a variable.
			$confirmation_template=$video_obj->getConfirmationTemplate();
			# Set the video contributor's id to a variable.
			$contributor_id=$video_obj->getContID();
			# Set the video's posting date to a variable.
			$date=$video_obj->getDate();
			# Set the video's description to a variable.
			$description=$video_obj->getDescription();
			# Set the video's Facebook value to a variable.
			$facebook=$populator->getFacebook();
			# Set the video's associated image id to a variable.
			$image_id=$video_obj->getImageID();
			# Set the video's associated institution id to a variable.
			$institution_name=$video_obj->getInstitution();
			# Get the Institution class.
			require_once MODULES.'Content'.DS.'Institution.php';
			# Instantiate a new Institution object.
			$institution_obj=new Institution();
			# Get the institution info via the institution name.
			$institution_obj->getThisInstitution($institution_name, FALSE);
			# Set the institution id to a variable.
			$institution_id=$institution_obj->getID();
			# Get the Language class.
			require_once MODULES.'Content'.DS.'Language.php';
			# Set the video's language to a variable.
			$language=$video_obj->getLanguage();
			# Instantiate a new Language object.
			$language_obj=new Language();
			# Get the language info via the language name.
			$language_obj->getThisLanguage($language, FALSE);
			# Set the language id to a variable.
			$language_id=$language_obj->getID();
			# Set the video's playlists to a variable.
			$playlists=$video_obj->getPlaylists();
			# Create an empty variable for the playlist id's.
			$playlist_ids=NULL;
			# Check if there are categories.
			if(!empty($playlists))
			{
				# Echange the values for the id's.
				$playlists=array_flip($playlists);
				# Separate the category id's with dashes (-).
				$playlist_ids='-'.implode('-', $playlists).'-';
			}
			# Set the video's publisher name to a variable.
			$publisher_name=$video_obj->getPublisher();
			# Get the Publisher class.
			require_once MODULES.'Content'.DS.'Publisher.php';
			# Instantiate a new Publisher object.
			$publisher_obj=new Publisher();
			# Get the publisher info via the publisher name.
			$publisher_obj->getThisPublisher($publisher_name, FALSE);
			# Set the publisher id to a variable.
			$publisher_id=$publisher_obj->getID();
			# Set the site name to a variable.
			$site_name=$main_content->getSiteName();
			# Set the video's title to a variable.
			$title=$video_obj->getTitle();
			# Set the video's Twitter value to a variable.
			$twitter=$populator->getTwitter();
			# Set the video's unique status to a variable.
			$unique=$populator->getUnique();
			# Set the video's publish year to a variable.
			$year=$video_obj->getYear();

			# Check if the form has been submitted.
			if(array_key_exists('_submit_check', $_POST) && (isset($_POST['video']) && (($_POST['video']==='Add Video') OR ($_POST['video']==='Update'))))
			{
				# Create a session that holds all the POST data (it will be destroyed if it is not needed.)
				$this->setSession();

				# Redirect the user to the appropriate page if their post data indicates that another form is needed to add content.
				$this->contentRedirect('video');

				# Instantiate FormValidator object
				$fv=new FormValidator();
				# Check if the title field was empty (or less than 2 characters or more than 1024 characters long).
				$empty_title=$fv->validateEmpty('title', 'Please enter a title for the video.', 2, 1024);
				# Check if the title field was empty (or less than 2 characters or more than 1024 characters long).
				$empty_title=$fv->validateEmpty('author', 'Please enter an author for the video.', 2, 1024);
				if(empty($playlist_ids))
				{
					# Set an error.
					$fv->setErrors('You must select at least one playlist for this video.');
				}

				# Check if the image is an id.
				if($validator->isInt($image_id)!==TRUE)
				{
					# Get the image info from the `images` table.
					$video_obj->getThisImage($image_id, FALSE);
					# Reset the variable with the id.
					$image_id=$video_obj->getImageID();
				}
				else
				{
					# Get the image info from the `images` table.
					$video_obj->getThisImage($image_id);
				}

				if($image_id!==NULL)
				{
					# Get the Image object.
					$image_obj=$video_obj->getImageObj();
					# Set the image file name to a variable.
					$thumbnail_file_name=$image_obj->getImage();

					# Set the variable that remembers that a video has been uploaded to TRUE (in case we need to remove the video).
					$uploaded_thumbnail=TRUE;
				}

				# Get the FileHandler class.
				require_once MODULES.'FileHandler'.DS.'FileHandler.php';
				# Instantiate the new FileHandler object.
				$file_handler=new FileHandler();
				# Create safe image name based on the title.
				$clean_filename=$file_handler->cleanFilename($title);

				if(empty($id) && $video_type=='file')
				{
					# Assign $_FILES to a variable.
					$u_video=$_FILES['video'];
					if(((is_uploaded_file($u_video['tmp_name'])!==TRUE) OR ($u_video['error'] === UPLOAD_ERR_NO_FILE) OR ($u_video['error'] === 4)) && empty($current_video))
					{
						# Set an error.
						$fv->setErrors('You must select a video file to upload.');
					}
					# Check if a video was uploaded and if there have been no errors so far.
					if(array_key_exists('video', $_FILES) && ($fv->checkErrors()===FALSE))
					{
						# Get the Upload class.
						require_once MODULES.'Form'.DS.'Upload.php';
						# Instantiate an Upload object.
						$upload=new Upload($_FILES['video']);

						# Check if the uploaded video size is not NULL.
						if($upload->getSize()!==NULL)
						{
							try
							{
								# Upload the video file.
								$document_upload=$upload->uploadFile(BODEGA.'videos'.DS, array('avi', 'mp4'), BODEGA.'videos'.DS, $clean_filename, $max_size, FALSE);

								# Reset the video file's name (ie: video_file_name.mp4).
								$new_video_name=$upload->getName();
							}
							catch(Exception $e)
							{
								throw $e;
							}

							# Check for errors.
							if($upload->checkErrors()===TRUE)
							{
								# Remove uploaded video.
								$upload->deleteFile(BODEGA.'videos'.DS.$new_video_name);
								# Get any errors.
								$document_errors=$upload->getErrors();
								# Loop through the errors.
								foreach($document_errors as $document_error)
								{
									# Set each error to our current error array.
									$fv->setErrors($document_error);
								}
							}
							# Check if the upload was successful.
							elseif($document_upload===TRUE)
							{
								# Set the variable that remembers that a video has been uploaded to TRUE (in case we need to remove the video).
								$uploaded_document=TRUE;
							}
						}
					}
				}
				elseif(empty($id) && $_POST['video-type']=='embed')
				{
					# Check if the embed field was empty (or less than 10 characters or more than 1024 characters long).
					$empty_title=$fv->validateEmpty('embed_code', 'Please enter an embed code for the video.', 10, 1024);

					# Set the YouTube ID to an array.
					$api_array=array('youtube_id' => $current_video);

					if($uploaded_thumbnail===FALSE)
					{
						# Get video array from YouTube.
						$video=$yt->listVideos('snippet', array('id' => $current_video));

						# Assign the video thumbnails to an array.
						$video_thumbnail_array=array('youtube_thumbnails' => $video['items'][0]['snippet']['thumbnails']['data']);

						# Merge YouTube ID and Thumbnail arrays.
						$api_array=array_merge($api_array, $video_thumbnail_array);
					}

					# json_encode the YouTube ID and Thumbnail.
					$embed_json=json_encode($api_array, JSON_FORCE_OBJECT);

					# Set to FALSE, just in case.
					$uploaded_document=FALSE;
				}

				# Check for errors to display so that the script won't go further.
				if($fv->checkErrors()===TRUE)
				{
					# Create a variable to the error heading.
					$alert_title='Resubmit the form after correcting the following errors:';
					# Concatenate the errors to the heading.
					$error=$fv->displayErrors();
					# Set the error message to the Document object datamember so that it me be displayed on the page.
					$doc->setError($error);
					# Check if there was an uploaded video file.
					if($uploaded_document===TRUE)
					{
						# Remove uploaded video file.
						$upload->deleteFile(BODEGA.'videos'.DS.$new_video_name);
					}
				}
				else
				{
					if($unique!=1)
					{
						# Get the Search class.
						require_once MODULES.'Search'.DS.'Search.php';
						# Make an array of fields to search in the `videos` table in the Database.
						$fields=array('title');
						# Instantiate a new Search object.
						$search=new Search();
						# Make an array of the terms to search for (enclose multiple word strings in double quotes.)
						$terms=array('"'.$title.'"');
						# Create an empty variable to hold the search filter.
						$filter='';
						# Check if the id is empty.
						if(!empty($id))
						{
							# Create a search filter that won't return the current record we may be editing.
							$filter='`id` != '.$db->quote($id);
						}
						# Search for duplicate records.
						$search->setAllResults($search->performSearch($terms, 'videos', $fields, 'id', $filter));
						# Set any search results to a variable.
						$duplicates=$search->getAllResults();
						# Create an empty array for the duplicate display.
						$dup_display=array();
						# Check if there were records returned.
						if(!empty($duplicates))
						{
							# Loop through the duplicates.
							foreach($duplicates as $duplicate)
							{
								# Instantiate a new Video object.
								$dup_video=new Video();
								# Get the info for this record.
								$dup_video->getThisVideo($duplicate->id);
								# Set the record fields to the dup_display array.
								$dup_display[$dup_video->getID()]=array(
									'id'=>$dup_video->getID(),
									'author'=>$dup_video->getAuthor(),
									'availability'=>$dup_video->getAvailability(),
									'contributor'=>$dup_video->getContID(),
									'date'=>$dup_video->getDate(),
									'description'=>$dup_video->getDescription(),
									'file_name'=>$dup_video->getFileName(),
									'institution'=>$dup_video->getInstitution(),
									'language'=>$dup_video->getLanguage(),
									'playlists'=>$dup_video->getPlaylists(),
									'publisher'=>$dup_video->getPublisher(),
									'title'=>$dup_video->getTitle(),
									'year'=>$dup_video->getYear()
								);
							}
							# Explicitly set unique to 0 (not unique).
							$populator->setUnique(0);
						}
						else
						{
							# Explicitly set unique to 1 (unique).
							$populator->setUnique(1);
						}
						$unique=$populator->getUnique();
						# Set the duplicates to display to the data member for retrieval outside of the method.
						$this->setDuplicates($dup_display);
					}
					# Check if the post is considered unique and may be added to the Database.
					if($unique==1)
					{
						# If there is no custom thumbnail image.
						if($uploaded_thumbnail===FALSE)
						{
							# If this is not a video being edited and it's a file.
							if(empty($id) && $_POST['video-type']=='file')
							{
								# Create Thumbnails.
								$cl2=new CommandLine('ffmpeg');
								$cl2->runScript("-i ".BODEGA.'videos'.DS.$new_video_name." -ss 00:00:05 -f mjpeg ".IMAGES_PATH."original".DS.$clean_filename.".jpg");
								$cl2->runScript("-i ".BODEGA.'videos'.DS.$new_video_name." -ss 00:00:05 -f mjpeg -vf scale=320:180 ".IMAGES_PATH.$clean_filename.".jpg");

								# Insert the thumbnail image into the `images` table.
								$insert_image='INSERT INTO `'.DBPREFIX.'images` ('.
									'`title`, '.
									'`image`, '.
									'`category`, '.
									' `contributor`'.
									') VALUES ('.
									$db->quote($db->escape(str_ireplace(array(DOMAIN_NAME, $site_name), array('%{domain_name}', '%{site_name}'), $title))).', '.
									$db->quote($db->escape($clean_filename.'.jpg')).',
									\'-36-\', '.
									$db->quote($contributor_id).
									')';
								# Run the sql query.
								$db_post=$db->query($insert_image);

								# Assign the image ID to a variable.
								$image_id=$db->get_insert_id();
							}
						}

						# Create the default value for the message action.
						$message_action='added';
						# Create the default sql as an INSERT and set it to a variable.
						$sql='INSERT INTO `'.DBPREFIX.'videos` ('.
							'`title`,'.
							'`description`,'.
							((!empty($new_video_name)) ? ' `file_name`,' : '').
							((!empty($embed_code)) ? ' `embed_code`,' : '').
							((isset($embed_json)) ? ' `api`,' : '').
							((!empty($author)) ? ' `author`,' : '').
							((!empty($year)) ? ' `year`,' : '').
							((!empty($playlist_ids)) ? ' `playlist`,' : '').
							' `availability`,'.
							' `date`,'.
							(($image_id!==NULL) ? ' `image`,' : '').
							' `institution`,'.
							((!empty($publisher_id)) ? ' `publisher`,' : '').
							' `language`,'.
							' `contributor`'.
							') VALUES ('.
							$db->quote($db->escape(str_ireplace(array(DOMAIN_NAME, $site_name), array('%{domain_name}', '%{site_name}'), $title))).','.
							((!empty($description)) ? ' '.$db->quote($db->escape(preg_replace("/<p>(.*?)<\/p>(\n?\r?(\n\r)?)/i", "$1\n", str_replace(array("\r\n", "\n", "\r"), '', htmlspecialchars_decode($description))))).',' : ' \'\',').
							((!empty($new_video_name)) ? ' '.$db->quote($db->escape($new_video_name)).',' : '').
							((!empty($embed_code)) ? ' '.$db->quote($db->escape($embed_code)).',' : '').
							((isset($embed_json)) ? ' '.$db->quote($embed_json).',' : '').
							((!empty($author)) ? ' '.$db->quote($db->escape($author)).',' : '').
							((!empty($year)) ? ' '.$db->quote($year).',' : '').
							((!empty($playlist_ids)) ? ' '.$db->quote($playlist_ids).',' : '').
							' '.$db->quote($availability).','.
							' '.$db->quote($date).','.
							(($image_id!==NULL) ? ' '.$db->quote($image_id).',' : '').
							' '.$db->quote($institution_id).','.
							((!empty($publisher_id)) ? ' '.$db->quote($publisher_id).',' : '').
							' '.$db->quote($language_id).','.
							' '.$db->quote($contributor_id).
							')';

						# Check if this is an UPDATE.
						if(!empty($id))
						{
							# Reset the value for the message action.
							$message_action='updated';
							# Reset the sql variable with the UPDATE sql.
							$sql='UPDATE `'.DBPREFIX.'videos` SET'.
								((isset($embed_json)) ? ' `api` = '.$db->quote($embed_json).',' : '').
								' `author` = '.((!empty($author)) ? ' '.$db->quote($author).',' : 'NULL,').
								' `availability` = '.$db->quote($availability).','.
								' `contributor` = '.$db->quote($contributor_id).','.
								' `date` = '.$db->quote($date).','.
								' `description` = '.((!empty($description)) ? ' '.$db->quote($db->escape(preg_replace("/<p>(.*?)<\/p>(\n?\r?(\n\r)?)/i", "$1\n", str_replace(array("\r\n", "\n", "\r"), '', htmlspecialchars_decode($description))))).',' : 'NULL,').
								((!empty($embed_code)) ? ' `embed_code` = '.$db->quote($db->escape($embed_code)).',' : '').
								((!empty($new_video_name)) ? ' `file_name` = '.$db->quote($db->escape($new_video_name)).',' : '').
								' `institution` = '.$db->quote($institution_id).','.
								' `image` = '.(($image_id!==NULL) ? ' '.$db->quote($image_id).',' : 'NULL,').
								' `language` = '.$db->quote($language_id).','.
								((!empty($playlist_ids)) ? ' `playlist` = '.$db->quote($playlist_ids).',' : '').
								' `publisher` = '.((!empty($publisher_id)) ? ' '.$db->quote($publisher_id).',' : 'NULL,').
								' `title` = '.$db->quote($db->escape(str_ireplace(array(DOMAIN_NAME, $site_name), array('%{domain_name}', '%{site_name}'), $title))).','.
								' `year` = '.((!empty($year)) ? ' '.$db->quote($year).'' : 'NULL').
								' WHERE `id` = '.$db->quote($id).
								' LIMIT 1';
						}
						try
						{
							# Run the sql query.
							$db_post=$db->query($sql);
							# Check if the query was successful.
							if($db_post>0)
							{
								# Set the ID from the insert SQL query.
								$insert_id=$db->get_insert_id();

								# If this is a new video (not editted).
								if(!empty($insert_id))
								{
									# Set the video's ID.
									$_SESSION['form']['video']['InsertID']=$insert_id;

									# If there is a video file.
									if($video_type=='file')
									{
										# Set the File name.
										$_SESSION['form']['video']['FileName']=$new_video_name;
									}
									elseif($video_type=='embed')
									{
										# Add the video to playlists on YouTube.
										# Create a resource id with video id and kind.
										$resourceId=new Google_Service_YouTube_ResourceId();
										$resourceId->setVideoId($current_video);
										$resourceId->setKind('youtube#video');

										# Get the playlists from the database.
										$playlist_results=$db->get_results('SELECT `category`, `api` FROM `'.DBPREFIX.'categories` WHERE `api` IS NOT NULL');

										# Loop through the database categories.
										foreach($playlist_results as $playlist_row)
										{
											# Find the playlist in the $playlists array.
											if(array_key_exists($playlist_row->category, $playlists))
											{
												# Decode the `api` field in the `categories` table.
												$playlist_api_decoded=json_decode($playlist_row->api);

												# Create a snippet with resource id.
												$playlistItemSnippet=new Google_Service_YouTube_PlaylistItemSnippet();
												$playlistItemSnippet->setPlaylistId($playlist_api_decoded->youtube_playlist_id);
												$playlistItemSnippet->setResourceId($resourceId);

												# Create a playlist item request request with snippet.
												$playlistItem=new Google_Service_YouTube_PlaylistItem();
												$playlistItem->setSnippet($playlistItemSnippet);

												# Execute the request and return an object containing information about the new playlistItem.
												$playlistItemResponse=$yt->PlaylistItemsInsert('snippet,contentDetails', $playlistItem);
											}
										}
									}
								}

								# Instantiate the new CommandLine object.
								$cl=new CommandLine();
								# Set the video form session to a new session for use in the command line.
								$_SESSION['video_upload']=$_SESSION['form']['video'];
								$_SESSION['video_upload']['Environment']=DOMAIN_NAME;
								$_SESSION['video_upload']['ConfirmationTemplate']=$confirmation_template;

								# Create an array with video data.
								$video_data=array('Environment'=>DOMAIN_NAME, 'SessionId'=>session_id(), 'SessionPath'=>session_save_path());

								# Run the upload script.
								$cl->runScript(MODULES.'Media'.DS.'YouTubeUpload.php', $video_data);

								# Check if the availability allows posting to social networks.
								if($availability==1)
								{
									# Check if the post should be posted on Twitter.com or Facebook.com.
									if($twitter==='0' OR $facebook==='post')
									{
										$post_url=VIDEOS_URL.'?video='.$db->get_insert_id();
									}
									# Check if the post should be posted on Twitter.com.
									if($twitter==='0')
									{
										# Set the Twitter constructor params to an array.
										$params=array(
											'consumer_key'=>TWITTER_CONSUMER_KEY,
											'consumer_secret'=>TWITTER_CONSUMER_SECRET,
											'oauth_token'=>TWITTER_TOKEN,
											'oauth_token_secret'=>TWITTER_TOKEN_SECRET
										);
										# Get the Twitter class.
										require_once MODULES.'Social'.DS.'Twitter'.DS.'Twitter.php';
										# Instantiate a new Twitter object.
										$twitter=new Twitter($params);
										$max_short_url_length=$twitter->getMaxShortURL_Length();
										$tweet=$twitter->postToTwitter(WebUtility::truncate($title, 139-$max_short_url_length, '&hellip;', FALSE, TRUE).' '.$post_url);
									}
									# Check if the post should be posted on Facebook.com.
									if($facebook==='post')
									{
										if(file_exists(IMAGES_PATH.'original'.DS.$thumbnail_file_name))
										{
											$image_name='original'.DS.$thumbnail_file_name;
										}
										elseif(file_exists(IMAGES_PATH.'original'.DS.$clean_filename.'.jpg'))
										{
											$image_name='original'.DS.$clean_filename.'.jpg';
										}
										else
										{
											# Defult image.
											$image_name='SiteShot.jpg';
										}

										require_once MODULES.'User'.DS.'Contributor.php';
										$contributor_obj=new Contributor();
										$contributor_obj->getThisContributor($contributor_id, 'id');
										$cont_privacy=$contributor_obj->getContPrivacy();
										$contributor_name='';
										# Check if the contributor should be hidden.
										if($cont_privacy!==NULL)
										{
											$contributor_name='Posted by '.$contributor_obj->getContName().' - ';
										}
										# Get the CustomFacebook class.
										require_once MODULES.'Social'.DS.'Facebook'.DS.'CustomFacebook.php';
										# Instantiate a new CustomFacebook object.
										$fb=new CustomFacebook();
										# Post to Facebook.
										$post_id=$fb->postToFB($contributor_name.'Read more at '.DOMAIN_NAME, $post_url, WebUtility::truncate($title, 420, '&hellip;', FALSE, TRUE), IMAGES.$image_name);
									}
								}

								# Remove the video's session.
								unset($_SESSION['form']);
								# Set a nice message for the user in a session.
								$_SESSION['message']='Your video was successfully '.$message_action.'!';
								# Redirect the user to the page they were on.
								$this->redirectNoDelete('video');
							}
							else
							{
								if(!empty($id))
								{
									# Set a nice message for the user in a session.
									$_SESSION['message']='The video\'s record was unchanged.';
								}
								# Check if there was an uploaded video file.
								if($uploaded_document===TRUE)
								{
									# Remove uploaded video file.
									$upload->deleteFile(BODEGA.'videos'.DS.$new_video_name);
								}
							}
						}
						catch(Exception $e)
						{
							# Check if there was an uploaded video file.
							if($uploaded_document===TRUE)
							{
								# Remove uploaded video file.
								$upload->deleteFile(BODEGA.'videos'.DS.$new_video_name);
							}
							throw $e;
						}
					}
					else
					{
						# Check if there was an uploaded video file.
						if($uploaded_document===TRUE)
						{
							# Remove uploaded video file.
							$upload->deleteFile(BODEGA.'videos'.DS.$new_video_name);
						}
					}
				}
			}
			return NULL;
		}
		catch(ezDB_Error $ez)
		{
			throw new Exception('There was an error posting to the `videos` table in the Database: '.$ez->error.', code: '.$ez->errno.'<br />Last query: '.$ez->last_query, E_RECOVERABLE_ERROR);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- processVideo

	/*** End public methods ***/



	/*** private methods ***/

	/**
	 * processVideoSelect
	 *
	 * Processes a submitted form selecting a video to add to a post.
	 *
	 * @access	private
	 * @return	string
	 */
	private function processVideoSelect()
	{
		# Check if this is a video select page.
		if(isset($_GET['select']) && $_GET['select']==='yes')
		{
			# Check if the form has been submitted.
			if(array_key_exists('_submit_check', $_POST) && ($_POST['video']=='Select Video'))
			{
				# Bring the alert-title variable into scope.
				global $alert_title;
				# Set the Document instance to a variable.
				$doc=Document::getInstance();

				# Check if the video id POST data was sent.
				if(isset($_POST['video_info']))
				{
					# Get the Video class.
					require_once MODULES.'Media'.DS.'Video.php';
					# Instantiate a new Video object.
					$video_obj=new Video();
					$colon_pos=strpos($_POST['video_info'], ':');
					$video_id=substr($_POST['video_info'], 0, $colon_pos);
					$video_name=substr($_POST['video_info'], $colon_pos+1);
					# Set the video id to the Video data member.
					$video_obj->setID($video_id);
					# Set the video name to the Video data member.
					$video_obj->setVideo($video_name);
					# Set the video's id to a variable.
					$video_id=$video_obj->getID();
					# Set the video's name to a variable.
					$video_name=$video_obj->getVideo();
				}
				else
				{
					# Set the error message to the Document object datamember so that it me be displayed on the page.
					$doc->setError('Please select a video file.');
				}
				# Redirect the User to the page they came from with a friendly message.
				$this->redirectVideo($video_name, 'selected');
			}
		}
	} #==== End -- processVideoSelect

	/**
	 * processVideoDelete
	 *
	 * Removes a video from the `videos` table and the actual file from the system. A wrapper method for the deleteVideo method in the Video class.
	 *
	 * @access	private
	 */
	private function processVideoDelete()
	{
		try
		{
			# Bring the Login object into scope.
			global $login;
			# Set the Database instance to a variable.
			$db=DB::get_instance();
			# Set the Document instance to a variable.
			$doc=Document::getInstance();
			# Set the Validator instance to a variable.
			$validator=Validator::getInstance();

			# Explicitly set the delete variable to FALSE; the POST will NOT be deleted.
			$delete=FALSE;
			$access=TRUE;
			# Check if the video's id was passed via GET data and that GET data indicates this is a delete.
			if(isset($_GET['video']) && isset($_GET['delete']))
			{
				# Check if the passed video id is an integer.
				if($validator->isInt($_GET['video'])===TRUE)
				{
					# Check if the form has been submitted.
					if(array_key_exists('_submit_check', $_POST) && isset($_POST['do']) && (isset($_POST['delete_video']) && ($_POST['delete_video']==='delete')))
					{
						# Get the Subcontent class. With this class, the Video object can be accessed as well as the SubContent.
						require_once MODULES.'Content'.DS.'SubContent.php';
						# Instantiate a new SubContent object.
						$subcontent=new SubContent();
						# Get the info for this video and set the return boolean to a variable.
						$record_retrieved=$subcontent->getThisVideo($_GET['video'], TRUE);
						# Check if the record was actually returned.
						if($record_retrieved===TRUE)
						{
							# Set the Video object to a local variable.
							$video_obj=$subcontent->getVideoObj();
							# Set the video name to a local variable.
							$video_name=$video_obj->getFileName();
							if(empty($video_name))
							{
								$video_name=$video_obj->getTitle();
							}
							# Set the "cleaned id to a local variable.
							$id=$subcontent->getVideoID();
							# Get all subcontent with this video associated.
							$subcontent_returned=$subcontent->getSubContent(NULL, NULL, 'branch', 'date', 'DESC', '`video` = '.$db->quote($id));
							# Set the product_returned variable to FALSE by default.
							$product_returned=FALSE;
							# Check if there were any subcontent returned.
							if($subcontent_returned===TRUE)
							{
								# Set the returned subcontent records to a local variable.
								$rows=$subcontent->getAllSubContent();
								# Loop throught the returned rows.
								foreach($rows as $row)
								{
									$branches=trim(str_replace('-', ' ', $row->branch).' '.MAN_USERS);
									# Check if the user has access to this record.
									$access=$login->checkAccess($branches);
									if($access===FALSE) { break; }
								}
							}
							# Check if this user still has access to delete this video.
							if($access===TRUE)
							{
								if(($subcontent_returned===TRUE))
								{
									try
									{
										# Remove the video from all `subcontent` records.
										$db_submit=$db->query('UPDATE '.
											'`'.DBPREFIX.'subcontent` '.
											'SET '.
											'`'.DBPREFIX.'subcontent`.`video` = NULL '.
											'WHERE '.
											'`'.DBPREFIX.'subcontent`.`video` = '.$db->quote($id));
										if(empty($db_submit))
										{
											# Set a nice message to the session.
											$_SESSION['message']='The video "'.$video_name.'" (id: '.$id.') was NOT removed from all records that reference it. Please contact <a href="'.APPLICATION_URL.'webSupport/">webSupport</a> to have this video removed.';
											# Redirect the user back to the page.
											$this->redirectNoDelete();
										}
									}
									catch(ezDB_Error $ez)
									{
										throw new Exception('There was an error removing the video "'.$video_name.'" (id: '.$id.') from the Database: '.$ez->error.', code: '.$ez->errno.'<br />Last query: '.$ez->last_query, E_RECOVERABLE_ERROR);
									}
								}
								# Get rid of any CMS form sessions.
								unset($_SESSION['form']['video']);
								# Delete the video from the Database and set the returned value to a variable.
								$deleted=$video_obj->deleteVideo($id, FALSE);
								if($deleted===TRUE)
								{
									$this->redirectVideo($video_name, 'deleted');
								}
								else
								{
									# Set a nice message to the session.
									$_SESSION['message']='The video "'.$video_name.'" (id: '.$id.') was NOT deleted from the video list, though it WAS removed from all records that reference it. Please contact <a href="'.APPLICATION_URL.'webSupport/">webSupport</a> to have this video removed.';
									# Redirect the user back to the page.
									$this->redirectNoDelete();
								}
							}
							# Set a nice message to the session.
							$_SESSION['message']='The video was NOT deleted. It is associated with records that you do not have the appropriate authorization to edit. If you still feel it is absolutely necessary to delete this video, please write an <a href="'.APPLICATION_URL.'webSupport/">email</a> with your thoughts.';
							# Redirect the user back to the page.
							$this->redirectNoDelete();
						}
						else
						{
							# Set a nice message to the session.
							$_SESSION['message']='The video was not found.';
							# Redirect the user back to the page.
							$this->redirectNoDelete('video');
						}
					}
					# Check if the form has been submitted to NOT delete the video.
					elseif(array_key_exists('_submit_check', $_POST) && (isset($_POST['do_not']) OR (isset($_POST['delete_video']) && ($_POST['delete_video']==='keep'))))
					{
						# Set a nice message to the session.
						$_SESSION['message']='The video was NOT deleted.';
						# Redirect the user back to the page.
						$this->redirectNoDelete();
					}
					else
					{
						# Create a delete form for this video and request confirmation from the user with the appropriate warnings.
						require TEMPLATES.'forms'.DS.'delete_form.php';
						return $display;
					}
				}
				# Redirect the user to the default redirect location. They have no business trying to pass a non-integer as an id!
				$doc->redirect(DEFAULT_REDIRECT);
			}
			return FALSE;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- processVideoDelete

	/**
	 * processVideoBack
	 *
	 * Processes a submitted form indicating that the User should be sent back to the form that sent them to fetch a file.
	 *
	 * @access	private
	 */
	private function processVideoBack()
	{
		try
		{
			# Create an array of possible indexes. These are forms that can send the user to get an institution.
			$indexes=array(
				'post',
				'product',
				'video'
			);
			# Set the resource value.
			$resource='video';
			$this->processBack($resource, $indexes);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- processVideoBack

	/**
	 * redirectVideo
	 *
	 * Redirect the user to the appropriate page if their post data indicates that another form sent the User
	 * to this form to aquire a video.
	 *
	 * @access	private
	 */
	private function redirectVideo($video_name, $action)
	{
		try
		{
			# Set the Document instance to a variable.
			$doc=Document::getInstance();
			# Get the Populator object and set it to a local variable.
			$populator=$this->getPopulator();
			# Get the Video object and set it to a local variable.
			$video_obj=$populator->getVideoObject();
			# Get the data for the new video.
			$video_obj->getThisVideo($video_name, FALSE);
			# Get the new video's id.
			$video_id=$video_obj->getID();
			# Remove the video session.
			unset($_SESSION['form']['video']);
			# Set a nice message for the user in a session.
			$_SESSION['message']='The video "'.$video_name.'" was successfully '.$action.'!';
			# Check if there is a post or content session.
			if(isset($_SESSION['form']['video']))
			{
				# Set the default origin form's name.
				$origin_form='video';
				# Set the default session video index name.
				$video_index='ID';
				# Set the post session video id.
				$_SESSION['form'][$origin_form][$video_index]=$video_id;
				# Redirect the user to the original post page.
				$doc->redirect($_SESSION['form'][$origin_form]['FormURL'][0]);
			}
			$remove=NULL;
			if(isset($_GET['delete']) && $action=='deleted')
			{
				$remove='video';
			}
			# Redirect the user to the page they were on.
			$this->redirectNoDelete($remove);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- redirectVideo

	/**
	 * setSession
	 *
	 * Creates a session that holds all the POST data (it will be destroyed if it is not needed.)
	 *
	 * @access	private
	 */
	private function setSession()
	{
		try
		{
			# Get the Populator object and set it to a local variable.
			$populator=$this->getPopulator();
			# Get the Video object and set it to a local variable.
			$video_obj=$populator->getVideoObject();

			# Set the form URL's to a variable.
			$form_url=$populator->getFormURL();
			# Set the current URL to a variable.
			$current_url=FormPopulator::getCurrentURL();
			# Check if the current URL is already in the form_url array. If not, add the current URL to the form_url array.
			if(!in_array($current_url, $form_url)) $form_url[]=$current_url;

			# Set the video's associated institution name to a variable.
			$institution_name=$video_obj->getInstitution();
			# Get the Institution class.
			require_once MODULES.'Content'.DS.'Institution.php';
			# Instantiate a new Institution object.
			$institution_obj=new Institution();
			# Get the institution info via the institution name.
			$institution_obj->getThisInstitution($institution_name, FALSE);
			# Set the institution id to a variable.
			$institution_id=$institution_obj->getID();

			# Set the video's language to a variable.
			$language=$video_obj->getLanguage();
			# Get the Language class.
			require_once MODULES.'Content'.DS.'Language.php';
			# Instantiate a new Language object.
			$language_obj=new Language();
			# Get the language info via the language name.
			$language_obj->getThisLanguage($language, FALSE);
			# Set the language id to a variable.
			$language_id=$language_obj->getID();

			# Set the video's publisher name to a variable.
			$publisher_name=$video_obj->getPublisher();
			# Get the Publisher class.
			require_once MODULES.'Content'.DS.'Publisher.php';
			# Instantiate a new Publisher object.
			$publisher_obj=new Publisher();
			# Get the publisher info via the publisher name.
			$publisher_obj->getThisPublisher($publisher_name, FALSE);
			# Set the publisher id to a variable.
			$publisher_id=$publisher_obj->getID();

			# Get the video type (file or embed).
			$video_type=$video_obj->getVideoType();
			# If the video type file.
			if($video_type=='file')
			{
				# Set the sessen key is FileName.
				$file_embed='FileName';
				# Set the value of $file_embed.
				$file_name=$video_obj->getFileName();
			}
			else
			{
				# Set the session key to Embed.
				$file_embed='EmbedCode';
				# Set the value of $file_embed.
				$file_name=$video_obj->getEmbedCode();
			}

			# Create a session that holds all the POST data (it will be destroyed if it is not needed.)
			$_SESSION['form']['video']=
				array(
					'ID'=>$video_obj->getID(),
					'FormURL'=>$form_url,
					'API'=>$video_obj->getAPI(),
					'Author'=>$video_obj->getAuthor(),
					'Availability'=>$video_obj->getAvailability(),
					'Category'=>$video_obj->getCategory(),
					'ContID'=>$video_obj->getContID(),
					'Date'=>$video_obj->getDate(),
					'Description'=>$video_obj->getDescription(),
					'Facebook'=>$populator->getFacebook(),
					'ImageID'=>$video_obj->getImageID(),
					'Institution'=>$institution_id,
					'Language'=>$language_id,
					'Playlists'=>$video_obj->getPlaylists(),
					'Publisher'=>$publisher_id,
					'Title'=>$video_obj->getTitle(),
					'Twitter'=>$populator->getTwitter(),
					'Unique'=>$populator->getUnique(),
					'VideoType'=>$video_obj->getVideoType(),
					$file_embed=>$file_name,
					'Year'=>$video_obj->getYear()
				);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} #==== End -- setSession

	/*** End private methods ***/

} # End VideoFormProcessor class.