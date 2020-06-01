/*
pdm.js

*/

function myLastScrollTo(id) {
  var e = document.getElementById(id);
  var box = e.getBoundingClientRect();
alert(box.top);
  window.scrollBy(0, box.top);
}

function myScrollTo(id) {
//alert("on va en "+id);
  var e = document.getElementById(id);
  var box = e.getBoundingClientRect();
  var k, inc;
  inc = (box.top >= 0) ? 1 : -1;
  for (k = 0; k < 49; k++) setTimeout("window.scrollBy(0," + Math.floor(box.top / 50) + ")", 10 * k);
  setTimeout("myLastScrollTo('" + id + "')", 500);
}

function scrollTo(idElement,idDiv) {
	var element = document.getElementById(idElement);
	var div = document.getElementById(idDiv);
   var topPos = element.offsetTop;
	var bottomPos = element.offsetTop + element.offsetHeight;
   div.scrollTop = bottomPos;
	div.scrollTop = topPos;
	
	element.scrollIntoView(true);
	
}