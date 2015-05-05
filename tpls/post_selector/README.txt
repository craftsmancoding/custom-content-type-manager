Do not edit these files directly!  Rather copy the entire tpls directory into your wp-content/uploads/cctm directory.  The plugin will use any .tpl files in that directory if they exist.

The post-selector refers to the thickbox that pops up when you choose values for a relation, image, or media field.  The tpls in this directory format the components and list items that make up that thickbox form.

You can add .tpls to the "item/", "search_forms/" or "wrappers/" directory -- simply name them after your field name.  E.g. if your custom field is named "my_pics", then the "item/my_pics.tpl" will be used to format single items displayed in the post-selector and "wrapper/my_pic.tpl" will wrap that output.  If no such files exist, then the _default.tpl files are used in each case.