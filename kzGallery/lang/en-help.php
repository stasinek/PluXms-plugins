<h2>Displaying some galleries of pictures inside an article or a static page</h2>
<p>First, drop all the pictures inside a special folder with using the medias explorer. Notice the path of the folder or press the copy button to copy it in the clipboard just for the current session (<em>sessionStorage</em>).</p>
<p>Don't forget for creating tumbnails when dropping the pictures, with the same heigth if it's possible.</p>
<p>So, edit the article and choose one of the following methods :</p>
<ol>
	<li>Select the path for the folder at the bottom of the page in the dropdown list or click on the paste button. Don't forget to save the article. By this way, only one gallery is setting.</li>
	<li><p>Edit the HTML code of the article and just insert the following tag :</p>
		<pre class="kzGallery"><code>&ltdiv data-gallery="path-to-the-folder-of-pictures/"&gtMy pictures&lt;div&gt;</code></pre>
		<p>Otherwise, the value of the clipboard can be pasted by hitting the ctrl and c keys on the keyboard.</p>
		<p>Of course, some others attributes like <em>class</em> can be add.</p>
		<p>When the page is displaying in the frontend, the inner of the tag is moved to the gallery of pictures by this plugin.</p>
		<p>The path of this folder is relative from the <strong>medias folder</strong></p>
	</li>
</ol>
<p>If the article is whithout a header, his body and the galleries are displaying at the home page. Take care on the download time for many pictures. In this last case, a better way is to add a header avoiding to display the galleries.</p>
<p>The header of article doesn't accept any gallery.</p>
<h2>Displaying some galleries of pictures in a static page</h2>
<ol>
	<li>Add the tags in the HTML code of the static page like an article.</li>
</ol>
<h2>Settings</h2>
<p>By default, this plugin is using the Lightbox2 library in javascript for the slide show.</p>
<p>A caption is add to each picture.</p>
<p>Each thumbnail is linked to the real size picture with a &lt;a&gt; tag. This tag has <strong>data-lightbox</strong> attribute for gathering each picture in one set. Many sets are possible inside an article.</p>
<p>Each options may be disallowed. At last, the gallery can be only a set of &ltimg&gt; tags for each thumbnail.</p>
<p>By disallowing the Lightbox2  library, the slideshow can be displaying by an other way. Use <em>div[data-gallery]</em> CSS selector in this case.</p>
