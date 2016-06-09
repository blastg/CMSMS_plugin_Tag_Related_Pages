<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_function_tag_related_pages($params, &$smarty)
{
	$content_props_table="content_props";
	$content_table="content";
	$db = cmsms()->GetDb();
	$config = cmsms()->GetConfig();
	$cms_db_prefix = $config['db_prefix'];

	$contentobj = cms_utils::get_current_content();
	$page_id = $contentobj->ID();
	
	if(($params['tag']) != '') {
		$tags = $params['tag']; 
	} else {
		$smarty->assign('related_pages', '');
		return;
	}
	$tags = preg_replace('/\s+/', '', $tags);         //remove spaces
	$tags = mysql_real_escape_string($tags);
	$tags = explode(",", $tags);
	$tags = array_filter($tags);                      //remove empty elements
	
	//-------------------------------------------------------------------------------------
	// Search for other pages_id related tag (if any)
	//-------------------------------------------------------------------------------------

	$q = "SELECT ";
	$q.= $cms_db_prefix .  $content_props_table . ".content_id";
	$q.= ", ";
	$q.= $cms_db_prefix .  $content_props_table . ".content";
	$q.= " FROM ";
	$q.= $cms_db_prefix. $content_props_table;
	$q.= " WHERE prop_name='tag' ";
	$q.= " AND ";
	$q.= " ( ";
	foreach($tags as $tag)
	{
		$q.= $cms_db_prefix .  $content_props_table . ".content";
		$q.= " LIKE '%";
		$q.= $tag;
		$q.= "%'";
		$q.= " OR ";
	}
	$q=substr_replace($q, "", -4);
	$q.= " ) ";
	$q.= " AND ";
	$q.= $cms_db_prefix .  $content_props_table . ".content_id";
	$q.= " <> " . $page_id;
	$q.= ";";
	//echo $q . "<br />";

	$dbresult = $db->Execute( $q );
	if( !$dbresult )
		{
		    echo 'DB error: '. $db->ErrorMsg()."<br/>";
	}
	while ($dbresult && $row = $dbresult->FetchRow()){
		$related_pages_ids[]= $row["content_id"];
	}

	//-------------------------------------------------------------------------------------
	// List page aliases  w/ similar tags 
	//-------------------------------------------------------------------------------------

		$q = "SELECT ";
		$q.= $cms_db_prefix .  $content_table . ".content_alias";
		$q.= ", ";
		$q.= $cms_db_prefix .  $content_table . ".active";
		$q.= " FROM ";
		$q.= $cms_db_prefix  .  $content_table;
		$q.= " WHERE ";
		$q.= $cms_db_prefix  .$content_table . ".active=1";
		$q.= " AND (";
	
	foreach($related_pages_ids as $related_page_id)
	{
			$q.= "content_id=";
			$q.= $related_page_id;
			$q.= " OR ";
	}
	$q=substr_replace($q, "", -4);
	$q.= ");";
	//echo $q . "<br />";

	$dbresult = $db->Execute( $q );
	if( !$dbresult )
		{
		    echo 'DB error: '. $db->ErrorMsg()."<br/>";
	}
	while ($dbresult && $row = $dbresult->FetchRow()){
		$related_pages[]=  $row["content_alias"];
	}
	$smarty->assign('related_pages', $related_pages);
}


function smarty_cms_help_function_tag_related_pages() {
?>
	<h3>What does this do?</h3>
	<p>This plugin will create relations between pages in CMSMS, simply adding one or more tag to the required page.</p>
	<h3>How to use:</h3>
	<p>Copy <b>function.tag_related_pages.php</b> file to <b>"/plugins"</b> directory of your site.
	<br /><br />
	Then, in your main theme, add this instruction:</p>

	<pre>{$tag="{content block='tag' oneline='true' wysiwyg='false'}" scope=global}</pre>
	</p>
	<p>Create a new <b>content block</b> named <b>related_pages</b> (Layout - Design Manager - Templates - Create a new Template - Core::Generic) with this content (customize according your preferences):</p>
	<pre>
	{tag_related_pages tag="{page_attr key="tag"}"}
	{if $related_pages ne ''}
	&lt;strong&gt;Related pages:&lt;/strong>&lt;br /&gt;
	&lt;ul&gt;
		{foreach $related_pages as $related_page}
		&lt;li&gt;{cms_selflink page="{$related_page}"}&lt;/li&gt;
		{/foreach}
	&lt;/ul&gt;
	{/if}
	</pre>
	<br /><br />
	<p>Show "related pages" in single pages (or in template) adding this instruction to target page:</p>
	<pre>{global_content name='related_pages'}</pre>
	<br /><br />
	<p>Add one (or more) comma separated tag(s) in "tag" field of your related pages, for example:<br />
	<pre>
	<b>sea, vacation, holidays</b>
	</pre>
	<br />or<br />
	<pre>
	<b>sea</b>
	</pre>
	<br />
	<br /><br />
	Please send bug or suggestion to blastg@gmail.com
	<br />
<?
}
function smarty_cms_about_function_tag_related_pages() {
?>
	<p>Author: blast&lt;blastg@gmail.com&gt;</p>

	<ul>Change History:</p>
		<ul>
			<li>0.1 First Release</li>
		</ul>
	</p>
<?php
}
?>
