
		</div>

		<!-- Scripts -->
		<script src="includes/js/jquery.min.js"></script>
		<script src="includes/js/jquery-ui.min.js"></script>
		<script src="includes/js/skel.min.js"></script>
		<script src="includes/js/skel-viewport.min.js"></script>
		<script src="includes/js/util.js"></script>
		<!--[if lte IE 8]><script src="includes/js/ie/respond.min.js"></script><![endif]-->
		<script src="includes/js/main.js"></script>

		<script>
		<?php 
		if(!empty($Page['jquery'])) {
			echo '
			$(function() { 
				' . $Page['jquery'] . ' 
			});';
		} 
		?>
		</script>

	</body>
</html>