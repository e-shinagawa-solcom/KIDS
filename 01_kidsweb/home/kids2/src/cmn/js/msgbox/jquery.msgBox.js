(function($, undefined) {

var messageBoxButtonClick = function(e) {
	var
	options = e.data[0],
	buttonIndex = e.data[1],
	callback = options.buttons[buttonIndex].callback;

	hide(options);

	if (typeof callback === 'function') {
		callback.call(this, e);
	}
};

var show = function(options) {
	if (options.blend) {
		options.$wrapper.fadeIn(options.blendDuration);
	} else {
		options.$wrapper.show();
	}
}

var hide = function(options) {
	if (options.blend) {
		options.$wrapper.fadeOut(options.blendDuration, function() {
			options.$wrapper.remove();
		});
	} else {
		options.$wrapper.remove();
	}
}

var createMessageBox = function(options) {
	var
	$outerWrapper = $('<div class="mb-outerWrapper">'),
	$innerWrapper = $('<div class="mb-innerWrapper">').appendTo($outerWrapper),
	$msgBox = $('<div class="mb">').appendTo($innerWrapper),
	$title = $('<div class="mb-title">').appendTo($msgBox),
	$content = $('<div class="mb-content">').appendTo($msgBox),
	$typeIcon = $('<div class="mb-typeIcon">'),
	$message = $('<div class="mb-message">'),
	$buttons = $('<div class="mb-buttons">').appendTo($msgBox),
	$button;

	options.$wrapper = $outerWrapper;

	if (options.type !== undefined) {
		$content.append($typeIcon.addClass('icon-' + options.type));
	}
	$content.append($message);

	$title.text(options.title);
	$message.html(options.message);
	options.buttons.forEach(function(button, buttonIndex) {
		$('<div class="mb-button">')
			.text(button.text)
			.on('click', [options, buttonIndex], messageBoxButtonClick)
			.appendTo($buttons);
	});

	$outerWrapper.hide();

	this.append($outerWrapper);

	show(options);
};

$.fn.msgBox = function(options) {

	createMessageBox.call(this, $.extend(true, {
		title: '',
		message: '',
		buttons: [],
		blend: false,
		blendDuration: 400
	}, options));

};

}(jQuery))