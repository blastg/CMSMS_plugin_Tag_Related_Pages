# CMSMS_plugin_Tag_Related_Pages
<br />
CMSMS plugin for tagging related pages
What does this do?

This plugin will create relations between pages in CMSMS, simply adding one or more tag to the required page.
How to use:

Copy function.tag_related_pages.php file to "/plugins" directory of your site.

Then, in your main theme, add this instruction:

{$tag="{content block='tag' oneline='true' wysiwyg='false'}" scope=global}

Create a new content block named related_pages (Layout - Design Manager - Templates - Create a new Template - Core::Generic) with this content (customize according you preferences):

	{tag_related_pages tag="{page_attr key="tag"}"}
	{if $related_pages ne ''}
	<strong>Related pages:</strong><br />
	<ul>
		{foreach $related_pages as $related_page}
		<li>{cms_selflink page="{$related_page}"}</li>
		{/foreach}
	</ul>
	{/if}
	



Show "related pages" in single pages (or in template) adding this instruction to target page:

{global_content name='related_pages'}



Add one (or more) comma separated tag(s) in "tag" field of your related pages, for example:

	sea, vacation, holidays
	


or

	sea
	

