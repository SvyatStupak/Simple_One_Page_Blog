$(document).ready(function () {
	showComments();
	showStat();

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

		hideForm();
	});

	$(document).on('click', '.reply', function () {
		var commentId = $(this).attr("id");
		$('#commentId').val(commentId);
		hideForm();
	});

	

	$('#addComment').click(function () {
		hideForm();
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

function showStat() {
	$.ajax({
		url: "dataForStat.php",
		method: "POST",
		success: function (response) {
			var resObj = JSON.parse(response);
			$('.card-text-neg').html(resObj.negativeRate);
			$('.card-text-all').html(resObj.allEstimate);
			$('.card-text-pos').html(resObj.positiveRate);
			
		}
	});
}

function hideForm() {
	$('#commentForm').slideToggle(300, function () {
		if ($(this).is(':hidden')) {
			$('#addComment').html('Add Comment');
		} else {
			$('#addComment').html('Hide Form');
			$('#name').focus();
		}
	});
	return false;
}

