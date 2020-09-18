<?php
/*
 * Template Name: Soon
 */
get_header(); ?>

<div>
	<img src="<?php echo IMG.'/logo.svg'; ?>" alt="" class="logo">
	<h1>Coming soon...</h1>
</div>

<style>
	body{
		display: flex;
		align-items: center;
		justify-content: center;
		height: 100vh;
		margin: 0;
		padding: 0;
	}
	.main-header, .main-footer{
		display: none;
	}
	.logo{
		position: fixed;
		top: 30px; left: 50%;
		transform: translate(-50%, 0);
	}
</style>

<?php get_footer();