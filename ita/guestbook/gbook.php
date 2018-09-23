<?php
/*******************************************************************************
*  Title: GBook - PHP Guestbook
*  Version: 1.7 from 20th August 2009
*  Author: Klemen Stirn
*  Website: http://www.phpjunkyard.com
********************************************************************************
*  COPYRIGHT NOTICE
*  Copyright 2004-2009 Klemen Stirn. All Rights Reserved.

*  The GBook may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.

*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden.

*  Using this code, in part or full, to create derivate work,
*  new scripts or products is expressly forbidden. Obtain permission
*  before redistributing this software over the Internet or in
*  any other medium. In all cases copyright and header must remain intact.
*  This Copyright is in full effect in any country that has International
*  Trade Agreements with the United States of America or
*  with the European Union.

*  Removing any of the copyright notices without purchasing a license
*  is expressly forbidden. To remove GBook copyright notice you must purchase
*  a license for this script. For more information on how to obtain
*  a license please visit the page below:
*  http://www.phpjunkyard.com/copyright-removal.php
*******************************************************************************/

define('IN_SCRIPT',true);

require('settings.php');
require($settings['language']);

/* Set some variables that will be used later */
$settings['verzija'] = '1.7';
$settings['number_of_entries'] = '';
$settings['number_of_pages'] = '';
$settings['pages_top'] = '';

/* Template path to use */
$settings['tpl_path'] = './templates/'.$settings['template'].'/';

/* Set target window for URLs */
$settings['target'] = $settings['url_blank'] ? ' target="_blank"' : '';

/* First thing to do is make sure the IP accessing GBook hasn't been banned */
gbook_CheckIP();

/* Get the action parameter */
$a = isset($_REQUEST['a']) ? gbook_input($_REQUEST['a']) : '';

/* And this will start session which will help prevent multiple submissions and spam */
if ($a=='sign' || $a=='add')
{
    session_name('GBOOK');
    session_start();

    $myfield['name']=str_replace(array('.','/'),'',sha1('name'.$settings['filter_sum']));
    $myfield['cmnt']=str_replace(array('.','/'),'',sha1('comments'.$settings['filter_sum']));
    $myfield['bait']=str_replace(array('.','/'),'',sha1('bait'.$settings['filter_sum']));
    $myfield['answ']=str_replace(array('.','/'),'',sha1('answer'.$settings['filter_sum']));
}

/* Don't cache any of the pages */
printNoCache();

/* Check actions */
if ($a)
{
	/* Session is blocked, show an error */
    if (!empty($_SESSION['block']))
    {
        problem($lang['e01'],0);
    }

    /* Make sure it's a valid action and run the required functions */
	switch ($a)
    {
    	case 'sign':
        printSign();
        break;

        case 'delete':
        confirmDelete();
        break;

        case 'viewprivate':
        confirmViewPrivate();
        break;

        case 'add':
        addEntry();
        break;

        case 'confirmdelete':
        doDelete();
        break;

        case 'showprivate':
        showPrivate();
        break;

        case 'reply':
        writeReply();
        break;

        case 'postreply':
        postReply();
        break;

        case 'viewIP':
        confirmViewIP();
        break;

        case 'showIP':
        showIP();
        break;

        case 'viewEmail':
        confirmViewEmail();
        break;

        case 'showEmail':
        showEmail();
        break;

        case 'approve':
        approveEntry();
        break;

        default:
        problem($lang['e11']);
	} // END Switch $a

} // END If $a

/* Prepare and show the GBook entries */
$settings['notice'] = defined('NOTICE') ? NOTICE : '';

$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
if ($page > 0)
{
    $start = ($page*10)-9;
    $end   = $start+9;
}
else
{
    $page  = 1;
    $start = 1;
    $end   = 10;
}

$lines = file($settings['logfile']);
$total = count($lines);

if ($total > 0)
{
    if ($end > $total)
    {
    	$end = $total;
    }
    $pages = ceil($total/10);

    $settings['number_of_entries'] = sprintf($lang['t01'],$total,$pages);
    $settings['number_of_pages'] = ($pages > 1) ? sprintf($lang['t75'],$pages) : '';

    if ($pages > 1)
    {
        $prev_page = ($page-1 <= 0) ? 0 : $page-1;
        $next_page = ($page+1 > $pages) ? 0 : $page+1;

        if ($prev_page)
        {
            $settings['pages_top'] .= '<a href="gbook.php?page=1">'.$lang['t02'].'</a> ';
        	if ($prev_page != 1)
            {
        		$settings['pages_top'] .= '<a href="gbook.php?page='.$prev_page.'">'.$lang['t03'].'</a> ';
            }
        }

        for ($i=1; $i<=$pages; $i++)
        {
            if ($i <= ($page+5) && $i >= ($page-5))
            {
               if ($i == $page)
               {
               	$settings['pages_top'] .= ' <b>'.$i.'</b> ';
               }
               else
               {
               	$settings['pages_top'] .= ' <a href="gbook.php?page='.$i.'">'.$i.'</a> ';
               }
            }
        }

        if ($next_page)
        {
        	if ($next_page != $pages)
            {
	            $settings['pages_top'] .= ' <a href="gbook.php?page='.$next_page.'">'.$lang['t04'].'</a>';
            }
        	$settings['pages_top'] .= ' <a href="gbook.php?page='.$pages.'">'.$lang['t05'].'</a>';
        }

    } // END If $pages > 1

} // END If $total > 0

printTopHTML();

if ($total == 0)
{
    include($settings['tpl_path'].'no_comments.php');
}
else
{
	printEntries($lines,$start,$end);
}

printDownHTML();
exit();


/***** START FUNCTIONS ******/

function approveEntry()
{
	global $settings, $lang;

	$approve = intval($_GET['do']);

	$hash = gbook_input($_GET['id'],$lang['e24']);
	$hash = preg_replace('/[^a-z0-9]/','',$hash);
	$file = 'apptmp/'.$hash.'.txt';

	/* Check if the file hash is correct */
	if (!file_exists($file))
	{
   		problem($lang['e25']);
	}

	/* Reject the link */
	if (!$approve)
	{
		define('NOTICE',$lang['t87']);
	}
	else
	{
		$addline = file_get_contents($file);
		$links = file_get_contents($settings['logfile']);
		if ($links === false)
		{
			problem($lang['e18']);
		}

		$addline .= $links;

		$fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
		fputs($fp,$addline);
		fclose($fp);
		define('NOTICE',$lang['t86']);
	}

    /* Delete the temporary file */
	unlink($file);

} // END approveEntry()





function check_secnum($secnumber,$checksum)
{
	global $settings, $lang;
	$secnumber.=$settings['filter_sum'].date('dmy');
	if ($secnumber == $checksum)
	{
		unset($_SESSION['checked']);
		return true;
	}
	else
	{
		return false;
	}
} // END check_secnum


function filter_bad_words($text)
{
	global $settings, $lang;
	$file = 'badwords/en.php';

	if (file_exists($file))
	{
		include_once($file);
	}
	else
	{
		problem($lang['e14']);
	}

	foreach ($settings['badwords'] as $k => $v)
	{
		$text = preg_replace("/\b$k\b/i",$v,$text);
	}

	return $text;
} // END filter_bad_words


function showPrivate()
{
	global $settings, $lang;

    $error_buffer = '';

	$num = isset($_POST['num']) ? intval($_POST['num']) : false;
    if ($num === false)
    {
    	problem($lang['e02']);
    }

    /* Check password */
    if (empty($_POST['pass']))
    {
    	$error_buffer .= $lang['e09'];
    }
    elseif ( gbook_input($_POST['pass']) != $settings['apass'] )
    {
    	$error_buffer .= $lang['e15'];
    }

    /* Any errors? */
    if ($error_buffer)
    {
    	confirmViewPrivate($error_buffer);
    }

	/* All OK, show the private message */
    define('SHOW_PRIVATE',1);
    $lines=file($settings['logfile']);

    printTopHTML();
    printEntries($lines,$num+1,$num+1);
    printDownHTML();

} // END showPrivate


function confirmViewPrivate($error='')
{
	global $settings, $lang;
	$num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : false;
    if ($num === false)
    {
    	problem($lang['e02']);
    }

    $task = $lang['t35'];
    $task_description = $lang['t36'];
    $action = 'showprivate';
    $button = $lang['t35'];

    printTopHTML();
    require($settings['tpl_path'].'admin_tasks.php');
    printDownHTML();

} // END confirmViewPrivate


function processsmileys($text)
{
	global $settings, $lang;

    /* File with emoticon settings */
	require($settings['tpl_path'].'emoticons.php');

	/* Replace some custom emoticon codes into GBook compatible versions */
	$text = preg_replace("/([\:\;])\-([\)op])/ie","str_replace(';p',':p','\\1'.strtolower('\\2'))",$text);
	$text = preg_replace("/([\:\;])\-d/ie","str_replace(';D',':D','\\1'.'D')",$text);

	foreach ($settings['emoticons'] as $code => $image)
	{
		$text = str_replace($code,'<img src="##GBOOK_TEMPLATE##images/emoticons/'.$image.'" border="0" alt="'.$code.'" title="'.$code.'" />',$text);
	}

	return $text;
} // END processsmileys


function doDelete()
{
	global $settings, $lang;

    $error_buffer = '';

	$num = isset($_POST['num']) ? intval($_POST['num']) : false;
    if ($num === false)
    {
    	problem($lang['e02']);
    }

    /* Check password */
    if (empty($_POST['pass']))
    {
    	$error_buffer .= $lang['e09'];
    }
    elseif ( gbook_input($_POST['pass']) != $settings['apass'] )
    {
    	$error_buffer .= $lang['e16'];
    }

    /* Any errors? */
    if ($error_buffer)
    {
    	confirmDelete($error_buffer);
    }

	/* All OK, delete the message */
	$lines=file($settings['logfile']);

    /* Ban poster's IP? */
	if (isset($_POST['addban']) && $_POST['addban']=='YES')
    {
	    gbook_banIP(trim(array_pop(explode("\t",$lines[$num]))));
	}

	unset($lines[$num]);

	$lines = implode('',$lines);
	$fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
	fputs($fp,$lines);
	fclose($fp);

	define('NOTICE', $lang['t37']);

} // END doDelete


function confirmDelete($error='')
{
	global $settings, $lang;
	$num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : false;
    if ($num === false)
    {
    	problem($lang['e02']);
    }

    $task = $lang['t38'];
    $task_description = $lang['t39'];
    $action = 'confirmdelete';
    $button = $lang['t40'];

    $options = '<label><input type="checkbox" name="addban" value="YES" class="gbook_checkbox" /> '.$lang['t23'].'</label>';

    printTopHTML();
    require($settings['tpl_path'].'admin_tasks.php');
    printDownHTML();

} // END confirmDelete


function check_mail_url()
{
	global $settings, $lang;
	$v = array('email' => '','url' => '');
	$char = array('.','@');
	$repl = array('&#46;','&#64;');

	$v['email']=htmlspecialchars($_POST['email']);
	if (strlen($v['email']) > 0 && !(preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$v['email'])))
    {
    	$v['email']='INVALID';
    }
	$v['email']=str_replace($char,$repl,$v['email']);

	if ($settings['use_url'])
	{
	    $v['url']=htmlspecialchars($_POST['url']);
	    if ($v['url'] == 'http://' || $v['url'] == 'https://') {$v['url'] = '';}
	    elseif (strlen($v['url']) > 0 && !(preg_match("/(http(s)?:\/\/+[\w\-]+\.[\w\-]+)/i",$v['url'])))
        {
        	$v['url'] = 'INVALID';
        }
	}
	elseif (!empty($_POST['url']))
	{
	    $_SESSION['block'] = 1;
	    problem($lang['e01'],0);
	}
	else
	{
	    $v['url'] = '';
	}

	return $v;
} // END check_mail_url


function addEntry()
{
	global $settings, $lang, $myfield;

    /* This part will help prevent multiple submissions */
    if ($settings['one_per_session'] && $_SESSION['add'])
    {
        problem($lang['e17'],0);
    }

    /* Check for obvious SPAM */
	if (!empty($_POST['name']) || isset($_POST['comments']) || !empty($_POST[$myfield['bait']]) || ($settings['use_url']!=1 && isset($_POST['url'])) )
	{
		gbook_banIP(gbook_IP(),1);
	}

	$name = gbook_input($_POST[$myfield['name']]);
	$from = gbook_input($_POST['from']);

    $a     = check_mail_url();
    $email = $a['email'];
    $url   = $a['url'];

    $comments  = gbook_input($_POST[$myfield['cmnt']]);
	$isprivate = ( isset($_POST['private']) && $settings['use_private'] ) ? 1 : 0;

    $sign_isprivate = $isprivate ? 'checked="checked"' : '';
    $sign_nosmileys = isset($_REQUEST['nosmileys']) ? 'checked="checked"' : 1;

    $error_buffer = '';

	if (empty($name))
	{
        $error_buffer .= $lang['e03'].'<br class="clear" />';
	}
	if ($email=='INVALID')
	{
        $error_buffer .= $lang['e04'].'<br class="clear" />';
        $email = '';
	}
	if ($url=='INVALID')
	{
        $error_buffer .= $lang['e05'].'<br class="clear" />';
        $url = '';
	}
	if (empty($comments))
	{
        $error_buffer .= $lang['e06'].'<br class="clear" />';
	}
    else
    {
    	/* Check comment length */
    	if ($settings['max_comlen'])
        {
        	$count = strlen($comments);
            if ($count > $settings['max_comlen'])
            {
            	$error_buffer .= sprintf($lang['t73'],$settings['max_comlen'],$count).'<br class="clear" />';
            }
        }

		/* Don't allow flooding with too much emoticons */
        if ($settings['smileys'] == 1 && !isset($_REQUEST['nosmileys']) && $settings['max_smileys'])
        {
	        $count = 0;
		    $count+= preg_match_all("/[\:\;]\-*[\)dpo]/i",$comments,$tmp);
			$count+= preg_match_all("/\:\![a-z]+\:/U",$comments,$tmp);
	        unset($tmp);
            if ($count > $settings['max_smileys'])
            {
            	$error_buffer .= sprintf($lang['t74'],$settings['max_smileys'],$count).'<br class="clear" />';
            }
        }
    }

    /* Use a logical anti-SPAM question? */
    $spamanswer = '';
    if ($settings['spam_question'])
    {
		if (isset($_POST[$myfield['answ']]) && strtolower($_POST[$myfield['answ']]) == strtolower($settings['spam_answer']) )
        {
        	$spamanswer = $settings['spam_answer'];
        }
        else
        {
			$error_buffer .= $lang['t67'].'<br class="clear" />';
        }
    }

	/* Use security image to prevent automated SPAM submissions? */
	if ($settings['autosubmit'])
	{
		$mysecnum = isset($_POST['mysecnum']) ? intval($_POST['mysecnum']) : 0;
		if (empty($mysecnum))
		{
            $error_buffer .= $lang['e07'].'<br class="clear" />';
		}
        else
        {
			require('secimg.inc.php');
			$sc=new PJ_SecurityImage($settings['filter_sum']);
			if (!($sc->checkCode($mysecnum,$_SESSION['checksum'])))
	        {
	            $error_buffer .= $lang['e08'].'<br class="clear" />';
			}
        }
	}

    /* Any errors? */
    if ($error_buffer)
    {
    	printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,$error_buffer,$spamanswer);
    }

	/* Check the message with JunkMark(tm)? */
	if ($settings['junkmark_use'])
	{
		$junk_mark = JunkMark($name,$from,$email,$url,$comments);

		if ($settings['junkmark_ban100'] && $junk_mark == 100)
        {
			gbook_banIP(gbook_IP(),1);
		}
        elseif ($junk_mark >= $settings['junkmark_limit'])
		{
			$_SESSION['block'] = 1;
			problem($lang['e01'],0);
		}
	}

    /* Everthing seems fine, let's add the message */
	$delimiter="\t";
	$m = date('m');
	if (isset($lang['m'.$m]))
	{
		$added = $lang['m'.$m] . date(" j, Y");
	}
	else
	{
		$added = date("F j, Y");
	}

    /* Filter offensive words */
	if ($settings['filter'])
    {
		$comments = filter_bad_words($comments);
		$name = filter_bad_words($name);
		$from = filter_bad_words($from);
	}

    /* Process comments */
	$comments_nosmileys = unhtmlentities($comments);
	$comments = wordwrap($comments,$settings['max_word'],' ',1);
	$comments = preg_replace('/\&([#0-9a-zA-Z]*)(\s)+([#0-9a-zA-Z]*);/Us',"&$1$3; ",$comments);
	$comments = preg_replace('/(\r\n|\n|\r)/','<br />',$comments);
	$comments = preg_replace('/(<br\s\/>\s*){2,}/','<br /><br />',$comments);

    /* Process emoticons */
    if ($settings['smileys'] == 1 && !isset($_REQUEST['nosmileys']))
    {
    	$comments = processsmileys($comments);
    }

    /* Create the new entry and add it to the entries file */
	$addline = $name.$delimiter.$from.$delimiter.$email.$delimiter.$url.$delimiter.$comments.$delimiter.$added.$delimiter.$isprivate.$delimiter.'0'.$delimiter.$_SERVER['REMOTE_ADDR']."\n";

    /* Prepare for e-mail... */
    $name = unhtmlentities($name);
    $from = unhtmlentities($from);

    /* Manually approve entries? */
    if ($settings['man_approval'])
    {
		$tmp = md5($_SERVER['REMOTE_ADDR'].$settings['filter_sum']);
		$tmp_file = 'apptmp/'.$tmp.'.txt';

		if (file_exists($tmp_file))
		{
			problem($lang['t81']);
		}

		$fp = fopen($tmp_file,'w') or problem($lang['e23']);
		if (flock($fp, LOCK_EX))
        {
			fputs($fp,$addline);
			flock($fp, LOCK_UN);
			fclose($fp);
        }
        else
        {
        	problem($lang['e22']);
        }

		$char = array('.','@');
		$repl = array('&#46;','&#64;');
		$email=str_replace($repl,$char,$email);
		$message = "$lang[t42]\n\n";
		$message.= "$lang[t82]\n\n";
		$message.= "$lang[t17] $name\n";
		$message.= "$lang[t18] $from\n";
		$message.= "$lang[t20] $email\n";
		$message.= "$lang[t19] $url\n";
		$message.= "$lang[t44]\n";
		$message.= "$comments_nosmileys\n\n";
		$message.= "$lang[t83]\n";
		$message.= "$settings[gbook_url]?id=$tmp&a=approve&do=1\n\n";
		$message.= "$lang[t84]\n";
		$message.= "$settings[gbook_url]?id=$tmp&a=approve&do=0\n\n";
		$message.= "$lang[t46]\n";

		mail($settings['admin_email'],$lang['t41'],$message,"Content-type: text/plain; charset=".$lang['enc']);

		/* Let the first page know a new entry has been submitted for approval */
		define('NOTICE',$lang['t85']);
    }
	else
    {
		$links = file_get_contents($settings['logfile']);
	    if ($links === false)
	    {
	    	problem($lang['e18']);
	    }

		$addline .= $links;

	    $fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
		fputs($fp,$addline);
		fclose($fp);

	    if ($settings['notify'] == 1)
		{
		    $char = array('.','@');
		    $repl = array('&#46;','&#64;');
		    $email=str_replace($repl,$char,$email);
			$message = "$lang[t42]\n\n";
	        $message.= "$lang[t43]\n\n";
	        $message.= "$lang[t17] $name\n";
	        $message.= "$lang[t18] $from\n";
	        $message.= "$lang[t20] $email\n";
	        $message.= "$lang[t19] $url\n";
	        $message.= "$lang[t44]\n";
	        $message.= "$comments_nosmileys\n\n";
	        $message.= "$lang[t45]\n";
	        $message.= "$settings[gbook_url]\n\n";
	        $message.= "$lang[t46]\n";

		    mail($settings['admin_email'],$lang['t41'],$message,"Content-type: text/plain; charset=".$lang['enc']);
		}


		/* Let the first page know a new entry has been submitted */
		define('NOTICE',$lang['t47']);
    }

	/* Register this session variable */
	$_SESSION['add']=1;

    /* Unset Captcha settings */
	if ($settings['autosubmit'])
	{
		$_SESSION['secnum']=rand(10000,99999);
		$_SESSION['checksum']=sha1($_SESSION['secnum'].$settings['filter_sum']);
		gbook_session_regenerate_id();
    }

} // END addEntry


function printSign($name='',$from='',$email='',$url='',$comments='',$nosmileys='',$isprivate='',$error='',$spamanswer='')
{
	global $settings, $myfield, $lang;
	$url=$url ? $url : 'http://';

    /* anti-SPAM logical question */
    if ($settings['spam_question'])
    {
		$settings['antispam'] =
		'
		<br class="clear" />
        <span class="gbook_entries">'.$settings['spam_question'].'</span><br class="clear" />
		<input type="text" name="'.$myfield['answ'].'" size="45" value="'.$spamanswer.'" />
		';
    }
    else
    {
		$settings['antispam'] = '';
    }

    /* Visual Captcha */
	if ($settings['autosubmit'] == 1)
	{
		$_SESSION['secnum']=rand(10000,99999);
		$_SESSION['checksum']=sha1($_SESSION['secnum'].$settings['filter_sum']);
		gbook_session_regenerate_id();

	    $settings['antispam'] .=
        '
		<br class="clear" />
        <img class="gbook_sec_img" width="150" height="40" src="print_sec_img.php" alt="'.$lang['t62'].'" title="'.$lang['t62'].'" /><br class="clear" />
		<span class="gbook_entries">'.$lang['t56'].'</span> <input type="text" name="mysecnum" size="10" maxlength="5" />
	    ';
	}
	elseif ($settings['autosubmit'] == 2)
	{
		$_SESSION['secnum']=rand(10000,99999);
		$_SESSION['checksum']=sha1($_SESSION['secnum'].$settings['filter_sum']);
		gbook_session_regenerate_id();

	    $settings['antispam'] .=
        '
		<br class="clear" />
        <br class="clear" />
        <span class="gbook_entries"><b>'.$_SESSION['secnum'].'</b></span><br class="clear" />
		<span class="gbook_entries">'.$lang['t56'].'</span> <input type="text" name="mysecnum" size="10" maxlength="5" />
	    ';
	}

    printTopHTML();
    require($settings['tpl_path'].'sign_form.php');
    printDownHTML();

} // END printSign


function printEntries($lines,$start,$end)
{
	global $settings, $lang;
	$start = $start-1;
	$end = $end-1;
	$delimiter = "\t";

    $template = file_get_contents($settings['tpl_path'].'comments.php');

	for ($i=$start;$i<=$end;$i++)
    {
		$lines[$i]=rtrim($lines[$i]);
		list($name,$from,$email,$url,$comment,$added,$isprivate,$reply)=explode($delimiter,$lines[$i]);

		if (!empty($isprivate) && !empty($settings['use_private']) && !defined('SHOW_PRIVATE'))
		{
			$comment = '
			<br class="clear" />
			<i><a href="gbook.php?a=viewprivate&amp;num='.$i.'">'.$lang['t58'].'</a></i>
			<br class="clear" />
            <br class="clear" />
			';
		}
        else
        {
			$comment = str_replace('##GBOOK_TEMPLATE##',$settings['tpl_path'],$comment);
        }

		if (!empty($reply))
		{
			$comment .= '<br class="clear" /><br class="cl