/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.toolbar = 'Full';
    config.toolbar_Full =
    [
        ['Source','-','Save','NewPage','-','Templates'],
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
        '/',
        ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['BidiLtr', 'BidiRtl'],
        ['Link','Unlink'],
        ['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak','Iframe'],
        '/',
        ['Styles','Format','Font','FontSize'],
        ['TextColor','BGColor'],
        ['Maximize','ShowBlocks','-','About']
    ];
    config.toolbar_Min =
    [
        ['Source','-','Save',],
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
        ['NumberedList','BulletedList','-'],
        ['Bold','Italic','Underline','-','Subscript','Superscript'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Maximize'],
    ];
};
