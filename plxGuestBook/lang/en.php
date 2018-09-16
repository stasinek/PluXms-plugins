<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

$LANG = array(

'L_PAGE_TITLE'					=> 'Guestbook',

# config.php
'L_GB_MAIL_AVAILABLE'			=> 'Mail sending function available',
'L_GB_MAIL_NOT_AVAILABLE'		=> 'Mail sending function unavailable',

'L_GB_MENU_DISPLAY'				=> 'Display the menu of the contact page',
'L_GB_MENU_TITLE'		    	=> 'Menu title',
'L_GB_MENU_TEXT'		    	=> 'Text to insert above the form',
'L_GB_MENU_POS'		    		=> 'Menu location',
'L_GB_EMAIL'		   			=> 'Mail recipient *',
'L_GB_EMAIL_SUBJECT'  			=> 'Mail subject',
'L_GB_THANKYOU_MESSAGE'			=> 'Thanks message',
'L_GB_TEMPLATE'					=> 'Template',
'L_GB_CAPTCHA'					=> 'Activate the anti-spam captcha',
'L_GB_MOD'						=> 'Moderate messages when created',
'L_GB_BYPAGE'					=> 'Number of messages displayed per page',
'L_GB_BYPAGE_ADMIN'				=> 'Number of messages displayed per page in administration area',
'L_GB_TRI_GB'					=> 'Messages sorting',
'L_GB_SORT'						=> 'downward from date',
'L_GB_RSORT'					=> 'upward from date',
'L_GB_SAVE'						=> 'Save',
'L_GB_COMMA'					=> '* separate recipients email addresses with commas',
'L_GB_SUPERVISION_EMAIL'		=> 'Supervision Email for guestbook publishing', 
'L_GB_TAB01'					=> 'General',
'L_GB_TAB02'					=> 'update',
'L_GB_TAB03'					=> 'presentation',

# admin.php
'L_GB_ALL'						=> 'All',
'L_GB_ONLINE'					=> 'On line',
'L_GB_OFFLINE'					=> 'Offline',
'L_GB_SET_ONLINE'				=> 'Set online',
'L_GB_SET_OFFLINE'				=> 'Set offline',
'L_GB_DELETE'					=> 'Delete',
'L_GB_FOR_SELECTION' 			=> 'For selection...',
'L_GB_ONLINE_LIST'				=> 'Online messages list',
'L_GB_OFFLINE_LIST'				=> 'List of messages waiting for validation',
'L_GB_ALL_LIST'					=> 'Messages list',
'L_GB_EDIT'						=> 'Edit',
'L_GB_ID'						=> 'ID',
'L_GB_EDIT_TITLE'				=> 'Edit this message',
'L_GB_NO_MESSAGE' 				=> 'no message',
'L_GB_ARTICLE_LINKED'			=> 'Article',
'L_GB_ARTICLE_LINKED_TITLE'		=> 'Article linked to this message',
'L_GB_OFFLINE' 					=> 'Offline',
'L_GB_ONLINE'					=> 'Online',
'L_GB_ONLINE_TITLE'				=> 'View this online message',
'L_GB_BACK_TO_MESSAGES'    		=> 'Back to messages list',
'L_GB_EDITING' 					=> 'Message editing',
'L_GB_AUTHOR_FIELD'				=> 'Author',
'L_GB_TYPE_FIELD'				=> 'Message type',
'L_GB_DATE_FIELD'				=> 'Date and time of the message',
'L_GB_IP_FIELD'					=> 'Ip',
'L_GB_SITE_FIELD'				=> 'Site',
'L_GB_EMAIL_FIELD'				=> 'Email',
'L_GB_STATUS_FIELD'				=> 'Status',
'L_GB_MESSAGE_FIELD'			=> 'Message',
'L_GB_DELETE_CONFIRM'			=> 'Delete this message ?',
'L_GB_PUBLISH_BUTTON'			=> 'Validate this message',
'L_GB_OFFLINE_BUTTON'			=> 'Set offline',
'L_GB_UPDATE_BUTTON'			=> 'Update',
'L_GB_WRITTEN_BY'				=> 'Written by',
'L_GB_LIST_ID'					=> 'ID',
'L_GB_LIST_DATE'				=> 'Date',
'L_GB_LIST_AUTHOR'				=> 'Author',
'L_GB_LIST_MESSAGE'				=> 'Message',
'L_GB_LIST_ACTION'				=> 'Action',
'L_GB_VIEW_EMAIL'				=> 'Show email address',

'L_GB_DEFAULT_MENU_NAME'		=> 'Guestbook',
'L_GB_DEFAULT_OBJECT'			=> '## PluXml ## Guestbook publishing',
'L_GB_DEFAULT_THANKYOU'			=> 'Thanks for your contribution.',

'L_GB_ERROR_EMAIL'				=> 'Will you please enter a valid email address',

# form.guestbook.php
'L_GB_MSG_WELCOME'				=> 'Thank you to take the time to write a message.',
'L_GB_ERR_AUTHOR'				=> 'Enter your name',
'L_GB_ERR_EMAIL'				=> 'Enter a valid email address',
'L_GB_ERR_SITE'					=> 'Enter a valid site address',
'L_GB_ERR_CONTENT'				=> 'Enter the content of your message',
'L_GB_ERR_ANTISPAM'				=> 'The anti-spam check failed',
'L_GB_ERR_SENDMAIL'				=> 'An error occured during the sending of your message',

'L_GB_FORM_AUTHOR'				=> 'Your name (or pseudo)',
'L_GB_FORM_MAIL'				=> 'Your email address',
'L_GB_FORM_WEBSITE'				=> 'Your website',
'L_GB_PLACEHOLDER_SITE'			=> 'http://',
'L_GB_FORM_VIEW_MAIL'			=> 'Show your email address to visitors',
'L_GB_FORM_ANTISPAM_INFO'		=> 'Note : email address in use is protected against SPAM.',
'L_GB_FORM_CONTENT'				=> 'The content of your message',
'L_GB_FORM_ANTISPAM'			=> 'Anti-spam check',
'L_GB_FORM_BTN_SEND'			=> 'Send',
'L_GB_FORM_BTN_SENDTO'			=> 'Post a message',
'L_GB_FORM_MESSAGE'				=> 'Message',
'L_GB_FORM_NO_POST'				=> 'No message',
'L_GB_FORM_MOD'					=> 'The message is undergoing moderation by the site administrator',
'L_GB_FORM_BY'					=> 'by',
'L_GB_FORM_THE'					=> 'on',
'L_GB_FORM_CORPS_1'				=> 'Guestbook publishing on',
'L_GB_FORM_CORPS_2'				=> 'proposition by',
'L_GB_FORM_CORPS_3'				=> 'His (her) web site',
'L_GB_FORM_CORPS_4'				=> 'Message',
'L_GB_FORM_CORPS_5'				=> 'Message undergoing moderation',
'L_GB_FORM_CORPS_6'				=> 'Access to administration',
'L_GB_FORM_CORPS_7'				=> 'Manage the messages',
'L_GB_FORM_TEXT'				=> 'Text to insert above "Post a message"',

# Update plugin
'L_VP_ACTUAL_VERSION'			=> 'Current version',
'L_VP_LAST_VERSION'				=> 'You have the latest version of the plugin',
'L_VP_NEW_VERSION'				=> 'A new version of the plugin',
'L_VP_NEW2_VERSION'				=> 'Is available on dpfpic.com',
'L_VP_ERROR'					=> 'Unable to see status, an error was encountered',
'L_VP_DESACTIVED'				=> 'The update has been disabled'
);
?>
