<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage SitRobotSit
 * @since SitRobotSit 1.0
 */
?><!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'sitrobotsit' ), max( $paged, $page ) );

	?></title>



<link rel="profile" href="http://gmpg.org/xfn/11" />


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
	
	
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
</head>

<body <?php body_class(); ?>>
<div id="container-nav">
    <section id="nav" class="group wrapper">
        <h3>Sit Robot Sit</h3>


		<!--<input type="checkbox" id="toggle"/>
        <label for="toggle" onclick>Menu</label>

        <nav>
            <ul>
                <li><a href="#portfolio">Portfolio</a></li>
                <li><a href="#resume">Resume</a></li>
            </ul>
        </nav>-->
    </section>
</div>


