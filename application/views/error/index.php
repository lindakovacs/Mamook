<?php /* application/views/error/index.php */

echo '<section id="main" class="main">',
	'<div class="main-1">',
		# Get the main content.
		$display_content,
	'</div>',
	'<div class="main-2">',
		# Display other content (forms).
		$display,
		# Display the error passed via GET data if this is a development server.
		$dev_display,
		$display_quote,
	'</div>',
	'<div class="main-3"></div>',
'</section>',

'<div id="box1" class="box1">',
	'<div id="box1a">',
	'</div>',
	'<div id="box1b">',
	'</div>',
	'<div id="box1c">',
	'</div>',
'</div>',

'<div id="menu2" class="box2">',
'</div>';
