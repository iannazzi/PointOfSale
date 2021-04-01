<?php
$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Product Image Bulk Uploader';
require_once ('../product_functions.php');		
$disable_check_login = true;


//ok we need to make sure the directories are created.
//uploaded images will go to /DataFiles/image_uploads
//the plugin makes the files and thumbs directory...

$directory_path = POS_PATH . '/DataFiles/image_uploads';
makeDir($directory_path);


//this is all the stuff from the plugin, pretty unmodified...
$html = '';

//add a button to process the queue....
$html .= '<h2>IF THIS CODE BREAKS DO THIS.....</h2>';
$html .= '<p>FTP images to ' .IMAGE_UPLOAD_PATH . '<p>';
$html .= '<p>Then process images using the button....<p>';
$html .= '<h2>When All The Uploads Are Complete Press The Process Queue Button</h2>';

//$html .= '<input class = "button" type="button" style="width:400px;" name="add_product" value="Process Uploaded Files (make sure uploads are complete)" onclick="open_win(\'process_uploaded_images.php\')"/>';
$html .= '<input class = "button" type="button" style="width:400px;" name="add_product" value="Process Uploaded Files (make sure uploads are complete)" onclick="open_win(\'process_uploaded_images.php\')"/>';

$html .= '<p>This will move the images into Datafiles/ProductImages renamed to the system ID .jpg</p>';
$html .= '<p> It will also tag the images as yours!</p>';


//this has all the cool styles in it... might want to download them....

//<!-- Bootstrap CSS Toolkit styles -->
$html .= '<link rel="stylesheet" href="uploaderCss/bootstrap.min.css">';
//<!-- Generic page styles -->
//$html .= '<link rel="stylesheet" href="'.POS_URL . '/3rdParty/jQuery-File-Upload/css/style.css">';
//<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
$html .= '<link rel="stylesheet" href="uploaderCss/bootstrap-responsive.min.css">';
//<!-- Bootstrap CSS fixes for IE6 -->
$html .= '<!--[if lt IE 7]><link rel="stylesheet" href="uploaderCss/bootstrap-ie6.min.css"><![endif]-->';
//<!-- Bootstrap Image Gallery styles -->
$html .= '<link rel="stylesheet" href="uploaderCss/bootstrap-image-gallery.min.css">';
$html .= '
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="'.POS_URL . '/3rdParty/jQuery-File-Upload/css/jquery.fileupload-ui.css">';

$html .= '<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="'.POS_URL . '/3rdParty/jQuery-File-Upload/css/jquery.fileupload-ui-noscript.css"></noscript>';
$html .= '
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
';

$html .= '
    <form id="fileupload" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
    </form>';
    
$html .= '
<!-- modal-gallery is the modal dialog used for the image gallery -->
<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd" tabindex="-1">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3 class="modal-title"></h3>
    </div>
    <div class="modal-body"><div class="modal-image"></div></div>
    <div class="modal-footer">
        <a class="btn modal-download" target="_blank">
            <i class="icon-download"></i>
            <span>Download</span>
        </a>
        <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000">
            <i class="icon-play icon-white"></i>
            <span>Slideshow</span>
        </a>
        <a class="btn btn-info modal-prev">
            <i class="icon-arrow-left icon-white"></i>
            <span>Previous</span>
        </a>
        <a class="btn btn-primary modal-next">
            <span>Next</span>
            <i class="icon-arrow-right icon-white"></i>
        </a>
    </div>
</div>';
$html .= '
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&\'gallery\'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields=\'{"withCredentials":true}\'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="'.POS_URL . '/3rdParty/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>';

//i downloaded these....
$html .= '
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="uploaderJs/tmpl.min.js"></script>';
$html .= '
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="uploaderJs/load-image.min.js"></script>';
$html .= '
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="uploaderJs/canvas-to-blob.min.js"></script>';
$html .= '
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="uploaderJs/bootstrap.min.js"></script>';
$html .= '<script src="uploaderJs/bootstrap-image-gallery.min.js"></script>';
$html .='
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="'.POS_URL . '/3rdParty/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="'.POS_URL . '/3rdParty/jQuery-File-Upload/js/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="'.POS_URL . '/3rdParty/jQuery-File-Upload/js/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="'.POS_URL . '/3rdParty/jQuery-File-Upload/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="bulk_image_uploader.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="'.POS_URL . '/3rdParty/jQuery-File-Upload/js/cors/jquery.xdr-transport.js"></script><![endif]-->
';


include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);
?>
