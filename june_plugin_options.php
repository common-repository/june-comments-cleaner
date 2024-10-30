<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	/*
	* Get submitted tags and save them in the db
	*/
	if ($_POST['tags_textarea'])
	{
		$tags = trim($_POST['tags_textarea']);
		update_option('june_comments_allowed_tags', $tags);
	}
	else update_option('june_comments_allowed_tags', '');
}
?>
<div class="wrap">
	<h3>WTC Comment Cleaner</h3>

<form action="" method="post">
	<p>
    	<label for="tags_textarea">Allowable tags in comments:</label>
        <br />
        <textarea cols="80" rows="7" id="tags_textarea" name="tags_textarea"><?php echo get_option('june_comments_allowed_tags'); ?></textarea>
    </p>
    <p>
    	<label style="font-size: 90%; color: #808080; font-style: italic;">* do not add commas, spaces or any other characters to separate tags!!</label>
    </p>
    <p><input type="submit" class="button-primary" value="Save" /></p>

	<input type="hidden" name="action" value="update" />
</form>
</div>
