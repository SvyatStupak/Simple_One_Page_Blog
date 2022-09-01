$(document).ready(function () {
	showComments();

	$(".value-set").stars({ value:4 });

	$('#commentForm').on('submit', function (event) {
		event.preventDefault();
		var formData = $(this).serialize();
		$.ajax({
			url: "comments.php",
			method: "POST",
			data: formData,
			dataType: "JSON",
			success: function (response) {
				if (!response.error) {
					$('#commentForm')[0].reset();
					$('#commentId').val('0');
					$('#message').html(response.message);
					showComments();
				} else if (response.error) {
					$('#message').html(response.message);
				}
			}
		})
	});

	$(document).on('click', '#addComment, .reply', function () {
		var commentId = $(this).attr("id");
		$('#commentId').val(commentId);
		$('#commentForm').slideToggle(300, function () {
			if ($(this).is(':hidden')) {
				$('#addComment').html('Add Comment');
				
			} else {
				$('#addComment').html('Hide Form');
				$('#name').focus();
			}
		});
	});

	$('#addComment').click(function () {
		$('#commentForm').slideToggle(300, function () {
			if ($(this).is(':hidden')) {
				$('#addComment').html('Add Comment');
			} else {
				$('#addComment').html('Hide Form');
				$('#name').focus();
			}
		});
		return false;
	});

});

// function to show comments
function showComments() {
	$.ajax({
		url: "show_comments.php",
		method: "POST",
		success: function (response) {
			$('#showComments').html(response);
		}
	})
}




