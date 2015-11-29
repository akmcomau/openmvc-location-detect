<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><?php echo $text_location_detect ?></h1>
		</div>
	</div>
	<div class="row">
		<form name="form-location-detecdt" method="post" action="<?php echo $this->url->getUrl('administrator/LocationDetect', 'config'); ?>">
			<div class="col-md-12">
				<strong>
					You must agree to the terms on this page:
					<a href="<?php echo $terms_url ?>" target="_blank"><?php echo $terms_url ?></a>
				</strong>
				<br /><br />
			</div>
			<div class="col-md-12">
				Download URL:
				<input class="form-control" type="text" name="url" value="<?php echo $download_url ?>" />
				<br ><br />
			</div>
			<div class="col-md-12">
				<strong><?php echo $text_take_a_while; ?></strong>
			</div>
			<div class="col-md-12">
				<input type="submit" name="form-location-detect-submit" class="btn btn-primary" value="<?php echo $text_update; ?>" />
			</div>
		</form>
	</div>
</div>
