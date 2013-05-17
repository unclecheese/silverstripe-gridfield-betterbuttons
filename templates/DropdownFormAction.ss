<button $AttributesHTML>
	<% if ButtonContent %>$ButtonContent<% else %>$Title<% end_if %>
</button>
<div id="$DropdownID" class="dropdown-form-action dropdown-form-action-tip">
    <ul class="dropdown-form-action-menu">
    	<% loop FieldList %>
        <li>$Field</li>
        <% end_loop %>
    </ul>
</div>
