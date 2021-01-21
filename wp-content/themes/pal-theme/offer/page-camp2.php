<?php
/*
 * Template Name: Camp 2
 */
get_header();
while ( have_posts() ) : the_post();
?>

  <section class="sold">
    <div class="container">
      <h2 class="sold-title">Hurry! Order now and save 50%off?</h2>
    </div>
  </section>

  <section class="products">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="products-sidebar">
            <div class="gallery">
              <img class="gallery-img" src="<?php echo OFFER; ?>/img/prd-slide-1-cream-new.png" width="100%">
              <ul class="gallery-nav">
                <li data-src="<?php echo OFFER; ?>/img/prd-slide-1-cream-new.png" class="active"><img
                    src="<?php echo OFFER; ?>/img/prd-slide-1-cream-new.png" alt=""></li>
                <li data-src="<?php echo OFFER; ?>/img/prd-slide-2-cream-new.png"><img
                    src="<?php echo OFFER; ?>/img/prd-slide-2-cream-new.png" alt=""></li>
                <li data-src="<?php echo OFFER; ?>/img/prd-slide-3-cream-new.png"><img
                    src="<?php echo OFFER; ?>/img/prd-slide-3-cream-new.png" alt=""></li>
              </ul>
            </div>
            <div class="solutions">
              <h4 class="solutions-title"><span>The Perfect Solution For</span></h4>
              <ul class="solutions-list">
                <li>
                  <img src="<?php echo OFFER; ?>/img/chk-s1-lft-lis-img1.png">
                  Anti-Aging
                </li>
                <li>
                  <img src="<?php echo OFFER; ?>/img/chk-s1-lft-lis-img2.png">
                  Clarifying
                </li>
                <li>
                  <img src="<?php echo OFFER; ?>/img/chk-s1-lft-lis-img3.png">
                  Lifting
                </li>
                <li>
                  <img src="<?php echo OFFER; ?>/img/chk-s1-lft-lis-img4.png">
                  Firming
                </li>
                <li>
                  <img src="<?php echo OFFER; ?>/img/chk-s1-lft-lis-img5.png">
                  Contouring
                </li>
                <li>
                  <img src="<?php echo OFFER; ?>/img/chk-s1-lft-lis-img6.png">
                  Tightening
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-8">

          <h1 class="products-title">Rejuvenation Complex Cream</h1>

          <div class="products-sub">
            Formulated by Top MD’s for Neck & Face ( <div class="stars">
              <ul class="stars-rating">
                <li class="stars-f"></li>
                <li class="stars-f"></li>
                <li class="stars-f"></li>
                <li class="stars-f"></li>
                <li class="stars-f"></li>
              </ul>
            </div> 4.9 out of 5)
          </div>

          <div class="products-desc">Unique cream creates dramatically younger skin, restores the firmness, volume & elasticity you’ve been looking for.</div>

          <ul class="row list-keys">
            <li class="col-sm-7">
              <span>
                <img src="<?php echo OFFER; ?>/img/chk-s1-rgt-list-icn1.png">
                Key Facts
              </span>
            </li>
            <li class="col-sm-5">
              <span>
                <img src="<?php echo OFFER; ?>/img/chk-s1-rgt-list-icn2.png">
                Helps With
              </span>
            </li>
          </ul>

          <ul class="row list-values">
            <li class="col-sm-7">Instantly tightens loose and sagging skin.</li>
            <li class="col-sm-5">Loose skin</li>
            <li class="col-sm-7">Adds volume & thickness to thin, papery skin.</li>
            <li class="col-sm-5">Sagging skin</li>
            <li class="col-sm-7">Restore your youthful look in weeks.</li>
            <li class="col-sm-5">Fine lines, wrinkles and “crow's feet”</li>
          </ul>
          <form method="post" action="ajax.php?method=cart" class="order-form re" id="frm" name="cart_form"
            accept-charset="utf-8" enctype="application/x-www-form-urlencoded;charset=utf-8">
            <input type="hidden" name="campaigns[1][id]" id="dynamic_input" value="">
          </form>
          <div class="products-tabs">
            <ul class="products-tabs__nav">
              <li data-tab="tab1" class="active">One Time Purchase</li>
              <li data-tab="tab2">Subscribe and Save</li>
            </ul>
            <div class="products-tabs__tab tab1 active">
              <div class="row">
                <?php $products = [1665, 1666, 1667]; ?>
                <div class="col-12 col-lg-4">
                  <div class="product">
                    <div class="product-quantity">1<br>Jar</div>
                    <div class="product-inner">
                      <div class="product-price">$59<span>/ea</span></div>
                      <div class="product-save">You Save $61</div>
                    </div>
                    <footer>
                      <a href="#" class="addToCart" id="prod1">Add To Cart</a>
                      <span>+ SHIPPING $4.95</span>
                    </footer>
                  </div>
                </div>
                <div class="col-12 col-lg-4">
                  <div class="product product-best">
                    <div class="product-quantity">6<br>Jars</div>
                    <div class="product-inner">
                      <div class="product-price">$50<span>/ea</span></div>
                      <div class="product-save">You Save $420</div>
                    </div>
                    <footer>
                      <a href="#" class="addToCart" id="prod3">Add To Cart</a>
                      <span>+ Free Shipping</span>
                    </footer>
                  </div>
                </div>
                <div class="col-12 col-lg-4">
                  <div class="product">
                    <div class="product-quantity">3<br>Jars</div>
                    <div class="product-inner">
                      <div class="product-price">$53<span>/ea</span></div>
                      <div class="product-save">You Save $201</div>
                    </div>
                    <footer>
                      <a href="#" class="addToCart" id="prod2">Add To Cart</a>
                      <span>+ Free Shipping</span>
                    </footer>
                  </div>
                </div>
              </div>
            </div>
            <div class="products-tabs__tab tab2">
              <div class="row">
                <?php $products = [1668, 1669, 1670]; ?>
                <div class="col-12 col-lg-4">
                  <div class="product">
                    <div class="product-quantity">1<br>Jar</div>
                    <div class="product-inner">
                      <div class="product-price">$53<span>/ea <span style="font-size: 70%;">Monthly</span></span></div>
                      <div class="product-save">You Save $67</div>
                    </div>
                    <footer>
                      <a href="#" class="addToCart" id="prod4">Add To Cart</a>
                      <span>+ SHIPPING $4.95</span>
                    </footer>
                  </div>
                </div>
                <div class="col-12 col-lg-4">
                  <div class="product product-best">
                    <div class="product-quantity">6<br>Jars</div>
                    <div class="product-inner">
                      <div class="product-price">$45<span>/ea <span style="font-size: 70%;">Monthly</span></span></div>
                      <div class="product-save">You Save $450</div>
                    </div>
                    <footer>
                      <a href="#" class="addToCart" id="prod6">Add To Cart</a>
                      <span>+ Free Shipping</span>
                    </footer>
                  </div>
                </div>
                <div class="col-12 col-lg-4">
                  <div class="product">
                    <div class="product-quantity">3<br>Jars</div>
                    <div class="product-inner">
                      <div class="product-price">$48<span>/ea <span style="font-size: 70%;">Monthly</span></span></div>
                      <div class="product-save">You Save $216</div>
                    </div>
                    <footer>
                      <a href="#" class="addToCart" id="prod5">Add To Cart</a>
                      <span>+ Free Shipping</span>
                    </footer>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- END .products-tabs -->

        </div>
      </div>
      <div class="promise">
        <h2 class="promise-title"><span>The OCMD Promise</span></h2>
        <div class="row">
          <div class="col-4 col-md-2">
            <img src="<?php echo OFFER; ?>/img/chk-bar-list-icn1.png">
            Paraben <br> Free
          </div>
          <div class="col-4 col-md-2">
            <img src="<?php echo OFFER; ?>/img/chk-bar-list-icn2.png">
            Fragrance <br> Free
          </div>
          <div class="col-4 col-md-2">
            <img src="<?php echo OFFER; ?>/img/chk-bar-list-icn3.png">
            Cruelty <br> Free
          </div>
          <div class="col-4 col-md-2">
            <img src="<?php echo OFFER; ?>/img/chk-bar-list-icn4.png">
            BHA <br> Free
          </div>
          <div class="col-4 col-md-2">
            <img src="<?php echo OFFER; ?>/img/chk-bar-list-icn5.png">
            Chemical <br> Free
          </div>
          <div class="col-4 col-md-2">
            <img src="<?php echo OFFER; ?>/img/chk-bar-list-icn6.png">
            Safe <br> to Use
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="details">
    <div class="container">
      <div class="resp">
        <div class="resp-sidebar  useLi">
          <h3 class="resp-title">Discover OCMD’s<br><span>Age-Defying Magic</span></h3>
          <ul class="resp-nav">
            <li data-resp="resp1" class="active">Product Details</li>
            <li data-resp="resp2">Benefits</li>
            <!-- <li data-resp="resp3">Ingredients</li> -->
            <li data-resp="resp4">How It Works</li>
            <li data-resp="resp5">Our Guarantee</li>
            <li data-resp="resp6">About OCMD</li>
          </ul>
        </div>
        <button data-respa="resp1"  class="accordion useAccordion active">Products Details</button>
        <div class="resp-tab resp1 active">
          <div class="row">
            <div class="col-lg-8">
              <h2>Product Details</h2>
              <p>The #1 age-defying “must have” cosmetic of 2020. Contains numerous age-defying secrets all in one bottle that rolls back the clock on thin, aging skin to create a more youthful look. Restore your skin and restore your confidence.</p>
            </div>
            <div class="col-lg-4">
              <img src="<?php echo OFFER; ?>/img/prd-det-prod-new.png" alt="">
            </div>
          </div>
          <h4>Experience The Breathtaking Transformation</h4>
          <div class="row list-resp">
            <div class="col-6 col-lg-3">
              <img src="<?php echo OFFER; ?>/img/trans-list-icn1.png" alt="">
              <h5>Refresh</h5>
              <p>Instantly wake up your skin.</p>
            </div>
            <div class="col-6 col-lg-3">
              <img src="<?php echo OFFER; ?>/img/trans-list-icn2.png" alt="">
              <h5>Revive</h5>
              <p>See the “honeymoon glow” in just days.</p>
            </div>
            <div class="col-6 col-lg-3">
              <img src="<?php echo OFFER; ?>/img/trans-list-icn3.png" alt="">
              <h5>Renew</h5>
              <p>Enjoy smooth, youthful skin that will last.</p>
            </div>
            <div class="col-6 col-lg-3">
              <img src="<?php echo OFFER; ?>/img/trans-list-icn4.png" alt="">
              <h5>Regain</h5>
              <p>Feel real confidence that everyone admires.</p>
            </div>
          </div>
          <h4>Works On All Skin Types</h4>
          <ul class="list-types">
            <li>
              <img src="<?php echo OFFER; ?>/img/skn-typ-lst-icn1.png">
              Normal
            </li>
            <li>
              <img src="<?php echo OFFER; ?>/img/skn-typ-lst-icn2.png">
              Oily
            </li>
            <li>
              <img src="<?php echo OFFER; ?>/img/skn-typ-lst-icn3.png">
              Dry
            </li>
            <li>
              <img src="<?php echo OFFER; ?>/img/skn-typ-lst-icn4.png">
              Combination
            </li>
            <li>
              <img src="<?php echo OFFER; ?>/img/skn-typ-lst-icn5.png">
              Sensitive
            </li>
          </ul>
          <h4>“Any Woman Can Enjoy Firmer, Younger Looking Skin”</h4>
          <div class="row author">
            <div class="col-md-3">
              <img src="<?php echo OFFER; ?>/img/fc-1.png" alt="" width="100%">
            </div>
            <div class="col-md-9">
              <p>
                Dr. Tess Mauricio is a Board Certified Dermatologist, fellow of the American Academy of Dermatology, graduate of Stanford University School of Medicine, and a Summa Cum Laude graduate of The University of California San Diego. She is the founder of M Beauty Clinic by Dr Tess in San Diego and Beverly Hills
              </p>
              <div class="author-text"><span>Dr. Tess Mauricio</span><br>Associated Professor of Dermatology,
                <br>University of Miami</div>
            </div>
          </div>
        </div>
        <button data-respa="resp2"  class="accordion useAccordion">Benefits</button>
        <div class="resp-tab resp2">
          <h2>Benefits</h2>
          <div class="row row-benef">
            <div class="col-lg-7">
              <ul class="list-benef">
                <li>
                  <h5>Instantly Tightens Loose & Saggy Skin</h5>
                  <p>Powerful age-defying peptides instantly start to rejuvenate the skin to create the profound smoothing and tightening effect your skin needs to look young again.</p>
                </li>
                <li>
                  <h5>Erases & Eliminates Wrinkles</h5>
                  <p>Change the muscle movements that lead to wrinkle formation. You’ll feel your wrinkles being erased.</p>
                </li>
                <li>
                  <h5>Restores Dreamy, Glowing, Radiant Skin</h5>
                  <p>Watch as your youthful skin reawakens and your radiant “honeymoon” glow returns once again to make you the envy of even women half your age.</p>
                </li>
                <li>
                  <h5>Supercharge Your Anti-Aging Routine</h5>
                  <p>OCMD harnesses the natural power of the skin’s biological clock to reverse as much as 20 years of aging in as little as 3 minutes a day.</p>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <!-- <button data-respa="resp3"  class="accordion useAccordion">Ingredients</button>
        <div class="resp-tab resp3">
          <h2>Ingredients</h2>
          <div class="ingr">
            <div class="ingr-box">
              <img src="<?php echo OFFER; ?>/img/ingr-img1.png" alt="">
              <h3>Progeline</h3>
              <p>The age-defying breakthrough works by dramatically reducing levels of a toxic protein scientifically linked to thin, aging skin. In an industry case study, a vast majority of ladies saw an increase in firmness and skin density from just the progeline alone.</p>
            </div>
            <div class="ingr-box">
              <img src="<?php echo OFFER; ?>/img/ingr-img2.png" alt="">
              <h3>Matrixyl 3000</h3>
              <p>A potent age-fighting compound that sends signals to replace lost collagen. Collagen is responsible for keeping the skin strong and firm. Matrixyl 3000 boosts collagen levels, restoring thickness and density to thin aging skin.</p>
            </div>
            <div class="ingr-box">
              <img src="<?php echo OFFER; ?>/img/ingr-img3.png" alt="">
              <h3>Argireline</h3>
              <p>Another potent age-fighting compound that sends signals to your skin to replace lost collagen. Collagen is responsible for keeping the skin strong and firm. Matrixyl 3000 boosts collagen levels, restoring thickness and density to thin aging skin.</p>
            </div>
          </div>
        </div> -->
        <button data-respa="resp4"  class="accordion useAccordion">How It Works</button>
        <div class="resp-tab resp4">
          <h2>How It Works</h2>
          <p>Gently massage a quarter-size amount of the Rejuvenation cream into your skin using an “upward motion” until it is completely absorbed. Best applied as last step in your skincare routine twice per day.</p>
          <div class="works">
            <div class="works-box">
              <img src="<?php echo OFFER; ?>/img/work-img1.jpg" alt="">
              <div class="works-info">
                <span>Step 1</span>
                <h3>Apply Rejuvenation Complex Cream</h3>
                <p>The luxurious cream helps hydrate, nourish & deep-condition the skin, preparing the skin to smoothen and tighten.</p>
              </div>
            </div>
            <div class="works-box">
              <img src="<?php echo OFFER; ?>/img/work-img2.jpg" alt="">
              <div class="works-info">
                <span>Step 2</span>
                <h3>Experience Lifting & Firming</h3>
                <p>Enjoy the fast and profound smoothing and tightening effect your skin needs to look young. Firms sagging skin across the face, jawline & neck to diminish elderly looking skin.</p>
              </div>
            </div>
            <div class="works-box">
              <img src="<?php echo OFFER; ?>/img/work-img3.jpg" alt="">
              <div class="works-info">
                <span>Step 3</span>
                <h3>Maintain Regular Use To Improve Results</h3>
                <p>Best results come from regular and consistent use of the cream. See and feel better results, faster by keeping your skin soft, smooth & supple, for a youthful appearance.</p>
              </div>
            </div>
          </div>
        </div>
        <button data-respa="resp5"  class="accordion useAccordion">Our Guarantee</button>
        <div class="resp-tab resp5">
          <h2>Our Guarantee</h2>
          <h4><span>Younger Looking Skin Or Your Money Back.</span></h4>
          <p>See the results of OCMD Rejuvenation Complex Cream for yourself. You have a full 90 days to try it and if you don’t see visibly tighter, firmer skin that makes your appearance seem years’ younger - simply send us a quick email and we will gladly refund your money. No Questions Asked!</p>
        </div>
        <button data-respa="resp6" class="accordion useAccordion">About OCMD</button>
        <div class="resp-tab resp6">
          <h2>About OCMD</h2>
          <p>OCMD is not your typical beauty company. We specialize in what we call “functional beauty,” meaning all of our products are formulated to make you look gorgeous while you wear them and to provide lasting, deeper benefits.</p>
          <p>The OCMD line is created with skin-rejuvenating and collagen-supporting actives such as Hyaluronic Acid, Oligopeptides, Resveratrol, Vitamin C and many more.</p>
          <p>Each product targets specific concerns — whether it’s to reduce the appearance of fine lines and wrinkles or to smooth skin texture.</p>
          <p>We’re proud to be a truly cruelty-free company — and have some of the highest standards for quality testing in the industry.</p>
          <p>As for our name? We believe that just like our city, every single one of our customers has their own story to tell. The strong women of Miami have inspired us and challenged us to grow over time. We envision that all our customers across America are much like them - strong, beautiful with their own story to tell.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="testi">
    <div class="container">
      <h2>OCMD <br> Customer Testimonials</h2>
      <div class="testi-sec">
        <div class="testi-bx">
          <span>L</span>
          <h3>Didn’t Expect A Big Difference BUT…</h3>
          <ul class="stars-rating">
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
          </ul>
          <p>“I was wrong. After a couple of weeks I’ve felt the skin around my face and neck really tighten. The soft,
            saggy skin is much less crepey looking. It works on my hands too. I’ll run out faster but it’s worth it!”
          </p>
          <p class="testi-txt3">Linda M</p>
        </div>
        <div class="testi-bx">
          <span>M</span>
          <h3>Feeling The Skin Tighten In A Good Way!</h3>
          <ul class="stars-rating">
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
          </ul>
          <p>“Smells amazing and goes on smooth. It has saved my skin. I used to worry how I looked every time I left
            the house and now thanks to Dr. J and OCMD all my elderly anxiety has gone!”</p>
          <p class="testi-txt3">Martha E</p>
        </div>
        <div class="testi-bx">
          <span>S</span>
          <h3>My Confidence Is FINALLY Back!</h3>
          <ul class="stars-rating">
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
          </ul>
          <p>“After a rough divorce I was really trying everything to feel good about myself and to be honest, not much
            was working. A friend sent me to OCMD to get their cream and every week I continue to be amazed by the
            results. I’m 55 now and feel in a better place than I did in my 30’s and 40’s.”</p>
          <p class="testi-txt3">Shay D</p>
        </div>
        <div class="testi-bx">
          <span>J</span>
          <h3>I’ve Found What Works</h3>
          <ul class="stars-rating">
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
            <li class="stars-f"></li>
          </ul>
          <p>“On the eighth day I did a picture comparison and my skin looks much more vibrant. It glows and friends
            tell me they can see a remarkable difference. I will never switch to anything else. I’ve found what works!”
          </p>
          <p class="testi-txt3">Joanna B</p>
        </div>
      </div>
    </div>
  </section>

  <section class="faq-section">
    <div class="container">
      <h2>OCMD <br> Frequently Asked Questions</h2>
      <div class="faq-box">
        <!-- <div class="faq active">
          <div class="question">What’s the “Aging Protein” Progerin Again?</div>
          <div class="answer">
            It’s a protein molecule that builds up in your skin cells. The science says it can prevent healthy new cell growth and cause loose, saggy skin. <br><br>
            Every year, progerin increases in women by 3%. By the time you’re 40, your levels become so high you have to do something. <br><br>
            In short, groundbreaking research shows that Progerin could be the true cause of aging skin and Dr. J has used cutting-edge science to perfectly formulate OCMD’s Rejuvenation Complex Cream to block the effects of the toxic “aging protein” Progerin.
          </div>
        </div> -->
        <div class="faq active">
          <div class="question">How Long Until I see Results?</div>
          <div class="answer">
            Many ladies see and feel skin that is smoother and firmer after the very first use but of course the best (and longer lasting) results come from daily, consistent use. <br><br>
            That’s why having a 1-month supply of OCMD’s Rejuvenation Complex Cream is good but having a 3-month or a 6-month supply is EVEN BETTER. <br><br>
            Our ladies report visibly younger, smoother skin in 21 days or less, but the breathtaking transformations come from using OCMD every day as part of their skincare routine. <br><br>
            This is why we put together bigger discounts for ladies who want to enjoy their new, ageless look around the clock.
          </div>
        </div>
        <div class="faq">
          <div class="question">Will It Work For Me?</div>
          <div class="answer">
            Yes! OCMD’s Rejuvenation Complex Cream is science-backed to work for men and women of any age, no matter their skin tone or what their skincare routine is right now.
          </div>
        </div>
        <div class="faq">
          <div class="question">What If It Doesn’t Work For Me?</div>
          <div class="answer">
            Our industry-best 90 day “Results or It’s Free!” GUARANTEE means that you can try us with confidence knowing that you get the exact kind of results we talk about or you pay nothing.<br><br>
            We understand you’ll only be convinced once you’ve tried it and seen the difference for yourself. So I want you to feel totally comfortable that you’re not risking a penny when you order today.<br><br>
            It’s simple: You have three full months to try OCMD. If you’re not totally thrilled by your transformation then simply call or email our team and every penny you’ve spent will be returned.<br><br>
            You don’t have to send back the empty jars or even provide a reason. You’ll just get a hassle-free refund for the total amount you spend today.
          </div>
        </div>
        <div class="faq">
          <div class="question">How Long Does 1 Bottle Last?</div>
          <div class="answer">
            Using OCMD twice a day as recommended (once in the morning and once in the evening before bed), one bottle will last at least 30 days.
          </div>
        </div>
        <div class="faq">
          <div class="question">Can I Use Rejuvenation Complex Cream With Other Products?</div>
          <div class="answer">
            YES! Our formula works beautifully with any skincare regimen and is specially formulated to compliment what our ladies are already doing (although many find they no longer need other “anti-aging” products).<br><br>
            Apply OCMD Rejuvenation Complex Cream after cleansing, and simply wait 15 minutes for our light, silky cream to fully absorb before using makeup.<br><br>
            It won’t affect the way your makeup goes on and it won’t change the color or texture of your foundation.
          </div>
        </div>
        <div class="faq">
          <div class="question">How Can I Order OCMD’s Rejuvenation Complex Cream?</div>
          <div class="answer">
            It’s EASY. Just select your chosen supply (1-month, 3-month and 6-month) and select whether you want to subscribe & save (have that same amount delivered every month) or one-time purchase (you will ONLY be billed for this order). Then complete the checkout process to complete your order. <br><br> Your order will be in your hands in 1-5 days.
          </div>
        </div>
        <div class="faq">
          <div class="question">Is Ordering Safe & Secure?</div>
          <div class="answer">
            YES. We take your privacy very seriously. We use several advanced security measures to maintain the safety of your personal information.
          </div>
        </div>
        <div class="faq">
          <div class="question">When Will My Order Arrive?</div>
          <div class="answer">
            OCMD prides itself on fast, efficient deliveries to all households in the US. We ship directly from our state-of-the-art facilities here in the US so your order will be with you in 1-5 days.
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="hurry">
    <div class="container">
      <h3 class="hurry-text">Hurry! Order Now & Save Up To 50% Off</h3>
      <a href="#">Order Now ></a>
    </div>
  </section>

  <footer class="footer-main">
    <div class="container">
      <img class="footer-logo" src="<?php echo OFFER; ?>/img/logo-n.png" alt="Logo">
      <ul class="footer-menu">
        <li><a href="#" data-popup="privacy">Privacy Policy</a></li>
        <li><a href="#" data-popup="shipping">Shipping & Delivery</a></li>
        <li><a href="#" data-popup="refunds">Refunds & Returns</a></li>
      </ul>
      <ul class="footer-info">
        <li>support@ocmd.co</li>
      </ul>
      <div class="footer-box">The information presented on this website is not intended as specific medical advice and
        is not a substitute for professional treatment or diagnosis. These statements have not been evaluated by the
        Food and Drug Administration. This product is not intended to diagnose, treat, cure, or prevent any disease.
      </div>
      <div class="footer-copy">2020 © OCMD. All rights reserved.</div>
    </div>
  </footer>

  <!-- include footer -->

  <section class="popup">
    <div class="popup--inner">
      <a href="#" id="closePopup">+</a>
      <div class="popup--content" id="popupContentBox"></div>
    </div>
  </section>

  <template id="privacy"><h1 class="section-title">Privacy Policy</h1><p>This Privacy Policy describes how your personal information is collected, used, and shared when you visit or make a purchase from ocmd.co (the “Site”).</p><h3>Personal information we collect</h3><p>When you visit the Site, we automatically collect certain information about your device, including information about your web browser, IP address, time zone, and some of the cookies that are installed on your device. Additionally, as you browse the Site, we collect information about the individual web pages or products that you view, what websites or search terms referred you to the Site, and information about how you interact with the Site. We refer to this automatically-collected information as “Device Information”. We collect Device Information using the following technologies: – “Cookies” are data files that are placed on your device or computer and often include an anonymous unique identifier. For more information about cookies, and how to disable cookies, visit http://www.allaboutcookies.org. – “Log files” track actions occurring on the Site, and collect data including your IP address, browser type, Internet service provider, referring/exit pages, and date/time stamps. – “Web beacons”, “tags”, and “pixels” are electronic files used to record information about how you browse the Site. – Additionally when you make a purchase or attempt to make a purchase through the Site, we collect certain information from you, including your name, billing address, shipping address, payment information (including credit card numbers), email address, and phone number. We refer to this information as “Order Information”. When we talk about “Personal Information” in this Privacy Policy, we are talking both about Device Information and Order Information.</p><h3>How do we use your personal information?</h3><p>We use the Order Information that we collect generally to fulfill any orders placed through the Site (including processing your payment information, arranging for shipping, and providing you with invoices and/or order confirmations). Additionally, we use this Order Information to: – Communicate with you; – Screen our orders for potential risk or fraud; and – When in line with the preferences you have shared with us, provide you with information or advertising relating to our products or services. We use the Device Information that we collect to help us screen for potential risk and fraud (in particular, your IP address), and more generally to improve and optimize our Site (for example, by generating analytics about how our customers browse and interact with the Site, and to assess the success of our marketing and advertising campaigns).</p><h3>Sharing you personal Information</h3><p>We share your Personal Information with third parties to help us use your Personal Information, as described above. For example, we use Shopify to power our online store–you can read more about how Shopify uses your Personal Information here: https://www.shopify.com/legal/privacy. We also use Google Analytics to help us understand how our customers use the Site — you can read more about how Google uses your Personal Information here: https://www.google.com/intl/en/policies/privacy/. You can also opt-out of Google Analytics here: https://tools.google.com/dlpage/gaoptout. Finally, we may also share your Personal Information to comply with applicable laws and regulations, to respond to a subpoena, search warrant or other lawful request for information we receive, or to otherwise protect our rights.</p><h3>Behavioural advertising</h3><p>As described above, we use your Personal Information to provide you with targeted advertisements or marketing communications we believe may be of interest to you. For more information about how targeted advertising works, you can visit the Network Advertising Initiative’s (“NAI”) educational page at http://www.networkadvertising.org/understanding-online-advertising/how-does-it-work. You can opt out of targeted advertising by using the links below: – Facebook: https://www.facebook.com/settings/?tab=ads – Google: https://www.google.com/settings/ads/anonymous – Bing: https://advertise.bingads.microsoft.com/en-us/resources/policies/personalized-ads – Additionally, you can opt out of some of these services by visiting the Digital Advertising Alliance’s opt-out portal at: http://optout.aboutads.info/.</p><h3>Do not track</h3><p>Please note that we do not alter our Site’s data collection and use practices when we see a Do Not Track signal from your browser.</p><h3>Your rights</h3><p>If you are a European resident, you have the right to access personal information we hold about you and to ask that your personal information be corrected, updated, or deleted. If you would like to exercise this right, please contact us through the contact information below. Additionally, if you are a European resident we note that we are processing your information in order to fulfill contracts we might have with you (for example if you make an order through the Site), or otherwise to pursue our legitimate business interests listed above. Additionally, please note that your information will be transferred outside of Europe, including to Canada and the United States.</p><h3>Data retention</h3><p>/* When you place an order through the Site, we will maintain your Order Information for our records unless and until you ask us to delete this information. */</p><h3>Changes</h3><p>We may update this privacy policy from time to time in order to reflect, for example, changes to our practices or for other operational, legal or regulatory reasons.</p><h3>Minors</h3><p>By using our website, you (the visitor) agree to allow third parties to process your IP address, in order to determine your location for the purpose of currency conversion. You also agree to have that currency stored in a session cookie in your browser (a temporary cookie which gets automatically removed when you close your browser). We do this in order for the selected currency to remain selected and consistent when browsing our website so that the prices can convert to your (the visitor) local currency.</p><h3>Contact us</h3><p>For more information about our privacy practices, if you have questions, or if you would like to make a complaint, please contact us by e‑mail at support@ocmd.co</p></template>
  <template id="shipping"><h1 class="section-title">Shipping &amp; Delivery</h1><h3>Shipping F.A.Q.’s</h3><ul><li><b>How long does it take my order to process?</b> <br> All orders take 1-3 business days to process.</li><li><b>Where do you ship from and how long does delivery take?</b> <br> US Orders are shipped locally from our Tampa and Salt Lake City Warehouses and take 1-3 business days to be delivered.</li><li><b>Do I receive confirmation of shipping?</b> <br> Once an order has been shipped, you will receive an email with your shipping information and tracking number.</li><li><b>When does OCMD ship?</b> <br> All orders process and ship Monday – Friday, including federal holidays. Packages do not ship from our distribution centers on weekends.</li></ul></template>
  <template id="refunds"><h1 class="section-title">Refunds &amp; Returns</h1><h3>Returns</h3><p>Our policy lasts 90 days. If 90 days have gone by since your purchase, unfortunately we can’t offer you a refund or exchange. If you purchased more than 1 item, please only open 1 at a time. The rest, must be unused and in their original packaging. For example – We will not accept returns if you purchased a 3-months supply and opened all of them within 90 days. Additional non-returnable items: – Gift cards – Downloadable software products To complete your return, we require a receipt or proof of purchase. Please do not send your purchase back to the manufacturer. There are certain situations where only partial refunds are granted (if applicable) – Book with obvious signs of use – CD, DVD, VHS tape, software, video game, cassette tape, or vinyl record that has been opened – Any item not in its original condition, is damaged or missing parts for reasons not due to our error – Any item that is returned more than 90 days after delivery</p><h3>Refunds (if applicable)</h3><p>Once your return is received and inspected, we will send you an email to notify you that we have received your returned item. We will also notify you of the approval or rejection of your refund. If you are approved, then your refund will be processed, and a credit will automatically be applied to your credit card or original method of payment, within a certain amount of days.</p><h3>Late or missing refunds (if applicable)</h3><p>If you haven’t received a refund yet, first check your bank account again. Then contact your credit card company, it may take some time before your refund is officially posted. Next contact your bank. There is often some processing time before a refund is posted. If you’ve done all of this and you still have not received your refund yet, please contact us at support@ocmd.co.</p><h3>Sale items (if applicable)</h3><p>Only regular priced items may be refunded, unfortunately sale items cannot be refunded.</p><h3>Exchanges (if applicable)</h3><p>We only replace items if they are defective or damaged. If you need to exchange it for the same item, send us an email at support@ocmd.co</p><h3>Gifts</h3><p>If the item was marked as a gift when purchased and shipped directly to you, you’ll receive a gift credit for the value of your return. Once the returned item is received, a gift certificate will be mailed to you. If the item wasn’t marked as a gift when purchased, or the gift giver had the order shipped to themselves to give to you later, we will send a refund to the gift giver and he will find out about your return.</p><h3>Shipping</h3><p>To return your product, please email us at: support@ocmd.co You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non-refundable. If you receive a refund, the cost of return shipping will be deducted from your refund. Depending on where you live, the time it may take for your exchanged product to reach you, may vary.</p></template>

<?php
endwhile;
get_footer(); ?>