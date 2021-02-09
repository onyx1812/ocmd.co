<?php
$testimonials = [
  [
    "title" => "Does Just What It Says",
    "description" => "“This cream is lovely to use. Its rich and creamy texture is just what I needed to lift my skin and to lift my spirits. Using this cream is a luxury as it does just what it says on the pot, and leaves my skin moisturised, supple and firmer.”",
    "name" => "Eileen B."
  ],
  [
    "title" => "Crows Feet And Brow Lines Have Faded, [Skin] Texture Is Improved…",
    "description" => "“I am entering my 70th year this month. I have been using [the cream] for about 6 weeks. Crow’s feet have faded, horizontal brow lines have faded. Skin texture has significantly improved.”",
    "name" => "Joy M."
  ],
  [
    "title" => "Reduced Fine Lines In Only 4 Days… ",
    "description" => "“I really love this product. I have only been using for 4 days, but it has cleared up blemishes, and reduced some fine lines around my mouth and eyes. I ordered the booster serum to go with it. Cannot wait to see the difference in a month from now!”",
    "name" => "Mikaela N."
  ],
  [
    "title" => "At First I Was Skeptical, But Then…",
    "description" => "“At first I was skeptical reading reviews online. But my skepticism was short lived. I am now on to my 3rd jar, my skin feels softer, smoother and looks rejuvenated. I have had compliments passed about my skin.”",
    "name" => "Susan B."
  ],
  [
    "title" => "Greatly Improved Areas About My Eyes, Forehead, and Mouth",
    "description" => "“This is an amazing product this is my third jar of the age defying cream. I am 59 years old and have noticed a much improved difference in my skin. It looks brighter and has greatly improved areas around my eyes, forehead and mouth. I will definitely continue with this cream it's worth every penny!!!” ”",
    "name" => "Kathryn S."
  ]
];
?>
<section class="testi">
  <div class="container">
    <h2>OCMD <br> Customer Testimonials</h2>
    <p>*Results may vary by individual</p>
    <div class="testi-sec">
      <?php foreach ($testimonials as $testi): ?>
        <div class="testi-bx">
          <span><?php echo $testi["name"][0]; ?></span>
          <h3><?php echo $testi["title"]; ?></h3>
          <ul class="stars-rating"><li class="stars-f"></li><li class="stars-f"></li><li class="stars-f"></li><li class="stars-f"></li><li class="stars-f"></li></ul>
          <p><?php echo $testi["description"]; ?></p>
          <p class="testi-txt3"><?php echo $testi["name"]; ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>