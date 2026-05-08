/* global wp, jQuery, _ */

(function ($, _) {
	if (typeof wp === 'undefined' || !wp.media || !wp.media.view || !wp.media.view.AudioDetails) {
		return;
	}

	var AudioDetails = wp.media.view.AudioDetails;
	if (AudioDetails.prototype.omiCaptionPatched) {
		return;
	}

	var originalTemplate = AudioDetails.prototype.template;

	AudioDetails.prototype.template = function (view) {
		var html = originalTemplate.call(this, view);
		var $wrapper = $('<div />').html(html);
		var $settings = $wrapper.find('.embed-audio-settings');

		if (!$settings.length || $settings.find('[data-setting="caption"]').length) {
			return $wrapper.html();
		}

		var captionValue = '';
		if (this.model && typeof this.model.get === 'function') {
			captionValue = this.model.get('caption') || '';
		} else if (view && typeof view.caption === 'string') {
			captionValue = view.caption || '';
		}

		var settingHtml = [
			'<span class="setting caption-setting">',
				'<label for="audio-details-caption" class="name">Bijschrift</label>',
				'<textarea id="audio-details-caption" data-setting="caption">',
				_.escape(captionValue),
				'</textarea>',
			'</span>'
		].join('');

		$settings.append(settingHtml);

		return $wrapper.html();
	};

	AudioDetails.prototype.omiCaptionPatched = true;
})(jQuery, _);
