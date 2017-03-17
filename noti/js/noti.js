/*
 * jQuery Repeatable Fields v1.1.4
 * http://www.rhyzz.com/repeatable-fields.html
 *
 * Copyright (c) 2014 Rhyzz
 * License MIT
*/

(function($) {
	$.fn.repeatable_fields = function(custom_settings) {
		var default_settings = {
			wrapper: '.wrapper',
			container: '.container',
			row: '.row',
			add: '.add',
			remove: '.remove',
			move: '.move',
			template: '.template',
			is_sortable: true,
			before_add: null,
			after_add: after_add,
			before_remove: null,
			after_remove: null,
			sortable_options: null,
		}

		var settings = $.extend(default_settings, custom_settings);

		// Initialize all repeatable field wrappers
		initialize(this);

		function initialize(parent) {
			$(settings.wrapper, parent).each(function(index, element) {
				var wrapper = this;

				var container = $(wrapper).children(settings.container);

				// Disable all form elements inside the row template
				$(container).children(settings.template).hide().find(':input').each(function() {
					jQuery(this).prop('disabled', true);
				});

				$(wrapper).on('click', settings.add, function(event) {
					event.stopImmediatePropagation();

					var row_template = $($(container).children(settings.template).clone().removeClass(settings.template.replace('.', ''))[0].outerHTML);

					// Enable all form elements inside the row template
					jQuery(row_template).find(':input').each(function() {
						jQuery(this).prop('disabled', false);
					});

					if(typeof settings.before_add === 'function') {
						settings.before_add(container);
					}

					var new_row = $(row_template).show().appendTo(container);

					if(typeof settings.after_add === 'function') {
						settings.after_add(container, new_row);
					}

					// The new row might have it's own repeatable field wrappers so initialize them too
					initialize(new_row);
				});

				$(wrapper).on('click', settings.remove, function(event) {
					event.stopImmediatePropagation();

					var row = $(this).parents(settings.row).first();

					if(typeof settings.before_remove === 'function') {
						settings.before_remove(container, row);
					}

					row.remove();

					if(typeof settings.after_remove === 'function') {
						settings.after_remove(container);
					}
				});

				if(settings.is_sortable === true && typeof $.ui !== 'undefined' && typeof $.ui.sortable !== 'undefined') {
					var sortable_options = settings.sortable_options !== null ? settings.sortable_options : {};

					sortable_options.handle = settings.move;

					$(wrapper).find(settings.container).sortable(sortable_options);
				}
			});
		}

		function after_add(container, new_row) {
			var row_count = $(container).children(settings.row).filter(function() {
				return !jQuery(this).hasClass(settings.template.replace('.', ''));
			}).length;

			$('*', new_row).each(function() {
				$.each(this.attributes, function(index, element) {
					this.value = this.value.replace(/{{row-count-placeholder}}/, row_count - 1);
				});
			});
		}
	}
})(jQuery);

jQuery(document).ready(function($){
		
		var noti_samples = {
						'none' : '',
						'bouncyflip' : '<span class="icon fa fa-check"></span><p>The event was added to your calendar. Check out all your events in your <a href="#">event overview</a>.</p>',
						'flip' : '<p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'exploader' : '<span class="icon icon-settings"></span><p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'slidetop' : '<span class="icon fa fa-check"></span><p>You have some interesting news in your inbox. Go <a href="#">check it out</a> now.</p>',
						'genie' : '<p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'jelly' : '<p>Hello there! I\'m a classic notification but I have some elastic jelliness thanks to <a href="http://bouncejs.com/">bounce.js</a>. </p>',
						'slide' : '<p>This notification has slight elasticity to it thanks to <a href="http://bouncejs.com/">bounce.js</a>.</p>',
						'scale' : '<p>This is just a simple notice. Everything is in order and this is a <a href="#">simple link</a>.</p>',
						'boxspinner' : '<p>I am using a beautiful spinner from <a href="http://tobiasahlin.com/spinkit/">SpinKit</a></p>',
						'cornerexpand' : '<p><span class="icon fa fa-check"></span> I\'m appaering in a morphed shape thanks to <a href="http://snapsvg.io/">Snap.svg</a></p>',
						'loadingcircle' : '<p>Whatever you did, it was successful!</p>',
						'thumbslider' : '<div class="ns-thumb"><img src="img/user1.jpg"/></div><div class="ns-content"><p><a href="#">Zoe Moulder</a> accepted your invitation.</p></div>'
					}
		jQuery('.repeat').each(function() {
			jQuery(this).repeatable_fields();
		});
		
		/* jQuery('.add-noti-past').click(function(){
			jQuery('.noti-panel').removeClass('active');
			jQuery('.add-past-wrapper').addClass('acctive');
		});
		
		jQuery('.add-noti-cat').click(function(){
			jQuery('.noti-panel').removeClass('active');
			jQuery('.add-category-wrapper').addClass('acctive');
		});
		
		jQuery('.add-noti-tag').click(function(){
			jQuery('.noti-panel').removeClass('active');
			jQuery('.add-tag-wrapper').addClass('acctive');
		});
		
		jQuery('.add-noti-new').click(function(){
			jQuery('.noti-panel').removeClass('active');
			jQuery('.add-new-wrapper').addClass('acctive');
		}); */
		
	
		
		
		$('body').delegate('.noti_save_check', 'change', function (e) {
			var t = $(this);
			if(t.is(':checked')){
				$('.noti-save-new').toggleClass('noti-hide');
				$('.noti-save-panel').addClass('active');
			} else {
				$('.noti-save-panel').removeClass('active');
			}
		});
		
		
		$('body').delegate('.noti-minimize', 'click', function (e) {
			var t = jQuery(this);
			var p = t.parent().parent();
			var index = t.attr('data-index');
			var div = p.find('.noti-options-wrapper-' + index);
			var title = p.find('.noti-options-title-' + index);
			t.toggleClass('collapsed');
			div.toggleClass('minimized');
			title.find('.noti-number').text(parseInt(index)+1);
			title.toggleClass('minimized');
		});
		$('body').delegate('.noti-advance-trigger', 'click', function (e) {
			var t = jQuery(this);
			var p = t.parent();
			var div = p.find('.noti-advance-panel');
			div.toggleClass('active');
		});
		$('body').delegate('.noti-override-trigger', 'click', function (e) {
			var t = jQuery(this);
			var p = t.parent();
			var div = p.find('.noti-override-panel');
			div.toggleClass('active');
		});
		$('body').delegate('.add-noti-past', 'click', function (e) {
			e.preventDefault();
			var t = jQuery(this);
			var p = t.parent().parent();
			var panel = p.find('.noti-past-wrapper');
			var index = t.attr('data-index');
			jQuery('#noti-kind-' + index + ' .button').removeClass('noti-button-active');
			t.addClass('noti-button-active');
			p.find('.noti-panel').removeClass('active');
			panel.addClass('active');
			jQuery('.noti_kind_' + index).val('past');
		});
		
		$('body').delegate('.add-noti-cat', 'click', function (e) {
			e.preventDefault();
			var t = jQuery(this);
			var p = t.parent().parent();
			var panel = p.find('.noti-category-wrapper');
			var index = t.attr('data-index');
			jQuery('#noti-kind-' + index + ' .button').removeClass('noti-button-active');
			t.addClass('noti-button-active');
			p.find('.noti-panel').removeClass('active');
			panel.addClass('active');
			jQuery('.noti_kind_' + index).val('category');
		});
		
		$('body').delegate('.add-noti-tag', 'click', function (e) {
			e.preventDefault();
			var t = jQuery(this);
			var p = t.parent().parent();
			var panel = p.find('.noti-tag-wrapper');
			var index = t.attr('data-index');
			jQuery('#noti-kind-' + index + ' .button').removeClass('noti-button-active');
			t.addClass('noti-button-active');
			p.find('.noti-panel').removeClass('active');
			panel.addClass('active');
			jQuery('.noti_kind_' + index).val('tag');
		});
		
		$('body').delegate('.add-noti-new', 'click', function (e) {
			e.preventDefault();
			var t = jQuery(this);
			var p = t.parent().parent();
			var panel = p.find('.noti-new-wrapper');
			var index = t.attr('data-index');
			tinyMCE.execCommand('mceAddEditor', false, 'noti_message_' + index); 
			jQuery('#noti-kind-' + index + ' .button').removeClass('noti-button-active');
			t.addClass('noti-button-active');
			p.find('.noti-panel').removeClass('active');
			panel.addClass('active');
			jQuery('.noti_kind_' + index).val('new');
		});
		
		$('body').delegate('.advance-trigger', 'click', function (e) {
			var t = jQuery(this);
			var p = t.parent();
			var div = p.find('.noti-advance');
			div.slideToggle(500);
		});
		
		$('body').delegate('.noti_sampler', 'click', function (e) {
			e.preventDefault();
			var t = $(this);
			var index = t.attr('data-index');
			var effect = $('#noti_effect-' + index).val();
			// var message = p.find('.noti_message');
			// tinyMCE.activeEditor.setContent(
			$('#noti_message_' + index).val(noti_samples[effect]);
		});
		
		$('body').delegate('.noti_layout', 'change', function (e) {
				e.preventDefault();
				var t = $(this);
				var layout = t.val();
				var p = t.parent();
				var effect = p.find('.noti_effect');
				var options = effect.find('option');
				var defOpt;
				options.show();
				layout_options = options.filter(function() { 
				  defOpt = $(this).data("layout") != 'none'
				  return $(this).data("layout") != layout && $(this).data("layout") != 'none' ;
				});
				layout_options.hide();
				$(effect).children('option[value=""]').attr("selected", "selected")
		});
		
});