$(function() {
	var $grid = $('#timeline').masonry({
		// options
		itemSelector: '.entry',
		columnWidth: '.grid-sizer',
		percentPosition: true,
		gutter: 8
	});
	// layout Masonry after each image loads
	$grid.imagesLoaded().progress( function() {
		$grid.masonry('layout');
	});

	// new memo
	$('#new-memo-form').click(function(e) {
		e.stopPropagation();
	});
	$('#new-memo-form').submit(function(e) {
		e.preventDefault();

		$('#memo-submit').prop('disabled', true);

		// submit form
		$.ajax({
			url: "api/submit.php",
			method:"POST",
			data: {
				edit: $('#d-edit').data('edit'),
				id: $('#d-pid').data('pid'),
				imageurl: $('#memo-imageurl').val(),
				description: $('#memo-description').val(),
				tags: $('#memo-tags').val()
			}
		}).done(function(msg) {
			msg = JSON.parse(msg);
			$('#memo-submit-result').text(msg.response);
			// clear form if successful to avoid accidental duplicate submissions
			console.log(msg.success);
			if (msg.success == true) {
				$('#memo-imageurl').val("");
				$('#memo-description').val("");
				$('#memo-tags').val("");
				console.log("cleared form");
			}	
			$('#memo-submit').prop('disabled', false);
		});
	});

	// scroll to bottom event
	_offset = 0;
	$(window).scroll(function() {
		//console.log($(window).scrollTop() + $(window).height(),  getDocHeight());
		if ($(window).scrollTop() + $(window).height() == getDocHeight()) {
			//console.log("bottom!");
			console.log("hit bottom; loading posts");
			$.ajax({
				url: "api/fetch.php",
				method:"POST",
				data: {
					q:$('#d-query').data('query'),
					profile:$('#d-profile').data('profile'),
					offset:$('#d-offset').data('offset')
				}
			}).done(function(msg) {
				console.log(msg);
				r = JSON.parse(msg);
				$('#d-offset').data('offset', r.offset);
				if (r.postlist != "") {
					$('#timeline').append(r.postlist).masonry('reloadItems');
					$('#timeline').masonry('layout');
				}
			});
		}
   });
	function getDocHeight() {
		var D = document;
		return Math.max(
			D.body.scrollHeight, D.documentElement.scrollHeight,
			D.body.offsetHeight, D.documentElement.offsetHeight,
			D.body.clientHeight, D.documentElement.clientHeight
		);
	}
});

var getUrlParameter = function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
};