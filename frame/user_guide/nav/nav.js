function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<ul>' +
			'<li><a href="'+base+'index.html">User Guide Home</a></li>' +	
			'<li><a href="http://www.frankmichel.de/formgenlib/">Demo Form</a></li>' +
		'</ul>' +	

		'<h3>Basic Info</h3>' +
		'<ul>' +
			'<li><a href="'+base+'basic/features.html">Features</a></li>' +
			'<li><a href="'+base+'basic/changelog.html">History</a></li>' +
			'<li><a href="'+base+'basic/credits.html">Credits</a></li>' +
			'<li><a href="'+base+'basic/license.html">License</a></li>' +			
		'</ul>' +	

		'<h3>Installation</h3>' +
		'<ul>' +
			'<li><a href="'+base+'installation/download.html">Download</a></li>' +
			'<li><a href="'+base+'installation/requirements.html">Requirements</a></li>' +
			'<li><a href="'+base+'installation/install.html">Installation</a></li>' +
			'<li><a href="'+base+'installation/troubleshooting.html">Troubleshooting</a></li>' +
		'</ul>' +	

		'<h3>Additional Resources</h3>' +
		'<ul>' +
			'<li><a href="http://codeigniter.com/forums/viewthread/107861/">Community Forums</a></li>' +
			'<li><a href="http://codeigniter.com/">CodeIgniter Home</a></li>' +
		'</ul>' +

		'</td><td class="td_sep" valign="top">' +

		'<h3>Introduction</h3>' +
		'<ul>' +
			'<li><a href="'+base+'introduction/getting_started.html">Getting Started</a></li>' +
			'<li><a href="'+base+'introduction/reserved.html">Reserved Names</a></li>' +
			'<li><a href="'+base+'introduction/oo.html">Object Orientation</a></li>' +
			'<li><a href="'+base+'introduction/chaining.html">Chaining</a></li>' +
			'<li><a href="'+base+'introduction/last_accessed.html">Last Accessed Element</a></li>' +
			'<li><a href="'+base+'introduction/form.html">Creating a Form</a></li>' +
		'</ul>' +	
		
		'<h3>General Topics</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/automation.html">Automation</a></li>' +
			'<li><a href="'+base+'general/stylesheet.html">Stylesheet</a></li>' +			
			'<li><a href="'+base+'general/configuration.html">Config File</a></li>' +
			'<li><a href="'+base+'general/label_display.html">Label Display</a></li>' +			
			'<li><a href="'+base+'general/prefix_suffix.html">Prefixes / Suffixes</a></li>' +
			'<li><a href="'+base+'general/replace.html">Replace vs. Combine</a></li>' +
			'<li><a href="'+base+'general/attributes.html">Attributes</a></li>' +
			'<li><a href="'+base+'general/validation.html">Validation</a></li>' +
			'<li><a href="'+base+'general/error_display.html">Error Display</a></li>' +
		'</ul>' +
		
		'</td><td class="td_sep" valign="top">' +
				
		'<h3>Form Elements</h3>' +
		'<ul>' +
			'<li><a href="'+base+'elements/open.html">open</a></li>' +
			'<li><a href="'+base+'elements/fieldset.html">fieldset</a></li>' +
			'<li><a href="'+base+'elements/hidden.html">hidden</a></li>' +
			'<li><a href="'+base+'elements/label.html">label</a></li>' +
			'<li><a href="'+base+'elements/text.html">text</a></li>' +
			'<li><a href="'+base+'elements/password.html">password</a></li>' +
			'<li><a href="'+base+'elements/textarea.html">textarea</a></li>' +
			'<li><a href="'+base+'elements/upload.html">upload</a></li>' +
			'<li><a href="'+base+'elements/iupload.html">iupload</a></li>' +
			'<li><a href="'+base+'elements/select.html">select</a></li>' +
			'<li><a href="'+base+'elements/checkbox.html">checkbox</a></li>' +
			'<li><a href="'+base+'elements/checkgroup.html">checkgroup</a></li>' +
			'<li><a href="'+base+'elements/radiogroup.html">radiogroup</a></li>' +
			'<li><a href="'+base+'elements/recaptcha.html">recaptcha</a></li>' +
			'<li><a href="'+base+'elements/button.html">button</a></li>' +
			'<li><a href="'+base+'elements/image.html">image</a></li>' +
			'<li><a href="'+base+'elements/submit.html">submit</a></li>' +
			'<li><a href="'+base+'elements/reset.html">reset</a></li>' +
		'</ul>' +
		
		'<h3>Special Elements</h3>' +
		'<ul>' +
			'<li><a href="'+base+'elements/span.html">span</a></li>' +
			'<li><a href="'+base+'elements/html.html">html</a></li>' +
			'<li><a href="'+base+'elements/br.html">br</a></li>' +
			'<li><a href="'+base+'elements/hr.html">hr</a></li>' +
			'<li><a href="'+base+'elements/space.html">space</a></li>' +
		'</ul>' +		

		'</td><td class="td_sep" valign="top">' +

		'<h3>Methods</h3>' +
		'<ul>' +
			'<li><a href="'+base+'methods/action.html">action</a></li>' +
			'<li><a href="'+base+'methods/add_class.html">add_class</a></li>' +
			'<li><a href="'+base+'methods/add_error.html">add_error</a></li>' +
			'<li><a href="'+base+'methods/alias.html">alias</a></li>' +
			'<li><a href="'+base+'methods/clear.html">clear</a></li>' +			
			'<li><a href="'+base+'methods/config.html">config</a></li>' +			
			'<li><a href="'+base+'methods/col.html">col</a></li>' +			
			'<li><a href="'+base+'methods/get.html">get</a></li>' +
			'<li><a href="'+base+'methods/get_array.html">get_array</a></li>' +
			'<li><a href="'+base+'methods/get_post.html">get_post</a></li>' +
			'<li><a href="'+base+'methods/indent.html">indent</a></li>' +
			'<li><a href="'+base+'methods/lang.html">lang</a></li>' +
			'<li><a href="'+base+'methods/margin.html">margin</a></li>' +
			'<li><a href="'+base+'methods/method.html">method</a></li>' +
			'<li><a href="'+base+'methods/model.html">model</a></li>' +
			'<li><a href="'+base+'methods/onsuccess.html">onsuccess</a></li>' +
//			'<li><a href="'+base+'methods/postprocess.html" class="tbd">postprocess</a></li>' +
			'<li><a href="'+base+'methods/remove.html">remove</a></li>' +
			'<li><a href="'+base+'methods/rem_att.html">rem_att</a></li>' +			
			'<li><a href="'+base+'methods/rem_class.html">rem_class</a></li>' +
			'<li><a href="'+base+'methods/set_att.html">set_att</a></li>' +
			'<li><a href="'+base+'methods/set_error.html">set_error</a></li>' +
			'<li><a href="'+base+'methods/set_error_label.html">set_error_label</a></li>' +
//			'<li><a href="'+base+'methods/set_rule.html" class="tbd">set_rule</a></li>' +
			'<li><a href="'+base+'methods/set_value.html">set_value</a></li>' +						
			'<li><a href="'+base+'methods/validate.html">validate</a></li>' +
		'</ul>' +	
		
		'</td></tr></table>');
}