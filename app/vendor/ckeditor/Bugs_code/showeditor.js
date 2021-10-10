var AllEditors = new Array();

function showckeditor (Quel, id) {
	CKEDITOR.config.entities = false;
	CKEDITOR.config.entities_latin = false;
	CKEDITOR.config.htmlEncodeOutput = false;

	AllEditors[id] = CKEDITOR.replace( Quel, {
		language: '<?php echo \Auth::user()->language; ?>',
		height: 175,
		toolbar : [
			{ name: 'Fichiers', items: ['Source']},
			{ name: 'CopieColle', items: ['Cut','Copy','Paste','PasteText','PasteFromWord','RemoveFormat']},
			{ name: 'FaireDefaire', items: ['Undo','Redo','-','Find','Replace','-','SelectAll']},
			{ name: 'Polices', items: ['Bold','Italic','Underline','TextColor']},
			{ name: 'ListeDec', items: ['horizontalrule','table','JustifyLeft','JustifyCenter','JustifyRight','Outdent','Indent','Blockquote']},
			{ name: 'Liens', items: ['Image', 'NumberedList','BulletedList','-','Link','Unlink']}
		]
	} );
}

function AffichonsEditor(id) {
	var CeComment = document.getElementById('comment'+id);
	var SesDiv = CeComment.childNodes;
	var SousDiv = SesDiv[1].childNodes;
	document.getElementById('div_comment_' + id + '_Sdiv').style.display = "block";
	var SSousDiv = 'textarea_' + id + '_SSdiv';
	setTimeout(function() {
		showckeditor (SSousDiv, id);
	} , 167);
}

function CachonsEditor(id) {
	if (document.getElementById('div_comment_' + id + '_Sdiv')) { 
		document.getElementById('div_comment_' + id + '_Sdiv').style.display = "none"; 
		AllEditors[id].setData(document.getElementById('div_comment_' + id + '_issue').innerHTML);
		id = 'textarea_' + id + '_SSdiv';
	}
	return AllEditors[id].getData();
}

function ChangeonsEditor(id, contenu) {
	return AllEditors[id].setData(contenu);
}

function EditonsTexte(url,id) {
	document.location.href = url + "&content=" + AllEditors[id].getData();
}

function SupprimonsEditor(id) {
	AllEditors[id].destroy();
}
