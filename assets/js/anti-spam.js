/**
 * Anti-Spam Module JavaScript
 * 
 * @package CP_Defender
 */

(function($) {
	'use strict';
	
	// Pattern Test Handler
	$(document).on('click', '.defender-test-pattern', function(e) {
		e.preventDefault();
		
		var $button = $(this);
		var $form = $button.closest('form');
		var regex = $form.find('#regex').val();
		var type = $form.find('#type').val();
		var $results = $('#test-results');
		
		if (!regex) {
			alert(defenderAntiSpam.i18n.testing || 'Bitte gib ein Pattern ein.');
			return;
		}
		
		$button.prop('disabled', true).text(defenderAntiSpam.i18n.testing);
		$results.html('<p class="defender-testing">Teste Pattern...</p>');
		
		$.ajax({
			url: defenderAntiSpam.ajaxUrl,
			type: 'POST',
			data: {
				action: 'defender_antispam_test_pattern',
				nonce: defenderAntiSpam.nonce,
				regex: regex,
				type: type
			},
			success: function(response) {
				$button.prop('disabled', false).text('Pattern testen');
				
				if (response.success) {
					var data = response.data;
					var html = '';
					
					if (data.error) {
						html = '<div class="notice notice-error inline"><p>' + data.error + '</p></div>';
					} else if (data.total === 0) {
						html = '<div class="notice notice-info inline"><p>Keine Treffer gefunden.</p></div>';
					} else {
						html = '<div class="notice notice-success inline">';
						html += '<p><strong>Treffer: ' + data.total + '</strong> (zeige ' + data.shown + ')</p>';
						html += '<ul style="list-style: disc; margin-left: 20px;">';
						$.each(data.matches, function(i, match) {
							html += '<li>' + $('<div>').text(match).html() + '</li>';
						});
						html += '</ul></div>';
					}
					
					$results.html(html);
				} else {
					$results.html('<div class="notice notice-error inline"><p>Fehler: ' + response.data.message + '</p></div>');
				}
			},
			error: function() {
				$button.prop('disabled', false).text('Pattern testen');
				$results.html('<div class="notice notice-error inline"><p>AJAX-Fehler aufgetreten.</p></div>');
			}
		});
	});
	
	// Blog Toggle (Spam/Unspam/Ignore)
	$(document).on('click', '.defender-toggle-blog', function(e) {
		e.preventDefault();
		
		var $button = $(this);
		var blogId = $button.data('blog-id');
		var actionType = $button.data('action');
		var $row = $button.closest('tr');
		
		if (!confirm('Bist du sicher?')) {
			return;
		}
		
		$button.prop('disabled', true);
		
		$.ajax({
			url: defenderAntiSpam.ajaxUrl,
			type: 'POST',
			data: {
				action: 'defender_antispam_toggle_blog',
				nonce: defenderAntiSpam.nonce,
				blog_id: blogId,
				action_type: actionType
			},
			success: function(response) {
				if (response.success) {
					$row.fadeOut(400, function() {
						$(this).remove();
					});
				} else {
					alert('Fehler: ' + response.data.message);
					$button.prop('disabled', false);
				}
			},
			error: function() {
				alert('AJAX-Fehler aufgetreten.');
				$button.prop('disabled', false);
			}
		});
	});
	
	// Select All Checkbox
	$(document).on('change', '#select-all, #select-all-blogs', function() {
		var isChecked = $(this).prop('checked');
		$('input[type="checkbox"][name^="pattern_ids"], input[type="checkbox"][name^="blog_ids"]')
			.prop('checked', isChecked);
	});
	
	// Confirmation for bulk delete
	$(document).on('submit', 'form', function(e) {
		var $form = $(this);
		
		if ($form.find('button[name="delete_patterns"]').is(':focus') ||
		    $form.find('button[name="bulk_action"]').is(':focus')) {
			
			var checkedCount = $form.find('input[type="checkbox"]:checked').not('#select-all, #select-all-blogs').length;
			
			if (checkedCount === 0) {
				alert('Bitte wähle mindestens einen Eintrag aus.');
				e.preventDefault();
				return false;
			}
			
			if ($form.find('button[name="delete_patterns"]').length) {
				if (!confirm(defenderAntiSpam.i18n.confirm_delete || 'Wirklich löschen?')) {
					e.preventDefault();
					return false;
				}
			}
		}
	});
	
	// Settings page: Show/Hide verification settings
	$(document).on('change', '#human_verification', function() {
		var value = $(this).val();
		$('.recaptcha-settings').toggle(value === 'recaptcha');
		$('.qa-settings').toggle(value === 'questions');
	});
	
	// Auto-save indicator
	var autoSaveTimer;
	$(document).on('change', 'form input, form select, form textarea', function() {
		var $form = $(this).closest('form');
		
		if ($form.find('.auto-save-indicator').length === 0) {
			$form.find('input[type="submit"]').after('<span class="auto-save-indicator" style="margin-left: 10px; color: #999;">Änderungen nicht gespeichert</span>');
		}
	});
	
	// Initialize tooltips if available
	if (typeof $.fn.tooltip === 'function') {
		$('[data-toggle="tooltip"]').tooltip();
	}
	
	// Initialize on ready
	$(document).ready(function() {
		// Trigger initial state for human verification
		if ($('#human_verification').length) {
			$('#human_verification').trigger('change');
		}
		
		// Add loading animation class
		$(document).ajaxStart(function() {
			$('body').addClass('defender-ajax-loading');
		}).ajaxStop(function() {
			$('body').removeClass('defender-ajax-loading');
		});
	});
	
})(jQuery);
