<?php
// No direct access.
defined('_JEXEC') or die;;
$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$menu 			 = $app->getMenu();
$this->language  = $doc->language;
$this->direction = $doc->direction;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >

<head>
<?php if (!isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') === false): ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PB7SJV');</script>
<!-- End Google Tag Manager -->
<?php endif; ?>  
  
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="nzs-verify" content="CLAIM-5418e9113eab6" />
    <jdoc:include type="head" />
    <?php
    // Add Stylesheet
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/vendor/bootstrap/css/bootstrap.min.css');
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/vendor/animate/css/animate.min.css');
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/vendor/font-awesome/css/font-awesome.min.css');
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/vendor/icomoon/icomoon.css');
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/vendor/owlcarousel/css/owl.carousel.min.css');
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/style.css');
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/all.css');
    $doc->addScriptDeclaration('var baseRoot = \''.JURI::root().'\'');
    $doc->addScript(JURI::root() . 'components/com_rsform/assets/js/script.js');

    ?>

  
  </head>
<body >
    <?php if (!isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') === false): ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PB7SJV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
 <?php endif; ?>  
<!-- Disable Loader
<div class="loader">
    <div class="cssload-loader">
        <div class="cssload-inner cssload-one"></div>
        <div class="cssload-inner cssload-two"></div>
        <div class="cssload-inner cssload-three"></div>
    </div>
</div>
-->
<div id="up_scroll"><i class="fa fa-angle-up"></i></div>
<!--- Header --->
<div id="header">
    <div class="container">
        <div class="row">
            <div class="col-md-4" id="logo"><a href="<?php echo $this->baseurl ?>" ><img class="img-fluid" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/logo.png" alt="Countrywide Distributors" /></a></div>
            <?php if($this->countModules('top')): ?>
                <div class="col-md-8" id="top-header">
                    <jdoc:include type="modules" name="top" />
                </div>
            <?php endif; ?>
        </div>
        <?php if($this->countModules('menu')): ?>
            <div id="menu-navigation">
                <div class="nav-mobile">
                    <a href="javascript:void(0)" id="mob-only">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </a>

                </div>
                <div class="clearfix"></div>
                <jdoc:include type="modules" name="menu" />
                <div class="clearfix"></div>
            </div>
        <?php endif;  ?>
    </div>
</div>
<div class="main-content">
    <?php if($this->countModules('banner')){ ?>
        <div class="banner"><jdoc:include type="modules" name="banner" /></div>
    <?php } ?>
    <?php if ($menu->getActive() == $menu->getDefault()) { ?>
            <jdoc:include type="component" />
    <?php } else{ ?>
           <div class="container">
               <div class="row">
                   <div class="col-md-12">
                       <jdoc:include type="component" />
                   </div>
               </div>
           </div>
    <?php } ?>
    <?php $searchword = JRequest::getString('searchword') ?>
        <?php $objJURI = JFactory::getURI();

       // echo$objJURI->getQuery();
        ?>
        <?php if($this->countModules('content_bottom')): ?>
                <?php if(!preg_match('/searchword/',$objJURI->getQuery())){ ?>
                 <jdoc:include type="modules" name="content_bottom"  />
                <?php } ?>
        <?php endif ?>

</div>

<?php if($this->countModules('footer_first') ||
         $this->countModules('footer_second') ||
         $this->countModules('footer_third') ||
         $this->countModules('footer_four')
      ):
    ?>
<footer class="main-footer">
    <div class="container">
        <div class="row">
            <?php if($this->countModules('footer_first')): ?>
                <div class="col-md-3">
                    <jdoc:include type="modules" name="footer_first"  />
                </div>
            <?php endif; ?>
            <?php if($this->countModules('footer_second')): ?>
                <div class="col-md-3">
                    <jdoc:include type="modules" name="footer_second" />
                </div>
            <?php endif; ?>
            <?php if($this->countModules('footer_third')): ?>
                <div class="col-md-3">
                    <jdoc:include type="modules" name="footer_third"  />
                </div>
            <?php endif; ?>
            <?php if($this->countModules('footer_four')): ?>
                <div class="col-md-3">
                    <jdoc:include type="modules" name="footer_four"  />
                </div>
            <?php endif; ?>
        </div>
    </div>
</footer>
<?php endif; ?>

<?php
//Add Script
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/js/jquery-1.12.4.min.js');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/vendor/bootstrap/js/tether.min.js');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/vendor/bootstrap/js/bootstrap.min.js');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/vendor/owlcarousel/js/owl.carousel.min.js');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/vendor/wow/wow.min.js');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/vendor/imagemapster/js/jquery.imagemapster.js');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/js/script.js');
?>

</body>
</html>



