<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage SitRobotSit
 * @since Sit Robot Sit 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

    <!-- Viewport meta tag to prevent iPhone from scaling our page -->
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" href="/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" href="/css/style.css"/>

    <!-- Normalize hide address bar for iOS and Android -->
    <script src="/js/hideaddressbar.js"></script>

    <!-- Add media query support for IE8 and under. Must place media queries in external stylesheet -->
    <script src="/js/respond.min.js"></script>

    <!-- Using picturefill to load conditional images and matchmedia to provide support for IE8 and below + older browsers -->
    <script src="/js/picturefill.js"></script>
    <script src="/js/matchmedia.js"></script>

    <!--[if lt IE 9]>
	<script src="/js/html5.js"></script>
    <![endif]-->

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>

    <script src="/js/scripts.js"></script>

    <!--[if IE 8]>
	<script src="/js/checkbox.js"></script>
    <![endif]-->

    <!--[if IE 6]>
	<script src="/js/selectivizr-min.js"></script>
    <![endif]-->

    <script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/2bfcc9d75f15bf188923670bd/07b2cae3dd3e0bd8b61f93f26.js");</script>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="container-nav">
    <section id="nav" class="group wrapper">

       <!-- <img src="/img/blog-header.png" alt="Sit Robot Sit Logo" width="158" height="158">
-->
        <h3>Sit Robot Sit</h3>

  <!--      <nav>
            <ul>
                <li><a href="#portfolio">Portfolio</a></li>
                <li><a href="#resume">Resume</a></li>
            </ul>
        </nav>-->
    </section>
</div>

<div id="container-content">
