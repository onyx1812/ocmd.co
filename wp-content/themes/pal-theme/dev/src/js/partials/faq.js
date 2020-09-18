(function(){
	const faqQuestion = document.querySelectorAll('.question');
	faqQuestion.forEach(faq=>{
		faq.addEventListener('click', function(e){
			e.preventDefault();
			this.parentNode.classList.toggle('active');
		});
	});
})();