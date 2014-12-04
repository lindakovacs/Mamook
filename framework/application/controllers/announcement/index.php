<?php /* public/announcement/index.php */

# Get the SubContent Class.
require_once Utility::locateFile(MODULES.'Content'.DS.'SubContent.php');

# Create display variables.
$display_main1='';
$display_main2='';
$display_main3='';
$display_box1a='';
$display_box1b='';
$display_box1c='';
$display_box2='';

$page_class='announcementpage';
$branch='Announcement';
$branch_nav='';
$display='';
$display_file='';

# Create a new SubContent object.
$subcontent=new SubContent();
# Get the branch subcontent display and set it to a variable.
$display_subcontent=$subcontent->displayBranchSubContent($branch);
# Set the page title to the post's title.
$page_title=$subcontent->getPostTitleDisplay();
if(!empty($page_title))
{
	$main_content->setPageTitle($page_title);
}

# Get the page title and subtitle to display in main-1.
$display_main1=$main_content->displayTitles();

# Get the main content to display in main-2. The "image_link" variable is defined in data/init.php.
$display_main2=$main_content->displayContent($image_link);
# Add the subcontent to main-2.
$display_main2.=$display_subcontent;
# Add any display content to main-2.
$display_main2.=$display;

# Get the quote text to display in main-3.
$display_main3=$main_content->displayQuote();

# Display the file info in box1a.
$display_box1a=$display_file;

# Get the announcement navigation list.
require Utility::locateFile(TEMPLATES.'announcement_nav.php');
# Set the "branch_nav" variable from the announcement_nav template to the display_box2 variable for display in the view.
$display_box2=$branch_nav;

/*
 ** In the page template we
 ** get the header
 ** get the masthead
 ** get the subnavbar
 ** get the navbar
 ** get the page view
 ** get the quick registration box
 ** get the footer
 */
require Utility::locateFile(TEMPLATES.'page.php');