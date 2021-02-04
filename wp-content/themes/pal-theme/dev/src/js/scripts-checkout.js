//= partials/images.js
//= partials/header.js
//= partials/subscribe.js

(function(){
  let subscriptions = document.querySelectorAll('.product-total .subscription-price');
  if(subscriptions.length > 0){
    document.querySelector('.expect-list li:first-child').style.display = 'none';
    document.querySelector('.expect-list li:nth-child(2)').style.marginTop = '0';
  }
})();