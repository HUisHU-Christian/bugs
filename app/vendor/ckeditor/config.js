/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
		config.language = 'fr',
		config.uicolor = '#6E9BFF',
		config.contentsLanguage = 'fr',
		config.entities = false,
		config.entities_greek = false,
		config.entities_latin = false,
		config.enterMode = CKEDITOR.ENTER_BR,
		config.filebrowserImageBrowseUrl = 'app/vendor/ckeditor/ckeditor_ChoisirImage.php', 
		config.filebrowserImageUploadUrl = 'app/vendor/ckeditor/ckeditor_RecevoirImage.php',
		config.forcePasteAsPlainText = true,
		config.language_list =[ 'fr-ca:French:Canada', 'en:English', 'es:Spanish' ],
		config.protectedSource.push( /<\?[\s\S]*?\?>/g ),
		config.shiftEnterMode = CKEDITOR.ENTER_P;
		config.extraPlugins = 'pastecode';
		config.extraPlugins = 'justify';

	// Simplify the dialog windows.
	config.entities = false;
	config.entities_latin = false;
	config.htmlEncodeOutput = false;
};
