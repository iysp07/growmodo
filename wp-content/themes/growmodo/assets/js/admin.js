
jQuery(function ($) {
	$('.gm-gallery').each(function () {
		var $box = $(this),
			$list = $box.find('.gm-gallery__list'),
			$input = $('#' + $box.data('target'));

		function sync() {
			var ids = $list.children().map(function () { return $(this).data('id'); }).get();
			$input.val(ids.join(','));
		}

		$list.sortable({ update: sync });

		$box.on('click', '.gm-gallery__remove', function () {
			$(this).closest('li').remove();
			sync();
		});

		$box.on('click', '.gm-gallery__add', function (e) {
			e.preventDefault();
			var frame = wp.media({ title: 'Select gallery images', multiple: 'add', library: { type: 'image' } });
			frame.on('select', function () {
				frame.state().get('selection').each(function (att) {
					var a = att.toJSON();
					if ($list.find('[data-id="' + a.id + '"]').length) { return; }
					var src = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
					$list.append(
						$('<li>').attr('data-id', a.id).data('id', a.id)
							.append($('<img>').attr('src', src))
							.append('<button type="button" class="gm-gallery__remove" aria-label="Remove">&times;</button>')
					);
				});
				sync();
			});
			frame.open();
		});
	});
});
