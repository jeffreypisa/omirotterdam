import $ from "jquery";

export function loadmoreposts() {

		var page = 1;
	
		$(document).on('click', '#load-more-posts', function(e) {
			e.preventDefault();
			page++;
	
			$.ajax({
				type: 'POST',
				url: '/wp-admin/admin-ajax.php',
				dataType: 'html',
				data: {
					'action' : 'get_news',
					'get_page' : page
				},
				success: function(data) {
					$('#theposts').append(data);
				},
				error: function(data) {
					console.log(data);
				}
			});
		});

	
}