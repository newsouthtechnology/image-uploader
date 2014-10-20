<?php require_once('load.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Image Grid</title>
</head>
<body>

<div class="content">
    <div class="row">

        <!-- We will use this to create a pagination tooldbar -->
        <div class="toolbar clear">
            <div class="pagination"></div>

            <!-- This will get populated with all of our images  -->
            <div id="images" class="clear"></div>

            <!-- We will use this to create a pagination toolbar  -->
            <div class="toolbar bottom clear">
                <div class="pagination"></div>
            </div>
        </div>
    </div>
</div>

<?php json_file_data() ?>

<script type="text/javascript" src="<?php get_url("js/jquery.min.js") ?>"></script>
<script type="text/javascript" src="<?php get_url("js/image-grid.js") ?>"></script>
<script>
    jQuery(function($) {

    });
</script>

</body>
</html>