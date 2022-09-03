<?php
include('templates/blocks/header.php');
?>
<title>Simple One Page Blog</title>

<script src="js/addComments.js"></script>



<?php include('templates/blocks/container.php'); ?>
<?php include('templates/stat.php'); ?>


<div id="showStat"></div>

<div class="container">
	<h2>Simple One Page Blog</h2>
	<div>
		<button type="text" name="addComment" id="addComment" class="btn btn-primary">Add Post</button>
	</div>
	<br>
	<form method="POST" id="commentForm" style="display: none;">
		<div class="form-group">
			<input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" required />
		</div>
		<div class="form-group">
			<textarea name="comment" id="comment" class="form-control" placeholder="Enter Comment" rows="5" required></textarea>
		</div>
		
		<br>
		<div class="value-set"></div>
		<div class="form-group">
			<input type="hidden" name="commentId" id="commentId" value="0" />
			<input type="submit" name="submit" id="submit" class="btn btn-primary" value="Post Comment" />
		</div>
	</form>
	<br>
	<div class="row">

		
</div>
<span id="message"></span>
<br>
<div id="showComments"></div>

<?php include('templates/blocks/footer.php'); ?>

