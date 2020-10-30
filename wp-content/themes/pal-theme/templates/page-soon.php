<?php
/*
 * Template Name: Soon
 */
get_header(); ?>

<div>
  <img src="<?php echo IMG.'/logo.png'; ?>" alt="" class="logo">
  <h1>Coming soon...<br>OPENING on November 10</h1>
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
.logo {
  width: 200px;
  display: block;
  margin: 0 auto;
}
h1{
  text-align: center;
}
</style>

<?php get_footer();