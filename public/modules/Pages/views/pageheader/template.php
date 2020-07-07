<?php

//view
$view = '
<div id="topbar">
	<div class="inner">
		<div class="content">
			<div class="right">
				{{^user}}
					<a href="/login" class="signin buttonLink gray"><i class="fa fa-sign-in"></i> Sign In</a>
				{{/user}}
				{{#user}}
					Welcome {{username}} <a href="/do/logout">Logout</a>
				{{/user}}
				<a href="" class="cart buttonLink"><i class="fa fa-shopping-cart"></i> $29.95 (2)</a>
			</div>
			<div class="left">
				<span class="call_cta"><strong>NutriHack</strong> <i class="fa fa-mobile"></i> 414.123.1234</span>
			</div>
		</div>
	</div>
</div>
<div id="navbar">
	<div class="inner">
		<a href="/" id="logo">NutriHack</a>
		<ul class="main_nav">
			<li><a href="/page/do"><i class="fa fa-eye"></i> Learn</a></li>
			<li><a href="/page/hack"><i class="fa fa-gears"></i> Hack</a></li>
			<li class="last"><a href="/page/go"><i class="fa fa-shopping-cart"></i> Get Some</a></li>
		</ul>
		<div id="msearch">
			<i class="fa fa-search"></i>
		</div>
		<div class="shadow"><!-- decal --></div>	
	</div>
</div>
';