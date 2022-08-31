$(document).ready(function () {
    $('#commentForm, #hideForm').hide();
    showComments();

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
		$('#commentForm, #hideForm').show();
        $('#name').focus();
        $('#addComment').hide();
	});

    $(document).on('click', '#hideForm', function () {
        $('#commentForm, #hideForm').hide();
        $('#addComment').show();
	});

});

function showComments() {
	$.ajax({
		url: "show_comments.php",
		method: "POST",
		success: function (response) {
			$('#showComments').html(response);
		}
	})
}