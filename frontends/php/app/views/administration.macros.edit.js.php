<?php
/*
** Zabbix
** Copyright (C) 2001-2019 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/
?>

<script type="text/x-jquery-tmpl" id="macro-row-tmpl">
	<?= (new CRow([
			(new CCol(
				(new CTextAreaFlexible('macros[#{rowNum}][macro]', '', ['add_post_js' => false]))
					->addClass('macro')
					->setWidth(ZBX_TEXTAREA_MACRO_WIDTH)
					->setAttribute('placeholder', '{$MACRO}')
			))->addClass(ZBX_STYLE_TEXTAREA_FLEXIBLE_PARENT),
			'&rArr;',
			(new CCol(
				(new CTextAreaFlexible('macros[#{rowNum}][value]', '', ['add_post_js' => false]))
					->setWidth(ZBX_TEXTAREA_MACRO_VALUE_WIDTH)
					->setAttribute('placeholder', _('value'))
			))->addClass(ZBX_STYLE_TEXTAREA_FLEXIBLE_PARENT),
			(new CCol(
				(new CTextAreaFlexible('macros[#{rowNum}][description]', '', ['add_post_js' => false]))
					->setWidth(ZBX_TEXTAREA_MACRO_VALUE_WIDTH)
					->setAttribute('placeholder', _('description'))
			))->addClass(ZBX_STYLE_TEXTAREA_FLEXIBLE_PARENT),
			(new CCol(
				(new CButton('macros[#{rowNum}][remove]', _('Remove')))
					->addClass(ZBX_STYLE_BTN_LINK)
					->addClass('element-table-remove')
			))->addClass(ZBX_STYLE_NOWRAP)
		]))
			->addClass('form_row')
			->toString()
	?>
</script>

<script type="text/javascript">
	jQuery(function($) {
		$('#tbl_macros')
			.on('click', 'button.element-table-remove', function() {
				// check if the macro has an hidden ID element, if it does - increment the deleted macro counter
				var macroNum = $(this).attr('id').split('_')[1];
				if ($('#macros_' + macroNum + '_globalmacroid').length) {
					var count = $('#update').data('removedCount') + 1;
					$('#update').data('removedCount', count);
				}
			})
			.dynamicRows({template: '#macro-row-tmpl'})
			.on('click', 'button.element-table-add', function() {
				$('#tbl_macros .<?= ZBX_STYLE_TEXTAREA_FLEXIBLE ?>').textareaFlexible();
			})
			.on('blur', '.<?= ZBX_STYLE_TEXTAREA_FLEXIBLE ?>', function() {
				if ($(this).hasClass('macro')) {
					macroToUpperCase(this);
				}
				$(this).trigger('input');
			});

		$('#update').click(function() {
			var removedCount = $(this).data('removedCount');

			if (removedCount) {
				return confirm(<?= CJs::encodeJson(_('Are you sure you want to delete')) ?> + ' ' + removedCount + ' ' + <?= CJs::encodeJson(_('macro(s)')) ?> + '?');
			}
		});

		$('form[name="macrosForm"]').submit(function() {
			$('input.macro').each(function() {
				macroToUpperCase(this);
			});
		});

		function macroToUpperCase(element) {
			var macro = $(element).val(),
				end = macro.indexOf(':');

			if (end == -1) {
				$(element).val(macro.toUpperCase());
			}
			else {
				var macro_part = macro.substr(0, end),
					context_part = macro.substr(end, macro.length);

				$(element).val(macro_part.toUpperCase() + context_part);
			}
		}
	});
</script>
