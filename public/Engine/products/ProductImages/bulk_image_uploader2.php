<?php
$binder_name = 'Products';
$access_type = 'Write';
$page_title = 'Product Image Bulk Uploader';
require_once ('../product_functions.php');		
$disable_check_login = true;

$html ='';
$html .= '<input id="fileupload" type="file" name="files[]" data-url="../../../DataFiles/image_uploads/" multiple>';
$html .= '<script src="'.POS_URL.'/3rdParty/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>';
$html .= '<script src="'.POS_URL.'/3rdParty/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>';
$html .= '<script src="'.POS_URL.'/3rdParty/jQuery-File-Upload/js/jquery.fileupload.js"></script>';

$html .= '<div id="progress">
    <div class="bar" style="width: 0%;"></div>
</div>';
$html .= '<style type="text/css">
	.bar {
    height: 18px;
    background: green;
}</style>';
$html .= '<script> $(function () {
    $(\'#fileupload\').fileupload({
        dataType: \'json\',
       add: function (e, data) {
            data.context = $(\'<button/>\').text(\'Upload\')
                .appendTo(document.body)
                .click(function () {
                    $(this).replaceWith($(\'<p/>\').text(\'Uploading...\'));
                    data.submit();
                });
        },

        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $(\'<p/>\').text(file.name).appendTo(document.body);
            });
        },
         progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $(\'#progress .bar\').css(
            \'width\',
            progress + \'%\'
        );
        }
    });
});</script>';

include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);
?>