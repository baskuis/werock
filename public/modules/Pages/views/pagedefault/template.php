<?php

//view
$view = '
<div id="wrapper">
	' . CoreTemplate::getView('pageheader') . '
	<div id="container">
		<div class="inner">
			<div class="content">
				<h1>{{title}}</h1>
				{{& html}}
			</div>
			{{#showTabs}}' . CoreTemplate::getView('pagetabs') . '{{/showTabs}}
		</div>	
	</div>
	' . CoreTemplate::getView('pagefooter') . '
</div>
';