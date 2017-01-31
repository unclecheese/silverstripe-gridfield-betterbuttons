<% if $IncludeFormTag %>
<form $FormAttributes data-layout-type="border">
<% end_if %>
	<div class="toolbar toolbar--north cms-content-header">
		<div class="cms-content-header-info">
			<% include SilverStripe\\Admin\\BackLink_Button %>
            <% with $Controller %>
                <% include SilverStripe\\Admin\\CMSBreadcrumbs %>
            <% end_with %>
		</div>
        <!-- <div class="cms-content-search">...</div> -->
    </div>

	<% with $Controller %>
		$EditFormTools
	<% end_with %>

	<div class="panel panel--padded panel--scrollable flexbox-area-grow <% if not $Fields.hasTabset %>cms-panel-padded<% end_if %>">
		<% if $Message %>
		<p id="{$FormName}_error" class="message $MessageType">$Message</p>
		<% else %>
		<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
		<% end_if %>

		<fieldset>
			<% if $Legend %><legend>$Legend</legend><% end_if %>
			<% loop $Fields %>
				$FieldHolder
			<% end_loop %>
			<div class="clear"><!-- --></div>
		</fieldset>
	</div>

	<div class="toolbar--south cms-content-actions cms-content-controls south">
		<% if $Actions %>
		<div class="btn-toolbar">
			<% loop $Actions %>
				$FieldHolder
			<% end_loop %>
			<% if $Controller.LinkPreview %>
			<a href="$Controller.LinkPreview" class="cms-preview-toggle-link ss-ui-button" data-icon="preview">
				<%t LeftAndMain.PreviewButton 'Preview' %> &raquo;
			</a>
			<% end_if %>
		</div>
		<% end_if %>
	</div>
	<div class="better-buttons-utils">
		<% loop $Utils %>
			$FieldHolder
		<% end_loop %>
	</div>

<% if $IncludeFormTag %>
</form>
<% end_if %>
