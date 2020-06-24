<script>
function setActive() {
  linkObj = document.getElementById('myTopnav').getElementsByTagName('a');
  for(i=0;i<linkObj.length;i++) { 
    if(document.location.href.indexOf(linkObj[i].href)>=0) {
      linkObj[i].classList.add("active");
    }
  }
}

window.onload = setActive;
</script> 
<div class="bg"></div>
<div id="banner"><img src="pess_logo3.png"></div>
<div class="topnav" id="myTopnav">
	<a href="logcall.php"><span>Log Call</span></a>
	<a href="update.php">Update</a>
	<a href="#">Report</a>
	<a href="#">History</a>
	<a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fa fa-bars"></i>
  </a>
	</div>
<div class="footer">&copy;Government of Singapore</div>
<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>
