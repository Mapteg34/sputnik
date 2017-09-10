<?php
if ( ! defined( "ABSPATH" ) ) {
	exit;
}

$cities = array(
	"Moscow",
	"Volgograd",
	"New-York",
	"Los Angeles"
);

global $post;

$custom = get_post_custom($post->ID);

$image_src = "";
$image_id = get_post_meta($post->ID, "_image_id", true);
$image_src = wp_get_attachment_url($image_id);

?>
<p>
	<label>City:</label><br />
	<select name="<?=Sputnik::HOUSE_CITY_FIELD?>">
		<?foreach($cities as $city):?>
			<option value="<?=$city?>" <?if($custom[Sputnik::HOUSE_CITY_FIELD][0]==$city):?>selected="selected"<?endif?>><?=$city?></option>
		<?endforeach?>
	</select>
</p>
<img id="logo" src="<?=$image_src?>" style="max-width:100%;" />
<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?=$image_id;?>" />
<p>
    <a title="<?=esc_attr_e( "Set logo" ) ?>" href="#" id="set-logo"><?=_e( "Set logo" ) ?></a>
    <a title="<?=esc_attr_e( "Remove logo" ) ?>" href="#" id="remove-logo" style="<?=(!$image_id ? "display:none;" : "" ); ?>"><?=_e( "Remove logo" ) ?></a>
</p>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        window.send_to_editor_default = window.send_to_editor;

        $("#set-logo").click(function(){
            window.send_to_editor = window.attach_image;
            tb_show("", "media-upload.php?post_id=<?=$post->ID ?>&amp;type=image&amp;TB_iframe=true");
            return false;
        });

        $("#remove-logo").click(function() {
            $("#upload_image_id").val("");
            $("#logo").attr("src", "");
            $(this).hide();
            return false;
        });

        window.attach_image = function(html) {
            $("body").append("<div id=\"temp_image\">" + html + "</div>");
            var img = $("#temp_image").find("img");

            imgurl   = img.attr("src");
            imgclass = img.attr("class");
            imgid    = parseInt(imgclass.replace(/\D/g, ""), 10);

            $("#upload_image_id").val(imgid);
            $("#remove-logo").show();

            $("img#logo").attr("src", imgurl);
            try{tb_remove();}catch(e){};
            $("#temp_image").remove();

            window.send_to_editor = window.send_to_editor_default;
        }
    });
</script>