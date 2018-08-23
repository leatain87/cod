<?php
/**
 * @version                $Id: index.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package                Joomla.Site
 * @subpackage	Templates.LiveInNZ-Custom
 * @copyright        Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >

<head>
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>
	$(function() {
		var pull 		= $('#mob-only');
			menu 		= $('#menu ul');
			menuHeight	= menu.height();

		$(pull).on('click', function(e) {
			e.preventDefault();
			menu.slideToggle();
		});

		$(window).resize(function(){
			var w = $(window).width();
			if(w > 540 && menu.is(':hidden')) {
				menu.removeAttr('style');
			}
		});
	});
</script>
<meta name="nzs-verify" content="CLAIM-5418e9113eab6" />
<jdoc:include type="head" />

<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/style.css" type="text/css" />

<!--[if lte IE 6]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<![endif]-->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-2762261-10', 'countrywidedist.co.nz');
  ga('send', 'pageview');

</script>

<script type="text/javascript">
  (function(d) {
    var config = {
      kitId: 'aad0vok',
      scriptTimeout: 3000
    },
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='//use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);
</script>

</head>
<body id="bg">
<a name="up" id="up"></a>

<div class="wrapper">
		<div id="header">
    		<div id="logo"><a href="<?php echo $this->baseurl ?>" ><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/logo.png" alt="Countrywide Distributors" /></a></div>
            <div id="menu">
				<a href="#" id="mob-only"></a>
				<jdoc:include type="modules" name="top" />
			</div>
            <div class="clear"></div>
    	</div>
	
	<?php if($this->countModules('banner')) : ?>
		<div id="banner">
			<jdoc:include type="modules" name="banner" style="xhtml" />
		</div>
	<?php endif; ?>
    
    <div id="main">
   		<jdoc:include type="component" />
		<div class="clear"></div>
    </div>
	 
<?php if($this->countModules('insidepagebanner')) : ?>
		<div id="banner">
			<jdoc:include type="modules" name="insidepagebanner" style="xhtml" />
		</div>
	<?php endif; ?>
	 
<?php
$app = JFactory::getApplication();
$menu = $app->getMenu();
if ($menu->getActive() == $menu->getDefault()) { ?>

<ul id="eight" class="caption-style-2">
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-chips.jpg" alt="Food Service" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Potato Chips</h3>
			 <p>Mr Chips, Talley's, Markwell Foods</p>
		 </div>
	 </div>
 </li>
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-prawns.jpg" alt="Food Industry" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Frozen Seafood</h3>
			 <p>Markwell Foods, United Seafood, United Fisheries, Independent Fisheries </p>
		 </div>
	 </div>
 </li>
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-chicken.jpg" alt="Food Company" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Poultry</h3>
			 <p>Tegel, Inghams</p>
		 </div>
	 </div>
 </li>
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-meats.jpg" alt="Food Service Distributor" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Meat</h3>
			 <p>Hellers, NZ Meat Market</p>
		 </div>
	 </div>
 </li>
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-baking.jpg" alt="Food Service" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Dry Goods</h3>
			 <p>Bakels, Heinz Watties, Nestle, Unilever Food Solutions</p>
		 </div>
	 </div>
 </li>
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-desserts.jpg" alt="Food Industry" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Desserts &amp; Ice Creams</h3>
			 <p>Florentines, Kiwi, Talley's, Gourmet, Kapiti</p>
		 </div>
	 </div>
 </li>
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-peas.jpg" alt="Food Company" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Frozen Vegetables</h3>
			 <p>Talley's, Watties, Markwell Foods</p>
		 </div>
	 </div>
 </li>
 <li>
	<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/e-pies.jpg" alt="Food Service Distributor" />
	<div class="caption">
		 <div class="blur"></div>
		 <div class="caption-text">
			 <h3>Countrywide Products</h3>
			 <p>Pies, Cherries, Cakes, Meat &amp; more</p>
		 </div>
	 </div>
 </li>
 <div class="clear"></div>
</ul>

<?php if($this->countModules('insidebanner')) : ?>
		<div id="banner">
			<jdoc:include type="modules" name="insidebanner" style="xhtml" />
		</div>
	<?php endif; ?>

<ul id="eight" class="caption-style-3">
<div class="wrapper" align="center">
<p>&nbsp;</p>
	<h1>Our Customers</h1>
<p>&nbsp;</p>
</div>
	<li><img src="/templates/cc_custom2/images/icon-01.jpg" alt="Food Service" />
		<div class="caption">
			<div class="caption-text">
				<p>Hotels/Motels, Holiday Parks, Bed &amp; Breakfasts</p>
			</div>
		</div>
	</li>
	<li><img src="/templates/cc_custom2/images/icon-02.jpg" alt="Food Industry" />
		<div class="caption">
			<div class="caption-text">
				<p>Wholesalers, Food &amp; Dairy Processing, Wineries</p>
			</div>
		</div>
	</li>
	<li><img src="/templates/cc_custom2/images/icon-03.jpg" alt="Food Industry" />
		<div class="caption">
			<div class="caption-text">
				<p>Hospitals, Nursing Homes, Retirement Villages</p>
			</div>
		</div>
	</li>
	<li><img src="/templates/cc_custom2/images/icon-04.jpg" alt="Food Service" />
		<div class="caption">
			<div class="caption-text">
				<p>Correctional Centres, Prisons, Defense Forces, Airlines</p>
			</div>
		</div>
	</li>
	<li><img src="/templates/cc_custom2/images/icon-05.jpg" alt="Food Industry" />
		<div class="caption">
			<div class="caption-text">
				<p>Schools, Universities &amp; Polytechnics, Early Childcare</p>
			</div>
		</div>
	</li>
	<li><img src="/templates/cc_custom2/images/icon-06.jpg" alt="Food Industry" />
		<div class="caption">
			<div class="caption-text">
				<p>Restaurants, Food Bars, Coffee Shops, Takeaways, KFC, McDonalds</p>
			</div>
		</div>
	</li>
<div class="clear"></div>
</ul>


<?php } else { } ?>
	 

    <div id="footer">
		<div class="moduletable">
			<jdoc:include type="modules" name="footer" style="xhtml" />
			<div class="clear">&nbsp;</div>
			<div id="flogo"><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/logo-sm.png" alt="Countrywide Distributors" /></div>
			<p class="copyr"><a target="_blank" href="http://www.alexanders.co.nz/web-design-christchurch.html">Web Design Christchurch</a> by Alexanders Internet Marketing</p>
			<p class="copyr" style="padding-right:245px;"><a target="_blank" href="/privacy-policy">Privacy Policy</a></p>
    	</div>
		<div id="fline"></div>
    </div>
</div>
    <!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PB7SJV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PB7SJV');</script>
<!-- End Google Tag Manager -->

</body>
</html>