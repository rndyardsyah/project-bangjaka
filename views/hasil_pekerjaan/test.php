
<!-- <link href="https://static.codepen.io/assets/embed/embed-998bd0ec553c3a5fc2f62c8b5f25a50381234b15027a07f4d17428675068ca4c.css" rel="stylesheet"> -->
<style>
#frame {
	width:100%;
	height:100vh;
	overflow:auto;
	border-left:solid 2px #9A9A9A;
	border-right:solid 2px #F1F1F1;
	border-top:solid 2px #9A9A9A;
	border-bottom:solid 2px #eee;
	padding:20px 0px;
	background:#525659;	
	color:#000;	
	font-family:"Times New Roman", Times, serif;
}

.frame-nav {
	width:100%;
	float:left;
	border:solid 1px #BFBFBF;
	/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#ffffff+0,f3f3f3+50,ededed+51,ffffff+100;White+Gloss+%232 */
	background: rgb(255,255,255); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(255,255,255,1) 0%, rgba(243,243,243,1) 50%, rgba(237,237,237,1) 51%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */
	
}

.btn-frame-nav {
	border:none;
	background: rgb(255,255,255); /* Old browsers*/
	background: -moz-linear-gradient(top,  rgba(255,255,255,1) 0%, rgba(243,243,243,1) 50%, rgba(237,237,237,1) 51%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */

}

body {
  background: rgb(204,204,204); 
}
page {  
  display: block;
  margin-bottom: 0.5cm;
  /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
}
page[size="A4"] {  
	background: white;
	margin: 0 auto;
	margin-top: 0.5cm;
	width: 21cm;
	height: 29.7cm; 
}

page[size="F4"] {  
	background: white;
	margin: 0 auto;
	margin-top: 0.5cm;
	width: 21cm;
	height: 36cm; 
}

page[size="batas"][layout="portrait"] {
  /* background: none !important; */
  width: 21.5cm;
  height: 1cm;  
}

page[size="A4"][layout="portrait"] {
  width: 29.7cm;
  height: 21cm;  
}
page[size="A3"] {
  width: 29.7cm;
  height: 42cm;
}
page[size="A3"][layout="portrait"] {
  width: 42cm;
  height: 29.7cm;  
}
page[size="A5"] {
  width: 14.8cm;
  height: 21cm;
}
page[size="A5"][layout="portrait"] {
  width: 21cm;
  height: 14.8cm;  
}
@media print {
  body, page {
    margin: 0;
    box-shadow: 0;
  }
}

@media only screen and (max-width: 720px),
(min-device-width: 768px) and (max-device-width: 1024px) {
    #frame {
        height: 13.4cm;
        padding: 8px 0px 0px 0px;
    }
    page[size="A4"] {
		margin: 0 auto;		
    }
    page[size="F4"] {
		margin: 0 auto;	
    }
}

</style>

<script>
// $('#zoom-in').click(function() {
   // updateZoom(0.1);
// });

// $('#zoom-out').click(function() {
   // updateZoom(-0.1);
// });


zoomLevel = 1;

var updateZoom = function(zoom) {
   zoomLevel += zoom;
   $('page').css({ zoom: zoomLevel, '-moz-transform': 'scale(' + zoomLevel + ')' });
}

var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
if (isMobile) {
  /* your code here */
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
}
</script>

<!--
<button type="button" id="zoom-in">zoom in</button>
<button type="button" id="zoom-out">zoom out</button> -->

<div class="row">
	<div class="col-lg-12">     	   	            
		<div class="frame-nav">
			<a target="_blank" class="btn btn-default btn-xs" href="#"><i class="fa fa-file-pdf-o"></i> PDF</a>
			<a class="btn btn-default btn-xs" onclick="swal({title: 'Perhatian', text: 'Maaf, sedang dalam pengembangan. Terima kasih untuk dukungannya. :)', type: 'warning',});return false;" href="#"><i class="fa fa-file-word-o"></i> DOC</a>
		</div>
		<div id="frame" style="margin-bottom: 20px;">	
			<page size="A4">    
				<?=$isi_template_surat_permohonan;?>     
			</page>
			<page size="A4">    
				<?=$isi_template_bas;?>     
			</page>
			<page size="F4">    
				<?=$isi_template_bast;?>     
			</page>
			<page size="batas" layout="portrait"></page>
		</div>
	</div>
</div>